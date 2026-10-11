<?php

declare(strict_types=1);

use Saloon\Http\Request;
use Spatie\LaravelData\Data;

arch('source files use strict types')
    ->expect('Tobiasn\BitbucketApi')
    ->toUseStrictTypes();

arch('requests extend the Saloon request and are final')
    ->expect('Tobiasn\BitbucketApi\Requests')
    ->classes()
    ->toExtend(Request::class)
    ->toBeFinal()
    ->ignoring('Tobiasn\BitbucketApi\Requests\Concerns');

arch('DTOs extend laravel-data and are final')
    ->expect('Tobiasn\BitbucketApi\DataTransferObjects')
    ->toExtend(Data::class)
    ->toBeFinal();

arch('enums are string-backed')
    ->expect('Tobiasn\BitbucketApi\Enums')
    ->toBeStringBackedEnums();
