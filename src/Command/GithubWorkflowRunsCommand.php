<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Api\Exception\ApiException;
use App\Domain\Api\GetWorkflowRuns;
use App\Dto\WorkflowRun;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GithubWorkflowRunsCommand extends Command
{
    protected static $defaultName = 'github:workflow:runs';

    private GetWorkflowRuns $getWorkflowRuns;

    public function __construct(
        GetWorkflowRuns $getWorkflowRuns
    ) {
        $this->getWorkflowRuns = $getWorkflowRuns;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show all workflow runs of a repository')
            ->addArgument(
                'repositoryName',
                InputArgument::REQUIRED,
                'A repository name'
            )
            ->addUsage('malteschlueter/github-api')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        // TODO: Add validation for repository name
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $repositoryName */
        $repositoryName = $input->getArgument('repositoryName');

        $io = new SymfonyStyle($input, $output);

        $io->title(sprintf('Github API - Workflow runs from <info>%s</info>', $repositoryName));

        $page = 1;

        do {
            try {
                $workflowRuns = $this->getWorkflowRuns->get($repositoryName, $page);
            } catch (ApiException $exception) {
                $io->error('Did you registered a correct personal access token?');
                $io->text('Please check your configuration.');

                return Command::FAILURE;
            }

            if (empty($workflowRuns->workflowRuns)) {
                break;
            }

            $io->table(
                [
                    'runNumber',
                    'headBranch',
                    'status',
                    'conclusion',
                ],
                array_map(static function (WorkflowRun $workflowRun): array {
                    return [
                        $workflowRun->runNumber,
                        $workflowRun->headBranch,
                        $workflowRun->status,
                        $workflowRun->conclusion,
                    ];
                }, $workflowRuns->workflowRuns)
            );
        } while (++$page);

        $io->writeln(sprintf('Total Workflow runs: %s', $workflowRuns->totalCount));

        return Command::SUCCESS;
    }
}
