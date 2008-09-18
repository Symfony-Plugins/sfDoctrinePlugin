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
   * @var array Names of the initialized connections
   */
  protected static $_initialized = array();

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
    if (isset(self::$_initialized[$parameters['name']]) && self::$_initialized[$parameters['name']])
    {
      return;
    }

    parent::initialize($parameters);

    $dsn = $this->getParameter('dsn');
    $name = $this->getParameter('name');

    // Make sure we pass non-PEAR style DSNs as an array
    if ( !strpos($dsn, '://'))
    {
      $dsn = array($dsn, $this->getParameter('username'), $this->getParameter('password'));
    }

    // Make the Doctrine connection for $dsn and $name
    $this->_doctrineConnection = Doctrine_Manager::connection($dsn, $name);
    $attributes = $this->getParameter('attributes', array());
    foreach ($attributes as $name => $value)
    {
      $this->_doctrineConnection->setAttribute($name, $value);
    }
    $this->loadListeners();
    self::$_initialized[$name] = true;
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
  }

  /**
   * Get the Doctrine connection instance
   *
   * @return Doctrine_Connection $conn
   */
  public function getDoctrineConnection()
  {
    return $this->_doctrineConnection;
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