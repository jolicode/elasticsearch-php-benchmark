<?php

namespace ElasticSearchClients\Clients;

use \ElasticSearch\Client;

/**
 * https://github.com/nervetattoo/elasticsearch
 */
class Nervetattoo implements ClientInterface
{
    protected $client;
    protected $client_wrong_node;

    public function __construct()
    {
        $this->client = Client::connection(array(
            'servers' => array('127.0.0.1:9200'), // only the first is used...
            'protocol' => 'http',
            'index' => self::INDEX_NAME,
            'type' => self::TYPE_NAME
        ));

        $this->client_wrong_node = Client::connection(array(
            'servers' => array('127.0.0.1:9201', '127.0.0.1:9200'),
            'protocol' => 'http',
            'index' => self::INDEX_NAME,
            'type' => self::TYPE_NAME
        ));
    }

    public function getDocument()
    {
        return $this->client->get(self::EXISTING_ID);
    }

    public function searchDocument()
    {
        $docs = $this->client->search(self::ONE_DOC_TERM);

        if ($docs['hits']['total'] != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $docs;
    }

    public function searchDocumentWithFacet()
    {
        $results = $this->client->search(array(
            'query' => array(
                'match_all' => array()
            ),
            'facets' => array('names' =>
                  array ('terms' =>
                         array('field' => 'author.name')
                  )
            ),
        ));

        return $results;
    }

    public function searchOnDisconnectNode()
    {
        $docs = $this->client_wrong_node->search(self::ONE_DOC_TERM);

        if ($docs['hits']['total'] != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $docs;
    }

    public function searchSuggestion()
    {
        $results = $this->client->search(array(
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
        return $this->client->refresh();
    }

    public function indexStats()
    {
        $stats = $this->client->request('/'.self::INDEX_NAME.'/_stats', "GET", false, true);

        return $stats['ok'];
    }
}
