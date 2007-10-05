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
  public function loadDoctrine()
  {
    require_once(dirname(dirname(__FILE__)).'/doctrine/Doctrine.php');
    
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
}