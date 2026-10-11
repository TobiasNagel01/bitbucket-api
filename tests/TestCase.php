<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Saloon\Http\Faking\MockClient;
use Saloon\Laravel\SaloonServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;
use Tobiasn\BitbucketApi\BitbucketApiServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SaloonServiceProvider::class,
            LaravelDataServiceProvider::class,
            BitbucketApiServiceProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        MockClient::destroyGlobal();

        parent::tearDown();
    }
}
