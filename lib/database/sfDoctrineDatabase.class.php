<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A symfony database driver for Doctrine.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id$
 */
class sfDoctrineDatabase extends sfDatabase
{
  /**
   * @var object Doctrine_Connection
   */
  protected $_doctrineConnection = null;

  /**
   * initialize
   *
   * @param array $parameters
   * @return void
   */
  public function initialize($parameters = array())
  {
    if (!$parameters)
    {
      return;
    }

    parent::initialize($parameters);

    // Load the doctrine configuration
    require(sfProjectConfiguration::getActive()->getConfigCache()->checkConfig('config/doctrine.yml'));

    // Load config in to parameter
    $this->setParameter('config', $config);

    $this->loadConnections();

    $this->loadAttributes($parameters['name']);
    $this->loadListeners();
  }

  /**
   * loadConnections
   *
   * Create and load the Doctrine connections
   *
   * @return void
   * @author Jonathan H. Wage
   */
  protected function loadConnections()
  {
    $dsn = $this->getParameter('dsn');

    // Make sure we pass non-PEAR style DSNs as an array
    if ( !strpos($dsn, '://'))
    {
      $dsn = array($dsn, $this->getParameter('username'), $this->getParameter('password'));
    }

    // Make the Doctrine connection for $dsn and $name
    $this->_doctrineConnection = Doctrine_Manager::connection($dsn, $this->getParameter('name'));
  }

  /**
   * Loads and sets all the Doctrine attributes that we loaded from doctrine.yml
   *
   * @return void
   */
  protected function loadAttributes($name)
  {
    $config = $this->getParameter('config');

    $attributes = $config['global_attributes'];

    $this->setAttributes($attributes, true);

    $connectionAttributesName = $name.'_attributes';
    if (isset($config[$connectionAttributesName]))
    {
      $attributes = $config[$connectionAttributesName];

      $this->setAttributes($attributes);
    }
  }

  /**
   * Set the passed attributes on the Doctrine_Manager or Doctrine_Connection
   *
   * @param  array   $attributes
   * @param  boolean $global
   * @return void
   */
  protected function setAttributes($attributes, $global = false)
  {
    foreach($attributes as $k => $v)
    {
      if ($global)
      {
        Doctrine_Manager::getInstance()->setAttribute(constant('Doctrine::ATTR_'.strtoupper($k)), $v);
      } else {
        $this->_doctrineConnection->setAttribute(constant('Doctrine::ATTR_'.strtoupper($k)), $v);
      }
    }
  }

  /**
   * Load all the listeners
   *
   * @return void
   */
  protected function loadListeners()
  {
    // Get encoding
    $encoding = $this->getParameter('encoding', 'UTF8');

    // Add the default sf_doctrineConnectionListener
    $eventListener = new sfDoctrineConnectionListener($this->_doctrineConnection, $encoding);
    $this->_doctrineConnection->addListener($eventListener);

    // Load Query Logger Listener
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $this->_doctrineConnection->addListener(new sfDoctrineLogger());
    }

    $config = $this->getParameter('config');

    // Add global listeners
    $this->setListeners($config['global_listeners']);

    // Add record listeners
    $this->setListeners($config['global_record_listeners'], 'addRecordListener');
  }

  /**
   * Set the listeners to the connection
   *
   * @param array $listeners
   * @return void
   */
  protected function setListeners($listeners, $type = 'addListener')
  {
    foreach ($listeners as $listener)
    {
      $this->_doctrineConnection->$type(new $listener());
    }
  }

  /**
   * Initializes the connection and sets it to object
   *
   * @return void
   */
  public function connect()
  {
    $this->connection = $this->_doctrineConnection->getDbh();
  }

  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
    if ($this->connection !== null)
    {
      $this->connection = null;
    }
  }
}