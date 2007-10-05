<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Olivier Verdier <Olivier.Verdier@gmail.com>
 * (c) 2007-2008 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Doctrine Data retrieve for retrieving objects
 *
 * @package    sfDoctrinePlugin
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @author     Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineDataRetriever
{
  static public function retrieveObjects($class, $peerMethod = 'findAll')
  {
    if (!$peerMethod)
    {
      $peer_method = 'findAll';
    }
    
    $table = Doctrine_Manager::getInstance()->getTable($class);
    
    return call_user_func(array($table, $peer_method));
  }
}