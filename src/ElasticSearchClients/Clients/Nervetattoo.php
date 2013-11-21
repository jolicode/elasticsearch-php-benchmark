<?php

namespace ElasticSearchClients\Clients;

use \ElasticSearch\Client;
use Symfony\Component\Stopwatch\Stopwatch;

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

    public function getDocument(Stopwatch &$stopwatch)
    {
        $stopwatch->start('getDocument');
        $this->client->get(self::EXISTING_ID);
        return $stopwatch->stop('getDocument');
    }

    public function searchDocument(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchDocument');
        $docs = $this->client->search(self::ONE_DOC_TERM);
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
            'query' => array(
                'match_all' => array()
            ),
            'facets' => array('names' =>
                  array ('terms' =>
                         array('field' => 'author.name')
                  )
            ),
        ));
        return $stopwatch->stop('searchDocumentWithFacet');
    }

    public function searchOnDisconnectNode(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchOnDisconnectNode');
        $docs = $this->client_wrong_node->search(self::ONE_DOC_TERM);
        $event = $stopwatch->stop('searchOnDisconnectNode');

        if ($docs['hits']['total'] != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $event;
    }

    public function searchSuggestion(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchSuggestion');
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
        $this->client->refresh();
        return $stopwatch->stop('indexRefresh');
    }

    public function indexStats(Stopwatch &$stopwatch)
    {
        $stopwatch->start('indexStats');
        $this->client->request('/'.self::INDEX_NAME.'/_stats', "GET", false, true);
        return $stopwatch->stop('indexStats');
    }
}
