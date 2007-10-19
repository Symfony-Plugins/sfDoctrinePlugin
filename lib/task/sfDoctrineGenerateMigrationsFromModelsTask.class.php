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
 * @version    SVN: $Id: sfDoctrineGenerateMigrationsFromModelsTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineGenerateMigrationsFromModelsTask extends sfDoctrineBaseTask
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
    
    $this->aliases = array('doctrine-generate-migrations-from-models', 'doctrine-gen-migrations-from-models');
    $this->namespace = 'doctrine';
    $this->name = 'generate-migrations-from-models';
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
    $this->bootstrapSymfony($arguments['application'], $options['env'], true);
    
    $this->loadConnections();
    
    $migrationsDirectory = sfConfig::get('sf_root_dir'). DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'migration' . DIRECTORY_SEPARATOR . 'doctrine';
    
    $modelsDirectory = sfConfig::get('sf_model_lib_dir') . DIRECTORY_SEPARATOR . 'doctrine';
    
    $this->filesystem->mkdirs($migrationsDirectory);
    
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', 'generating migrations from models'))));
    
    Doctrine::generateMigrationsFromModels($migrationsDirectory, $modelsDirectory);
  }
}