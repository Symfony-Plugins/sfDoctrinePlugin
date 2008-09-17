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
 * sfWebDebugPanelDoctrine adds a panel to the web debug toolbar with Doctrine information.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfWebDebugPanelDoctrine.class.php 11205 2008-08-27 16:24:17Z fabien $
 */
class sfWebDebugPanelDoctrine extends sfWebDebugPanel
{
  /**
   * Constructor.
   *
   * @param sfWebDebug $webDebug The web debut toolbar instance
   */
  public function __construct(sfWebDebug $webDebug)
  {
    parent::__construct($webDebug);

    $this->webDebug->getEventDispatcher()->connect('debug.web.filter_logs', array($this, 'filterLogs'));
  }

  public function getTitle()
  {
    if ($sqlLogs = $this->getSqlLogs())
    {
      return '<img src="'.$this->webDebug->getOption('image_root_path').'/database.png" /> '.count($sqlLogs);
    }
  }

  public function getPanelTitle()
  {
    return 'SQL queries';
  }

  public function getPanelContent()
  {
    return '
      <div id="sfWebDebugDatabaseLogs">
      <ol><li>'.implode("</li>\n<li>", $this->getSqlLogs()).'</li></ol>
      </div>
    ';
  }

  public function filterLogs(sfEvent $event, $logs)
  {
    $newLogs = array();
    foreach ($logs as $log)
    {
      if ('sfDoctrineLogger' != $log['type'])
      {
        $newLogs[] = $log;
      }
    }

    return $newLogs;
  }

  static public function listenToAddPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('db', new self($event->getSubject()));
  }

  protected function getSqlLogs()
  {
    $logs = array();
    foreach ($this->webDebug->getLogger()->getLogs() as $log)
    {
      if (preg_match('/\b(SELECT|INSERT|UPDATE|DELETE)\b/', $log['message'], $match))
      {
        $logs[] = $log['message'];
      }
    }

    return $logs;
  }
}