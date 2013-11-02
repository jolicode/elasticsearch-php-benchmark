<?php
namespace ElasticSearchClients\Clients;

interface ClientInterface
{
    const INDEX_NAME        = "client_bench";
    const TYPE_NAME         = "post";
    const EXISTING_ID       = "1";
    const ONE_DOC_TERM      = "dimensionnelles";

    public function getDocument();

    public function searchDocument();
}
