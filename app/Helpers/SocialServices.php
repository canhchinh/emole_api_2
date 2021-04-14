<?php

namespace App\Helpers;

use App\Services\GoogleService;
use App\Services\TwitterService;
use Illuminate\Support\Facades\Log;
use Sovit\TikTok\Api;

class SocialServices
{
    /** @var TwitterService */
    protected $twitterService;

    /** @var Api */
    protected $tiktok;

    /** @var GoogleService */
    protected $googleService;

    /**
     * SocialServices constructor.
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct()
    {
        $this->twitterService = app()->make(TwitterService::class);
        $this->tiktok = app()->make(Api::class);
        $this->googleService = app()->make(GoogleService::class);
    }

    /**
     * @param $users
     * @return array
     */
    public function getStatistics($users)
    {
        $snsFollowersCount = [];
        foreach ($users as $userSearch) {
            if ($userSearch->twitter_user) {
                try {
                    $twitterInfoUser = $this->twitterService->getUsers(null, $userSearch->twitter_user);
                    if ($twitterInfoUser) {
                        $snsFollowersCount[$userSearch->id]['twitter'] = $twitterInfoUser['followers_count'];
                    }
                } catch (\Exception $e) {
                    Log::info('Twitter called error: ' . $e->getMessage());
                }
            }

            if ($userSearch->tiktok_user) {
                try {
                    $tiktokInfoUser = $this->tiktok->getUser($userSearch->tiktok_user);
                    if ($tiktokInfoUser) {
                        $snsFollowersCount[$userSearch->id]['tiktok'] = $tiktokInfoUser->stats->followerCount;
                    }
                } catch (\Exception $e) {
                    Log::info('Tiktok called error: ' . $e->getMessage());
                }
            }

            if ($userSearch->youtube_channel) {
                try {
                    $channelInfo = $this->googleService->getInfoChannel($userSearch->youtube_channel);
                    if ($channelInfo) {
                        $snsFollowersCount[$userSearch->id]['youtube'] = $channelInfo['subscriberCount'];
                    }
                } catch (\Exception $e) {
                    Log::info('Youtube called error: ' . $e->getMessage());
                }
            }
        }

        return $snsFollowersCount;
    }
}
