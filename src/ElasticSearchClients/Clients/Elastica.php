<?php

namespace ElasticSearchClients\Clients;

use Elastica\Client;
use Elastica\Facet\Terms;
use Elastica\Query;
use Elastica\Suggest\Term;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * http://elastica.io/
 */
class Elastica implements ClientInterface
{
    protected $client;
    protected $client_wrong_node;

    public function __construct($benchmarkType)
    {
        $this->client = new Client(array(
            'path'            => null,
            'url'             => null,
            'proxy'           => null,
            'transport'       => null,
            'persistent'      => true,
            'timeout'         => null,
            'connections'     => array(
                array('host' => 'localhost', 'port' => 9200, 'persistent' => true),
            ), // host, port, path, timeout, transport, persistent, timeout, config -> (curl, headers, url)
            'roundRobin'      => false,
            'log'             => false,
            'retryOnConflict' => 0,
        ));

        $this->client_wrong_node = new Client(array(
            'path'            => null,
            'url'             => null,
            'proxy'           => null,
            'transport'       => null,
            'persistent'      => true,
            'timeout'         => null,
            'connections'     => array(
                array('host' => 'localhost', 'port' => 9201, 'persistent' => true),
                array('host' => 'localhost', 'port' => 9200, 'persistent' => true),
            ), // host, port, path, timeout, transport, persistent, timeout, config -> (curl, headers, url)
            'roundRobin'      => false,
            'log'             => false,
            'retryOnConflict' => 0,
        ));
    }

    public function getDocument(Stopwatch &$stopwatch)
    {
        $stopwatch->start('getDocument');
        $this->client->getIndex(self::INDEX_NAME)->getType(self::TYPE_NAME)->getDocument(self::EXISTING_ID);
        return $stopwatch->stop('getDocument');

    }

    public function searchDocument(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchDocument');
        $docs = $this->client->getIndex(self::INDEX_NAME)->getType(self::TYPE_NAME)->search(self::ONE_DOC_TERM);
        $event = $stopwatch->stop('searchDocument');

        if ($docs->getTotalHits() != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $event;
    }

    public function searchDocumentWithFacet(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchDocumentWithFacet');

        $query = Query::create("");
        $facet = new Terms('names');
        $facet->setField('author.name');
        $query->addFacet($facet);
        $search = $this->client->getIndex(self::INDEX_NAME)->getType(self::TYPE_NAME)->createSearch($query);

        $search->search();

        return $stopwatch->stop('searchDocumentWithFacet');
    }

    public function searchOnDisconnectNode(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchOnDisconnectNode');
        $docs = $this->client_wrong_node->getIndex(self::INDEX_NAME)->getType(self::TYPE_NAME)->search(self::ONE_DOC_TERM);
        $event = $stopwatch->stop('searchOnDisconnectNode');

        if ($docs->getTotalHits() != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $event;

    }

    public function searchSuggestion(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchSuggestion');
        $query = Query::create(self::SUGGESTER_TEXT);

        $suggest = new Term();
        $suggest->addTerm('suggest1', array('text' => self::SUGGESTER_TEXT, 'term' => array('field' => '_all', 'size' => 4)));

        $search = $this->client->getIndex(self::INDEX_NAME)->getType(self::TYPE_NAME)->createSearch($query);
        $search->addSuggest($suggest);

        $results = $search->search();
        $suggests = $results->getSuggests();

        $event = $stopwatch->stop('searchSuggestion');

        if (!isset($suggests['suggest1'])) {
            throw new \Exception("Suggestion is broken, no suggestion received");
        }

        return $event;
    }

    public function indexRefresh(Stopwatch &$stopwatch)
    {
        $stopwatch->start('indexRefresh');
        $index = $this->client->getIndex(self::INDEX_NAME);
        $index->refresh();
        return $stopwatch->stop('indexRefresh');
    }

    public function indexStats(Stopwatch &$stopwatch)
    {
        $stopwatch->start('indexStats');
        $index = $this->client->getIndex(self::INDEX_NAME);
        $index->getStats()->getData();
        return $stopwatch->stop('indexStats');
    }
}
