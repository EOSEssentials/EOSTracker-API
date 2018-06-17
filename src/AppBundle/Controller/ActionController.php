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
        $cache = $this->get('api.cache_service');

        $size = $request->query->getInt('size', 30);
        $page = $request->query->getInt('page', 1);
        $response = $cache->get()->get('action'.$size.'_'.$page);
        if (!$response) {
            $items = $service->get($page, $size);
            foreach ($items as $item) {
                $response[] = $item->toArray();
            }

            $cache->get()->set('action'.$size.'_'.$page, $response, $cache::DEFAULT_CACHING);

        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/actions/{id}", name="action")
     */
    public function actionAction(string $id)
    {
        $service = $this->get('api.action_service');
        $item = $service->findOneBy(['id' => $id]);
        if (!$item) {
            return new JsonResponse(['error' => 'entity not found'], 404);
        }

        return new JsonResponse($item->toArray());

    }
}
