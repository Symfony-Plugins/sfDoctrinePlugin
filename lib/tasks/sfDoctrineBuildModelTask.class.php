<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Create classes for the current model.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineBuildModelTask.class.php 5231 2007-09-22 14:21:31Z fabien $
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

The task read the schema information in [config/*schema.xml|COMMENT] and/or
[config/*schema.yml|COMMENT] from the project and all installed plugins.

You mix and match YML and XML schema files. The task will convert
YML ones to XML before calling the Doctrine task.

The model classes files are created in [lib/model|COMMENT].

This task never overrides custom classes in [lib/model|COMMENT].
It only replaces files in [lib/model/om|COMMENT] and [lib/model/map|COMMENT].
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $directory = sfConfig::get('sf_model_lib_dir').'/doctrine';
    
    $this->importSchema(sfConfig::get('sf_root_dir').'/config/doctrine', $directory);
    
    $plugins = sfFinder::type('dir')->maxdepth(0)->ignore_version_control()->in(sfConfig::get('sf_plugins_dir'));
    
    foreach ($plugins as $path)
    {
      $this->importPluginSchema($path);
    }
  }
  
  protected function importSchema($directory, $outputDirectory)
  {
    $schemas = sfFinder::type('file')->ignore_version_control()->name('*.yml')->in($directory);
    
    $import = new Doctrine_Import_Schema();
    $schema = $import->buildSchema($schemas, 'yml');
     
    $array = $schema['schema'];
    
    $builder = new Doctrine_Import_Builder();
    $builder->setTargetPath($outputDirectory);
    $builder->generateBaseClasses(true);
    
    foreach ($array as $name => $properties) {
        $options = $import->getOptions($properties, $outputDirectory);
        $columns = $import->getColumns($properties);
        $relations = $import->getRelations($properties);            
        
        $options['inheritance']['extends'] = 'sfDoctrineRecord';
        $options['override_parent'] = true;
        
        $builder->buildRecord($options, $columns, $relations);
    }
  }
  
  protected function importPluginSchema($path)
  {
    $name = basename($path);
    
    $pluginSchemas = sfFinder::type('file')->ignore_version_control()->name('*.yml')->in($path . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'doctrine');
    
    $import = new Doctrine_Import_Schema();
    $schema = $import->buildSchema($pluginSchemas, 'yml');
    
    $array = $schema['schema'];
    
    foreach ($array as $name => $properties) {
        $options = $import->getOptions($properties, null);
        $columns = $import->getColumns($properties);
        $relations = $import->getRelations($properties);            
        
        $this->writePluginProjectDefinition($path, $options);
        $this->writePluginBaseDefinition($path, $options, $columns, $relations);
        $this->writePluginDefinition($path, $options);
    }
  }
  
  protected function writePluginProjectDefinition($path, $options)
  {
    $name = basename($path);
    
    $builder = new Doctrine_Import_Builder();
    
    $modelPath = sfConfig::get('sf_model_lib_dir').'/doctrine' . DIRECTORY_SEPARATOR . $name;
    
    if (!file_exists($modelPath)) {
      $this->filesystem->mkdirs($modelPath);
    }
    
    $options['fileName'] = $modelPath . DIRECTORY_SEPARATOR . $options['className'] . '.class.php';
    $options['inheritance']['extends'] = 'Plugin' . $options['className'];
    $options['no_definition'] = true;
    
    $builder->writeDefinition($options, array(), array());
  }
  
  protected function writePluginBaseDefinition($path, $options, $columns, $relations)
  {
    $name = basename($path);
    
    $builder = new Doctrine_Import_Builder();
    
    $modelPath = sfConfig::get('sf_model_lib_dir').'/doctrine' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'generated';
    
    if (!file_exists($modelPath)) {
      $this->filesystem->mkdirs($modelPath);
    }
    
    $options['className'] = 'Base' . $options['className'];
    $options['inheritance']['extends'] = 'sfDoctrineRecord';
    $options['fileName'] = $modelPath . DIRECTORY_SEPARATOR . $options['className'] . '.class.php';
    $options['override_parent'] = true;
    
    $builder->writeDefinition($options, $columns, $relations);
  }
  
  protected function writePluginDefinition($path, $options)
  {
    $builder = new Doctrine_Import_Builder();
    
    $pluginModelPath = $path . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'doctrine';
    
    if (!file_exists($pluginModelPath)) {
      $this->filesystem->mkdirs($pluginModelPath);
    }
    
    $options['inheritance']['extends'] = 'Base' . $options['className'];
    $options['className'] = 'Plugin' . $options['className'];
    $options['fileName'] = $pluginModelPath . DIRECTORY_SEPARATOR . $options['className'] . '.class.php';
    
    // We only want to generate this file if it doesn't exist.
    //if (!file_exists($options['fileName'])) {
      $builder->writeDefinition($options, array(), array());
    //}
  }
}