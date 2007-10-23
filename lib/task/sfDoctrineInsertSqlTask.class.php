<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Inserts SQL for current model.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
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
    $this->bootstrapSymfony();
    
    $this->callDoctrineCli('create-tables');
  }
}