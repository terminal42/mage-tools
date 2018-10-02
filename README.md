Mage Tools for deployment
=========================

A set of predefined tasks and helpful libraries for the [Magallanes](http://magephp.com/) PHP Deployment Tool.

Most of the tasks were created to ease the deployment of [Symfony](http://symfony.com/)
and [Contao](https://contao.org/) applications. Check the list below for a full list of available tasks
and their configurations.

Usage
-----

To use the tasks simply added them to your ```.mage.yml``` file. The recommended setup is:

```yaml
pre-deploy:
    - 'Terminal42\MageTools\Task\IntegrityCheck\ContaoTask'
on-deploy:
    # ... symlinks, composer install ...
    - 'Terminal42\MageTools\Task\Symfony\PlatformReleaseTask'
    # ... symfony cache warmup, symfony assets install ...
    - 'Terminal42\MageTools\Task\Maintenance\LockTask'
on-release:
    - 'Terminal42\MageTools\Task\Symfony\AcceleratorCacheClearTask'
post-release:
    - 'Terminal42\MageTools\Task\Backup\DatabaseBackupTask'
    - 'Terminal42\MageTools\Task\Doctrine\MigrateTask'
    - 'Terminal42\MageTools\Task\Doctrine\CacheClearTask'
    # ... symfony cache clear ...
    - 'Terminal42\MageTools\Task\Maintenance\UnlockTask'
```

Available tasks
---------------

##### Terminal42\MageTools\Task\Symfony\AcceleratorCacheClearTask

Clears the accelerator cache. The [AcceleratorCacheBundle](https://github.com/Smart-Core/AcceleratorCacheBundle)
is required for this to work.

```yaml
on-release:
    - 'Terminal42\MageTools\Task\Symfony\AcceleratorCacheClearTask': { flags: "--opcode" }
```

##### Terminal42\MageTools\Task\Symfony\PlatformReleaseTask

Updates the platform version in the parameters.yml file.
Uses `git describe` to fetch the version internally and adds the output
as `platform_version` to your `parameters.yml`.

```yaml
on-deploy:
    - 'Terminal42\MageTools\Task\Symfony\PlatformReleaseTask'
```

### Backup

##### Terminal42\MageTools\Task\Backup\DatabaseBackupTask

Runs the database backup task using [backup-manager/symfony bundle](https://github.com/backup-manager/symfony).
This task should be run before ane database changes are made. The parameters are reflecting the bundle
configuration under `bm_backup_manager`.

```yaml
post-release:
    - 'Terminal42\MageTools\Task\Backup\DatabaseBackupTask': { database: 'production', storage: 'local' }

    # Optional parameters:
    # - filename (defaults to: Y-m-d-H:i:s.sql)
    # - compression (defaults to: none)
    # - flags (defaults to: none)
```

### Contao

##### Terminal42\MageTools\Task\Contao\AutomatorTask

Runs the Contao automator task. You must provide the task name.
Execute the Run "vendor/bin/contao-console contao:automator" command to see available tasks.

```yaml
post-release:
    - 'Terminal42\MageTools\Task\Contao\AutomatorTask': { task: 'purgeSearchCache', env: 'prod' }
```

### Doctrine

##### Terminal42\MageTools\Task\Doctrine\CacheClearTask

Clear the Doctrine metadata, query and result cache.

```yaml
post-release:
    - 'Terminal42\MageTools\Task\Doctrine\CacheClearTask': { env: 'prod' }
```

##### Terminal42\MageTools\Task\Doctrine\MigrateTask

Run the Doctrine migrations.

```yaml
post-release:
    - 'Terminal42\MageTools\Task\Doctrine\MigrateTask': { env: 'prod' }
```

### Integrity check

##### Terminal42\MageTools\Task\IntegrityCheck\ContaoTask

Checks Contao by executing ```contao:version``` command in Symfony's console.

```yaml
pre-deploy:
    - 'Terminal42\MageTools\Task\IntegrityCheck\ContaoTask'
```

### Maintenance

##### Terminal42\MageTools\Task\Maintenance\LockTask

Enable the maintenance mode.

```yaml
on-deploy:
    - 'Terminal42\MageTools\Task\Maintenance\LockTask': { env: 'prod' }
```

##### Terminal42\MageTools\Task\Maintenance\UnlockTask

Disable the maintenance mode.

```yaml
post-release:
    - 'Terminal42\MageTools\Task\Maintenance\UnlockTask': { env: 'prod' }
```

Custom commands
---------------

### Deploy all

Deploys system to all configured environments.

```
vendor/bin/mage-terminal42 deploy-all
```

### Connect via SSH

Allows to open the SSH connection based on configured environments.

Basic usage (will take the first host defined for the environment):

```
vendor/bin/mage-terminal42 ssh production
```

For multiple hosts given you have config:

```yaml
hosts:
    - webserver1
    - webserver2
    - webserver3
```

You can connect to them using:

```
vendor/bin/mage-terminal42 ssh --host=webserver3 production
vendor/bin/mage-terminal42 ssh --host=2 production
```
