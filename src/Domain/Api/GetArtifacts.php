<?php

declare(strict_types=1);

namespace App\Domain\Api;

use App\Domain\Api\Exception\ApiException;
use App\Dto\ArtifactCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetArtifacts
{
    private const ENDPOINT_URL = '/repos/%s/actions/artifacts';

    private string $githubApiUrl;
    private string $githubUsername;
    private string $githubPersonalAccessToken;
    private HttpClientInterface $httpClient;
    private SerializerInterface $serializer;

    public function __construct(
        string $githubApiUrl,
        string $githubUsername,
        string $githubPersonalAccessToken,
        HttpClientInterface $httpClient,
        SerializerInterface $serializer
    ) {
        $this->githubApiUrl = $githubApiUrl;
        $this->githubUsername = $githubUsername;
        $this->githubPersonalAccessToken = $githubPersonalAccessToken;
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    public function get(string $repositoryName, int $page = 1): ArtifactCollection
    {
        $url = $this->getApiUrl($repositoryName);

        $response = $this->httpClient->request('GET', $url, [
            'auth_basic' => [
                'username' => $this->githubUsername,
                'password' => $this->githubPersonalAccessToken,
            ],
            'query' => [
                'page' => $page,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new ApiException('Can\'t get workflow runs.');
        }

        /** @var ArtifactCollection $artifactCollection */
        $artifactCollection = $this->serializer->deserialize(
            $response->getContent(),
            ArtifactCollection::class,
            JsonEncoder::FORMAT
        );

        return $artifactCollection;
    }

    private function getApiUrl(string $repositoryName): string
    {
        return $this->githubApiUrl . sprintf(self::ENDPOINT_URL, $repositoryName);
    }
}
