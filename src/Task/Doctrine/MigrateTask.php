<?php

namespace Terminal42\MageTools\Task\Doctrine;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class MigrateTask extends AbstractTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/doctrine/migrate';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Terminal42] Doctrine – run migration';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();

        $command = sprintf(
            '%s doctrine:migrations:migrate --env=%s %s',
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
        $userGlobalOptions = $this->runtime->getConfigOption('symfony', []);
        $userEnvOptions    = $this->runtime->getEnvOption('symfony', []);
        $options           = array_merge(
            ['console' => 'bin/console', 'env' => 'dev', 'flags' => '-n --allow-no-migration'],
            (is_array($userGlobalOptions) ? $userGlobalOptions : []),
            (is_array($userEnvOptions) ? $userEnvOptions : []),
            $this->options
        );

        return $options;
    }
}
