<?php

namespace ElasticSearchClients\Clients;

use Sherlock\components\facets\Terms;
use Sherlock\requests\Command;
use Sherlock\Sherlock as Sherlock;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * http://sherlockphp.org/
 */
class SherlockPHP implements ClientInterface
{
    protected $client;
    protected $client_wrong_node;

    public function __construct($benchmarkType)
    {
        $this->client = new \Sherlock\Sherlock();
        $this->client->addNode("127.0.0.1", 9200);
        //$this->client->addNode("localhost", 9201);
        //$this->client->autodetectClusterState(); // is called on 0 nodes otherwise

        $this->client_wrong_node = new \Sherlock\Sherlock();
        $this->client_wrong_node->addNode("localhost", 9200);
        $this->client_wrong_node->addNode("127.0.0.1", 9201);
    }

    public function getDocument(Stopwatch &$stopwatch)
    {
        $stopwatch->start('getDocument');
        $q = $this->client->getDocument();
        $q->id(self::EXISTING_ID);
        $q->type(self::TYPE_NAME);
        $q->index(self::INDEX_NAME);

        $q->execute();

        return $stopwatch->stop('getDocument');
    }

    public function searchDocument(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchDocument');
        $s = $this->client->search();
        $s->index(self::INDEX_NAME);
        $s->type(self::TYPE_NAME);

        $qs = Sherlock::queryBuilder()->QueryString()->query(self::ONE_DOC_TERM);

        $docs = $s->query($qs)->execute();
        $event = $stopwatch->stop('searchDocument');

        if ($docs->total != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $event;
    }

    public function searchDocumentWithFacet(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchDocumentWithFacet');
        $s = $this->client->search();
        $s->index(self::INDEX_NAME);
        $s->type(self::TYPE_NAME);

        $ma = Sherlock::queryBuilder()->MatchAll();

        $facet = new Terms(array('facetname' => 'names'));
        $facet->fields(array('author.name'));

        $s->query($ma)->facets($facet)->execute();

        return $stopwatch->stop('searchDocumentWithFacet');

    }

    public function searchOnDisconnectNode(Stopwatch &$stopwatch)
    {
        $stopwatch->start('searchOnDisconnectNode');
        $s = $this->client_wrong_node->search();
        $s->index(self::INDEX_NAME);
        $s->type(self::TYPE_NAME);

        $qs = Sherlock::queryBuilder()->QueryString()->query(self::ONE_DOC_TERM);

        $docs = $s->query($qs)->execute();
        $event = $stopwatch->stop('searchOnDisconnectNode');

        if ($docs->total != 1) {
            throw new \Exception("Search does not match 1 document");
        }

        return $event;
    }

    /**
     * We CAN'T do Suggestion with Sherlock
     */
    public function searchSuggestion(Stopwatch &$stopwatch)
    {
        throw new \Exception("Can't perform suggestion request");

        $s = $this->client->search();
        $s->index(self::INDEX_NAME);
        $s->type(self::TYPE_NAME);

        $qs = Sherlock::queryBuilder()->QueryString()->query(self::SUGGESTER_TEXT);

        $docs = $s
            ->query($qs)
            ->suggest(array(
                'suggest1' => array('text' => self::SUGGESTER_TEXT, 'term' => array('field' => '_all', 'size' => 4))
            ))->execute();

        $suggests = isset($docs->responseData['suggests']) ? $docs->responseData['suggests'] : false;

        if (isset($suggests['suggest1'])) {
            return $suggests['suggest1']['options'][0]['text'];
        } else {
            throw new \Exception("Suggestion is broken, no suggestion received");
        }
    }

    /**
     * Not supported
     */
    public function indexRefresh(Stopwatch &$stopwatch)
    {
        throw new \Exception("Can't perform refresh request");
    }

    /**
     * Not supported
     */
    public function indexStats(Stopwatch &$stopwatch)
    {
        throw new \Exception("Can't perform stats request");

        $command = new Command();
        $command->index(self::INDEX_NAME)->action('stats');

        // ???
    }
}
