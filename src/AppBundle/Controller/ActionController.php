<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActionController extends Controller
{
    /**
     * @Route("/actions", name="actions")
     */
    public function actionsAction(Request $request)
    {
        $service = $this->get('api.action_service');
        $data = [];

        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);
        $result = $this->get('cache.app')->getItem('action'.$size.'_'.$page);
        if (!$result->isHit()) {
            $items = $service->get($page, $size);
            foreach ($items as $item) {
                $data[] = $item->toArray();
            }

            $result->set($data)->expiresAfter(new \DateInterval('PT15S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }

    /**
     * @Route("/actions/{id}", name="action")
     */
    public function actionAction(string $id)
    {
        $result = $this->get('cache.app')->getItem('action_'.$id);
        if (!$result->isHit()) {
            $service = $this->get('api.action_service');
            $item = $service->findOneBy(['id' => $id]);
            if (!$item) {
                return new JsonResponse(['error' => 'entity not found'], 404);
            }

            $result->set($item->toArray());
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());

    }

    /**
     * @Route("/transactions/{tx}/actions/{seq}", name="tx_action_seq")
     */
    public function actionSeqAction(string $tx, string $seq, Request $request)
    {
        $parent = $request->query->getInt('parentId', 0);
        $result = $this->get('cache.app')->getItem('tx_action_'.$tx.'_'.$seq.'_'.$parent);
        if (!$result->isHit()) {
            $serviceTx = $this->get('api.transaction_service');
            $transaction = $serviceTx->findOneBy(['id' => $tx]);
            $service = $this->get('api.action_service');
            $item = $service->findOneBy(['transaction' => $transaction, 'seq' => $seq, 'parentId' => $parent]);
            if (!$item) {
                return new JsonResponse(['error' => 'entity not found'], 404);
            }

            $result->set($item->toArray());
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());

    }
}
