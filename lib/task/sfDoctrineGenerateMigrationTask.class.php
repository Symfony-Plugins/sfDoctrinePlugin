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
 * @version    SVN: $Id: sfDoctrineGenerateMigrationTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineGenerateMigrationTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::REQUIRED, 'The name of the migration'),
    ));
    
    $this->aliases = array('doctrine-generate-migration');
    $this->namespace = 'doctrine';
    $this->name = 'generate-migration';
    $this->briefDescription = 'Generate migration class';

    $this->detailedDescription = <<<EOF
The [doctrine:generate-migration|INFO] task generates migration template

  [./symfony doctrine:generate-migration|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony();
    
    $this->callDoctrineCli('generate-migration', array('name' => $arguments['name']));
  }
}