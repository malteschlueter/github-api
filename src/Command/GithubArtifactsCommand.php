<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Api\Exception\ApiException;
use App\Domain\Api\GetArtifacts;
use App\Dto\Artifact;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GithubArtifactsCommand extends Command
{
    protected static $defaultName = 'github:artifacts';

    private GetArtifacts $getArtifacts;

    public function __construct(
        GetArtifacts $getArtifacts
    ) {
        $this->getArtifacts = $getArtifacts;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show all artifacts of a repository')
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

        $io->title(sprintf('Github API - Artifacts from <info>%s</info>', $repositoryName));

        $page = 1;

        do {
            try {
                $artifactCollection = $this->getArtifacts->get($repositoryName, $page);
            } catch (ApiException $exception) {
                $io->error('Did you registered a correct personal access token?');
                $io->text('Please check your configuration.');

                return Command::FAILURE;
            }

            if (empty($artifactCollection->artifacts)) {
                break;
            }

            $io->table(
                [
                    'Name',
                    'Size in MB',
                    'Url',
                    'Archive Download Url',
                    'Expired',
                    'Created At',
                    'Expires At',
                ],
                array_map(static function (Artifact $artifact): array {
                    return [
                        $artifact->name,
                        round($artifact->sizeInBytes / 1024 / 1024, 3),
                        $artifact->url,
                        $artifact->archiveDownloadUrl,
                        $artifact->expired ? 'Yes' : 'No',
                        $artifact->createdAt->format('Y-m-d H:i:s'),
                        $artifact->expiresAt->format('Y-m-d H:i:s'),
                    ];
                }, $artifactCollection->artifacts)
            );
        } while (++$page);

        $io->writeln(sprintf('Total Artifacts: %s', $artifactCollection->totalCount));

        return Command::SUCCESS;
    }
}
