<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/stats", name="stats")
     */
    public function statsAction()
    {
        $result = $this->get('cache.app')->getItem('stats.action');
        if (!$result->isHit()) {
            $data = [
                $this->get('api.block_service')->count([]),
                $this->get('api.transaction_service')->count([]),
                $this->get('api.account_service')->count([]),
                $this->get('api.action_service')->count(['parentId' => 0]),
            ];
            $result->set($data)->expiresAfter(new \DateInterval('PT15S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }

    /**
     * @Route("/stats/actions/{hours}", name="stats_actions")
     */
    public function statsActionsAction(string $hours)
    {
        $result = $this->get('cache.app')->getItem('stats.actions_'.$hours);
        if (!$result->isHit()) {
            $data = $this->get('api.action_service')->countLast((int)$hours);
            $result->set($data)->expiresAfter(new \DateInterval('PT600S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }
}
