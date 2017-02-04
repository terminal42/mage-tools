<?php

namespace Terminal42\MageTools\Task\Maintenance;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class LockTask extends AbstractTask
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
        return '[Terminal42] Maintenance – enable maintenance mode';
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

    /**
     * Get the options
     *
     * @return array
     */
    protected function getOptions()
    {
        $userGlobalOptions = $this->runtime->getConfigOption('symfony', []);
        $userEnvOptions    = $this->runtime->getEnvOption('symfony', []);
        $options           = array_merge(
            ['console' => 'bin/console', 'env' => 'dev', 'flags' => ''],
            (is_array($userGlobalOptions) ? $userGlobalOptions : []),
            (is_array($userEnvOptions) ? $userEnvOptions : []),
            $this->options
        );

        return $options;
    }
}
