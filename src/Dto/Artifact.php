<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @psalm-suppress MissingConstructor
 */
class Artifact
{
    public int $id;
    public string $nodeId;
    public string $name;
    public int $sizeInBytes;
    public string $url;
    public string $archiveDownloadUrl;
    public bool $expired;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
    public \DateTimeImmutable $expiresAt;
}
