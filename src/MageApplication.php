<?php

namespace Terminal42\MageTools;

use Mage\Command\AbstractCommand;
use Terminal42\MageTools\Command\DeployAllCommand;
use Terminal42\MageTools\Command\SshCommand;

class MageApplication extends \Mage\MageApplication
{
    /**
     * @inheritDoc
     */
    protected function loadBuiltInCommands()
    {
        parent::loadBuiltInCommands();

        $this->loadCommands([DeployAllCommand::class, SshCommand::class]);
    }

    /**
     * Load the provided commands
     *
     * @param array $commands
     */
    protected function loadCommands(array $commands)
    {
        foreach ($commands as $command) {
            /** @var AbstractCommand $instance */
            $instance = new $command();
            $instance->setRuntime($this->runtime);

            $this->add($instance);
        }
    }
}
