<?php

namespace Terminal42\MageTools\Command;

use Mage\Command\AbstractCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployAllCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('deploy-all')
            ->setDescription('Deploy all environments')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->requireConfig();
        $command = $this->getApplication()->find('deploy');

        foreach (array_keys($this->runtime->getConfiguration()['environments']) as $environment) {
            if ($environment !== 'blue') {
                continue;
            }

            $code = $command->run(new ArrayInput(['command' => 'deploy', 'environment' => $environment]), $output);

            if ($code > 0) {
                return $code;
            }
        }

        return 0;
    }
}
