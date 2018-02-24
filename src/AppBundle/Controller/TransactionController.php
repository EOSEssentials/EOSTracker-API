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
        $filter = (object) [];
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

        $cursor = $db->get()->Transactions->aggregateCursor( [
            [ '$match' => $filter ],
            [ '$lookup' => [
                     'from' => 'Blocks',
                     'localField' => 'block_id',
                     'foreignField' => 'block_id',
                     'as' => 'block_docs' ] ], 
            [ '$unwind' => '$block_docs' ], 
            [ '$project' => [
                     'ref_block_timestamp' => '$block_docs.timestamp',
                     'transaction_id' => 1,
                     'sequence_num' => 1,
                     'block_id' => 1,
                     'ref_block_num' => 1,
                     'ref_block_prefix' => 1,
                     'expiration' =>  1,
                     'signatures' => 1,
                     'actions' => 1 ] ],
            [ '$sort' => [ 'ref_block_timestamp' => -1 ] ],
            [ '$skip' => (int)$request->get('page', 0) * $size ],
            [ '$limit' => $size ]
        ] );

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }
}
