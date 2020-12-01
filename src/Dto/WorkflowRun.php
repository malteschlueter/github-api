<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @psalm-suppress MissingConstructor
 */
class WorkflowRun
{
    public int $id;
    public string $headBranch;
    public int $runNumber;
    public string $status;
    public ?string $conclusion;
}
