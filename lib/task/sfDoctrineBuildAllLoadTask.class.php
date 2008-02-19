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
 * Creates Databases, Generates Doctrine model, SQL, initializes database, and load data.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfDoctrineBuildAllLoadTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('doctrine-build-all-load');
    $this->namespace = 'doctrine';
    $this->name = 'build-all-load';
    $this->briefDescription = 'Generates Doctrine model, SQL, initializes database, and load data';

    $this->detailedDescription = <<<EOF
The [doctrine:build-all-load|INFO] task is a shortcut for four other tasks:

  [./symfony doctrine:build-all-load frontend|INFO]

The task is equivalent to:

  [./symfony doctrine:build-model|INFO]
  [./symfony doctrine:build-sql|INFO]
  [./symfony doctrine:insert-sql|INFO]
  [./symfony doctrine:data-load frontend|INFO]

The task takes an application argument because of the [doctrine:data-load|COMMENT]
task. See [doctrine:data-load|COMMENT] help page for more information.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $buildAll = new sfDoctrineBuildAllTask($this->dispatcher, $this->formatter);
    $buildAll->run();

    $loadData = new sfDoctrineLoadDataTask($this->dispatcher, $this->formatter);
    $loadData->run();
  }
}
