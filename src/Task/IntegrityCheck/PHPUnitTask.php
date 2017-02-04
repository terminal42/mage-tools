<?php

namespace Terminal42\MageTools\Task\IntegrityCheck;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class PHPUnitTask extends AbstractTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/integrity-check/phpunit';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Terminal42] Integrity check - PHPUnit';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Process $process */
        $process = $this->runtime->runLocalCommand(trim('vendor/bin/phpunit'));

        return $process->isSuccessful();
    }
}
