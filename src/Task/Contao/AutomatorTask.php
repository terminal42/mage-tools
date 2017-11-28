<?php

namespace Terminal42\MageTools\Task\Contao;

use Mage\Task\AbstractTask;
use Mage\Task\Exception\ErrorException;
use Symfony\Component\Process\Process;

class AutomatorTask extends AbstractTask
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

        /** @var Process $process */
        $process = $this->runtime->runLocalCommand(trim(sprintf(
            '%s contao:automator %s --env=%s',
            $options['console'],
            $options['task'],
            $options['env']
        )));

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
            ['console' => './vendor/bin/contao-console', 'env' => 'dev', 'task' => ''],
            $this->runtime->getMergedOption('symfony'),
            $this->options
        );

        return $options;
    }
}
