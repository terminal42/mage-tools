<?php

namespace Terminal42\MageTools\Task\IntegrityCheck;

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
        $options = array_merge(
            ['console' => './vendor/bin/contao-console'],
            $this->runtime->getMergedOption('symfony'),
            $this->options
        );

        return $options;
    }
}
