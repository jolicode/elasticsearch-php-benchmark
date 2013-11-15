<?php
namespace ElasticSearchClients\Clients;

interface ClientInterface
{
    const INDEX_NAME        = "client_bench";
    const TYPE_NAME         = "post";
    const EXISTING_ID       = "1";
    const ONE_DOC_TERM      = "dimensionnelles";
    const SUGGESTER_TEXT    = "lags";

    public function getDocument();

    public function searchDocument();

    public function searchDocumentWithFacet();

    public function searchOnDisconnectNode();

    public function searchSuggestion();

    public function indexStats();

    public function indexRefresh();
}
