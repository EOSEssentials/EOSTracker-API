<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActionController extends Controller
{
    const DEFAULT_SIZE = 30;

    /**
     * @Route("/actions", name="actions")
     */
    public function messagesAction(Request $request)
    {
        $db = $this->get('eos_explorer.mongo_service');

        $size = (int)$request->get('size', self::DEFAULT_SIZE);
        $filter = ($request->get('transaction_id')) ? [
            'transaction_id' => (string)$request->get('transaction_id'),
            'action_id' => (int)$request->get('action_id'),
        ] : [];
        $items = [];
        $cursor = $db->get()->Actions
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
