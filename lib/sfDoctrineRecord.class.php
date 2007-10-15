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
 * sfDoctrineRecord
 * 
 * @package    sfDoctrinePlugin
 * @author     Jonathan H. Wage <jwage@mac.com>
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @version    SVN: $Id: sfDoctrineRecord.php 5284 2007-09-26 08:55:32Z hartym $
 */
abstract class sfDoctrineRecord extends Doctrine_Record
{
  /**
   * get
   *
   * @param string $name 
   * @param string $load 
   * @return void
   */
  public function get($name, $load = true)
  {
    $getter = 'get' . Doctrine::classify($name);
    
    if (method_exists($this, $getter))
    {
      return $this->$getter($load);
    }
    
    return parent::get($name, $load);
  }
  
  /**
   * set
   *
   * @param string $name 
   * @param string $value 
   * @param string $load 
   * @return void
   */
  public function set($name, $value, $load = true)
  {
    $setter = 'set' . Doctrine::classify($name);
    
    if (method_exists($this, $setter))
    {
      return $this->$setter($value, $load);
    }
    
    return parent::set($name, $value, $load);
  }
}