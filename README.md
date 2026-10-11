# Bitbucket SDK

Unofficial PHP SDK for the [Bitbucket Cloud REST API 2.0](https://developer.atlassian.com/cloud/bitbucket/rest/), built on [Saloon](https://docs.saloon.dev) and [spatie/laravel-data](https://spatie.be/docs/laravel-data).

## Installation

```bash
composer require tobiasnagel01/bitbucket-sdk
```

The service provider is auto-discovered. Publish the config if you need to change it:

```bash
php artisan vendor:publish --tag=bitbucket-api-config
```

```dotenv
BITBUCKET_API_CLIENT_ID=
BITBUCKET_API_CLIENT_SECRET=
BITBUCKET_API_REDIRECT_URI=
# optional, defaults to https://api.bitbucket.org/2.0
BITBUCKET_API_BASE_URL=
```

## Usage

Every endpoint is a Saloon request. Send it through the connector and call `dto()` for a typed object.

```php
use Tobiasn\BitbucketApi\BitbucketApiConnector;
use Tobiasn\BitbucketApi\Requests\Repositories\GetRepositoryRequest;

$connector = new BitbucketApiConnector;
$connector->authenticate($authenticator); // see "Authentication"

$repo = $connector->send(new GetRepositoryRequest('my-workspace', 'my-repo'))->dtoOrFail();

echo $repo->full_name; // "my-workspace/my-repo"
```

### Pagination

List requests return the first page from `dto()`. Use `paginate()` to walk every page lazily.

```php
use Tobiasn\BitbucketApi\Enums\PullRequestState;
use Tobiasn\BitbucketApi\Requests\PullRequests\ListPullRequestsRequest;

$request = new ListPullRequestsRequest('my-workspace', 'my-repo', state: PullRequestState::OPEN, sort: '-updated_on');

foreach ($connector->paginate($request)->collect() as $pullRequest) {
    echo $pullRequest->title;
}

// Or limit the walk:
$connector->paginate($request)->setPerPageLimit(50)->setMaxPages(3)->collect();
```

The paginator follows Bitbucket's `next` link, so it works for page-numbered endpoints and cursor-based ones like commits.

### Writing

```php
use Tobiasn\BitbucketApi\Enums\MergeStrategy;
use Tobiasn\BitbucketApi\Requests\PullRequests\CreatePullRequestRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\MergePullRequestRequest;

$pr = $connector->send(new CreatePullRequestRequest(
    workspace: 'my-workspace',
    repoSlug: 'my-repo',
    title: 'Add feature',
    sourceBranch: 'feature/thing',
    destinationBranch: 'main',
    closeSourceBranch: true,
    reviewerUuids: ['{reviewer-uuid}'],
))->dtoOrFail();

$connector->send(new MergePullRequestRequest(
    'my-workspace', 'my-repo', $pr->id,
    message: 'Merged via SDK',
    mergeStrategy: MergeStrategy::SQUASH,
))->dtoOrFail();
```

### Authentication

The connector uses Saloon's OAuth 2 authorization code grant with the configured client credentials and scopes:

```php
$url = $connector->getAuthorizationUrl();           // redirect the user here
$authenticator = $connector->getAccessToken($code);  // in the callback
$connector->authenticate($authenticator);
```

Any other Saloon authenticator works too, for example `new TokenAuthenticator($accessToken)` for an app password or repository access token.

## Covered endpoints

| Area | Requests |
|---|---|
| Users | `GetCurrentUserRequest` |
| Workspaces | `ListWorkspacesRequest`, `GetWorkspaceRequest` |
| Repositories | `ListRepositoriesInWorkspaceRequest`, `GetRepositoryRequest`, `CreateRepositoryRequest`, `ListBranchesRequest`, `GetBranchRequest`, `ListCommitsRequest`, `GetCommitRequest` |
| Pull requests | `ListPullRequestsRequest`, `GetPullRequestRequest`, `CreatePullRequestRequest`, `UpdatePullRequestRequest`, `MergePullRequestRequest`, `DeclinePullRequestRequest`, `ApprovePullRequestRequest`, `UnapprovePullRequestRequest`, `ListPullRequestCommentsRequest`, `CreatePullRequestCommentRequest`, `GetPullRequestDiffRequest`, `GetPullRequestDiffstatRequest`, `ListPullRequestActivityRequest`, `ListPullRequestCommitsRequest` |
| Pipelines | `ListPipelinesRequest`, `GetPipelineRequest`, `TriggerPipelineRequest`, `ListPipelineStepsRequest`, `GetPipelineStepLogRequest` |

Notes:

- Bitbucket accepts several `state` values only as repeated query keys, which Saloon cannot emit. Pass a single `state`, or use `q: 'state="OPEN" OR state="MERGED"'`.
- Dates are ISO-8601 strings. Pull request activity and pipeline target/trigger payloads are kept as arrays because their shape varies.

## Development

```bash
composer test      # pest
composer analyse   # phpstan level 10
composer fix-code  # pint
```
