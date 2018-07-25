<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends Controller
{
    /**
     * @Route("/transactions", name="transactions")
     */
    public function transactionsAction(Request $request)
    {
        $service = $this->get('api.transaction_service');
        $data = [];

        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);
        $result = $this->get('cache.app')->getItem('transactions_'.$size.'_'.$page);
        if (!$result->isHit()) {
            $items = $service->get($page, $size);
            foreach ($items as $item) {
                $data[] = $item->toArray();
            }

            $result->set($data)->expiresAfter(new \DateInterval('PT10S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }

    /**
     * @Route("/v2/transactions", name="transactions2")
     */
    public function transactions2Action(Request $request)
    {
        $size = min(30, $request->query->getInt('size', 30));
        $page = max(0, $request->query->getInt('page', 1) - 1);
        $items = [];

        $result = $this->get('cache.app')->getItem('transactions_v2_'.$size.'_'.$page);
        if (!$result->isHit()) {
            $client = new \MongoClient($this->getParameter('env(MONGO_URL)'));
            $mongo = $client->selectDB('EOS');

            $cursor = $mongo->transactions
                ->find([], [])
                ->sort(['createdAt' => -1])
                ->skip($page * $size)
                ->limit($size);
            foreach ($cursor as $key => $document) {
                $items[] = $document;
            }

            $result->set($items)->expiresAfter(new \DateInterval('PT10S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }

    /**
     * @Route("/transactions/{id}", name="transaction")
     */
    public function transactionAction(string $id)
    {
        $result = $this->get('cache.app')->getItem('transaction_'.$id);
        if (!$result->isHit()) {
            $service = $this->get('api.transaction_service');
            $item = $service->findOneBy(['id' => $id]);
            if (!$item) {
                return new JsonResponse(['error' => 'entity not found'], 404);
            }

            $result->set($item->toArray());
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }

    /**
     * @Route("/transactions/{id}/actions", name="transactions_action")
     */
    public function blockTransactionsAction(string $id, Request $request)
    {
        $service = $this->get('api.transaction_service');
        $actionService = $this->get('api.action_service');
        $data = [];

        $item = $service->findOneBy(['id' => $id]);

        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);

        $result = $this->get('cache.app')->getItem('transactions_action_'.$id.'._'.$size.'_'.$page);
        if (!$result->isHit()) {
            $items = $actionService->getForTransaction($item, $page, $size);
            foreach ($items as $item) {
                $data[] = $item->toArray();
            }

            $result->set($data);
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }
}
