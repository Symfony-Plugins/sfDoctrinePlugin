<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfDoctrineBaseTask.class.php');

/**
 * Create classes for the current model.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     Romain Dorgueil <romain.dorgueil@sensio.com>
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineBuildModelTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('doctrine-build-model');
    $this->namespace = 'doctrine';
    $this->name = 'build-model';
    $this->briefDescription = 'Creates classes for the current model';

    $this->detailedDescription = <<<EOF
The [doctrine:build-model|INFO] task creates model classes from the schema:

  [./symfony doctrine:build-model|INFO]

The task read the schema information in [config/doctrine/*.yml|COMMENT]
from the project and all installed plugins.

You mix and match YML and XML schema files. The task will convert
YML ones to XML before calling the Doctrine task.

The model classes files are created in [lib/model|COMMENT].

This task never overrides custom classes in [lib/model|COMMENT].
It only replaces files in [lib/model/generated|COMMENT].
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    print_r(sfConfig::getAll());
    $directory = sfConfig::get('sf_lib_dir').'/model/doctrine';

    $this->importSchema(sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'doctrine', $directory);

    $plugins = sfFinder::type('dir')->maxdepth(0)->ignore_version_control()->in(sfConfig::get('sf_plugins_dir'));

    foreach ($plugins as $path)
    {
      $this->importPluginSchema($path);
    }
  }

  protected function writeModelTableClass($name, $path, $extends = 'Doctrine_Table')
  {
    $code  = "<?php" . PHP_EOL;
    $code .= "// Automatically generated by sfDoctrinePlugin\n";
    $code .= "class " . $name . " extends " . $extends . "\n{ }";

    $writePath = $path . DIRECTORY_SEPARATOR . $name . ".class.php";

    if (!file_exists($writePath)) {
      $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('generating %s: %s', $name, $writePath)))));

      file_put_contents($writePath, $code);
    }
  }

  protected function importSchema($directory, $outputDirectory)
  {
    $schemas = sfFinder::type('file')->ignore_version_control()->name('*.yml')->in($directory);

    $import = new Doctrine_Import_Schema();
    $array = $import->buildSchema($schemas, 'yml');

    // retrieve Doctrine_Import_Builder instance
    $builder = $this->prepareDoctrineImportBuilderInstance($outputDirectory);
    $builder->generateBaseClasses(true);

    foreach ($array as $name => $properties)
    {
      $options = $import->getOptions($properties, $outputDirectory);
      $columns = $import->getColumns($properties);
      $relations = $import->getRelations($properties);
      $indexes = $import->getIndexes($properties);
      $attributes = $import->getAttributes($properties);
      $templates = $import->getTemplates($properties);
      $actAs = $import->getActAs($properties);

      $options['inheritance']['extends'] = !isset($options['inheritance']['extends']) ? 'sfDoctrineRecord':$options['inheritance']['extends'];
      $options['override_parent'] = true;

      $this->writeModelTableClass($options['className'] . 'Table', $outputDirectory);

      $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('generating %s: %s', 'Base' . $options['className'], $outputDirectory . DIRECTORY_SEPARATOR . 'Base' . $options['className'] . '.class.php')))));

      $builder->buildRecord($options, $columns, $relations, $indexes, $attributes, $templates, $actAs);
    }
  }

  protected function importPluginSchema($path)
  {
    $name = basename($path);

    $pluginSchemas = sfFinder::type('file')->ignore_version_control()->name('*.yml')->in($path . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'doctrine');

    $import = new Doctrine_Import_Schema();
    $array = $import->buildSchema($pluginSchemas, 'yml');

    foreach ($array as $name => $properties)
    {
      $options = $import->getOptions($properties, null);
      $columns = $import->getColumns($properties);
      $relations = $import->getRelations($properties);
      $indexes = $import->getIndexes($properties);
      $attributes = $import->getAttributes($properties);
      $templates = $import->getTemplates($properties);
      $actAs = $import->getActAs($properties);

      $this->writePluginProjectDefinition($path, $options);
      $this->writePluginBaseDefinition($path, $options, $columns, $relations, $indexes, $attributes, $templates, $actAs);
      $this->writePluginDefinition($path, $options);
    }
  }

  protected function writePluginProjectDefinition($path, $options)
  {
    $name = basename($path);

    // retrieve Doctrine_Import_Builder instance
    $modelPath = sfConfig::get('sf_lib_dir').'/model/doctrine/'.$name;
    $builder = $this->prepareDoctrineImportBuilderInstance($modelPath);

    $options['fileName'] = $modelPath . DIRECTORY_SEPARATOR . $options['className'] . '.class.php';
    $options['inheritance']['extends'] = !isset($options['inheritance']['extends']) ? 'Plugin' . $options['className']:$options['inheritance']['extends'];
    $options['no_definition'] = true;

    $this->writeModelTableClass($options['className'] . 'Table', $modelPath, $options['inheritance']['extends'] . 'Table');

    if (!file_exists($options['fileName']))
    {
      $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('generating %s: %s', $options['className'], $options['fileName'])))));

      $builder->writeDefinition($options, array(), array());
    }
  }

  protected function writePluginBaseDefinition($path, $options, $columns, $relations, $indexes, $attributes)
  {
    $name = basename($path);

    // retrieve Doctrine_Import_Builder instance
    $modelPath = sfConfig::get('sf_lib_dir').'/model/doctrine/'.$name.'/generated';
    $builder = $this->prepareDoctrineImportBuilderInstance($modelPath);

    $options['className'] = 'Base'.$options['className'];
    $options['abstract'] = true;
    $options['inheritance']['extends'] = !isset($options['inheritance']['extends']) ? 'sfDoctrineRecord':$options['inheritance']['extends'];
    $options['fileName'] = $modelPath.'/'.$options['className'].'.class.php';
    $options['override_parent'] = true;

    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('generating %s: %s', $options['className'], $options['fileName'])))));

    $builder->writeDefinition($options, $columns, $relations, $indexes, $attributes);
  }

  protected function writePluginDefinition($path, $options)
  {
    // retrieve Doctrine_Import_Builder instance
    $pluginModelPath = sfConfig::get('sf_lib_dir').'/model/doctrine';
    $builder = $this->prepareDoctrineImportBuilderInstance($pluginModelPath);

    $options['inheritance']['extends'] = !isset($options['inheritance']['extends']) ? 'Base' . $options['className']:$options['inheritance']['extends'];
    $options['className'] = 'Plugin' . $options['className'];
    $options['fileName'] = $pluginModelPath . DIRECTORY_SEPARATOR . $options['className'] . '.class.php';
    $options['abstract'] = true;
    $options['no_definition'] = true;

    $this->writeModelTableClass($options['className'] . 'Table', $pluginModelPath);

    // We only want to generate this file if it doesn't exist.
    if (!file_exists($options['fileName']))
    {
      $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', sprintf('generating %s: %s', $options['className'], $options['fileName'])))));

      $builder->writeDefinition($options, array(), array());
    }
  }

  /**
   * Gets or create our Doctrine_Import_Builder instance, and set the path/suffix parameters. If
   * target directory in filesystem does not exists, create it.
   *
   * @param string $path
   * @param string $suffix
   *
   * @return Doctrine_Import_Builder
   */
  protected function prepareDoctrineImportBuilderInstance($path, $suffix='.class.php')
  {
    $instance = null;

    if (null===$instance)
    {
      $instance = new Doctrine_Import_Builder();
    }

    $instance->setTargetPath($path);
    $instance->setSuffix($suffix);

    if (!file_exists($path))
    {
      $this->getFilesystem()->mkdirs($path);
    }

    return $instance;
  }
}
