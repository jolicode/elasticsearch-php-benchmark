<?php

namespace ElasticSearchClients\Clients;

use \Elasticsearch\Client;
use \Elasticsearch\Endpoints\Indices\Refresh;

/**
 * https://github.com/elasticsearch/elasticsearch-php
 */
class Elasticsearch implements ClientInterface
{
    protected $client;
    protected $client_wrong_node;

    public function __construct()
    {
        $params = array('hosts' => array (
            '127.0.0.1:9200',
        ));
        $this->client = new Client($params);

        $params = array('hosts' => array (
            '127.0.0.1:9201',
            '127.0.0.1:9200',
        ));
        $this->client_wrong_node = new Client($params);
    }

    public function getDocument()
    {
        $getParams = array(
            'index' => self::INDEX_NAME,
            'type'  => self::TYPE_NAME,
            'id'    => self::EXISTING_ID
        );

        return $this->client->get($getParams);
    }

    public function searchDocument()
    {
        $params = array(
            'index' => self::INDEX_NAME,
            'type'  => self::TYPE_NAME,
            'body'  => array(
                'query' => array('query_string' => array(
                    'query' => self::ONE_DOC_TERM,
                ))
            )
        );

        $docs = $this->client->search($params);

        if ($docs['hits']['total'] != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $docs;
    }

    public function searchDocumentWithFacet()
    {
        $results = $this->client->search(array(
            'index' => self::INDEX_NAME,
            'type'  => self::TYPE_NAME,
            'body'  => array(
                'query' => array(
                    'match_all' => array()
                ),
                'facets' => array('names' =>
                      array ('terms' =>
                             array('field' => 'author.name')
                      )
                ),
            )
        ));

        return $results;
    }

    public function searchOnDisconnectNode()
    {
        $docs = $this->client_wrong_node->search(array(
            'index' => self::INDEX_NAME,
            'type'  => self::TYPE_NAME,
            'body'  => array(
                'query' => array('query_string' => array(
                    'query' => self::ONE_DOC_TERM,
                ))
            )
        ));

        if ($docs['hits']['total'] != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $docs;
    }

    public function searchSuggestion()
    {
        $results = $this->client->search(array(
            'index' => self::INDEX_NAME,
            'type'  => self::TYPE_NAME,
            'body'  => array(
                'query' => array(
                    'query_string' => array(
                        'query' => self::SUGGESTER_TEXT
                    )
                ),
                'suggest' => array(
                    'suggest1' => array (
                        'text' => self::SUGGESTER_TEXT,
                        'term' => array('field' => '_all', 'size' => 4)
                    )
                ),
            )
        ));

        $suggests = $results['suggest'];

        if (isset($suggests['suggest1'])) {
            return $suggests['suggest1'][0]['options'][0]['text'];
        } else {
            throw new \Exception("Suggestion is broken, no suggestion received");
        }
    }

    public function indexRefresh()
    {
        return $this->client->indices()->refresh(array('index' => self::INDEX_NAME));
    }

    public function indexStats()
    {
        $stats = $this->client->indices()->stats(array('index' => self::INDEX_NAME));

        return $stats['ok'];
    }
}
