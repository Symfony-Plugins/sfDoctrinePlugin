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
 * Autoloading and initialization for doctrine.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineAutoload.php 9122 2008-05-20 23:18:21Z dwhittle $
 */

require_once(dirname(__FILE__) . '/../doctrine/Doctrine.php');

$configuration = sfProjectConfiguration::getActive();

require_once($configuration->getConfigCache()->checkConfig('config/databases.yml'));

if (method_exists(sfProjectConfiguration::getActive(), 'configureDoctrine'))
{
  $configuration->configureDoctrine();
}