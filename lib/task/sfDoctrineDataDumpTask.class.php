<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Dumps data to the fixtures directory.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineDataDumpTask.class.php 5232 2007-09-22 14:50:33Z fabien $
 */
class sfDoctrineDumpDataTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('target', sfCommandArgument::REQUIRED, 'The target filename'),
      new sfCommandArgument('individual_files', sfCommandArgument::OPTIONAL, 'Whether or not to have individiaul fixtures file for each model.')
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environement', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->aliases = array('doctrine-dump-data');
    $this->namespace = 'doctrine';
    $this->name = 'data-dump';
    $this->briefDescription = 'Dumps data to the fixtures directory';

    $this->detailedDescription = <<<EOF
The [doctrine:data-dump|INFO] task dumps database data:

  [./symfony doctrine:data-dump frontend dump|INFO]

The task dumps the database data in [data/fixtures/%target%|COMMENT].

The dump file is in the YML format and can be reimported by using
the [doctrine:data-load|INFO] task.

By default, the task use the [doctrine|COMMENT] connection as defined in [config/databases.yml|COMMENT].
You can use another connection by using the [connection|COMMENT] option:

  [./symfony doctrine:data-load --connection="name" frontend|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony($arguments['application'], $options['env'], true);
    
    $this->loadModels();
    
    $filename = $arguments['target'];

    if (!sfToolkit::isPathAbsolute($filename))
    {
      $dir = sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'fixtures';
      $this->filesystem->mkdirs($dir);
      $filename = $dir.DIRECTORY_SEPARATOR.$filename;
    }

    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('dumping data to "%s"', $filename)))));
    
    $individualFiles = (isset($arguments['individual_files']) && $arguments['individual_files']) ? true:false;
    
    Doctrine_Facade::dumpData($filename, $individualFiles);
  }
}