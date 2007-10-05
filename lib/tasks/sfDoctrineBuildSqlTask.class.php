<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Create SQL for the current model.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineBuildSqlTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineBuildSqlTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name')
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environement', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));
    
    $this->aliases = array('doctrine-build-sql');
    $this->namespace = 'doctrine';
    $this->name = 'build-sql';
    $this->briefDescription = 'Creates SQL for the current model';

    $this->detailedDescription = <<<EOF
The [doctrine:build-sql|INFO] task creates SQL statements for table creation:

  [./symfony doctrine:build-sql|INFO]

The generated SQL is optimized for the database configured in [config/doctrine.ini|COMMENT]:

  [doctrine.database = mysql|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony($arguments['application'], $options['env'], true);
    
    $sqlPath = sfConfig::get('sf_root_dir').'/data/sql';
    
    $sql = Doctrine::exportSql(sfConfig::get('sf_root_dir').'/lib/model/doctrine');
    
    file_put_contents($sqlPath.'/doctrine-schema.sql', implode("\n", $sql));
    
    // Build sql for all of the plugins
    $plugins = sfFinder::type('dir')->maxdepth(0)->ignore_version_control()->in(sfConfig::get('sf_plugins_dir'));
    
    foreach ($plugins as $plugin)
    {
      $name = basename($plugin);
      
      $pluginModels = sfConfig::get('sf_plugins_dir').'/'.$name.'/lib/model/doctrine';
      
      if (file_exists($pluginModels))
      {
        $sql = Doctrine::exportSql($pluginModels);
        
        file_put_contents($sqlPath.'/doctrine-'.$name.'-schema.sql', implode("\n", $sql));
      }
    }
  }
}