<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Responses;

use Saloon\Http\Response;
use Tobiasn\BitbucketApi\Exceptions\BitbucketApiParsingException;

class BitbucketApiResponse extends Response
{
    public function string(string $key): string
    {
        $value = $this->json($key);

        if (!is_string($value)) {
            throw new BitbucketApiParsingException("Failed to parse " . $key . " from Bitbucket API");
        }

        return $value;
    }

    public function bool(string $key): bool
    {
        $value = $this->json($key);

        if (!is_bool($value)) {
            throw new BitbucketApiParsingException("Failed to parse " . $key . " from Bitbucket API");
        }

        return $value;
    }

    /**
     * @return array<mixed>
     */
    public function arr(string $key): array
    {
        $value = $this->json($key);

        if (!is_array($value)) {
            throw new BitbucketApiParsingException("Failed to parse " . $key . " from Bitbucket API");
        }

        return $value;
    }
}
