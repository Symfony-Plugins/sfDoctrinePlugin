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
 * Inserts SQL for current model.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfDoctrineGenerateMigrationsModelsTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {    
    $this->aliases = array('doctrine-generate-migrations-models', 'doctrine-gen-migrations-from-models');
    $this->namespace = 'doctrine';
    $this->name = 'generate-migrations-models';
    $this->briefDescription = 'Generate migration classes from an existing set of models';

    $this->detailedDescription = <<<EOF
The [doctrine:generate-migration|INFO] task generates migration classes from an existing set of models

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
