<?php
namespace App\Services;

use Google\Client;
use Illuminate\Http\Request;
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

    public function __construct(Google_Client $client)
    {
        $this->client = $client;
//        $this->auth();
    }

    private function auth() : void
    {
        $this->client->setApplicationName('YoutubeApp');
        $this->client->setScopes([
            Google_Service_YouTube::YOUTUBE_READONLY
        ]);
        $this->client->setAuthConfig(base_path() . '/client_secret.json');
        $authCode = '';
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
        $this->client->setAccessToken($accessToken);
    }

    public function getInfoChannel(string $channelId) : array
    {
        try {
            $service = new Google_Service_YouTube($this->client);

            $queryParams = [
                'id' => $channelId
            ];

            $response = $service->channels->listChannels('statistics', $queryParams);
            return json_decode($response);
        } catch (Exception $e) {
            return [];
        }
    }
}
