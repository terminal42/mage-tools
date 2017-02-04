<?php

namespace Terminal42\MageTools\Task\Composer;

use Mage\Task\AbstractTask;
use Symfony\Component\Process\Process;

class SelfUpdateTask extends AbstractTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/composer/self-update';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Terminal42] Composer – self-update';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();
        $command = sprintf('%s self-update %s', $options['path'], $options['release']);

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
        $userOptions = $this->runtime->getConfigOption('composer', []);
        $options     = array_merge(
            ['path' => 'composer', 'release' => ''],
            (is_array($userOptions) ? $userOptions : []),
            $this->options
        );

        return $options;
    }
}
