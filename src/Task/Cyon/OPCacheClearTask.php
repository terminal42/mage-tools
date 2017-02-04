<?php

namespace Terminal42\MageTools\Task\Cyon;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class OPCacheClearTask extends AbstractTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/cyon/opcache-clear';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Terminal42] Cyon – clear OPCache';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Process $process */
        $process = $this->runtime->runRemoteCommand('pkill lsphp || true', false);

        return $process->isSuccessful();
    }
}
