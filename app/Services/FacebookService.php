<?php

namespace App\Services;

use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Support\Facades\Log;
use \Facebook\Facebook;

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
        // Returns a `Facebook\Response` object
        /**
         * @var \Facebook\FacebookResponse
         */
        $response = $this->fb->get(
            $query,
            $access_token ? $access_token : null
        );

        return $response->getDecodedBody();
    }

    /**
     * @param $user_id
     * @return int
     */
    public function getFollowersCount($user_id) : int
    {
        try {
            // Returns a `Facebook\Response` object
            /**
             * @var \Facebook\FacebookResponse
             */
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
}

