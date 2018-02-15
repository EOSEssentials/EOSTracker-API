<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlockController extends Controller
{
    const DEFAULT_SIZE = 30;

    /**
     * @Route("/blocks", name="blocks")
     */
    public function blocksAction(Request $request)
    {
        $size = (int)$request->get('size', self::DEFAULT_SIZE);
        $filter = ($request->get('block_num')) ? ['block_num' => (int)$request->get('block_num')] : [];
        $items = [];

        $db = $this->get('eos_explorer.mongo_service');

        $cursor = $db->get()->Blocks
            ->find($filter)
            ->sort(['block_num' => -1])
            ->skip((int)$request->get('page', 0) * $size)
            ->limit($size);

        foreach ($cursor as $key => $document) {
            $items[] = $document;
        }

        return new JsonResponse($items);
    }
}
