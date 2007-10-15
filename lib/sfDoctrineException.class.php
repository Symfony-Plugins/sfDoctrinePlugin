<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Olivier Verdier <Olivier.Verdier@gmail.com>
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    sfDoctrinePlugin
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @version    SVN: $Id: sfDoctrineException.class.php 2675 2006-11-14 07:00:59Z chtito $
 */
class sfDoctrineException extends sfException
{
  /**
   * Class constructor.
   *
   * @param string The error message.
   * @param int    The error code.
   */
  public function __construct ($message = null, $code = 0)
  {
    $this->setName('sfDoctrineException');
    
    parent::__construct($message, $code);
  }
}