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
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
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