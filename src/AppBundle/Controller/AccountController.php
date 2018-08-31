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
        $data = [];

        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);

        $result = $this->get('cache.app')->getItem('accounts_'.$size.'_'.$page);
        if (!$result->isHit()) {
            $items = $service->get($page, $size);
            foreach ($items as $item) {
                $data[] = $item->toArray();
            }

            $result->set($data)->expiresAfter(new \DateInterval('PT10S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());

    }

    /**
     * @Route("/accounts/{name}", name="account")
     */
    public function accountAction(string $name)
    {
        $result = $this->get('cache.app')->getItem('account_'.$name);
        if (!$result->isHit()) {
            $service = $this->get('api.account_service');
            $item = $service->findOneBy(['name' => $name]);
            if (!$item) {
                return new JsonResponse(['error' => 'entity not found'], 404);
            }
            $result->set($item->toArray())->expiresAfter(new \DateInterval('PT10S'));
            $this->get('cache.app')->save($result);
        }


        return new JsonResponse($result->get());
    }

    /**
     * @Route("/accounts/key/{key}", name="account_by_key")
     */
    public function accountByKeyAction(string $key)
    {
        $service = $this->get('api.account_service');
        $item = $service->withPublicKey($key);

        return new JsonResponse(['name' => isset($item[0]) ? $item[0]['account'] : null]);
    }
}