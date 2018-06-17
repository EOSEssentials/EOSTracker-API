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
        $cache = $this->get('api.cache_service');

        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);
        $response = $cache->get()->get('transaction'.$size.'_'.$page);
        if (!$response) {
            $items = $service->get($page, $size);
            foreach ($items as $item) {
                $response[] = $item->toArray();
            }
            $cache->get()->set('transaction'.$size.'_'.$page, $response, $cache::DEFAULT_CACHING);

        }


        return new JsonResponse($response);
    }

    /**
     * @Route("/transactions/{id}", name="transaction")
     */
    public function transactionAction(string $id)
    {
        $service = $this->get('api.transaction_service');
        $item = $service->findOneBy(['id' => $id]);
        if (!$item) {
            return new JsonResponse(['error' => 'entity not found'], 404);
        }
        return new JsonResponse($item->toArray());
    }

    /**
     * @Route("/transactions/{id}/actions", name="transactions_action")
     */
    public function blockTransactionsAction(string $id, Request $request)
    {
        $service = $this->get('api.transaction_service');
        $actionService = $this->get('api.action_service');
        $cache = $this->get('api.cache_service');

        $item = $service->findOneBy(['id' => $id]);

        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);

        $response = $cache->get()->get('transactions_action_'.$id.'._'.$size.'_'.$page);
        if (!$response) {
            $items = $actionService->getForTransaction($item, $page, $size);
            foreach ($items as $item) {
                $response[] = $item->toArray();
            }

            $cache->get()->set('transactions_action_'.$id.'._'.$size.'_'.$page, $response, $cache::BIG_CACHING);
        }


        return new JsonResponse($response);
    }
}
