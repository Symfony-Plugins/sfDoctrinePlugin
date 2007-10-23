<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    sfDoctrinePlugin
 * @author     Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrinePager extends sfPager implements Serializable
{
  protected
    $query;

  public function __construct($class, $defaultMaxPerPage = 10)
  {
    parent::__construct($class, $defaultMaxPerPage);
    
    $this->setQuery(Doctrine_Query::create()->from($class));
  }
  

  public function serialize()
  {
    $vars = get_object_vars($this);
    unset($vars['query']);
    return serialize($vars);
  }

  public function unserialize($serialized)
  {
    $array = unserialize($serialized);

    foreach($array as $name => $values) {
        $this->$name = $values;
    }
  }

  public function init()
  {
    
    $count = $this->getQuery()->offset(0)->limit(0)->count();
    
    $this->setNbResults($count);
    
    $p = $this->getQuery();
    $p->offset(0);
    $p->limit(0);
    if ($this->getPage() == 0 || $this->getMaxPerPage() == 0)
    {
      $this->setLastPage(0);
    }
    else
    {
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
      
      $offset = ($this->getPage() - 1) * $this->getMaxPerPage();
      
      $p->offset($offset);
      $p->limit($this->getMaxPerPage());
    }
  }

  public function getQuery()
  {
    return $this->query;
  }

  public function setQuery($query)
  {
    $this->query = $query;
  }

  protected function retrieveObject($offset)
  {
    $cForRetrieve = clone $this->getQuery();
    $cForRetrieve->offset($offset - 1);
    $cForRetrieve->limit(1);

    $results = $cForRetrieve->execute();

    return $results[0];
  }

  public function getResults($fetchtype = null)
  {
    $p = $this->getQuery();
    
    if ($fetchtype == 'array')
      return $p->execute(array(), Doctrine::FETCH_ARRAY);
    
    return $p->execute();
  }
}