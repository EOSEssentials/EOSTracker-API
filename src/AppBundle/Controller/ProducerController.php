<?php

namespace AppBundle\Controller;

use AppBundle\Services\CacheService;
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
            $cache->get()->set('producers.action', $items, CacheService::BIG_CACHING);
        }

        return new JsonResponse($items);
    }

    /**
     * @Route("/bps/{url}", name="bps", requirements={"url"=".+"})
     */
    public function bpsAction(string $url)
    {
        $cache = $this->get('api.cache_service');

        $urlParsed = parse_url($url);
        if (!isset($urlParsed['host'], $urlParsed['scheme'])) {
            return new JsonResponse(['error' => 'invalid url'], 400);
        }

        $urlJsonBp = $urlParsed['scheme'].'://'.$urlParsed['host'].'/bp.json';
        $content = $cache->get()->get($urlJsonBp);
        if ($content) {
            return new JsonResponse($content);
        }

        $content = json_decode(file_get_contents($urlJsonBp));
        if (!$content || !isset($content->producer_account_name)) {
            return new JsonResponse(['error' => 'invalid JSON'], 400);
        }

        $cache->get()->set($urlJsonBp, $urlJsonBp, CacheService::BIG_CACHING);

        return new JsonResponse($content);
    }
}
