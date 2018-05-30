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
        $size = $request->query->getInt('size', 20);
        $page = $request->query->getInt('page', 1);
        $response = [];
        $items = $service->get($page, $size);
        foreach ($items as $item) {
            $response[] = $item->toArray();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/action", name="action")
     */
    public function actionAction(string $id)
    {
        $service = $this->get('api.action_service');
        $item = $service->findOneBy(['id' => $id]);

        return new JsonResponse($item->toArray());

    }
}
