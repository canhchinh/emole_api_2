<?php

namespace App\Services;

use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Support\Facades\Log;
use \Facebook\Facebook;

class FacebookService {

    const APP_ID_ENV_NAME = 'FACEBOOK_APP_ID';
    const APP_SECRET_ENV_NAME = 'APP_SECRET_ENV_NAME';
    const DEFAULT_GRAPH_VERSION = 'v10.0';

    /**
     * @var Facebook
     */
    private $fb;

    public function __construct()
    {
        $app_id = getenv(static::APP_ID_ENV_NAME);
        $app_secret = getenv(static::APP_SECRET_ENV_NAME);
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
                "$user_id"
            );

            return $response->getGraphNode()->getField('followed_by_count');
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

