<?php

namespace Terminal42\MageTools\Task;

use Mage\Runtime\Exception\RuntimeException;
use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class PlatformReleaseTask extends AbstractTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/platform-release';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Terminal42] Platform release (update version in parameters.yml)';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Process $process */
        $process = $this->runtime->runLocalCommand('git describe');
        $version = $process->isSuccessful() ? $process->getOutput() : '';

        /** @var Process $process */
        $process = $this->runtime->runRemoteCommand('cat app/config/parameters.yml', true);

        if (!$process->isSuccessful()) {
            return false;
        }

        try {
            $params = Yaml::parse($process->getOutput());
            $params['parameters']['platform_version'] = trim($version);

            /** @var Process $process */
            $process = $this->runtime->runRemoteCommand(
                sprintf('echo %s > app/config/parameters.yml', escapeshellarg(Yaml::dump($params))),
                true
            );
        } catch (ParseException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return $process->isSuccessful();
    }
}
