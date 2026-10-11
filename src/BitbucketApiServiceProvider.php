<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi;

use Illuminate\Support\ServiceProvider;

class BitbucketApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/bitbucket-api.php' => config_path('bitbucket-api.php'),
        ], 'bitbucket-api-config');

        $this->mergeConfigFrom(__DIR__.'/../config/bitbucket-api.php', 'bitbucket-api');
    }
}
