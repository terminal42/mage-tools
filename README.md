Mage Tools for deployment
=========================

A set of predefined tasks and helpful libraries for the [Magallanes](http://magephp.com/) PHP Deployment Tool.

Most of the tasks were created to ease the deployment of Symfony and Contao applications. As an extra there are some
other helpful tasks for projects such as [Gulp](http://gulpjs.com/) or [PHPUnit](https://phpunit.de/) tests.
Check the list below for a full list of available tasks and their configurations.  

Usage
-----

To use the tasks simply added them to your ```.mage.yml``` file. The recommended setup is:

```yaml
pre-deploy:
    - 'Terminal42\MageTools\Task\IntegrityCheck\ContaoTask'
    - 'Terminal42\MageTools\Task\IntegrityCheck\PHPUnitTask'
    - 'Terminal42\MageTools\Task\GulpTask'
on-deploy:
    - 'Terminal42\MageTools\Composer\SelfUpdateTask'
    # ... symlinks, composer install, symfony cache warmup, symfony assets install ...
    - 'Terminal42\MageTools\Task\Maintenance\LockTask'
on-release:
    - 'Terminal42\MageTools\Task\Cyon\OPCacheClearTask'
post-release:
    - 'Terminal42\MageTools\Task\Doctrine\MigrateTask'
    - 'Terminal42\MageTools\Task\Doctrine\CacheClearTask'
    # ... symfony cache clear ...
    - 'Terminal42\MageTools\Task\Maintenance\UnlockTask'
```

Available tasks
---------------

##### Terminal42\MageTools\Task\GulpTask

Runs the Gulp default task.

```yaml
pre-deploy:
    - 'Terminal42\MageTools\Task\GulpTask': { env: 'prod' }
```
  
### Composer

##### Terminal42\MageTools\Composer\SelfUpdateTask

Self-update the composer. You can also set the specific composer release.

```yaml
on-deploy:
    - 'Terminal42\MageTools\Composer\SelfUpdateTask': { release: '1.0.0' }
```
  
### Cyon

##### Terminal42\MageTools\Cyon\OPCacheClearTask

Clear the OPCache on the server by running ```pkill lsphp``` command.

```yaml
on-release:
    - 'Terminal42\MageTools\Cyon\OPCacheClearTask'
```
  
### Doctrine

##### Terminal42\MageTools\Doctrine\CacheClearTask

Clear the Doctrine metadata, query and result cache.

```yaml
post-release:
    - 'Terminal42\MageTools\Doctrine\CacheClearTask': { env: 'prod' }
```

##### Terminal42\MageTools\Doctrine\MigrateTask

Run the Doctrine migrations.

```yaml
post-release:
    - 'Terminal42\MageTools\Doctrine\MigrateTask': { env: 'prod' }
```
  
### Integrity check
   
##### Terminal42\MageTools\Task\IntegrityCheck\ContaoTask

Checks Contao by executing ```contao:version``` command in Symfony's console.

```yaml
pre-deploy:
    - 'Terminal42\MageTools\Task\IntegrityCheck\ContaoTask'
```
   
##### Terminal42\MageTools\Task\IntegrityCheck\PHPUnitTask

Runs the PHPUnit tests.

```yaml
pre-deploy:
    - 'Terminal42\MageTools\Task\IntegrityCheck\PHPUnitTask'
```
  
### Maintenance

##### Terminal42\MageTools\Maintenance\LockTask

Enable the maintenance mode.

```yaml
on-deploy:
    - 'Terminal42\MageTools\Maintenance\LockTask': { env: 'prod' }
```

##### Terminal42\MageTools\Maintenance\UnlockTask

Disable the maintenance mode.

```yaml
post-release:
    - 'Terminal42\MageTools\Maintenance\UnlockTask': { env: 'prod' }
```
