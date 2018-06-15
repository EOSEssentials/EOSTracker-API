<?php

namespace AppBundle\Services;


interface CacheService
{
    const DEFAULT_CACHING = 10;
    const BIG_CACHING = 300;

    public function get();
}
