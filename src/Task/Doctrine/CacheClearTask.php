<?php

namespace Terminal42\MageTools\Task\Doctrine;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class CacheClearTask extends AbstractTask
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
        return '[Terminal42] Doctrine – clear cache';
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
     * Get the options
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = array_merge(
            ['console' => 'bin/console', 'env' => 'dev', 'flags' => '--flush'],
            $this->runtime->getMergedOption('symfony'),
            $this->options
        );

        return $options;
    }
}
