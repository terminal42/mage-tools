<?php

namespace Terminal42\MageTools\Command;

use Mage\Command\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class DeployAllCommand extends AbstractCommand
{
    /**
     * @var array
     */
    private $statePerEnv = [];

    /**
     * @var ConsoleSectionOutput
     */
    private $tableOutput;

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
        if (!isset($config['deploy_all_working_dir']) || !is_dir($config['deploy_all_working_dir'])) {
            $io->error('You have to configure the "deploy_all_working_dir" and it has to exist!');
            return 1;
        }

        $workingDir = realpath($config['deploy_all_working_dir']);

        $fs = new Filesystem();
        $envs = array_keys($config['environments']);

        // Clean dir
        $io->comment('Preparing "deploy_all_working_dir"');

        if (!$this->removeDir($workingDir)) {
            $io->error('Failed.');
            return 1;
        }
        $fs->mkdir($workingDir);
        $io->success('Done.');

        $this->tableOutput = $output->section();

        // Copy whole directory to tmp dir to then delete the working dir and sync it to the env folders
        $io->comment('Preparing central directory for deployment.');
        $tmpDir = sys_get_temp_dir() . '/' . uniqid('t42_mage_deploy_all', true);

        if ($this->copyDir(getcwd(), $tmpDir)) {
            if (!$this->removeDir($tmpDir . '/' . $fs->makePathRelative($workingDir, getcwd()))) {
                $io->success('Could not remove working dir in central directory.');
                return 1;
            }
            $io->success('Done.');
        } else {
            $io->error('Failed.');
            return 1;
        }

        // Build processes
        $copyProcesses = [];
        $deployProcesses = [];

        $phpBinary = $this->getPhpBinary();

        $io->comment('Preparing all processes for all environments.');
        foreach ($envs as $env) {
            $this->statePerEnv[$env] = [
                'working_dir' => 'copying',
                'deployment' => 'waiting',
            ];

            // Create working dir for env
            $envDir = $workingDir . '/' . $env;
            $fs->mkdir($envDir);

            // Copy files to working dir
            $copyProcesses[$env] = $this->createCopyDirProcess($tmpDir, $envDir);

            // Prepare deployment process
            $process = new Process([$phpBinary, './vendor/bin/mage', 'deploy', $env], $envDir);
            $process->setTimeout(null);
            $deployProcesses[$env] = $process;
        }
        $io->success('Done.');

        $this->updateTable();

        // Start copying from central directory to environments
        $hasError = false;

        $this->runMultipleProcessesAsync($copyProcesses, function($env, Process $process) use (&$hasError, $deployProcesses) {
            if (0 === $process->getExitCode()) {
                $this->statePerEnv[$env]['working_dir'] = 'successful';

                // Successful, can already start deployment of this environment then
                $deployProcesses[$env]->start();
                $this->statePerEnv[$env]['deployment'] = 'running';

            } else {
                $this->statePerEnv[$env]['working_dir'] = 'error';
                $hasError = true;
            }

            $this->updateTable();
        });

        if ($hasError) {
            $io->error('Could not copy the data from the central directory to the env directory!');
            return 1;
        }

        $this->runMultipleProcessesAsync($deployProcesses, function($env, Process $process) use (&$hasError) {
            if (0 === $process->getExitCode()) {
                $this->statePerEnv[$env]['deployment'] = 'successful';
            } else {
                $this->statePerEnv[$env]['deployment'] = 'error';
                $hasError = true;
            }

            $this->updateTable();
        }, function($env) {
            $this->statePerEnv[$env]['deployment'] = 'running';
        });

        $io->writeln('');

        if ($hasError) {
            $io->error('Some deployments were not successful!');
            return 1;
        }

        $io->success('All deployments successful!');
        return 0;
    }

    /**
     * @param Process[] $processes
     * @param callable $onFinished
     * @param callable $onStarted
     */
    private function runMultipleProcessesAsync(array $processes, callable $onFinished, callable $onStarted = null): void
    {
        $processesSize = count($processes);
        $processesFinishedSize = 0;

        while ($processesSize !== $processesFinishedSize) {
            /** @var Process[] $processes */
            foreach($processes as $env => $process) {
                if (!$process->isStarted()) {
                    $process->start();
                    if (null !== $onStarted) {
                        $onStarted($env, $process);
                    }
                }

                if (!$process->isRunning()) {
                    $onFinished($env, $process);

                    $processesFinishedSize++;
                    unset($processes[$env]);
                }
            }

            sleep(5);
        }
    }

    private function removeDir(string $dir): bool
    {
        try {
            $process = new Process(['rm', '-rf', $dir]);
            $process->setTimeout(null);
            $process->mustRun();
            return true;
        } catch (ProcessFailedException $e) {
            return false;
        }

        return false;
    }

    private function copyDir(string $from, string $to): bool
    {
        try {
            $process = $this->createCopyDirProcess($from, $to);
            $process->mustRun();

            return true;
        } catch (ProcessFailedException $e) {
            var_dump($e->getMessage());
            return false;
        }

        return false;
    }

    private function createCopyDirProcess(string $from, string $to): Process
    {
        $from = rtrim($from, '/.') . '/.';
        $process = new Process(['cp', '-R', $from, $to]);
        $process->setTimeout(null);

        return $process;
    }

    private function getPhpBinary(): string
    {
        $executableFinder = new PhpExecutableFinder();
        return $executableFinder->find();
    }

    private function updateTable()
    {
        $this->tableOutput->clear();
        $table = new Table($this->tableOutput);
        $table->setHeaders(array('Environment', 'Working Directory Status', 'Deployment Status'));

        $rows = [];

        foreach ($this->statePerEnv as $env => $state) {
            $rows[] = [$env, $state['working_dir'], $state['deployment']];
        }

        $table->setRows($rows);
        $table->render();
    }
}
