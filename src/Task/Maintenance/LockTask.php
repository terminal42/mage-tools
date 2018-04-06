<?php

namespace Terminal42\MageTools\Task\Maintenance;

use Mage\Task\BuiltIn\Symfony\AbstractSymfonyTask;
use Symfony\Component\Process\Process;

class LockTask extends AbstractSymfonyTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/maintenance/lock';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Maintenance] Enabling maintenance mode';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();

        $command = sprintf(
            '%s lexik:maintenance:lock --no-interaction --env=%s %s',
            $options['console'],
            $options['env'],
            $options['flags']
        );

        /** @var Process $process */
        $process = $this->runtime->runRemoteCommand(trim($command), true);

        return $process->isSuccessful();
    }
}
