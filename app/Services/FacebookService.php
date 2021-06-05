<?php

namespace App\Services;

use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Support\Facades\Log;
use \Facebook\Facebook;
use GuzzleHttp;

class FacebookService {
    const DEFAULT_GRAPH_VERSION = 'v10.0';

    /**
     * @var Facebook
     */
    private $fb;

    public function __construct()
    {
        $app_id = config('services.facebook.client_id');
        $app_secret = config('services.facebook.client_secret');
        try {
            $this->fb = new Facebook([
                'app_id' => $app_id,
                'app_secret' => $app_secret,
                'default_graph_version' => static::DEFAULT_GRAPH_VERSION,
                'default_access_token' => $app_id . '|' . $app_secret
            ]);
        } catch (FacebookSDKException $e) {
            Log::error("Facebook SDK Exception : " . $e->getMessage());
            Throw new \Exception("Facebook SDK Exception : " . $e->getMessage());
        }
    }

    /**
     * @return Facebook
     */
    public function getFacebook() : Facebook
    {
        return $this->fb;
    }

    /**
     * @param string $query
     * @param null $access_token
     * @return int|mixed|null
     */
    public function getUserInfo($query = 'me', $access_token = null)
    {
        try {
            // Returns a `Facebook\Response` object
            /**
             * @var \Facebook\FacebookResponse
             */
            $response = $this->fb->get(
                $query,
                $access_token ? $access_token : null
            );

            return $response->getDecodedBody();
        } catch(\Exception $e) {
            Log::error("Facebook Response Exception : " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param $access_token
     * @return array
     * @throws FacebookSDKException
     */
    public function getLongTermToken($access_token) {
        $app_id = config('services.facebook.client_id');
        $app_secret = config('services.facebook.client_secret');
        try {
            /**
             * @var \Facebook\FacebookResponse
             */
            $response = $this->fb->get(
                "oauth/access_token?grant_type=fb_exchange_token&fb_exchange_token=$access_token&client_id=$app_id&client_secret=$app_secret",
            );
            return $response->getGraphNode()->getField('access_token');
        } catch(\Exception $e) {
            Log::error("Facebook Response Exception : " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param $user_id
     * @return int
     */
    public function getFollowersCount($user_id, $access_token = null) : int
    {
        try {
            // Returns a `Facebook\Response` object
            /**
             * @var \Facebook\FacebookResponse
             */
            $client = new GuzzleHttp\Client();
            $res = $client->get(
                "https://graph.facebook.com/$user_id",
                [
                    'query' => [
                        'access_token' => $access_token,
                        'fields' => 'business_discovery'
                    ]
                ]
            );
            Log::error("Test save business instagram : " . json_decode($res->getBody()));

            $response = $this->fb->get(
                "$user_id?fields=ig_id,name,followers_count"
            );

            return $response->getGraphNode()->getField('followers_count');
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            Log::error("Facebook Response Exception : " . $e->getMessage());
            return 0;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            Log::error("Facebook SDK Exception : " . $e->getMessage());
            return 0;
        } catch (\Exception $e) {
            Log::error("Facebook Get Followers Count Exception : " . $e->getMessage());
            return 0;
        }
    }

    /**
     * @param $access_token
     * @return any
     */
    public function getUserInstagram($access_token){
        try {
            $client = new GuzzleHttp\Client();
            $res = $client->get(
                'https://graph.facebook.com/me',
                [
                    'query' => [
                        'access_token' => $access_token,
                        'fields' => 'accounts{connected_instagram_account}'
                    ]
                ]
            );
            $data = $res->getBody();
            return json_decode($data);
        } catch(\Exception $e) {
            Log::error("Facebook Response Exception : " . $e->getMessage());
            return null;
        }
    }
}

