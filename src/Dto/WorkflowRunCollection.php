<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @psalm-suppress MissingConstructor
 */
class WorkflowRunCollection
{
    public int $totalCount;

    /**
     * @var WorkflowRun[]
     */
    public array $workflowRuns;
}
