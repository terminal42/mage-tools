<?php

namespace Terminal42\MageTools\Task\Maintenance;

use Mage\Task\BuiltIn\Symfony\AbstractSymfonyTask;
use Symfony\Component\Process\Process;

class UnlockTask extends AbstractSymfonyTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/maintenance/unlock';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Maintenance] Disabling maintenance mode';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();

        $command = sprintf(
            '%s lexik:maintenance:unlock --no-interaction --env=%s %s',
            $options['console'],
            $options['env'],
            $options['flags']
        );

        /** @var Process $process */
        $process = $this->runtime->runRemoteCommand(trim($command), true);

        return $process->isSuccessful();
    }
}
