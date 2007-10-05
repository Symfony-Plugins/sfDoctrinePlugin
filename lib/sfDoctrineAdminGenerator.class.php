<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Olivier Verdier <Olivier.Verdier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class for handling admin generation for Doctrine
 *
 * @package    sfDoctrinePlugin
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @version    SVN: $Id: sfDoctrineAdminGenerator.class.php 5271 2007-09-25 13:50:13Z hartym $
 */
class sfDoctrineAdminGenerator extends sfAdminGenerator
{
  protected $table;

  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('sfDoctrineAdmin');
  }

  protected function loadMapBuilderClasses()
  {
    $conn = Doctrine_Manager::getInstance()->openConnection('mock://no-one@localhost/empty', null, false);
    $this->table = $conn->getTable($this->getClassName());
  }

  protected function getTable()
  {
    return $this->table;
  }

  protected function loadPrimaryKeys()
  {
    $identifier = $this->getTable()->getIdentifier();
    if (is_array($identifier))
    {
      foreach ($identifier as $_key)
      {
        $this->primaryKey[] = new sfDoctrineAdminColumn($_key);
      }
    }
    else
    {
      $this->primaryKey[] = new sfDoctrineAdminColumn($identifier);
    }
    // FIXME: check that there is at least one primary key [ and if there is not, what to do???? ]
  }

  public function getColumns($paramName, $category='NONE')
  {

    $columns = parent::getColumns($paramName, $category);

    // set the foreign key indicator
    $relations = $this->getTable()->getRelations();

    $cols = $this->getTable()->getColumns();

    foreach ($columns as $index => $column)
    {
      if (isset($relations[$column->getName()]))
      {
        $fkcolumn = $relations[$column->getName()];
        $columnName = $relations[$column->getName()]->getLocal();
        if ($columnName != 'id') // i don't know why this is necessary
        {
          $column->setRelatedClassName($fkcolumn->getTable()->getComponentName());
          $column->setColumnName($columnName);

          if (isset($cols[$columnName])) // if it is not a many2many
            $column->setColumnInfo($cols[$columnName]);

          $columns[$index] = $column;
        }
      }
    }

    return $columns;
  }

  function getAllColumns()
  {
    $cols = $this->getTable()->getColumns();
    $rels = $this->getTable()->getRelations();
    $columns = array();
    foreach ($cols as $name => $col)
    {
      // we set out to replace the foreign key to their corresponding aliases
      $found = null;
      foreach ($rels as $alias=>$rel)
      {
        $relType = $rel->getType();
        if ($rel->getLocal() == $name && $relType != Doctrine_Relation::MANY_AGGREGATE && $relType != Doctrine_Relation::MANY_COMPOSITE)
          $found = $alias;
      }
      if ($found)
      {
        $name = $found;
      }
      $columns[] = new sfDoctrineAdminColumn($name, $col);
    }
    return $columns;
  }

  function getAdminColumnForField($field, $flag = null)
  {
    $cols = $this->getTable()->getColumns(); // put this in an internal variable?
    return  new sfDoctrineAdminColumn($field, (isset($cols[$field]) ? $cols[$field] : null), $flag);
  }

  function getPHPObjectHelper($helperName, $column, $params, $localParams = array())
  {
    $params = $this->getObjectTagParams($params, $localParams);

    // special treatment for object_select_tag:
    if ($helperName == 'select_tag')
    {
      $column = new sfDoctrineAdminColumn($column->getColumnName(), null, null);
    }
    return sprintf ('object_%s($%s, %s, %s)', $helperName, $this->getSingularName(), var_export($this->getColumnGetter($column), true), $params);
  }

  function getColumnGetter($column, $developed = false, $prefix = '')
  {
    if ($developed)
      return sprintf("$%s%s->get('%s')", $prefix, $this->getSingularName(), $column->getName());
    // no parenthesis, we return a method+parameters array
    return array('get', array($column->getName()));
  }

  function getColumnSetter($column, $value, $singleQuotes = false, $prefix = 'this->')
  {
    if ($singleQuotes)
      $value = sprintf("'%s'", $value);
    return sprintf('$%s%s->set(\'%s\', %s)', $prefix, $this->getSingularName(), $column->getName(), $value);
  }

  function getRelatedClassName($column)
  {
    return $column->getRelatedClassName();
  }

  public function getColumnEditTag($column, $params = array())
  {
    if ($column->getDoctrineType() == 'enum')
    {
      // FIXME: this is called already in the sfAdminGenerator class!!!
      $params = array_merge(array('control_name' => $this->getSingularName().'['.$column->getName().']'), $params);

      $values = $this->getTable()->getEnumValues($column->getName());
      $params = array_merge(array('enumValues'=>$values), $params);
      return $this->getPHPObjectHelper('enum_tag', $column, $params);
    }
    return parent::getColumnEditTag($column, $params);
  }
}