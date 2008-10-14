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
 * Base sfDoctrineRecord extends the base Doctrine_Record in Doctrine to provide some
 * symfony specific functionality to Doctrine_Records
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id$
 */
abstract class sfDoctrineRecord extends Doctrine_Record
{
  static protected
    $_initialized    = false,
    $_defaultCulture = 'en';

  /**
   * Custom Doctrine_Record constructor.
   * Used to initialize I18n to make sure the culture is set from symfony
   *
   * @return void
   */
  public function construct()
  {
    self::initializeI18n();

    if ($this->getTable()->hasRelation('Translation')) {
      $this->unshiftFilter(new sfDoctrineRecordI18nFilter());
    }
  }

  /**
   * Initialize I18n culture from symfony sfUser instance
   * Add event listener to change default culture whenever the user changes culture
   *
   * @return void
   */
  public static function initializeI18n()
  {
    if (!self::$_initialized)
    {
      if (!self::$_initialized && class_exists('sfProjectConfiguration', false))
      {
        $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
        $dispatcher->connect('user.change_culture', array('sfDoctrineRecord', 'listenToChangeCultureEvent'));
      }

      if (class_exists('sfContext', false) && sfContext::hasInstance() && $user = sfContext::getInstance()->getUser())
      {
        self::$_defaultCulture = $user->getCulture();
      }
      self::$_initialized = true;
    }
  }

  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent An sfEvent instance
   */
  public static function listenToChangeCultureEvent(sfEvent $event)
  {
    self::$_defaultCulture = $event['culture'];
  }

  /**
   * Sets the default culture
   *
   * @param string $culture
   */
  static public function setDefaultCulture($culture)
  {
    self::$_defaultCulture = $culture;
  }

  /**
   * Return the default culture
   *
   * @return string the default culture
   */
  static public function getDefaultCulture()
  {
    self::initializeI18n();

    return self::$_defaultCulture;
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
      try
      {
        return $this->get($descriptionColumn);
      } catch (Exception $e) {}
    }

    return sprintf('No description for object of class "%s"', $this->getTable()->getComponentName());
  }

  /**
   * This magic __call is used to provide propel style accessors to Doctrine models
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
      } else {
        return parent::__call($m, $a);
      }
    } catch(Exception $e) {
      return parent::__call($m, $a);
    }
  }
}