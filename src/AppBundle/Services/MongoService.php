<?php

namespace AppBundle\Services;

class MongoService
{
    private $mongo;

    public function __construct($server, $dbName)
    {
        $mongo = new \MongoClient($server);
        $this->mongo = $mongo->selectDB($dbName);
    }

    public function get()
    {
        return $this->mongo;
    }
}
