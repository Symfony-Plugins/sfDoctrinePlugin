<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Creates a schema.xml from an existing database.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineBuildSchemaTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineBuildSchemaTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::OPTIONAL, 'The application name'),
    ));
    
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev')
    ));
    
    $this->aliases = array('doctrine-build-schema');
    $this->namespace = 'doctrine';
    $this->name = 'build-schema';
    $this->briefDescription = 'Creates a schema.xml from an existing database';

    $this->detailedDescription = <<<EOF
The [doctrine:build-schema|INFO] task introspects a database to create a schema:

  [./symfony doctrine:build-schema|INFO]

The task creates a yml file.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony($arguments['application'], $options['env'], true);
    $this->loadConnections();
    
    $schema = sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'schema.yml';
    
    Doctrine_Facade::generateYamlFromDb($schema);
  }
}