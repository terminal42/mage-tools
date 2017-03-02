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
post-release:
    - 'Terminal42\MageTools\Task\Doctrine\MigrateTask'
    - 'Terminal42\MageTools\Task\Doctrine\CacheClearTask'
    # ... symfony cache clear ...
    - 'Terminal42\MageTools\Task\Maintenance\UnlockTask'
```

Available tasks
---------------

##### Terminal42\MageTools\Task\Symfony\PlatformReleaseTask

Updates the platform version in the parameters.yml file.
Uses `git describe` to fetch the version internally and adds the output
as `platform_version` to your `parameters.yml`.

```yaml
on-deploy:
    - 'Terminal42\MageTools\Task\Symfony\PlatformReleaseTask'
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
