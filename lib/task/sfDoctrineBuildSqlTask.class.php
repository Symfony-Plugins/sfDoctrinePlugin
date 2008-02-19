<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfDoctrineBaseTask.class.php');

/**
 * Create SQL for the current model.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineBuildSqlTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {        
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
    $this->bootstrapSymfony();
    $this->callDoctrineCli('generate-sql');
  }
}
