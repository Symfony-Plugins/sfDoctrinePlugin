<?php

/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Drops database for current model.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineDropDbTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineDropDbTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('doctrine-drop-db');
    $this->namespace = 'doctrine';
    $this->name = 'drop-db';
    $this->briefDescription = 'Drops database for current model';

    $this->detailedDescription = <<<EOF
The [doctrine:drop-db|INFO] task drops the database:

  [./symfony doctrine:drop-db|INFO]

The task read connection information in [config/doctrine/databases.yml|COMMENT]:
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony();
    
    $this->callDoctrineCli('drop-db');
  }
}