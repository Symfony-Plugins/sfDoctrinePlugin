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
      new sfCommandArgument('target', sfCommandArgument::REQUIRED, 'The target filename'),
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
    $this->bootstrapSymfony();
    
    $args = array();
    if (isset($arguments['target']))
    {
      $filename = $arguments['target'];

      if (!sfToolkit::isPathAbsolute($filename))
      {
        $dir = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'fixtures';
        $filename = $dir . DIRECTORY_SEPARATOR . $filename;
      }
    
      $args = array('data_fixtures_path' => $filename);
    }

    $this->callDoctrineCli('dump-data', $args);
  }
}