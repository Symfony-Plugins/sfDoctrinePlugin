<?php

sfConfig::get('sf_orm', 'doctrine');

if (sfConfig::get('sf_web_debug'))
{
  require_once dirname(__FILE__).'/../lib/debug/sfWebDebugPanelDoctrine.class.php';

  $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelDoctrine', 'listenToAddPanelEvent'));
}

$manager = Doctrine_Manager::getInstance();
$manager->setAttribute('export', 'all');
$manager->setAttribute('validate', false);
$manager->setAttribute('recursive_merge_fixtures', true);
$manager->setAttribute('auto_accessor_override', true);
$manager->setAttribute('quote_identifier', true);
$manager->setAttribute('autoload_table_classes', true);
