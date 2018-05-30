<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends Controller
{
    /**
     * @Route("/accounts", name="accounts")
     */
    public function accountsAction(Request $request)
    {
        $service = $this->get('api.account_service');
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
     * @Route("/accounts/{name}", name="account")
     */
    public function accountAction(string $name)
    {
        $service = $this->get('api.account_service');
        $item = $service->findOneBy(['name' => $name]);

        return new JsonResponse($item->toArray());
    }
}