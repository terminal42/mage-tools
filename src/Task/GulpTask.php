<?php

namespace Terminal42\MageTools\Task;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class GulpTask extends AbstractTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/gulp';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Terminal42] Run Gulp tasks';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $command = sprintf('./node_modules/.bin/gulp %s', ($this->options['env'] === 'prod') ? '--prod' : '');

        /** @var Process $process */
        $process = $this->runtime->runLocalCommand(trim($command));

        return $process->isSuccessful();
    }
}
