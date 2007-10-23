<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfDoctrineDatabase
 * 
 * Provides connectivity for the Doctrine.
 *
 * @package    sfDoctrinePlugin
 * @author     Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineDatabase extends sfDatabase
{
  /**
   * doctrineConnection
   *
   * @var string
   */
  protected $doctrineConnection = null;
  
  /**
   * initialize
   *
   * @param string $parameters 
   * @param string $name 
   * @return void
   */
  public function initialize($parameters = array(), $name = null)
  {
    if (!$parameters)
    {
      return;
    }
    
    parent::initialize($parameters);

    // Load default database connection to load if specified
    if ($defaultDatabase = sfConfig::get('sf_default_database'))
    {
      if ($name != $defaultDatabase)
      {
        return;
      }
    }
    
    // Load the doctrine configuration
    require(sfConfigCache::getInstance()->checkConfig('config/doctrine.yml'));
    
    // Load config in to parameter
    $this->setParameter('config', $config);
    
    // Load schemas information for connection binding
    if ($schemas = sfConfigCache::getInstance()->checkConfig('config/schemas.yml', true))
    {
      require_once($schemas);
    }
    
    $this->setParameter('name', $name);

    $this->loadConnections();

    $this->loadAttributes($name);
    $this->loadListeners($name);
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
    // Get Connection method
    $method = $this->getParameter('method', 'dsn');

    // get parameters
    switch ($method)
    {
      case 'dsn':
        $dsn = $this->getParameter('dsn');

        if ($dsn == null)
        {
          // missing required dsn parameter
          $error = 'Database configuration specifies method "dsn", but is missing dsn parameter';

          throw new sfDatabaseException($error);
        }
        
        break;
    }

    // Make sure we pass non-PEAR style DSNs as an array
    if ( !strpos($dsn, '://'))
    {
      $dsn = array($dsn, $this->getParameter('username'), $this->getParameter('password'));
    }
    
    // Make the Doctrine connection for $dsn and $name
    $this->doctrineConnection = Doctrine_Manager::connection($dsn, $this->getParameter('name'));
  }
  
  /**
   * loadAttributes
   *
   * Load all the Doctrine attributes that we loaded from doctrine.yml
   *
   * @return void
   * @author Jonathan H. Wage
   */
  protected function loadAttributes($name)
  {
    $config = $this->getParameter('config');
    
    $attributes = $config['global_attributes'];
    
    $this->setAttributes($attributes);
    
    $connectionAttributesName = $name.'_attributes';
    if (isset($config[$connectionAttributesName]))
    {
      $attributes = $config[$connectionAttributesName];
      
      $this->setAttributes($attributes);
    }
  }
  
  /**
   * setAttributes
   *
   * @param string $attributes 
   * @return void
   */
  protected function setAttributes($attributes)
  {
    foreach($attributes as $k => $v)
    {
      $this->doctrineConnection->setAttribute(constant('Doctrine::ATTR_'.strtoupper($k)), $v);
    }
  }
  
  /**
   * loadListeners
   *
   * Load all the listeners
   *
   * @return void
   * @author Jonathan H. Wage
   */
  protected function loadListeners($name)
  {
    // Get encoding
    $encoding = $this->getParameter('encoding', 'UTF8');
    
    // Add the default sfDoctrineConnectionListener
    $eventListener = new sfDoctrineConnectionListener($this->doctrineConnection, $encoding);
    $this->doctrineConnection->addListener($eventListener);
    
    // Load Query Logger Listener
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $this->doctrineConnection->addListener(new sfDoctrineQueryLogger());
    }
    
    $this->doctrineConnection->addRecordListener(new sfDoctrineRecordListener());
    
    $config = $this->getParameter('config');
    
    $this->setListeners($config['global_listeners']);
    $this->setListeners($config['global_record_listeners'], 'addRecordListener');
  }
  
  /**
   * setListeners
   *
   * @param string $listeners 
   * @return void
   */
  protected function setListeners($listeners, $type = 'addListener')
  {
    foreach ($listeners as $listener)
    {
      $this->doctrineConnection->$type(new $listener());
    }
  }
  
  /**
   * connect
   *
   * Sets the connection to Doctrine PDO Instance
   *
   * @return void
   * @author Jonathan H. Wage
   */
  public function connect()
  {
    $this->connection = $this->doctrineConnection->getDbh();
  }
  
  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
    if ($this->connection !== null) {
      $this->connection = null;
    }
  }
}