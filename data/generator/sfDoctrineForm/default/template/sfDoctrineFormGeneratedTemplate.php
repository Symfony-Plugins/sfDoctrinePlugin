[?php

/**
 * <?php echo $this->table->getOption('name') ?> form base class.
 *
 * @package    form
 * @subpackage <?php echo $this->underscore($this->table->getOption('name')) ?>

 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class Base<?php echo $this->table->getOption('name') ?>Form extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
<?php foreach ($this->table->getColumns() as $name => $column): ?>
      '<?php echo strtolower($name) ?>'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($name)) ?> => new <?php echo $this->getWidgetClassForColumn($column) ?>(<?php echo $this->getWidgetOptionsForColumn($column) ?>),
<?php endforeach; ?>
<?php foreach ($this->getManyToManyTables() as $tables): ?>
      '<?php echo $this->underscore($tables['middleTable']->getOption('name')) ?>_list'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($this->underscore($tables['middleTable']->getOption('name')).'_list')) ?> => new sfWidgetFormDoctrineSelectMany(array('model' => '<?php echo $tables['relatedTable']->getOption('name') ?>')),
<?php endforeach; ?>
    ));

    $this->setValidators(array(
<?php foreach ($this->table->getColumns() as $column): ?>
      '<?php echo strtolower($column->getColumnName()) ?>'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($column->getColumnName())) ?> => new <?php echo $this->getValidatorClassForColumn($column) ?>(<?php echo $this->getValidatorOptionsForColumn($column) ?>),
<?php endforeach; ?>
<?php foreach ($this->getManyToManyTables() as $tables): ?>
      '<?php echo $this->underscore($tables['middleTable']->getOption('name')) ?>_list'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($this->underscore($tables['middleTable']->getOption('name')).'_list')) ?> => new sfValidatorDoctrineChoiceMany(array('model' => '<?php echo $tables['relatedTable']->getOption('name') ?>', 'required' => false)),
<?php endforeach; ?>
    ));

    $this->widgetSchema->setNameFormat('<?php echo $this->underscore($this->table->getOption('name')) ?>[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return '<?php echo $this->table->getOption('name') ?>';
  }

<?php if ($this->isI18n()): ?>
  public function getI18nModelName()
  {
    return '<?php echo $this->getI18nModel() ?>';
  }

  public function getI18nFormClass()
  {
    return '<?php echo $this->getI18nModel() ?>Form';
  }
<?php endif; ?>

<?php if ($this->getManyToManyTables()): ?>
  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

<?php foreach ($this->getManyToManyTables() as $tables): ?>
    if (isset($this->widgetSchema['<?php echo $this->underscore($tables['middleTable']->getOption('name')) ?>_list']))
    {
      $values = array();
      foreach ($this->object->get<?php echo $tables['middleTable']->getOption('name') ?>s() as $obj)
      {
        $values[] = $obj->get<?php echo $tables['relatedColumn']->getOption('name') ?>();
      }

      $this->setDefault('<?php echo $this->underscore($tables['middleTable']->getOption('name')) ?>_list', $values);
    }

<?php endforeach; ?>
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

<?php foreach ($this->getManyToManyTables() as $tables): ?>
    $this->save<?php echo $tables['middleTable']->getOption('name') ?>List($con);
<?php endforeach; ?>
  }

<?php foreach ($this->getManyToManyTables() as $tables): ?>
  public function save<?php echo $tables['middleTable']->getOption('name') ?>List($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['<?php echo $this->underscore($tables['middleTable']->getOption('name')) ?>_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $c = new Criteria();
    $c->add(<?php echo $tables['middleTable']->getOption('name') ?>Peer::<?php echo strtoupper($tables['column']->getColumnName()) ?>, $this->object->getPrimaryKey());
    <?php echo $tables['middleTable']->getOption('name') ?>Peer::doDelete($c, $con);

    $values = $this->getValue('<?php echo $this->underscore($tables['middleTable']->getOption('name')) ?>_list');
    if (is_array($values))
    {
      foreach ($values as $value)
      {
        $obj = new <?php echo $tables['middleTable']->getOption('name') ?>();
        $obj->set<?php echo $tables['column']->getOption('name') ?>($this->object->getPrimaryKey());
        $obj->set<?php echo $tables['relatedColumn']->getOption('name') ?>($value);
        $obj->save();
      }
    }
  }

<?php endforeach; ?>
<?php endif; ?>
}
