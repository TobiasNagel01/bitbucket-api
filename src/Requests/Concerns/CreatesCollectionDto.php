<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Concerns;

use Illuminate\Support\Collection;
use Saloon\Http\Response;
use Spatie\LaravelData\Data;
use Tobiasn\BitbucketApi\Exceptions\BitbucketApiParsingException;

trait CreatesCollectionDto
{
    /**
     * Map the `values` array of a paginated Bitbucket response to a collection of DTOs.
     *
     * @template T of Data
     *
     * @param  class-string<T>  $dto
     * @return Collection<int, T>
     */
    protected function mapValues(Response $response, string $dto): Collection
    {
        $values = $response->json('values');

        if (! is_array($values)) {
            throw new BitbucketApiParsingException('Failed to parse values from Bitbucket API');
        }

        return collect($values)
            ->map(fn (mixed $item) => $dto::from($item))
            ->values();
    }
}
