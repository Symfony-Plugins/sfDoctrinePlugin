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
 * sfDoctrineRoute represents a route that is bound to a Doctrine class.
 *
 * A Doctrine route can represent a single Doctrine object or a list of objects.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineRoute.class.php 11475 2008-09-12 11:07:23Z fabien $
 */
class sfDoctrineRoute extends sfObjectRoute
{
  /**
   * Constructor.
   *
   * @param string $pattern       The pattern to match
   * @param array  $defaults      An array of default parameter values
   * @param array  $requirements  An array of requirements for parameters (regexes)
   * @param array  $options       An array of options
   *
   * @see sfObjectRoute
   */
  public function __construct($pattern, array $defaults = array(), array $requirements = array(), array $options = array())
  {
    parent::__construct($pattern, $defaults, $requirements, $options);

    $this->options['object_model'] = $this->options['model'];
    $this->options['model'] = constant($this->options['model'].'::PEER');
  }

  protected function getObjectForParameters($parameters)
  {
    if (!isset($this->options['method']))
    {
      $this->options['method'] = 'doSelectOne';

      $className = $this->options['model'];
      $criteria = new Criteria();
      $variables = $this->getRealVariables();
      if (!count($variables))
      {
        return false;
      }

      foreach ($variables as $variable)
      {
        $constant = call_user_func(array($className, 'translateFieldName'), $variable, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);
        $criteria->add($constant, $parameters[$variable]);
      }

      $parameters = $criteria;
    }

    return parent::getObjectForParameters($parameters);
  }

  protected function getObjectsForParameters($parameters)
  {
    if (!isset($this->options['method']))
    {
      $this->options['method'] = 'doSelect';
      $parameters = new Criteria();
    }

    return parent::getObjectForParameters($parameters);
  }

  protected function doConvertObjectToArray($object)
  {
    if (isset($this->options['convert_method']))
    {
      return parent::doConvertObjectToArray($object);
    }

    $className = $this->options['model'];

    $parameters = array();
    foreach ($this->getRealVariables() as $variable)
    {
      $method = 'get'.call_user_func(array($className, 'translateFieldName'), $variable, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);

      $parameters[$variable] = $object->$method();
    }

    return $parameters;
  }
}