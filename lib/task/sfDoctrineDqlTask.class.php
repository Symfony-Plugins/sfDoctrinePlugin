<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfDoctrineBaseTask.class.php');

/**
 * Creates database for current model.
 *
 * @package    sfDoctrinePlugin
 * @subpackage Task
 * @author     2006-2007 Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineDqlTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('dql_query', sfCommandArgument::REQUIRED, 'The DQL query to execute', null),
    ));
    
    $this->aliases = array('doctrine-dql');
    $this->namespace = 'doctrine';
    $this->name = 'dql';
    $this->briefDescription = 'Execute a DQL query and view the results';

    $this->detailedDescription = <<<EOF
Execute a DQL query and display the formatted results
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->bootstrapSymfony();
    
    $this->callDoctrineCli('dql', array('dql_query' => $arguments['dql_query']));
  }
}
