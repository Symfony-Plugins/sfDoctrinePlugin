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
 * sfDoctrineCli
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id$
 */
class sfDoctrineCli extends Doctrine_Cli
{
  protected $dispatcher;
  protected $formatter;

  /**
   * setDispatcher
   *
   * @param string $dispatcher
   * @return void
   */
  public function setDispatcher($dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  /**
   * setFormatter
   *
   * @param string $formatter
   * @return void
   */
  public function setFormatter($formatter)
  {
    $this->formatter = $formatter;
  }

  /**
   * notify
   *
   * @param string $notification
   * @param string $style
   * @return void
   */
  public function notify($notification = null, $style = 'HEADER')
  {
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', $notification))));
  }

  /**
   * notifyException
   *
   * @param string $exception
   * @return void
   * @throws sfException
   */
  public function notifyException($exception)
  {
    throw new sfException($exception->getMessage());
  }
}