<?php

namespace Terminal42\MageTools\Task\Contao;

use Mage\Task\BuiltIn\Symfony\AbstractSymfonyTask;
use Mage\Task\Exception\ErrorException;
use Symfony\Component\Process\Process;

class AutomatorTask extends AbstractSymfonyTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/contao/automator';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        $options = $this->getOptions();

        return '[Contao] Automator task: ' . $options['task'];
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();

        if (!$options['task']) {
            throw new ErrorException('Parameter "task" is not defined. Run "vendor/bin/contao-console contao:automator" to see the available tasks.');
        }

        $command = trim(sprintf('%s contao:automator %s --env=%s',
            $options['console'],
            $options['task'],
            $options['env']
        ));

        /** @var Process $process */
        $process = $this->runtime->runRemoteCommand($command, true);

        return $process->isSuccessful();
    }

    /**
     * @inheritDoc
     */
    protected function getSymfonyOptions()
    {
        return ['console' => './vendor/bin/contao-console', 'task' => ''];
    }
}
