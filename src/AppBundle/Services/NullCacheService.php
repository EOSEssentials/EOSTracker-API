<?php

namespace AppBundle\Services;

use Symfony\Component\Cache\Simple\NullCache;

class NullCacheService implements CacheService
{
    /** @var NullCache */
    private $cache;

    public function __construct()
    {
        $this->cache = new NullCache();
    }

    public function get()
    {
        return $this->cache;
    }
}
