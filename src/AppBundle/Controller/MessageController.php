<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\ApcuCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends Controller
{

    const DEFAULT_SIZE = 30;
    const DEFAULT_CACHING = 4;

    /**
     * @Route("/messages", name="messages")
     */
    public function messagesAction(Request $request)
    {
        $size = (int)$request->get('size', self::DEFAULT_SIZE);
        $filter = ($request->get('transaction_id')) ? [
            'transaction_id' => (string)$request->get('transaction_id'),
            'message_id' => (int)$request->get('msg_id'),
        ] : [];
        $items = [];
        $cursor = $this->getDB()->Messages
            ->find($filter)
            ->sort(['createdAt' => -1])
            ->skip((int)$request->get('page', 0) * $size)
            ->limit($size);

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }


    private function getDB()
    {
        $mongo = new \MongoClient($this->getParameter('mongodb_server'));

        return $mongo->selectDB($this->getParameter('db_name'));
    }
}