<?php
if (sfConfig::get('sf_web_debug'))
{
  require_once dirname(__FILE__).'/../lib/debug/sfWebDebugPanelDoctrine.class.php';

  $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelDoctrine', 'listenToAddPanelEvent'));
}