<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    /**
     * @Route("/tweets/{username}/avatar.png", name="tweets_user_avatar")
     */
    public function tweetsUserAvatarAction(string $username)
    {
        $service = $this->get('api.twitter_service');

        $avatar = $service->avatarForUser($username);
        /* if ($avatar) {

        }

        $response = new BinaryFileResponse($filePath);
        $response->headers->set('Content-Type', 'image/png';
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
        */
        return new JsonResponse([$avatar]);
    }
}
