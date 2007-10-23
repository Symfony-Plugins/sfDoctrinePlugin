<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    sfDoctrinePlugin
 * @author     Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id: sfDoctrineQueryLogger.class.php 4728 2007-07-27 10:42:49Z mahono $
 */

class sfDoctrineQueryLogger extends Doctrine_EventListener
{
  protected $connection = null;
  protected $encoding = 'UTF8';

  /**
   * preExecute
   *
   * @param string $Doctrine_Event 
   * @return void
   */
  public function preExecute(Doctrine_Event $event)
  {
    $this->sfLogQuery('{sfDoctrine Execute} executeQuery : ', $event);
  }
  
  /**
   * postExecute
   *
   * @param string $Doctrine_Event 
   * @return void
   */
  public function postExecute(Doctrine_Event $event)
  {
    $this->sfAddTime();
  }

  /**
   * postPrepare
   *
   * @param string $Doctrine_Event 
   * @return void
   */
  public function postPrepare(Doctrine_Event $event)
  {
    $this->sfAddTime();
  }

  /**
   * preStmtExecute
   *
   * @param string $Doctrine_Event 
   * @return void
   */
  public function preStmtExecute(Doctrine_Event $event)
  {
    $this->sfLogQuery('{sfDoctrine Statement} executeQuery : ', $event);
  }

  /**
   * postStmtExecute
   *
   * @param string $Doctrine_Event 
   * @return void
   */
  public function postStmtExecute(Doctrine_Event $event)
  {
    $this->sfAddTime();
  }
  
  /**
   * postStmtExecute
   *
   * @param string $Doctrine_Event 
   * @return void
   */
  public function preQuery(Doctrine_Event $event)
  {
    $this->sfLogQuery('{sfDoctrine Query} executeQuery : ', $event);
  }

  /**
   * postQuery
   *
   * @param string $Doctrine_Event 
   * @return void
   */
  public function postQuery(Doctrine_Event $event)
  {
    $this->sfAddTime();
  }
  
  /**
   * sfLogQuery
   *
   * @param string $message 
   * @param string $event 
   * @return void
   */
  protected function sfLogQuery($message, $event)
  {
    $message .= $event->getQuery();

    if ($params = $event->getParams())
    {
      $message .= ' - ('.implode(', ', $params) . ' )';
    }

    sfContext::getInstance()->getLogger()->log($message);
    $sqlTimer = sfTimerManager::getTimer('Database (Doctrine)');
  }

  /**
   * sfAddTime
   *
   * @return void
   */
  protected function sfAddTime()
  {
    sfTimerManager::getTimer('Database (Doctrine)')->addTime();
  }
}