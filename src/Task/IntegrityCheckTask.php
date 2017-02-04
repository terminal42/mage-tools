<?php

namespace Terminal42\MageTools\Task;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class IntegrityCheckTask extends AbstractTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/integrity-check';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Terminal42] Integrity check';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options  = $this->getOptions();
        $commands = [sprintf('%s contao:version', $options['console'])];

        // Enable phpunit tests
        if ($options['phpunit']) {
            $commands[] = 'vendor/bin/phpunit';
        }

        foreach ($commands as $command) {
            /** @var Process $process */
            $process = $this->runtime->runLocalCommand(trim($command));

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
        $userGlobalOptions = $this->runtime->getConfigOption('symfony', []);
        $userEnvOptions    = $this->runtime->getEnvOption('symfony', []);
        $options           = array_merge(
            ['console' => './vendor/bin/contao-console', 'phpunit' => true],
            (is_array($userGlobalOptions) ? $userGlobalOptions : []),
            (is_array($userEnvOptions) ? $userEnvOptions : []),
            $this->options
        );

        return $options;
    }
}
