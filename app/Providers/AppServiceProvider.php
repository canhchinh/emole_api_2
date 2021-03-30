<?php

namespace App\Providers;

use App\Services\TwitterLoginService;
use Illuminate\Support\ServiceProvider;
use League\OAuth1\Client\Server\Twitter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TwitterLoginService::class, function ($app) {
            $config = config('services.twitter');
            return new TwitterLoginService(new Twitter($this->formatConfig($config)));
        });
    }

    /**
     * Format the server configuration.
     *
     * @param  array  $config
     * @return array
     */
    private function formatConfig(array $config): array
    {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => $config['redirect'],
        ], $config);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
