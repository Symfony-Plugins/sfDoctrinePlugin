<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for all symfony Doctrine tasks.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
abstract class sfDoctrineBaseTask extends sfBaseTask
{

  static protected $done = false;

  public function initialize(sfEventDispatcher $dispatcher, sfFormatter $formatter)
  {
    parent::initialize($dispatcher, $formatter);

    if (!self::$done)
    {
      $libDir = dirname(__FILE__).'/..';

      $autoloader = sfSimpleAutoload::getInstance();
      $autoloader->addDirectory($libDir);
      $autoloader->register();

      self::$done = true;
    }
  }

  public function bootstrapSymfony($app = null, $env = 'dev', $debug = true)
  {
    // if we've already bootstrapped....
    if (defined('SF_ROOT_DIR'))
      return;

    if (!isset($app)) 
    {
       $applications = sfFinder::type('dir')->maxdepth(0)->ignore_version_control()->in(sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'apps'); 

       if (isset($applications[0])) {
         $app = basename($applications[0]);
       } else {
         throw new Exception('You must have at least one application');
       }
    }
    return parent::bootstrapSymfony($app, $env, $debug);

  }

  /**
   * callDoctrineCli
   *
   * @param string $task 
   * @param string $args 
   * @return void
   */
  public function callDoctrineCli($task, $args = array())
  {
    $pluginDirs = glob(sfConfig::get('sf_root_dir').'/plugins/*/data');
    $fixtures = sfFinder::type('dir')->name('fixtures')->in(array_merge($pluginDirs, array(sfConfig::get('sf_data_dir'))));
    $models = sfConfig::get('sf_model_lib_dir') . DIRECTORY_SEPARATOR . 'doctrine';
    $migrations = sfConfig::get('sf_lib_dir') . DIRECTORY_SEPARATOR . 'migration' . DIRECTORY_SEPARATOR . 'doctrine';
    $sql = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'sql';
    $yaml = sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'doctrine';
  
    $config = array('data_fixtures_path'  =>  $fixtures,
                    'models_path'         =>  $models,
                    'migrations_path'     =>  $migrations,
                    'sql_path'            =>  $sql,
                    'yaml_schema_path'    =>  $yaml);
  
    $arguments = array('./symfony', $task);
  
    foreach ($args as $key => $arg)
    {
      if (isset($config[$key]))
      {
        $config[$key] = $arg;
      } else {
        $arguments[] = $arg;
      }
    }
  
    $cli = new sfDoctrineCli($config);
    $cli->setDispatcher($this->dispatcher);
    $cli->setFormatter($this->formatter);
    $cli->run($arguments);
  }
}
