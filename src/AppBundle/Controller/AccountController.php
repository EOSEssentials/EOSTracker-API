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
        $cache = $this->get('api.cache_service');

        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);
        $response = $cache->get()->get('account'.$size.'_'.$page);
        if (!$response) {
            $items = $service->get($page, $size);
            foreach ($items as $item) {
                $response[] = $item->toArray();
            }

            $cache->get()->set('accounts'.$size.'_'.$page, $response, $cache::DEFAULT_CACHING);

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
     * @Route("/accounts/key/{key}", name="account_by_key")
     */
    public function accountByKeyAction(string $key)
    {
        $service = $this->get('api.account_service');
        $item = $service->withPublicKey($key);
        return new JsonResponse(['name' => isset($item[0]) ? $item[0]['account']: null]);
    }

    /**
     * @Route("/accounts/{name}/actions", name="account_actions")
     */
    public function accountActionsAction(string $name, Request $request)
    {
        $service = $this->get('api.action_service');
        $accountService = $this->get('api.account_service');
        $cache = $this->get('api.cache_service');

        $account = $accountService->findOneBy(['name' => $name]);

        if(!$account) {
            return new JsonResponse('Not found', 404);
        }
        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);

        $response = $cache->get()->get('account_'.$name.'_'.$size.'_'.$page);
        if (!$response) {
            $items = $service->getForAccount($account, $page, $size);
            foreach ($items as $item) {
                $response[] = $item->toArray();
            }

            $cache->get()->set('account_'.$name.'_'.$size.'_'.$page, $response, $cache::DEFAULT_CACHING);

        }

        return new JsonResponse($response);
    }
}