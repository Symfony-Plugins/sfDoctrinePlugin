<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Olivier Verdier <Olivier.Verdier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a column for the doctrine admin generator
 *
 * @package    sfDoctrinePlugin
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @version    SVN: $Id: sfDoctrineAdminGenerator.class.php 5271 2007-09-25 13:50:13Z hartym $
 */
class sfDoctrineAdminColumn extends sfAdminColumn
{
  // doctrine to creole type conversion
  static $docToCreole = array(
    'boolean'   => 1,
    'string'    => 17,
    'integer'   => 5,
    'date'      => 10,
    'timestamp' => 12,
    'time'      => 11,
    'enum'      => 4,
    'float'     => 8,
    'double'    => 8,
    'clob'      => 16,
    'blob'      => 15,
    'object'    => 7,
    'array'     => 7,
    'decimal'	  => 18,
  );

  protected $relatedClassName = null;
  protected $name = null;
  protected $columnName; // stores the real foreign id column

  function getDoctrineType()
  {
    return isset($this->column['type']) ? $this->column['type'] : null;
  }

  function getCreoleType()
  {
    $dType = $this->getDoctrineType();

    // we simulate the CHAR/VARCHAR types to generate input_tags
    if(($dType == 'string') and ($this->getSize() < 256))
    {
      return 17;
    }

    return $dType ? self::$docToCreole[$dType] : -1;
  }

  function getSize()
  {
    return $this->column['length'];
  }

  function isNotNull()
  {
    //FIXME THIS NEEDS TO BE UPDATE-but I don't know the format for the column array
    if (isset($this->column[2]['notnull']))
      return $this->column[2]['notnull'];
    return false;
  }

  function isPrimaryKey()
  {
    if (isset($this->column['primary']))
      return $this->column['primary'];
    return false;
  }

  function setRelatedClassName($newName)
  {
    $this->relatedClassName = $newName;
  }

  function getRelatedClassName()
  {
    return $this->relatedClassName;
  }

  function setColumnName($newName)
  {
    $this->columnName = $newName;
  }

  function getColumnName()
  {
    return $this->columnName;
  }

  function setColumnInfo($col)
  {
    $this->column = $col;
  }

  // FIXME: this method is never used... remove it?
  function setName($newName)
  {
    $this->name = $newName;
  }

  function getName()
  {
    if (isset($this->name))
    {
      return $this->name;
    }
    // a bit kludgy: the field name is actually in $this->phpName
    return parent::getPhpName();
  }

  function isForeignKey()
  {
    return isset($this->relatedClassName);
  }

  // all the calls that were forwarded to the table object with propel
  // have to be dealt with explicitly here, otherwise:
  public function __call($name, $arguments)
  {
    throw new Exception(sprintf('Unhandled call: "%s"', $name));
  }
}