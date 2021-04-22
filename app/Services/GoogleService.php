<?php
namespace App\Services;

use Google_Client;
use Google_Service_YouTube;
use Mockery\Exception;

class GoogleService
{
    /**
     * The OAuth server implementation.
     *
     * @var Google_Client
     */
    protected $client;

    /**
     * youtube service
     *
     * @var Google_Service_YouTube
     */
    protected $youtube;

    public function __construct(Google_Client $client)
    {
        $this->client = $client;
        $this->auth();
        $this->youtube = new Google_Service_YouTube($this->client);
    }

    private function auth() : void
    {
        $this->client->setApplicationName('YoutubeApp');
        $this->client->setScopes([
            Google_Service_YouTube::YOUTUBE_READONLY
        ]);
        $this->client->setAuthConfig(base_path() . '/client_secret.json');
    }

    public function getInfoChannel(string $channelId) : array
    {
        try {
            $queryParams = [
                'id' => $channelId
            ];

            $res = $this->youtube->channels->listChannels('statistics', $queryParams);
            return empty($res->items) ? [] : (array) $res->items[0]->statistics;
        } catch (Exception $e) {
            return [];
        }
    }
}
