<?php

/*
 * This file is part of the sfDoctrinePlugin package.
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
 * @version    SVN: $Id: sfDoctrineMigrateTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineMigrateTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('version', sfCommandArgument::OPTIONAL, 'The version to migrate to', null),
    ));
    
    $this->aliases = array('doctrine-migrate');
    $this->namespace = 'doctrine';
    $this->name = 'migrate';
    $this->briefDescription = 'Migrates database to current/specified version';

    $this->detailedDescription = <<<EOF
The [doctrine:migrate|INFO] task migrates database to current/specified version

  [./symfony doctrine:migrate|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony();
    
    $this->callDoctrineCli('migrate', array('version' => $arguments['version']));
  }
}