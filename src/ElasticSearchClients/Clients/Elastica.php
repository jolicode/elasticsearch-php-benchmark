<?php

namespace ElasticSearchClients\Clients;

use Elastica\Client;

/**
 * http://elastica.io/
 */
class Elastica implements ClientInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(array(
            'host'            => 'localhost',
            'port'            => 9200,
            'path'            => null,
            'url'             => null,
            'proxy'           => null,
            'transport'       => null,
            'persistent'      => true,
            'timeout'         => null,
            'connections'     => array(), // host, port, path, timeout, transport, persistent, timeout, config -> (curl, headers, url)
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
}
