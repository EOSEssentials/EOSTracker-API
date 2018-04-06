<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WalletController extends Controller
{

    const DEFAULT_SIZE = 30;

    /**
     * @Route("/wallet/actions", name="wallet_actions")
     */
    public function messagesByHandlerAction(Request $request)
    {
        $db = $this->get('eos_explorer.mongo_service');

        $size = (int)$request->get('size', self::DEFAULT_SIZE);
        $scope = $request->get('scope');
        $handler = $request->get('handler');
        $items = [];
        $cursor = $db->get()->Transactions
            ->aggregate([
                [
                    '$lookup' =>
                        [
                            'from' => 'Actions',
                            'localField' => 'transaction_id',
                            'foreignField' => 'transaction_id',
                            'as' => 'message',
                        ],
                ]
                ,
                [
                    '$match' => [
                        '$and' => [
                            ["scope" => $scope],
                            ["message.handler_account_name" => $handler],
                        ],
                    ],
                ],
                ['$unwind' => '$action'],
                ['$sort' => ['createdAt' => -1]],
                ['$skip' => (int)$request->get('page', 0) * $size],
                ['$limit' => $size],
            ]);

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }

    /**
     * @Route("/wallet/actions/groups", name="actions_count")
     */
    public function groupMessagesByHandlerAction(Request $request)
    {

        $db = $this->get('eos_explorer.mongo_service');

        $scope = $request->get('scope');
        $items = [];
        $cursor = $db->get()->Transactions
            ->aggregate([
                [
                    '$lookup' =>
                        [
                            'from' => 'Actions',
                            'localField' => 'transaction_id',
                            'foreignField' => 'transaction_id',
                            'as' => 'actions',
                        ],
                ]
                ,
                ['$match' => ["scope" => $scope]],
                ['$unwind' => '$actions'],
                [
                    '$group' => [
                        '_id' => '$actions.handler_account_name',
                        'count' => ['$sum' => 1],
                    ],
                ],
            ]);

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }
}