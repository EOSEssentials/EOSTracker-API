<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends Controller
{
    /**
     * @Route("/votes/{producer}", name="votes")
     */
    public function votesAction(string $producer, Request $request)
    {
        $cache = $this->get('api.cache_service');
        $service = $this->get('api.vote_service');
        $page = $request->query->getInt('page', 0);
        $items = $cache->get()->get('votes_'.$producer.$page);
        if (!$items) {
            $items = $service->forProducer($producer, $page);
            $formattedItems = [];
            foreach($items as $item) {
                $formattedItems[] = [
                    'account' => $item['account'],
                    'staked' => $item['staked'],
                    'votes' => json_decode($item['votes'])
                ];
            }
            $items = $formattedItems;
            $cache->get()->set('producers.action', $items, 60);
        }

        return new JsonResponse($items);
    }
}
