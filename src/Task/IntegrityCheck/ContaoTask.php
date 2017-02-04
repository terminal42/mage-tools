<?php

namespace Terminal42\MageTools\Task;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class ContaoTask extends AbstractTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/integrity-check/contao';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Terminal42] Integrity check - Contao';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();

        /** @var Process $process */
        $process = $this->runtime->runLocalCommand(trim(sprintf('%s contao:version', $options['console'])));

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
            ['console' => './vendor/bin/contao-console'],
            (is_array($userGlobalOptions) ? $userGlobalOptions : []),
            (is_array($userEnvOptions) ? $userEnvOptions : []),
            $this->options
        );

        return $options;
    }
}
