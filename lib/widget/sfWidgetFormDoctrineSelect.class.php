<?php

/*
 * This file is part of the symfony package.
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormDoctrineSelect represents a select HTML tag for a model.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfWidgetFormDoctrineSelect.class.php 7746 2008-03-05 12:10:27Z fabien $
 */
class sfWidgetFormDoctrineSelect extends sfWidgetFormSelect
{
  /**
   * @see sfWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = new sfCallable(array($this, 'getChoices'));

    parent::__construct($options, $attributes);
  }

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * model:      The model class (required)
   *  * add_empty:  Whether to add a first empty value or not (false by default)
   *                If the option is not a Boolean, the value will be used as the text value
   *  * method:     The method to use to display object values (__toString by default)
   *  * order_by:   An array composed of two fields:
   *                  * The column to order by the results (must be in the PhpName format)
   *                  * asc or desc
   *  * criteria:   A criteria to use when retrieving objects
   *  * connection: The Doctrine connection to use (null by default)
   *  * multiple:   true if the select tag must allow multiple selections
   *
   * @see sfWidgetFormSelect
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('add_empty', false);
    $this->addOption('method', '__toString');
    $this->addOption('order_by', null);
    $this->addOption('criteria', null);
    $this->addOption('connection', null);
    $this->addOption('multiple', false);

    parent::configure($options, $attributes);
  }

  /**
   * Returns the choices associated to the model.
   *
   * @return array An array of choices
   */
  public function getChoices()
  {
    $choices = array();
    if (false !== $this->getOption('add_empty'))
    {
      $choices[''] = true === $this->getOption('add_empty') ? '' : $this->getOption('add_empty');
    }

    $q = Doctrine_Query::create()
          ->from($this->getOption('model') . ' a');

    if ($order = $this->getOption('order_by'))
    {
      $q->orderBy('a.' . $order[0] . ' ' . $order[1]);
    }

    $objects = $q->execute();
    $method = $this->getOption('method');
    foreach ($objects as $object)
    {
      $choices[current($object->getPrimaryKey())] = $object->$method();
    }

    return $choices;
  }

  public function __clone()
  {
    $this->setOption('choices', new sfCallable(array($this, 'getChoices')));
  }
}