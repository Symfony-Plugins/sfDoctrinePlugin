<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generate migrations from database
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id: sfDoctrineGenerateMigrationsDbTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineGenerateMigrationsDbTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('doctrine-generate-migrations-db', 'doctrine-gen-migrations-from-db');
    $this->namespace = 'doctrine';
    $this->name = 'generate-migrations-db';
    $this->briefDescription = 'Generate migration classes from existing database connections';

    $this->detailedDescription = <<<EOF
The [doctrine:generate-migration|INFO] task generates migration classes from existing database connections

  [./symfony doctrine:generate-migration|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony();
    
    $this->callDoctrineCli('generate-migrations-db');
  }
}