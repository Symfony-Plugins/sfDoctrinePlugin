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
 * Loads data from fixtures directory.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineLoadDataTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('doctrine-load-data');
    $this->namespace = 'doctrine';
    $this->name = 'data-load';
    $this->briefDescription = 'Loads data from fixtures directory';

    $this->detailedDescription = <<<EOF
The [doctrine:data-load|INFO] task loads data fixtures into the database:

  [./symfony doctrine:data-load frontend|INFO]

The task loads data from all the files found in [data/fixtures/|COMMENT].

If you want to load data from other directories, you can use
the [--dir|COMMENT] option:

  [./symfony doctrine:data-load --dir="data/fixtures" --dir="data/data" frontend|INFO]

If you don't want the task to remove existing data in the database,
use the [--append|COMMENT] option:

  [./symfony doctrine:data-load --append frontend|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony();
    $this->callDoctrineCli('load-data');
  }
}
