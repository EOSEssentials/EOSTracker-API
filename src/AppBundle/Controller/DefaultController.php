<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\ApcuCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    const DEFAULT_SIZE = 30;
    const DEFAULT_CACHING = 4;

    /**
     * @Route("/stats", name="stats")
     */
    public function statsAction()
    {
        $db = $this->getDB();
        $cache = $this->getCache();

        $result = $cache->get('stats.action');
        if (!$result) {
            $result = [
                $db->Blocks->count(),
                $db->Transactions->count(),
                $db->Accounts->count(),
                $db->Messages->count(),
            ];
            $cache->set('stats.action', $result, self::DEFAULT_CACHING);
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/blocks", name="blocks")
     */
    public function blocksAction(Request $request)
    {
        $size = (int)$request->get('size', self::DEFAULT_SIZE);
        $filter = ($request->get('block_num')) ? ['block_num' => (int)$request->get('block_num')] : [];
        $items = [];

        $cursor = $this->getDB()->Blocks
            ->find($filter)
            ->sort(['block_num' => -1])
            ->skip((int)$request->get('page', 0) * $size)
            ->limit($size);

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }

    /**
     * @Route("/transactions", name="transactions")
     */
    public function transactionsAction(Request $request)
    {
        $size = (int)$request->get('size', self::DEFAULT_SIZE);
        $filter = [];
        if ($request->get('transaction_id')) {
            $filter = ['transaction_id' => (string)$request->get('transaction_id')];
        }
        if ($request->get('scope')) {
            $filter = ['scope' => (string)$request->get('scope')];
        }
        if ($request->get('block_id')) {
            $filter = ['block_id' => (string)$request->get('block_id')];
        }
        $items = [];
        $cursor = $this->getDB()->Transactions
            ->find($filter)
            ->sort(['createdAt' => -1])
            ->skip((int)$request->get('page', 0) * $size)
            ->limit($size);

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }

    /**
     * @Route("/accounts", name="accounts")
     */
    public function accountsAction(Request $request)
    {
        $size = (int)$request->get('size', self::DEFAULT_SIZE);
        $filter = ($request->get('name')) ? ['name' => (string)$request->get('name')] : [];
        $items = [];
        $cursor = $this->getDB()->Accounts
            ->find($filter)
            ->sort(['createdAt' => -1])
            ->skip((int)$request->get('page', 0) * $size)
            ->limit($size);

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }

    /**
     * @Route("/producers", name="producers")
     */
    public function producersAction()
    {
        $cache = $this->getCache();
        $items = $cache->get('producers.action');
        if (!$items) {
            $cursor = $this->getDB()->Blocks->aggregate([
                ['$group' => ["_id" => '$producer_account_id', "count" => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
            ]);

            foreach ($cursor['result'] as $key => $document) {
                $account = $this->getDB()->Accounts->findOne(['name' => $document['_id']]);
                $items[$key] = $account;
                $items[$key]['count'] = $document['count'];
            }

            $cache->set('producers.action', $items, 30);
        }

        return new JsonResponse($items);
    }

    /**
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request)
    {
        $cache = $this->getCache();
        $query = $request->get('query');

        $result = $cache->get('search.action.'.$query);
        if (!$result) {
            $result = $this->getDB()->Blocks->findOne(['block_id' => $query]);

            $cache->set('search.action.'.$query, $result, 300);
        }

        return new JsonResponse($result);
    }

    private function getDB()
    {
        $mongo = new \MongoClient($this->getParameter('mongodb_server'));

        return $mongo->selectDB($this->getParameter('db_name'));
    }

    private function getCache()
    {
        $cache = new ApcuCache();

        return $cache;
    }
}
