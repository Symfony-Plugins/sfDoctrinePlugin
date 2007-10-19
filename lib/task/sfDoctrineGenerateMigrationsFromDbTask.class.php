<?php

/*
 * This file is part of the symfony package.
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
 * @version    SVN: $Id: sfDoctrineGenerateMigrationsFromDbTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineGenerateMigrationsFromDbTask extends sfDoctrineBaseTask
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
    
    $this->aliases = array('doctrine-generate-migrations-from-db', 'doctrine-gen-migrations-from-db');
    $this->namespace = 'doctrine';
    $this->name = 'generate-migrations-from-db';
    $this->briefDescription = 'Generate migration classes from existing database connections';

    $this->detailedDescription = <<<EOF
The [doctrine:generate-migration|INFO] task generates migration classes from existing database connections

  [./symfony doctrine:generate-migration|INFO]
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
    
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', 'generating migrations from databases'))));
    
    Doctrine::generateMigrationsFromDb($migrationsDirectory);
  }
}