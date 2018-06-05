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
     * @Route("/blocks/{id}", name="block")
     */
    public function blockAction(string $id)
    {
        $service = $this->get('api.block_service');
        $item = $service->findOneBy(['blockNumber' => $id]);

        return new JsonResponse($item->toArray());
    }

    /**
     * @Route("/blocks/id/{id}", name="block_by_id")
     */
    public function blockbyIdAction(string $id)
    {
        $service = $this->get('api.block_service');
        $item = $service->findOneBy(['id' => $id]);

        return new JsonResponse($item->toArray());
    }

    /**
     * @Route("/blocks/{id}/transactions", name="block_transactions")
     */
    public function blockTransactionsAction(string $id, Request $request)
    {
        $service = $this->get('api.transaction_service');
        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);
        $response = [];
        $items = $service->getForBlock($id, $page, $size);
        foreach ($items as $item) {
            $response[] = $item->toArray();
        }

        return new JsonResponse($response);
    }
}
