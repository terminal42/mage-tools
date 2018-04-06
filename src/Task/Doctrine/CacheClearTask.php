<?php

namespace Terminal42\MageTools\Task\Doctrine;

use Mage\Task\BuiltIn\Symfony\AbstractSymfonyTask;
use Symfony\Component\Process\Process;

class CacheClearTask extends AbstractSymfonyTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/doctrine/cache-clear';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Doctrine] Clear cache';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();

        $commands = [
            sprintf(
                '%s doctrine:cache:clear-metadata --env=%s %s',
                $options['console'],
                $options['env'],
                $options['flags']
            ),
            sprintf(
                '%s doctrine:cache:clear-query --env=%s %s',
                $options['console'],
                $options['env'],
                $options['flags']
            ),
            sprintf(
                '%s doctrine:cache:clear-result --env=%s %s',
                $options['console'],
                $options['env'],
                $options['flags']
            ),
        ];

        foreach ($commands as $command) {
            /** @var Process $process */
            $process = $this->runtime->runRemoteCommand(trim($command), true);

            if (!$process->isSuccessful()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function getSymfonyOptions()
    {
        return ['flags' => '--flush'];
    }
}
