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
        $service = $this->get('api.vote_service');
        $page = $request->query->getInt('page', 0);
        $result = $this->get('cache.app')->getItem('votes_'.$producer.$page);
        if (!$result->isHit()) {
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
            $result->set($items)->expiresAfter(new \DateInterval('PT60S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }
}
