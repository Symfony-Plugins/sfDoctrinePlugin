<?php
/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr.
 * (c) 2006-2007 Olivier Verdier <olivier.verdier@gmail.com>
 * (c) 2007-2008 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfDoctrineDatabase provides connectivity for the Doctrine.
 *
 * @package    sfDoctrinePlugin
 * @author     Maarten den Braber <mdb@twister.cx>
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @author     Dan Porter
 * @author     Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id: sfDoctrineDatabase.class.php 5288 2007-09-26 14:40:43Z michal $
 */
class sfDoctrineDatabase extends sfDatabase
{
  protected $doctrineConnection = null;

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
    
    $this->loadConfig($name);
    $this->loadSchemas();

    $this->loadConnection();

    $this->loadAttributes();
    $this->loadListeners();  
  }
  
  /**
   * loadSchemas
   *
   * Load schemas file for connection/component binding
   *
   * @return void
   * @author Jonathan H. Wage
   */
  protected function loadSchemas()
  {
    // Load schemas information for connection binding
    if ($schemas = sfConfigCache::getInstance()->checkConfig('config/schemas.yml', true))
    {
      require_once($schemas);
    }
  }
  
  /**
   * loadConnection
   *
   * Create and load the Doctrine connection
   *
   * @return void
   * @author Jonathan H. Wage
   */
  protected function loadConnection()
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
  protected function loadAttributes()
  {
    // Load attributes to Doctrine connection
    $attributes = $this->getParameter('attributes');
    
    foreach($attributes as $k => $v)
    {
      $this->doctrineConnection->setAttribute(constant('Doctrine::'.$k), $v);
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
  protected function loadListeners()
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
  }
  
  /**
   * loadConfig
   *
   * Load the Doctrine configuration for the connection
   * 
   * @param string $name Name of the Doctrine connection
   * @return void
   * @author Jonathan H. Wage
   */
  protected function loadConfig($name)
  {
    // Load the doctrine configuration
    require(sfConfigCache::getInstance()->checkConfig('config/doctrine.yml'));
    
    // Load everything in to parameters
    $this->setParameter('attributes', $default_attributes);
    $this->setParameter('name', $name);
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