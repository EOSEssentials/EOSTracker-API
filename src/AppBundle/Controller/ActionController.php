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
        $service = $this->get('api.action_service');
        $item = $service->findOneBy(['id' => $id]);
        if (!$item) {
            return new JsonResponse(['error' => 'entity not found'], 404);
        }

        return new JsonResponse($item->toArray());

    }
}
