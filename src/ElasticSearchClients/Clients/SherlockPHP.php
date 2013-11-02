<?php

namespace ElasticSearchClients\Clients;

use Sherlock\components\facets\Terms;
use Sherlock\Sherlock as Sherlock;

/**
 * http://sherlockphp.org/
 */
class SherlockPHP implements ClientInterface
{
    protected $client;
    protected $client_wrong_node;

    public function __construct()
    {
        $this->client = new \Sherlock\Sherlock();
        $this->client->addNode("localhost", 9201);
        $this->client->addNode("localhost", 9200);

        $this->client_wrong_node = new \Sherlock\Sherlock();
        $this->client_wrong_node->addNode("localhost", 9200);
        $this->client_wrong_node->addNode("localhost", 9201);
    }

    public function getDocument()
    {
        $q = $this->client->getDocument();
        $q->id(self::EXISTING_ID);
        $q->type(self::TYPE_NAME);
        $q->index(self::INDEX_NAME);

        return $q->execute();
    }

    public function searchDocument()
    {
        $s = $this->client->search();
        $s->index(self::INDEX_NAME);
        $s->type(self::TYPE_NAME);

        $qs = Sherlock::queryBuilder()->QueryString()->query(self::ONE_DOC_TERM);

        $docs = $s->query($qs)->execute();

        if ($docs->total != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $docs;
    }

    public function searchDocumentWithFacet()
    {
        $s = $this->client->search();
        $s->index(self::INDEX_NAME);
        $s->type(self::TYPE_NAME);

        $ma = Sherlock::queryBuilder()->MatchAll();

        $facet = new Terms(array('facetname' => 'names'));
        $facet->fields(array('author.name'));

        $docs = $s->query($ma)->facets($facet)->execute();

        return $docs;
    }

    public function searchOnDisconnectNode()
    {
        $s = $this->client_wrong_node->search();
        $s->index(self::INDEX_NAME);
        $s->type(self::TYPE_NAME);

        $qs = Sherlock::queryBuilder()->QueryString()->query(self::ONE_DOC_TERM);

        $docs = $s->query($qs)->execute();

        if ($docs->total != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $docs;
    }
}
