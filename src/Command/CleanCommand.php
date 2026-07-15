<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as BaseCommand;
use Lazarini\HyperfTus\Services\TusServer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to clean up expired uploads.
 */
#[Command]
class CleanCommand extends BaseCommand
{
    protected ?string $name = 'tus:clean';
    protected string $description = 'Clean up expired TUS uploads';

    public function __construct(
        private readonly TusServer $tusServer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Cleaning up expired uploads...');

        $count = $this->tusServer->cleanExpired();

        if ($count > 0) {
            $output->writeln("<info>Cleaned {$count} expired upload(s).</info>");
        } else {
            $output->writeln('<comment>No expired uploads found.</comment>');
        }

        return self::SUCCESS;
    }
}
