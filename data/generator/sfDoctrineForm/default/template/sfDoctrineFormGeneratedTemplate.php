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
<?php foreach ($this->getManyToManyRelations() as $relation): ?>
      '<?php echo $this->underscore($relation['refTable']->getOption('name')) ?>_list'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($this->underscore($relation['refTable']->getOption('name')).'_list')) ?> => new sfWidgetFormDoctrineSelectMany(array('model' => '<?php echo $relation['table']->getOption('name') ?>')),
<?php endforeach; ?>
    ));

    $this->setValidators(array(
<?php foreach ($this->table->getColumns() as $name => $column): ?>
      '<?php echo strtolower($name) ?>'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($name)) ?> => new <?php echo $this->getValidatorClassForColumn($column) ?>(<?php echo $this->getValidatorOptionsForColumn($name, $column) ?>),
<?php endforeach; ?>
<?php foreach ($this->getManyToManyRelations() as $relation): ?>
      '<?php echo $this->underscore($relation['refTable']->getOption('name')) ?>_list'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($this->underscore($relation['refTable']->getOption('name')).'_list')) ?> => new sfValidatorDoctrineChoiceMany(array('model' => '<?php echo $relation['table']->getOption('name') ?>', 'required' => false)),
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

<?php if ($this->getManyToManyRelations()): ?>
  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

<?php foreach ($this->getManyToManyRelations() as $relation): ?>
    if (isset($this->widgetSchema['<?php echo $this->underscore($relation['refTable']->getOption('name')) ?>_list']))
    {
      $values = array();
      foreach ($this->object-><?php echo $relation['alias']; ?> as $obj)
      {
        $values[] = $obj-><?php echo $relation->getForeignFieldName(); ?>;
      }

      $this->setDefault('<?php echo $this->underscore($relation['refTable']->getOption('name')) ?>_list', $values);
    }

<?php endforeach; ?>
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

<?php foreach ($this->getManyToManyRelations() as $relation): ?>
    $this->save<?php echo $relation['refTable']->getOption('name') ?>List($con);
<?php endforeach; ?>
  }

<?php foreach ($this->getManyToManyRelations() as $relation): ?>
  public function save<?php echo $relation['refTable']->getOption('name') ?>List($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['<?php echo $this->underscore($relation['refTable']->getOption('name')) ?>_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $q = Doctrine_Query::create()
          ->delete()
          ->from('<?php echo $relation['refTable']->getOption('name') ?> r')
          ->where('r.<?php echo $relation->getLocalFieldName() ?>', $this->object->identifier())
          ->execute();

    $values = $this->getValue('<?php echo $this->underscore($relation['refTable']->getOption('name')) ?>_list');
    if (is_array($values))
    {
      foreach ($values as $value)
      {
        $obj = new <?php echo $relation['refTable']->getOption('name') ?>();
        $obj-><?php echo $relation->getLocalFieldName() ?> = $this->object->getPrimaryKey();
        $obj-><?php echo $relation->getForeignFieldName() ?> = $value;
        $obj->save();
      }
    }
  }

<?php endforeach; ?>
<?php endif; ?>
}