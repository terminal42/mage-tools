<?php

namespace Terminal42\MageTools\Task\IntegrityCheck;

use Mage\Task\BuiltIn\Symfony\AbstractSymfonyTask;
use Symfony\Component\Process\Process;

class ContaoTask extends AbstractSymfonyTask
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
        return '[Contao] Integrity check';
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
     * @inheritDoc
     */
    protected function getSymfonyOptions()
    {
        return ['console' => './vendor/bin/contao-console'];
    }
}
