<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Api\DeleteWorkflowRun;
use App\Domain\Api\Exception\ApiException;
use App\Domain\Api\GetWorkflowRuns;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GithubWorkflowRunDeleteCommand extends Command
{
    protected static $defaultName = 'github:workflow:run:delete';

    private GetWorkflowRuns $getWorkflowRuns;
    private DeleteWorkflowRun $deleteWorkflowRun;

    public function __construct(
        GetWorkflowRuns $getWorkflowRuns,
        DeleteWorkflowRun $deleteWorkflowRun
    ) {
        $this->getWorkflowRuns = $getWorkflowRuns;
        $this->deleteWorkflowRun = $deleteWorkflowRun;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Delete all workflow runs of a repository')
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
        $io->title(sprintf(
            'Github API - Delete all workflow runs from <info>%s</info>',
            $repositoryName
        ));

        $deletedWorkflowRuns = 0;
        $page = 1;

        do {
            try {
                $workflowRunCollection = $this->getWorkflowRuns->get($repositoryName, $page);
            } catch (ApiException $exception) {
                $io->error('Did you registered a correct personal access token?');
                $io->text('Please check your configuration.');

                return Command::FAILURE;
            }

            if (empty($workflowRunCollection->workflowRuns)) {
                break;
            }

            foreach ($workflowRunCollection->workflowRuns as $workflowRun) {
                $this->deleteWorkflowRun->delete($repositoryName, $workflowRun->id);
                ++$deletedWorkflowRuns;
            }
        } while (++$page);

        $io->writeln(sprintf('Deleted <info>%s</info> workflow runs.', $deletedWorkflowRuns));

        return Command::SUCCESS;
    }
}
