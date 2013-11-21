<?php

namespace ElasticSearchClients\Clients;

use \Elasticsearch\Client;
use \Elasticsearch\Endpoints\Indices\Refresh;
use Symfony\Component\Stopwatch\Stopwatch;

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

    public function getDocument(Stopwatch &$stopwatch)
    {
        $stopwatch->start('getDocument');
        $this->client->get(array(
                'index' => self::INDEX_NAME,
                'type'  => self::TYPE_NAME,
                'id'    => self::EXISTING_ID
            ));
        return $stopwatch->stop('getDocument');

    }

    public function searchDocument(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchDocument');
        $docs = $this->client->search(array(
                'index' => self::INDEX_NAME,
                'type'  => self::TYPE_NAME,
                'body'  => array(
                    'query' => array('query_string' => array(
                        'query' => self::ONE_DOC_TERM,
                    ))
                )
            ));
        $event = $stopwatch->stop('searchDocument');

        if ($docs['hits']['total'] != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $event;

    }

    public function searchDocumentWithFacet(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchDocumentWithFacet');
        $this->client->search(array(
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
        return $stopwatch->stop('searchDocumentWithFacet');
    }

    public function searchOnDisconnectNode(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchOnDisconnectNode');
        $docs = $this->client_wrong_node->search(array(
                'index' => self::INDEX_NAME,
                'type'  => self::TYPE_NAME,
                'body'  => array(
                    'query' => array('query_string' => array(
                        'query' => self::ONE_DOC_TERM,
                    ))
                )
            ));
        $event = $stopwatch->start('searchOnDisconnectNode');

        if ($docs['hits']['total'] != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $event;
    }

    public function searchSuggestion(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchSuggestion');
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
        $event = $stopwatch->stop('searchSuggestion');

        $suggests = $results['suggest'];

        if (!isset($suggests['suggest1'])) {
            throw new \Exception("Suggestion is broken, no suggestion received");
        }

        return $event;
    }

    public function indexRefresh(Stopwatch &$stopwatch)
    {
        $stopwatch->start('indexRefresh');
        $this->client->indices()->refresh(array('index' => self::INDEX_NAME));
        return $stopwatch->stop('indexRefresh');
    }

    public function indexStats(Stopwatch &$stopwatch)
    {
        $stopwatch->start('indexStats');
        $this->client->indices()->stats(array('index' => self::INDEX_NAME));
        return $stopwatch->stop('indexStats');
    }
}
