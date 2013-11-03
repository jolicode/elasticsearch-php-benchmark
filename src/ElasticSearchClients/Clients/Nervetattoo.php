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
            'servers' => '127.0.0.1:9200',
            'protocol' => 'http',
            'index' => self::INDEX_NAME,
            'type' => self::TYPE_NAME
        ));

        $this->client_wrong_node = Client::connection(array(
            'servers' => '127.0.0.1:9201',
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
}
