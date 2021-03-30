<?php
namespace App\Services;

use Illuminate\Http\Request;
use Atymic\Twitter\Twitter;

class TwitterService
{
    /**
     * The OAuth server implementation.
     *
     * @var Twitter
     */
    protected $twitter;

    public function __construct(Twitter $twitter)
    {
        $this->twitter = $twitter;
    }

    public function getUsers(string $userId = null, string $screenName = null): array
    {
        try {
            $param = [
                'format' => 'array'
            ];
            if ($userId !== null) {
                $param['user_id'] = $userId;
            }
            if ($screenName !== null) {
                $param['screen_name'] = $screenName;
            }

            return $this->twitter->getUsers($param);
        } catch (\Exception $e) {
            return [];
        }
    }

}
