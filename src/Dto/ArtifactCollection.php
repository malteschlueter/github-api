<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @psalm-suppress MissingConstructor
 */
class ArtifactCollection
{
    public int $totalCount;

    /**
     * @var Artifact[]
     */
    public array $artifacts;
}
