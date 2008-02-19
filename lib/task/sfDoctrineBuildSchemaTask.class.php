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
 * Creates a schema.xml from an existing database.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineBuildSchemaTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {    
    $this->aliases = array('doctrine-build-schema');
    $this->namespace = 'doctrine';
    $this->name = 'build-schema';
    $this->briefDescription = 'Creates a schema.xml from an existing database';

    $this->detailedDescription = <<<EOF
The [doctrine:build-schema|INFO] task introspects a database to create a schema:

  [./symfony doctrine:build-schema|INFO]

The task creates a yml file.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony();
    
    $this->callDoctrineCli('generate-yaml-db');
  }
}
