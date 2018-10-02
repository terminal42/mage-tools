<?php

namespace Terminal42\MageTools\Task\Backup;

use Mage\Task\BuiltIn\Symfony\AbstractSymfonyTask;
use Mage\Task\Exception\ErrorException;
use Symfony\Component\Process\Process;

class DatabaseBackupTask extends AbstractSymfonyTask
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'terminal42/backup/database-backup';
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        $options = $this->getOptions();

        return '[Backup] Database backup: ' . $options['database'];
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $options = $this->getOptions();

        if (!$options['database']) {
            throw new ErrorException('Parameter "database" is not defined. You must provide the database connection you would like to back up. See the backup-manager/symfony configuration bundle for details.');
        }

        if (!$options['storage']) {
            throw new ErrorException('Parameter "storage" is not defined. You must provide the storage you would like to use. See the backup-manager/symfony configuration bundle for details.');
        }

        $command = trim(sprintf('%s backup-manager:backup %s %s --env=%s %s',
            $options['console'],
            $options['database'],
            $options['storage'],
            $options['env'],
            $options['flags']
        ));

        // Filename
        if ($options['filename']) {
            $command = sprintf('%s --filename %s', $command, $options['filename']);
        }

        // Compression
        if ($options['compression']) {
            $command = sprintf('%s -c %s', $command, $options['compression']);
        }

        /** @var Process $process */
        $process = $this->runtime->runRemoteCommand($command, true);

        return $process->isSuccessful();
    }

    /**
     * @inheritDoc
     */
    protected function getSymfonyOptions()
    {
        return [
            'console' => './vendor/bin/contao-console',
            'database' => '',
            'storage' => '',
            'compression' => '',
            'filename' => sprintf('%s.sql', date('Y-m-d-H:i:s')),
            'flags' => '',
        ];
    }
}
