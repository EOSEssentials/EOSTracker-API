<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TwitterController extends Controller
{
    /**
     * @Route("/tweets", name="tweets")
     */
    public function tweetsAction(Request $request)
    {
        $service = $this->get('api.twitter_service');
        $page = $request->query->getInt('page', 0);
        return new JsonResponse($service->all($page));
    }

    /**
     * @Route("/tweets/{username}", name="tweets_user")
     */
    public function tweetsUserAction(string $username, Request $request)
    {
        $service = $this->get('api.twitter_service');
        $page = $request->query->getInt('page', 0);
        return new JsonResponse($service->forUser($username, $page));
    }
}
