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
        $size = $request->query->getInt('size', 30);
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

    /**
     * @Route("/accounts/{name}/actions", name="account_actions")
     */
    public function accountActionsAction(string $name, Request $request)
    {
        $service = $this->get('api.action_service');
        $accountService = $this->get('api.account_service');
        $account = $accountService->findOneBy(['name' => $name]);
        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);
        $response = [];
        $items = $service->getForAccount($account, $page, $size);
        foreach ($items as $item) {
            $response[] = $item->toArray();
        }

        return new JsonResponse($response);
    }
}