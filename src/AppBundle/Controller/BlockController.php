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
        $item = $service->findOneBy(['id' => $id]);

        return new JsonResponse($item->toArray());
    }
}
