<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectRouteCollection represents a collection of routes bound to Propel objects.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelRouteCollection.class.php 11475 2008-09-12 11:07:23Z fabien $
 */
class sfPropelRouteCollection extends sfObjectRouteCollection
{
  protected
    $routeClass = 'sfPropelRoute';
}