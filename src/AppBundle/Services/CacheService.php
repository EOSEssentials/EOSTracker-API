<?php

namespace AppBundle\Services;


interface CacheService
{
    const DEFAULT_CACHING = 10;

    public function get();
}
