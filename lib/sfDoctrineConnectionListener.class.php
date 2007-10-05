<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Olivier Verdier <Olivier.Verdier@gmail.com>
 * (c) 200-2008 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Connection listener for Doctrine
 *
 * @package    sfDoctrinePlugin
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @author     Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineConnectionListener extends Doctrine_EventListener
{
  public function __construct($connection, $encoding)
  {
    $this->connection = $connection;
    $this->encoding = $encoding;
  }
  
  public function postConnect(Doctrine_Event $event)
  {
    $this->connection->setCharset($this->encoding);
    $this->connection->setDateFormat();
  }
}