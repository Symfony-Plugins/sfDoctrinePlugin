<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for all symfony Doctrine tasks.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineBaseTask.class.php 5232 2007-09-22 14:50:33Z fabien $
 */
abstract class sfDoctrineBaseTask extends sfBaseTask
{
  public function loadModels()
  {
    $directories = array();
    $directories[] = sfConfig::get('sf_model_lib_dir') . DIRECTORY_SEPARATOR . 'doctrine';
    
    $plugins = sfFinder::type('dir')->maxdepth(0)->ignore_version_control()->in(sfConfig::get('sf_plugins_dir'));
    
    foreach ($plugins as $plugin)
    {
      $name = basename($plugin);  
      $pluginModels = sfConfig::get('sf_plugins_dir').'/'.$name.'/lib/model/doctrine';
      
      if (file_exists($pluginModels)) {
        $directories[] = $pluginModels;
      }
    }
    
    $models = Doctrine::loadModels($directories);
  }
  
  public function loadConnections()
  {
    $doctrineConfigPath = sfConfig::get('sf_plugins_dir').'/sfDoctrinePlugin/config/doctrine.yml';
    
    $config = new sfDoctrineConfigHandler();
    $php = str_replace('<?php', '', $config->execute(array($doctrineConfigPath)));
    
    eval($php);
    
    $databases = sfYaml::load(sfConfig::get('sf_config_dir').'/databases.yml');
    $databases = $databases['all'];
    
    foreach ($databases as $name => $database)
    {
      $info = Doctrine_Manager::getInstance()->parseDsn($database['param']['dsn']);
      
      $dsn = $info['dsn'];
      $user = $info['user'];
      $password = $info['pass'];
      
      $connection = Doctrine_Manager::getInstance()->openConnection(new PDO($dsn, $user, $password), $name);
      
      // Load attributes to connection
      foreach($default_attributes as $k => $v)
      {
        $connection->setAttribute(constant('Doctrine::'.$k), $v);
      }
    }
    
    // Apply connection component binding
    $schemasPath = sfConfig::get('sf_config_dir').'/schemas.yml';
    
    if (file_exists($schemasPath)) {
      $schemas = new sfDoctrineSchemasConfigHandler();
      $php = str_replace('<?php', '', $schemas->execute(array($schemasPath)));
      
      eval($php);
    }
  }
}