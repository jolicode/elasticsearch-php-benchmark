<?php

namespace ElasticSearchClients\Clients;

use Elastica\Client;
use Elastica\Facet\Terms;
use Elastica\Query;

/**
 * http://elastica.io/
 */
class Elastica implements ClientInterface
{
    protected $client;
    protected $client_wrong_node;

    public function __construct()
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
                array('host' => 'localhost', 'port' => 9201, 'persistent' => true),
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

    public function getDocument()
    {
        return $this->client->getIndex(self::INDEX_NAME)->getType(self::TYPE_NAME)->getDocument(self::EXISTING_ID);
    }

    public function searchDocument()
    {
        $docs = $this->client->getIndex(self::INDEX_NAME)->getType(self::TYPE_NAME)->search(self::ONE_DOC_TERM);

        if ($docs->getTotalHits() != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $docs;
    }

    public function searchDocumentWithFacet()
    {
        $query = Query::create("");
        $facet = new Terms('names');
        $facet->setField('author.name');

        $query->addFacet($facet);
        $search = $this->client->getIndex(self::INDEX_NAME)->getType(self::TYPE_NAME)->createSearch($query);

        return $search->search();
    }

    public function searchOnDisconnectNode()
    {
        $docs = $this->client_wrong_node->getIndex(self::INDEX_NAME)->getType(self::TYPE_NAME)->search(self::ONE_DOC_TERM);

        if ($docs->getTotalHits() != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $docs;
    }
}
