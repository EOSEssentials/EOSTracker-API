<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProducerController extends Controller
{
    /**
     * @Route("/producers", name="producers")
     */
    public function producersAction()
    {
        $cache = $this->get('api.cache_service');
        $service = $this->get('api.account_service');

        $items = $cache->get()->get('producers.action');
        if (!$items) {
            $items = [];
            //$items = $service->producers(new \DateTime('1 day ago')); TODO: too expensive
            $cache->get()->set('producers.action', $items, 60);
        }

        return new JsonResponse($items);
    }
}
