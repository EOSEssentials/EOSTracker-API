<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProducerController extends Controller
{

    const DEFAULT_SIZE = 30;
    const DEFAULT_CACHE = 30;

    /**
     * @Route("/producers", name="producers")
     */
    public function producersAction()
    {
        $cache = $this->get('eos_explorer.cache_service');
        $db = $this->get('eos_explorer.mongo_service');

        $items = $cache->get()->get('producers.action');
        if (!$items) {
            $cursor = $db->get()->Blocks->aggregate([
                ['$group' => ["_id" => '$producer_account_id', "count" => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
            ], ["cursor" => [ "batchSize" => 0 ]]);

            foreach ($cursor['result'] as $key => $document) {
                $account = $db->get()->Accounts->findOne(['name' => $document['_id']]);
                $items[$key] = $account;
                $items[$key]['count'] = $document['count'];
            }

            $cache->get()->set('producers.action', $items, self::DEFAULT_CACHE);
        }

        return new JsonResponse($items);
    }
}