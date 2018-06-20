<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProducerController extends Controller
{
    /**
     * @Route("/bps/{url}", name="bps", requirements={"url"=".+"})
     */
    public function bpsAction(string $url)
    {
        $urlParsed = parse_url($url);
        if (!isset($urlParsed['host'], $urlParsed['scheme'])) {
            return new JsonResponse(['error' => 'invalid url'], 400);
        }

        $urlJsonBp = $urlParsed['scheme'].'://'.$urlParsed['host'].'/bp.json';
        $result = $this->get('cache.app')->getItem(md5($urlJsonBp));
        if ($result->isHit()) {
            return new JsonResponse($result->get());
        }

        $content = json_decode(file_get_contents($urlJsonBp));
        if (!$content || !isset($content->producer_account_name)) {
            return new JsonResponse(['error' => 'invalid JSON'], 400);
        }

        $result->set($content)->expiresAfter(new \DateInterval('PT300S'));
        $this->get('cache.app')->save($result);
        return new JsonResponse($result->get());
    }
}
