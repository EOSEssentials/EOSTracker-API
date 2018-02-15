<?php

namespace AppBundle\Services;

use Symfony\Component\Cache\Simple\ApcuCache;

class ApcuCacheService implements CacheService
{
    /** @var ApcuCache */
    private $cache;

    public function __construct()
    {
        $this->cache = new ApcuCache();
    }

    public function get()
    {
        return $this->cache;
    }
}
