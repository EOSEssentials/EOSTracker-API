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
        if ($avatar) {
            return $this->redirect('https://images.weserv.nl/?url='.$this->removeHttp($avatar).'&h=150');
        }
        return $this->redirect('https://api.adorable.io/avatars/102/'.$username.'@adorable.png');
    }

    private function removeHttp($url) {
        $disallowed = array('http://', 'https://');
        foreach($disallowed as $d) {
            if(strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }
        return $url;
    }
}
