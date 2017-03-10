<?php

namespace Terminal42\MageTools\Task\Symfony;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class AcceleratorCacheClearTask extends AbstractTask
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
        return '[Terminal42] Clear accelerator cache';
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

    /**
     * Get the options
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = array_merge(
            ['console' => 'bin/console', 'env' => 'dev', 'flags' => ''],
            $this->runtime->getMergedOption('symfony'),
            $this->options
        );

        return $options;
    }
}
