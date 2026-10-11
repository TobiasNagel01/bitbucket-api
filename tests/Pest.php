<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tobiasn\BitbucketApi\BitbucketApiConnector;
use Tobiasn\BitbucketApi\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Build a mock response from a raw Bitbucket API JSON body stored in tests/Fixtures.
 */
function apiFixture(string $name, int $status = 200): MockResponse
{
    $path = __DIR__.'/Fixtures/'.$name.'.json';

    if (! is_file($path)) {
        throw new RuntimeException("Fixture [{$name}] not found at {$path}");
    }

    $contents = file_get_contents($path);

    if ($contents === false) {
        throw new RuntimeException("Fixture [{$name}] could not be read");
    }

    return MockResponse::make($contents, $status, ['Content-Type' => 'application/json']);
}

/**
 * Build a connector with a mock client mapping request classes (or a sequence) to fixtures.
 *
 * @param  array<class-string|int, MockResponse|string>  $map  Values may be fixture names or MockResponse instances.
 * @return array{BitbucketApiConnector, MockClient}
 */
function mockConnector(array $map): array
{
    $responses = [];

    foreach ($map as $key => $value) {
        $responses[$key] = $value instanceof MockResponse ? $value : apiFixture($value);
    }

    $mockClient = new MockClient($responses);

    $connector = new BitbucketApiConnector;
    $connector->withMockClient($mockClient);

    return [$connector, $mockClient];
}
