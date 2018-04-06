<?php

namespace Terminal42\MageTools\Task\Doctrine;

use Mage\Task\BuiltIn\Symfony\AbstractSymfonyTask;
use Symfony\Component\Process\Process;

class MigrateTask extends AbstractSymfonyTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/doctrine/migrate';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return '[Doctrine] Executing migrations';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();

        $command = sprintf(
            '%s doctrine:migrations:migrate --env=%s %s',
            $options['console'],
            $options['env'],
            $options['flags']
        );

        /** @var Process $process */
        $process = $this->runtime->runCommand(trim($command));

        return $process->isSuccessful();
    }

    /**
     * @inheritDoc
     */
    protected function getSymfonyOptions()
    {
        return ['flags' => '-n --allow-no-migration'];
    }
}
