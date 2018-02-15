<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    const DEFAULT_SIZE = 30;

    /**
     * @Route("/stats", name="stats")
     */
    public function statsAction()
    {
        $db = $this->get('eos_explorer.mongo_service');
        $cache = $this->get('eos_explorer.cache_service');

        $result = $cache->get()->get('stats.action');
        if (!$result) {
            $result = [
                $db->get()->Blocks->count(),
                $db->get()->Transactions->count(),
                $db->get()->Accounts->count(),
                $db->get()->Messages->count(),
            ];
            $cache->get()->set('stats.action', $result, $cache::DEFAULT_CACHING);
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request)
    {
        $db = $this->get('eos_explorer.mongo_service');
        $cache = $this->get('eos_explorer.cache_service');

        $query = $request->get('query');

        $result = $cache->get()->get('search.action.'.$query);
        if (!$result) {
            $result = $db->get()->Blocks->findOne(['block_id' => $query]);

            $cache->get()->set('search.action.'.$query, $result, 300);
        }

        return new JsonResponse($result);
    }
}
