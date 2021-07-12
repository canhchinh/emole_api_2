<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Server\Twitter;

class TwitterLoginService
{
    /**
     * The OAuth server implementation.
     *
     * @var \League\OAuth1\Client\Server\Twitter
     */
    protected $server;

    public function __construct(Twitter $server)
    {
        $this->server = $server;
    }

    public function getLoginInfo(): array
    {
        try {
            $tempCredentials = $this->server->getTemporaryCredentials();
            $urlLogin = $this->server->getAuthorizationUrl($tempCredentials);
            return [
                'url' => $urlLogin,
                'request_oauth_token' => Crypt::encryptString($tempCredentials->getIdentifier()),
                'request_oauth_token_secret' => Crypt::encryptString($tempCredentials->getSecret())
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getUserInfo(Request $request)
    {
        if (!$this->hasNecessaryVerifier($request)) {
            throw new \Exception('Invalid request. Missing OAuth verifier.');
        }

        return $this->server->getTemporaryCredentials();
        // return $this->server->getUserDetails($this->getToken($request));
    }

    /**
     * Get the token credentials for the request.
     *
     * @param Request $request
     * @return \League\OAuth1\Client\Credentials\TokenCredentials
     * @throws \League\OAuth1\Client\Credentials\CredentialsException
     * @throws \Exception
     */
    private function getToken(Request $request): \League\OAuth1\Client\Credentials\TokenCredentials
    {
        if (!$request->get('request_oauth_token') || !$request->get('request_oauth_token_secret')) {
            throw new \Exception('Missing temporary OAuth credentials.');
        }

        $temporaryCredentials = new TemporaryCredentials();
        $requestOauthToken = Crypt::decryptString($request->request_oauth_token);
        $requestOauthTokenSecret = Crypt::decryptString($request->request_oauth_token_secret);
        $temporaryCredentials->setIdentifier($requestOauthToken);
        $temporaryCredentials->setSecret($requestOauthTokenSecret);

        return $this->server->getTokenCredentials(
            $temporaryCredentials,
            $request->get('oauth_token'),
            $request->get('oauth_verifier')
        );
    }

    /**
     * Determine if the request has the necessary OAuth verifier.
     *
     * @return bool
     */
    private function hasNecessaryVerifier(Request $request)
    {
        return $request->has('oauth_token') && $request->has('oauth_verifier');
    }
}