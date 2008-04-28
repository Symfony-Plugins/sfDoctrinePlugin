<?php
/*
 * This file is part of the sfDoctrinePlugin package.
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
 * @version    SVN: $Id$
 */
abstract class sfDoctrineRecord extends Doctrine_Record
{
  /**
   * __toString
   *
   * @return void
   * @author Jonathan H. Wage
   */
  public function __toString()
  {
    // if the current object doesn't exist we return nothing
    if (!$this->exists())
    {
      return '-';
    }

    // we try to guess a column which would give a good description of the object
    foreach (array('name', 'title', 'description', 'id') as $descriptionColumn)
    {
      if ($this->getTable()->hasColumn($descriptionColumn))
      {
        return $this->get($descriptionColumn);
      }
    }

    return sprintf('No description for object of class "%s"', $this->getTable()->getComponentName());
  }

  /**
   * getPrimaryKey
   *
   * @return void
   */
  public function getPrimaryKey()
  {
    return $this->identifier();
  }

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
   * rawGet
   *
   * @param string $name
   * @param string $load
   * @return void
   */
  public function rawGet($name)
  {
    return parent::rawGet($name);
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

  /**
   * rawSet
   *
   * @param string $name
   * @param string $value
   * @return void
   */
  public function rawSet($name, $value)
  {
    parent::set($name, $value);
  }

  /**
   * __call
   *
   * @param string $m
   * @param string $a
   * @return void
   */
  public function __call($m, $a)
  {
    try {
      $verb = substr($m, 0, 3);

      if ($verb == 'set' || $verb == 'get')
      {
        $camelColumn = substr($m, 3);

        // If is a relation
        if (in_array($camelColumn, array_keys($this->getTable()->getRelations())))
        {
          $column = $camelColumn;
        } else {
          $column = sfInflector::underscore($camelColumn);
        }

        if ($verb == 'get')
        {
          return $this->get($column);
        } else {
          return $this->set($column, $a[0]);
        }
      }
    } catch(Exception $e) {
      return parent::__call($m, $a);
    }
  }
}