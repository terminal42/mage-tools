<?php

namespace Terminal42\MageTools\Task;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class HtaccessUpdateTask extends AbstractTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/htaccess-update';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Terminal42] Update the .htaccess file';
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
