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
        $size = $request->query->getInt('size', 20);
        $page = $request->query->getInt('page', 1);
        $response = [];
        $items = $service->get($page, $size);
        foreach ($items as $item) {
            $response[] = $item->toArray();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/transactions/{id}", name="transaction")
     */
    public function transactionAction(string $id)
    {
        $service = $this->get('api.block_service');
        $item = $service->findOneBy(['id' => $id]);

        return new JsonResponse($item->toArray());
    }
}
