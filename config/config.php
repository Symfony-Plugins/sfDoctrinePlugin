<?php

sfConfig::set('sf_orm', 'doctrine');
sfConfig::set('sf_admin_module_web_dir', '/sfDoctrinePlugin');

if (sfConfig::get('sf_web_debug'))
{
  require_once dirname(__FILE__).'/../lib/debug/sfWebDebugPanelDoctrine.class.php';

  $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelDoctrine', 'listenToAddPanelEvent'));
}

$manager = Doctrine_Manager::getInstance();
$manager->setAttribute('export', 'all');
$manager->setAttribute('validate', true);
$manager->setAttribute('recursive_merge_fixtures', true);
$manager->setAttribute('auto_accessor_override', true);
$manager->setAttribute('autoload_table_classes', true);

$configuration = sfProjectConfiguration::getActive();

if (method_exists($configuration, 'configureDoctrine'))
{
  $configuration->configureDoctrine($manager);
}