<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlockController extends Controller
{
    /**
     * @Route("/blocks", name="blocks")
     */
    public function blocksAction(Request $request)
    {
        $service = $this->get('api.block_service');
        $data = [];
        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);

        $result = $this->get('cache.app')->getItem('blocks'.$size.'_'.$page);
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
     * @Route("/blocks/{id}", name="block")
     */
    public function blockAction(string $id)
    {
        $result = $this->get('cache.app')->getItem('block_'.$id);
        if (!$result->isHit()) {
            $service = $this->get('api.block_service');
            $item = $service->findOneBy(['blockNumber' => $id]);
            if (!$item) {
                return new JsonResponse(['error' => 'entity not found'], 404);
            }

            $result->set($item->toArray());
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }

    /**
     * @Route("/blocks/id/{id}", name="block_by_id")
     */
    public function blockbyIdAction(string $id)
    {
        $result = $this->get('cache.app')->getItem('block_id_'.$id);
        if (!$result->isHit()) {
            $service = $this->get('api.block_service');
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
     * @Route("/blocks/{id}/transactions", name="block_transactions")
     */
    public function blockTransactionsAction(string $id, Request $request)
    {
        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);
        $result = $this->get('cache.app')->getItem('block_transaction_'.$id.'_'.$page.'_'.$size);
        if (!$result->isHit()) {
            $service = $this->get('api.transaction_service');
            $response = [];
            $items = $service->getForBlock($id, $page, $size);
            foreach ($items as $item) {
                $response[] = $item->toArray();
            }

            $result->set($response);
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }
}
