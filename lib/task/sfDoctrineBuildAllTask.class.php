<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates Doctrine model, SQL and initializes the database.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineBuildAllTask.class.php 5232 2007-09-22 14:50:33Z fabien $
 */
class sfDoctrineBuildAllTask extends sfDoctrineBaseTask
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
    
    $this->aliases = array('doctrine-build-all');
    $this->namespace = 'doctrine';
    $this->name = 'build-all';
    $this->briefDescription = 'Generates Doctrine model, SQL and initializes the database';

    $this->detailedDescription = <<<EOF
The [doctrine:build-all|INFO] task is a shortcut for three other tasks:

  [./symfony doctrine:build-all|INFO]

The task is equivalent to:

  [./symfony doctrine:build-model|INFO]
  [./symfony doctrine:insert-sql|INFO]

See those three tasks help page for more information.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $buildDb = new sfDoctrineBuildDbTask($this->dispatcher, $this->formatter);
    $buildDb->run();
    
    $buildModel = new sfDoctrineBuildModelTask($this->dispatcher, $this->formatter);
    $buildModel->run();

    $insertSql = new sfDoctrineInsertSqlTask($this->dispatcher, $this->formatter);
    $insertSql->run(array('application' => $arguments['application']));
  }
}