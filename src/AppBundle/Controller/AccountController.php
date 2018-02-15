<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends Controller
{
    const DEFAULT_SIZE = 30;

    /**
     * @Route("/accounts", name="accounts")
     */
    public function accountsAction(Request $request)
    {
        $db = $this->get('eos_explorer.mongo_service');

        $size = (int)$request->get('size', self::DEFAULT_SIZE);
        $filter = ($request->get('name')) ? ['name' => (string)$request->get('name')] : [];
        $items = [];
        $cursor = $db->get()->Accounts
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
     * @Route("/accounts/name", name="accounts_name")
     */
    public function accountsNameAction(Request $request)
    {
        $db = $this->get('eos_explorer.mongo_service');

        $size = (int)$request->get('size', self::DEFAULT_SIZE);
        $items = [];
        $cursor = $db->get()->Accounts
            ->find(['name' => ['$regex' => $request->get('name')]], ['name' => 1, '_id' => 0])
            ->skip((int)$request->get('page', 0) * $size)
            ->limit($size);

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }
}