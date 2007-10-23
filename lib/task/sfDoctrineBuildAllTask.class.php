<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates Doctrine model, SQL and initializes the database.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineBuildAllTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
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
    $insertSql->run();
  }
}