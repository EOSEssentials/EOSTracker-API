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
        $chainId = $request->headers->get('X-Chain', null);

        $result = $this->get('cache.app')->getItem('tweets_'.$page.'_'.$chainId);
        if (!$result->isHit()) {
            $data = $service->all($page, $chainId);
            $result->set($data)->expiresAfter(new \DateInterval('PT3S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }

    /**
     * @Route("/tweets/stats", name="tweets_stats")
     */
    public function tweetsStatsAction(Request $request)
    {
        $service = $this->get('api.twitter_service');
        $chainId = $request->headers->get('X-Chain', null);

        $result = $this->get('cache.app')->getItem('tweets_stats_'.$chainId);
        if (!$result->isHit()) {
            $data = $service->stats($chainId);
            $result->set($data)->expiresAfter(new \DateInterval('PT120S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }

    /**
     * @Route("/tweets/{username}", name="tweets_user")
     */
    public function tweetsUserAction(string $username, Request $request)
    {
        $service = $this->get('api.twitter_service');
        $page = $request->query->getInt('page', 0);
        $chainId = $request->headers->get('X-Chain', null);

        return new JsonResponse($service->forUser($username, $page, $chainId));
    }

    /**
     * @Route("/tweets/{username}/avatar.png", name="tweets_user_avatar")
     */
    public function tweetsUserAvatarAction(string $username, Request $request)
    {
        $service = $this->get('api.twitter_service');
        $chainId = $request->headers->get('X-Chain', null);

        $result = $this->get('cache.app')->getItem('tweets_avatar_'.$username.'_'.$chainId);
        if (!$result->isHit()) {
            $url = 'https://api.adorable.io/avatars/102/'.$username.'@adorable.png';
            $avatar = $service->avatarForUser($username, $chainId);
            if ($avatar) {
                $url = 'https://images.weserv.nl/?url='.$this->removeHttp($avatar).'&h=150';
            }
            $result->set($url)->expiresAfter(new \DateInterval('PT30S'));
            $this->get('cache.app')->save($result);
        }

        return $this->redirect($result->get());
    }

    /**
     * @Route("/tweets/{username}/stats", name="tweets_user_stats")
     */
    public function tweetsUserStatsAction(string $username, Request $request)
    {
        $service = $this->get('api.twitter_service');
        $chainId = $request->headers->get('X-Chain', null);

        $result = $this->get('cache.app')->getItem('tweets_user_stats_'.$username.'_'.$chainId);
        if (!$result->isHit()) {
            $data = $service->statsForUser($username, $chainId);
            $result->set($data)->expiresAfter(new \DateInterval('PT60S'));
            $this->get('cache.app')->save($result);
        }

        return new JsonResponse($result->get());
    }

    private function removeHttp($url)
    {
        $disallowed = array('http://', 'https://');
        foreach ($disallowed as $d) {
            if (strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }

        return $url;
    }
}
