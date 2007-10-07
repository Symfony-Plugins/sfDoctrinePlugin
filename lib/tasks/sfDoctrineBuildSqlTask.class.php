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
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
    ));
    
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev')
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
    $this->loadConnections();
    $models = $this->loadModels();
    
    $sqlPath = sfConfig::get('sf_root_dir').'/data/sql';
    
    $sql = Doctrine_Manager::connection()->export->exportClassesSql($models);
    
    file_put_contents($sqlPath.'/doctrine-schema.sql', implode("\n", $sql));
  }
}