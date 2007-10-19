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
 * @version    SVN: $Id: sfDoctrineBuildDbTask.class.php 4743 2007-07-30 10:21:06Z fabien $
 */
class sfDoctrineBuildDbTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('doctrine-build-db');
    $this->namespace = 'doctrine';
    $this->name = 'build-db';
    $this->briefDescription = 'Creates database for current model';

    $this->detailedDescription = <<<EOF
The [doctrine:build-db|INFO] task creates the database:

  [./symfony doctrine:build-db|INFO]

The task read connection information in [config/doctrine/databases.yml|COMMENT]:
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databases = sfYaml::load(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml');
    $databases = $databases['all'];
    
    $manager = Doctrine_Manager::getInstance();
    
    foreach ($databases as $name => $database)
    {
      $dsn = $database['param']['dsn'];
      $info = $manager->parseDsn($dsn);
      
      $dsn = $info['scheme'].':host='.$info['host'];
      $user = $info['user'];
      $password = $info['pass'];
      
      $connection = $manager->openConnection(new PDO($dsn, $user, $password), $name);
      
      try {
        $connection->export->createDatabase($info['database']);
        
        $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('build database: %s succeeded', $info['database'])))));
      } catch(Exception $e) {
        $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('build database: %s failed with error: %s', $info['database'], $e->getMessage())))));
      }
      
      $manager->closeConnection($connection);
    }
  }
}