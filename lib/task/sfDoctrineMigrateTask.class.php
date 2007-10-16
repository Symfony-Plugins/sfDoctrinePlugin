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
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
    ));
    
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev')
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
    $this->bootstrapSymfony($arguments['application'], $options['env'], true);
    
    $this->loadConnections();
    
    $migrationsDirectory = sfConfig::get('sf_root_dir'). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'migration' . DIRECTORY_SEPARATOR . 'doctrine';
    
    $this->filesystem->mkdirs($migrationsDirectory);
    
    $migration = new Doctrine_Migration($migrationsDirectory);
    
    $to = isset($arguments['version']) ? $arguments['version']:$migration->getLatestVersion();
  
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('migrating to version: %s', $to)))));
    
    $migration->migrate($to);
  }
}