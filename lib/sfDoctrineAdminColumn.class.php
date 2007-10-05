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
    'boolean'   => CreoleTypes::BOOLEAN,
    'string'    => CreoleTypes::TEXT,
    'integer'   => CreoleTypes::INTEGER,
    'date'      => CreoleTypes::DATE,
    'timestamp' => CreoleTypes::TIMESTAMP,
    'time'      => CreoleTypes::TIME,
    'enum'      => CreoleTypes::TINYINT,
    'float'     => CreoleTypes::FLOAT,
    'double'    => CreoleTypes::FLOAT,
    'clob'      => CreoleTypes::CLOB,
    'blob'      => CreoleTypes::BLOB,
    'object'    => CreoleTypes::VARCHAR,
    'array'     => CreoleTypes::VARCHAR,
    'decimal'	=> CreoleTypes::DECIMAL,
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
      return CreoleTypes::VARCHAR;
    }

    return $dType ? self::$docToCreole[$dType] : CreoleTypes::OTHER;
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