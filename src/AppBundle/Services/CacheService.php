<?php

namespace AppBundle\Services;


interface CacheService
{
    const DEFAULT_CACHING = 4;

    public function get();
}
