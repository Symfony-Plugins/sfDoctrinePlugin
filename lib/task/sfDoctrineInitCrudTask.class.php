<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Initializes a Doctrine CRUD module.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id: sfDoctrineInitCrudTask.class.php 5260 2007-09-24 09:22:11Z fabien $
 */
class sfDoctrineInitCrudTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('module', sfCommandArgument::REQUIRED, 'The module name'),
      new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'The model class name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('theme', null, sfCommandOption::PARAMETER_REQUIRED, 'The theme name', 'default'),
    ));

    $this->aliases = array('doctrine-init-crud');
    $this->namespace = 'doctrine';
    $this->name = 'init-crud';
    $this->briefDescription = 'Initializes a Doctrine CRUD module';

    $this->detailedDescription = <<<EOF
The [doctrine:init-crud|INFO] task generates a Doctrine CRUD module:

  [./symfony doctrine:init-crud frontend article Article|INFO]

The task creates a [%module%|COMMENT] module in the [%application%|COMMENT] application
for the model class [%model%|COMMENT].

The created module is an empty one that inherit its actions and templates from
a runtime generated module in [%sf_app_cache_dir%/modules/auto%module%|COMMENT].

Most of the time, you want to generate a CRUD module as a starting point for your
development. In this case, you will want use the [doctrine:generate-crud|COMMENT] task.

The generator can use a customized theme by using the [--theme|COMMENT] option:

  [./symfony doctrine:init-crud --theme="custom" frontend article Article|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $properties = parse_ini_file(sfConfig::get('sf_config_dir').'/properties.ini', true);

    $constants = array(
      'PROJECT_NAME' => isset($properties['name']) ? $properties['name'] : 'symfony',
      'APP_NAME'     => $arguments['application'],
      'MODULE_NAME'  => $arguments['module'],
      'MODEL_CLASS'  => $arguments['model'],
      'AUTHOR_NAME'  => isset($properties['author']) ? $properties['author'] : 'Your name here',
    );

    $moduleDir = sfConfig::get('sf_root_dir').'/'.sfConfig::get('sf_apps_dir_name').'/'.$arguments['application'].'/'.sfConfig::get('sf_app_module_dir_name').'/'.$arguments['module'];

    // create basic application structure
    $finder = sfFinder::type('any')->ignore_version_control()->discard('.sf');
    $dirs = sfLoader::getGeneratorSkeletonDirs('sfDoctrineAdmin', $options['theme']);
    foreach ($dirs as $dir)
    {
      if (is_dir($dir))
      {
        $this->filesystem->mirror($dir, $moduleDir, $finder);
        break;
      }
    }

    // create basic test
    $this->filesystem->copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/module/test/actionsTest.php', sfConfig::get('sf_root_dir').'/test/functional/'.$arguments['application'].'/'.$arguments['module'].'ActionsTest.php');

    // customize test file
    $this->filesystem->replaceTokens(sfConfig::get('sf_root_dir').'/test/functional/'.$arguments['application'].DIRECTORY_SEPARATOR.$arguments['module'].'ActionsTest.php', '##', '##', $constants);

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    $this->filesystem->replaceTokens($finder->in($moduleDir), '##', '##', $constants);
  }
}