<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends Controller
{

    const DEFAULT_SIZE = 30;

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
        $db = $this->get('eos_explorer.mongo_service');

        $cursor = $db->get()->Transactions
            ->find($filter)
            ->sort(['createdAt' => -1])
            ->skip((int)$request->get('page', 0) * $size)
            ->limit($size);

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }
}