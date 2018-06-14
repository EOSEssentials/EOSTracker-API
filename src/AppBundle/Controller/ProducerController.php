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

    /**
     * @Route("/bps/{url}", name="bps", requirements={"url"=".+"})
     */
    public function bpsAction($url)
    {
        $urlParsed = parse_url($url);
        if (!isset($urlParsed['host'], $urlParsed['scheme'])) {
            return new JsonResponse(['error' => 'invalid url'], 400);
        }

        $urlJsonBp = $urlParsed['scheme'].'://'.$urlParsed['host'].'/bp.json';
        $exist = false;
        $content = apcu_fetch($urlJsonBp, $exist);
        if ($exist) {
            return new JsonResponse($content);
        }

        $content = json_decode(file_get_contents($urlJsonBp));
        if (!$content || !isset($content->producer_account_name)) {
            return new JsonResponse(['error' => 'invalid JSON'], 400);
        }

        apcu_store($urlJsonBp, $content, 300);

        return new JsonResponse($content);
    }
}
