<?php

namespace Terminal42\MageTools\Task\Symfony;

use Mage\Task\BuiltIn\Symfony\AbstractSymfonyTask;
use Symfony\Component\Process\Process;

class AcceleratorCacheClearTask extends AbstractSymfonyTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/accelerator-cache-clear';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Symfony] Clear accelerator cache';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();

        $command = sprintf(
            '%s cache:accelerator:clear --no-interaction --env=%s %s',
            $options['console'],
            $options['env'],
            $options['flags']
        );

        /** @var Process $process */
        $process = $this->runtime->runCommand(trim($command));

        return $process->isSuccessful();
    }
}
