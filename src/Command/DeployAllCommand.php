<?php

namespace Terminal42\MageTools\Command;

use Mage\Command\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class DeployAllCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('deploy-all')
            ->setDescription('Deploy all environments')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->requireConfig();
        $io = new SymfonyStyle($input, $output);
        $io->title('terminal42 magellanes deploy-all command');

        $config = $this->runtime->getConfiguration();
        if (!isset($config['deploy_all_working_dir'])) {
            $io->error('You have to configure the "deploy_all_working_dir"!');
            return 1;
        }

        $workingDir = realpath($config['deploy_all_working_dir']);

        $fs = new Filesystem();
        $envs = array_keys($config['environments']);

        $statePerEnv = [];

        // Clean dir
        $io->comment('Preparing "deploy_all_working_dir"');

        if (is_dir($workingDir)) {
            $fs->remove($workingDir);
        }
        $fs->mkdir($workingDir);
        $io->success('Done.');

        /** @var ConsoleSectionOutput $section */
        $section = $output->section();
        $phpBinary = $this->getPhpBinary();

        // Build processes
        $processes = [];

        // Copy whole directory to tmp dir to then delete the working dir and sync it to the env folders
        $io->comment('Preparing central directory for deployment.');
        $tmpDir = sys_get_temp_dir() . '/' . uniqid('t42_mage_deploy_all', true);
        $fs->mirror(getcwd(), $tmpDir);
        $fs->remove($tmpDir . '/' . $fs->makePathRelative($workingDir, getcwd()));
        $io->success('Done.');

        $io->comment('Preparing all environments.');
        foreach ($envs as $env) {
            $statePerEnv[$env] = 'running';

            // Create working dir for env
            $envDir = $workingDir . '/' . $env;
            $fs->mkdir($envDir);

            // Copy files to working dir
            $fs->mirror($tmpDir, $envDir);

            // Prepare process
            $processes[$env] = new Process([$phpBinary, './vendor/bin/mage', 'deploy', $env], $envDir);
        }
        $io->success('Done.');

        $this->updateTable($section, $statePerEnv);

        // Start processes
        foreach($processes as $env => $process) {
            $process->start();
        }

        $processesSize = count($processes);
        $processesFinishedSize = 0;
        $hasError = false;

        while ($processesSize !== $processesFinishedSize) {
            /** @var Process[] $processes */
            foreach($processes as $env => $process) {
                if (!$process->isRunning()) {
                    if (0 === $process->getExitCode()) {
                        $statePerEnv[$env] = 'successful';
                    } else {
                        $statePerEnv[$env] = 'error';
                        $hasError = true;
                    }

                    $this->updateTable($section, $statePerEnv);

                    $processesFinishedSize++;
                    unset($processes[$env]);
                }
            }

            sleep(10);
        }

        $io->writeln('');

        if ($hasError) {
            $io->error('Some deployments were not successful!');
            return 1;
        }

        $io->success('All deployments successful!');
        return 0;
    }

    private function getPhpBinary(): string
    {
        $executableFinder = new PhpExecutableFinder();
        return $executableFinder->find();
    }

    private function updateTable(ConsoleSectionOutput $section, array $statePerEnv)
    {
        $section->clear();
        $table = new Table($section);
        $table->setHeaders(array('Environment', 'Deployment Status'));

        $rows = [];

        foreach ($statePerEnv as $env => $state) {
            $rows[] = [$env, $state];
        }

        $table->setRows($rows);
        $table->render();
    }
}
