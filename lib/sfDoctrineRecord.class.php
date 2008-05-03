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
   * @return string $string
   */
  public function __toString()
  {
    // if the current object doesn't exist we return nothing
    if (!$this->exists())
    {
      return '-';
    }

    $guesses = array('name',
                     'title',
                     'description',
                     'subject',
                     'keywords',
                     'id');

    // we try to guess a column which would give a good description of the object
    foreach ($guesses as $descriptionColumn)
    {
      if ($this->getTable()->hasColumn($descriptionColumn))
      {
        return $this->get($descriptionColumn);
      }
    }

    return sprintf('No description for object of class "%s"', $this->getTable()->getComponentName());
  }

  /**
   * Get the primary key of a Doctrine_Record.
   * This a proxy method to Doctrine_Record::identifier() for Propel BC
   *
   * @return mixed $identifier Array for composite primary keys and string for single primary key
   */
  public function getPrimaryKey()
  {
    return $this->identifier();
  }
}