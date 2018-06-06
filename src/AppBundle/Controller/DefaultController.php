<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/stats", name="stats")
     */
    public function statsAction()
    {
        $cache = $this->get('api.cache_service');

        $result = $cache->get()->get('stats.action');
        if (!$result) {
            $result = [
                $this->get('api.block_service')->count([]),
                $this->get('api.transaction_service')->count([]),
                $this->get('api.account_service')->count([]),
                $this->get('api.action_service')->count([]),
            ];
            $cache->get()->set('stats.action', $result, $cache::DEFAULT_CACHING);
        }

        return new JsonResponse($result);
    }
}
