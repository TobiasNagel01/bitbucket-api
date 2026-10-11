<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\PullRequests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Tobiasn\BitbucketApi\DataTransferObjects\CommentDTO;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pullrequests/#api-repositories-workspace-repo-slug-pullrequests-pull-request-id-comments-post
 */
final class CreatePullRequestCommentRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  int  $pullRequestId  The ID of the pull request.
     * @param  string  $content  Raw Markdown content of the comment.
     * @param  string|null  $inlinePath  File path for an inline comment.
     * @param  int|null  $inlineTo  Line number in the new file for an inline comment.
     * @param  int|null  $inlineFrom  Line number in the old file for an inline comment.
     * @param  int|null  $parentId  ID of the comment being replied to.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly int $pullRequestId,
        private readonly string $content,
        private readonly ?string $inlinePath = null,
        private readonly ?int $inlineTo = null,
        private readonly ?int $inlineFrom = null,
        private readonly ?int $parentId = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pullrequests/'.$this->pullRequestId.'/comments';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $inline = $this->inlinePath !== null
            ? array_filter([
                'path' => $this->inlinePath,
                'to' => $this->inlineTo,
                'from' => $this->inlineFrom,
            ], fn (mixed $value) => $value !== null)
            : null;

        return array_filter([
            'content' => ['raw' => $this->content],
            'inline' => $inline,
            'parent' => $this->parentId !== null ? ['id' => $this->parentId] : null,
        ], fn (mixed $value) => $value !== null);
    }

    public function createDtoFromResponse(Response $response): CommentDTO
    {
        return CommentDTO::from($response->json());
    }
}
