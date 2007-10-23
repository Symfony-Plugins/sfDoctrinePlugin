<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Creates database for current model.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineBuildDbTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('doctrine-build-db');
    $this->namespace = 'doctrine';
    $this->name = 'build-db';
    $this->briefDescription = 'Creates database for current model';

    $this->detailedDescription = <<<EOF
The [doctrine:build-db|INFO] task creates the database:

  [./symfony doctrine:build-db|INFO]

The task read connection information in [config/doctrine/databases.yml|COMMENT]:
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony();
    
    $this->callDoctrineCli('create-db');
  }
}