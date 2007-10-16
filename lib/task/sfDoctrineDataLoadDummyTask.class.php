<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Loads dummy data in to your models
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineDataDumpTask.class.php 5232 2007-09-22 14:50:33Z fabien $
 */
class sfDoctrineDataLoadDummyTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('num', sfCommandArgument::OPTIONAL, 'Number of dummy records to populate per model')
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environement', 'dev'),
      new sfCommandOption('append', null, sfCommandOption::PARAMETER_NONE, 'Don\'t delete current data in the database')
    ));

    $this->aliases = array('doctrine-load-data-dummy');
    $this->namespace = 'doctrine';
    $this->name = 'data-load-dummy';
    $this->briefDescription = 'Loads dummy data in to your models';

    $this->detailedDescription = <<<EOF
The [doctrine:data-load-dummy|INFO] task loads dummy data in to your models:

  [./symfony doctrine:data-load-dummy frontend|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony($arguments['application'], $options['env'], true);
    
    $this->loadModels();
    
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', 'loading dummy data'))));
    
    $append = (isset($options['append']) && $options['append']) ? true:false;
    $num = isset($arguments['num']) ? $arguments['num']:5;
    
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('loading %s records of dummy data for each model', $num)))));
    
    Doctrine_Facade::loadDummyData($append, $num);
  }
}