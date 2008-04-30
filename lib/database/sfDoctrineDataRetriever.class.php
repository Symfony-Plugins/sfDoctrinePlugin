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
 * @version    SVN: $Id$
 */
class sfDoctrineDataRetriever
{
  static public function retrieveObjects($class, $peer_method = 'findAll')
  {
    if (!$peer_method)
    {
      $peer_method = 'findAll';
    }

    $table = Doctrine::getTable($class);

    return call_user_func(array($table, $peer_method));
  }
}