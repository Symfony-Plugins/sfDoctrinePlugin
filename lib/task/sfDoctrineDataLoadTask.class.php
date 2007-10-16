<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Loads data from fixtures directory.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineDataLoadTask.class.php 5232 2007-09-22 14:50:33Z fabien $
 */
class sfDoctrineLoadDataTask extends sfDoctrineBaseTask
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
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('append', null, sfCommandOption::PARAMETER_NONE, 'Don\'t delete current data in the database'),
      new sfCommandOption('dir', null, sfCommandOption::PARAMETER_REQUIRED | sfCommandOption::IS_ARRAY, 'The directories to look for fixtures'),
    ));

    $this->aliases = array('doctrine-load-data');
    $this->namespace = 'doctrine';
    $this->name = 'data-load';
    $this->briefDescription = 'Loads data from fixtures directory';

    $this->detailedDescription = <<<EOF
The [doctrine:data-load|INFO] task loads data fixtures into the database:

  [./symfony doctrine:data-load frontend|INFO]

The task loads data from all the files found in [data/fixtures/|COMMENT].

If you want to load data from other directories, you can use
the [--dir|COMMENT] option:

  [./symfony doctrine:data-load --dir="data/fixtures" --dir="data/data" frontend|INFO]

If you don't want the task to remove existing data in the database,
use the [--append|COMMENT] option:

  [./symfony doctrine:data-load --append frontend|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (!defined('SF_ROOT_DIR')) {
      $this->bootstrapSymfony($arguments['application'], $options['env'], true);
    }
    
    sfSimpleAutoload::getInstance()->unregister();
    sfSimpleAutoload::getInstance()->register();

    if (count($options['dir']))
    {
      $fixturesDirs = $options['dir'];
    } else {
      if (!$pluginDirs = glob(sfConfig::get('sf_root_dir').'/plugins/*/data'))
      {
        $pluginDirs = array();
      }
      
      $fixturesDirs = sfFinder::type('dir')->name('fixtures')->in(array_merge($pluginDirs, array(sfConfig::get('sf_data_dir'))));
    }
    
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('load data from "%s"', implode(',', $fixturesDirs))))));
    
    $append = (isset($options['append']) && $options['append']) ? true:false;
    
    Doctrine_Facade::loadData($fixturesDirs, $append);
  }
}