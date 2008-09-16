<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfDoctrineBaseTask.class.php');

/**
 * Enable Doctrine as the default orm in a given symfony application
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineEnableTask.class.php 8916 2008-05-13 01:35:44Z Jonathan.Wage $
 */
class sfDoctrineEnableTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
    ));

    $this->aliases = array('doctrine-enable');
    $this->namespace = 'doctrine';
    $this->name = 'enable';
    $this->briefDescription = 'Enable Doctrine as the default orm in a given symfony application';

    $this->detailedDescription = <<<EOF
The [doctrine:enable|INFO] task enables Doctrine as the default orm in a given symfony application:

  [./symfony doctrine:enable|INFO]

The task modifies various configuration settings to prepare Doctrine as the default orm|COMMENT]:
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (strtolower(sfConfig::get('sf_orm')) == 'doctrine')
    {
      throw new sfDoctrineException('Doctrine is already enabled as the default orm');
    }

    // Copy default doctrine.yml to project config directory
    $doctrineConfigPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'doctrine.yml';
    $doctrineConfigCopyPath = sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . 'doctrine.yml';
    $this->getFileSystem()->copy($doctrineConfigPath, $doctrineConfigCopyPath);

    // Change settings.yml to have orm => doctrine
    $appSettingsPath = sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . 'settings.yml';
    $appSettingsText = file_get_contents($appSettingsPath);
    $appSettingsText .= "all:\n  .settings:\n    orm: doctrine";
    file_put_contents($appSettingsPath, $appSettingsText);
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', 'Changed ' . $appSettingsPath . ' default orm to Doctrine'))));

    // Change databases.yml to use sfDoctrineDatabase instead of sfPropelDatabase
    $databasesPath = sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml';
    $databasesText = file_get_contents($databasesPath);
    $databasesText = str_replace('sfPropelDatabase', 'sfDoctrineDatabase', $databasesText);
    file_put_contents($databasesPath, $databasesText);
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', 'Changed ' . $databasesPath . ' to use sfDoctrineDatabase'))));

    // Make doctrine directories
    $this->getFileSystem()->mkdirs(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'doctrine');
    $this->getFileSystem()->mkdirs(sfConfig::get('sf_lib_dir') . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'doctrine');
    $this->getFileSystem()->mkdirs(sfConfig::get('sf_lib_dir') . DIRECTORY_SEPARATOR . 'migration' . DIRECTORY_SEPARATOR . 'doctrine');
    $this->getFileSystem()->mkdirs(sfConfig::get('sf_lib_dir') . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . 'doctrine');
  }
}