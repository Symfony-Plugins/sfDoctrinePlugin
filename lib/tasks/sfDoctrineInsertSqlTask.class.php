<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Inserts SQL for current model.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineInsertSqlTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineInsertSqlTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('doctrine-insert-sql');
    $this->namespace = 'doctrine';
    $this->name = 'insert-sql';
    $this->briefDescription = 'Inserts SQL for current model';

    $this->detailedDescription = <<<EOF
The [doctrine:insert-sql|INFO] task creates database tables:

  [./symfony doctrine:insert-sql|INFO]

The task connects to the database and executes all SQL statements
found in [config/sql/*schema.sql|COMMENT] files.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->loadConnections();
    
    Doctrine::exportSchema(sfConfig::get('sf_root_dir').'/lib/model/doctrine');
  }
}