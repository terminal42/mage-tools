<?php

namespace Terminal42\MageTools;

use Symfony\Component\Console\Input\InputInterface;
use Terminal42\MageTools\Command\DeployAllCommand;

class MageApplication extends \Mage\MageApplication
{
    /**
     * @inheritDoc
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'deploy-all';
    }

    /**
     * @inheritDoc
     */
    protected function loadBuiltInCommands()
    {
        parent::loadBuiltInCommands();

        $command = new DeployAllCommand();
        $command->setRuntime($this->runtime);

        $this->add($command);
    }
}
