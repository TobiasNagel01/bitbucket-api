<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi;

use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;
use Tobiasn\BitbucketApi\Pagination\BitbucketPaginator;
use Tobiasn\BitbucketApi\Requests\Users\GetCurrentUserRequest;
use Tobiasn\BitbucketApi\Responses\BitbucketApiResponse;

class BitbucketApiConnector extends Connector implements HasPagination
{
    use AuthorizationCodeGrant;

    protected ?string $response = BitbucketApiResponse::class;

    public function resolveBaseUrl(): string
    {
        return rtrim(config()->string('bitbucket-api.base_url', 'https://api.bitbucket.org/2.0'), '/');
    }

    /**
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function defaultOauthConfig(): OAuthConfig
    {
        /** @var array<string> $scopes */
        $scopes = collect(config()->array('bitbucket-api.scopes'))->filter(fn ($s) => is_string($s))->toArray();

        return OAuthConfig::make()
            ->setAllowBaseUrlOverride()
            ->setClientId(config()->string('bitbucket-api.oauth.client_id'))
            ->setClientSecret(config()->string('bitbucket-api.oauth.client_secret'))
            ->setDefaultScopes($scopes)
            ->setRedirectUri(config()->string('bitbucket-api.oauth.redirect'))
            ->setAuthorizeEndpoint('https://bitbucket.org/site/oauth2/authorize')
            ->setTokenEndpoint('https://bitbucket.org/site/oauth2/access_token')
            ->setUserEndpoint('/user');
    }

    /**
     * Iterate over every page of a list request. Use `->collect()` for a lazy collection of DTOs.
     */
    public function paginate(Request $request): BitbucketPaginator
    {
        return new BitbucketPaginator($this, $request);
    }

    protected function resolveUserRequest(OAuthConfig $oauthConfig): Request
    {
        return new GetCurrentUserRequest;
    }
}
