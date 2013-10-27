<?php
namespace Clients;

interface ClientInterface
{
    public function getDocumentByTypeAndId($type, $id);
}
