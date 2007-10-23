<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Creates database for current model.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineRebuildDbTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineRebuildDbTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('doctrine-rebuild-db');
    $this->namespace = 'doctrine';
    $this->name = 'rebuild-db';
    $this->briefDescription = 'Creates database for current model';

    $this->detailedDescription = <<<EOF
The [doctrine:rebuild-db|INFO] task creates the database:

  [./symfony doctrine:rebuild-db|INFO]

The task read connection information in [config/doctrine/databases.yml|COMMENT]:
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony();
    
    $this->callDoctrineCli('rebuild-db');
  }
}