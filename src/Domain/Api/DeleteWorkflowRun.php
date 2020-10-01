<?php

declare(strict_types=1);

namespace App\Domain\Api;

use App\Domain\Api\Exception\ApiException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeleteWorkflowRun
{
    private const ENDPOINT_URL = '/repos/%s/actions/runs/%s';

    private string $githubApiUrl;
    private string $githubUsername;
    private string $githubPersonalAccessToken;
    private HttpClientInterface $httpClient;

    public function __construct(
        string $githubApiUrl,
        string $githubUsername,
        string $githubPersonalAccessToken,
        HttpClientInterface $httpClient
    ) {
        $this->githubApiUrl = $githubApiUrl;
        $this->githubUsername = $githubUsername;
        $this->githubPersonalAccessToken = $githubPersonalAccessToken;
        $this->httpClient = $httpClient;
    }

    public function delete(string $repositoryName, int $workflowRunId): void
    {
        $url = $this->getApiUrl($repositoryName, $workflowRunId);

        $response = $this->httpClient->request('DELETE', $url, [
            'auth_basic' => [
                'username' => $this->githubUsername,
                'password' => $this->githubPersonalAccessToken,
            ],
        ]);

        if (204 !== $response->getStatusCode()) {
            throw new ApiException(sprintf('Can\'t delete workflow run #%s.', $workflowRunId));
        }
    }

    private function getApiUrl(string $repositoryName, int $workflowRunId): string
    {
        return $this->githubApiUrl . sprintf(self::ENDPOINT_URL, $repositoryName, $workflowRunId);
    }
}
