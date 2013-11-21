<?php
namespace ElasticSearchClients\Clients;

use Symfony\Component\Stopwatch\Stopwatch;

interface ClientInterface
{
    const INDEX_NAME        = "client_bench";
    const TYPE_NAME         = "post";
    const EXISTING_ID       = "1";
    const ONE_DOC_TERM      = "dimensionnelles";
    const SUGGESTER_TEXT    = "lags"; // suggest "logs" :)

    public function getDocument(Stopwatch &$stopwatch);

    public function searchDocument(Stopwatch &$stopwatch);

    public function searchDocumentWithFacet(Stopwatch &$stopwatch);

    public function searchOnDisconnectNode(Stopwatch &$stopwatch);

    public function searchSuggestion(Stopwatch &$stopwatch);

    public function indexStats(Stopwatch &$stopwatch);

    public function indexRefresh(Stopwatch &$stopwatch);
}
