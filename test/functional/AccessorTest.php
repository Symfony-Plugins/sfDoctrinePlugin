<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
$fixtures = 'fixtures/fixtures.yml';
require_once(dirname(__FILE__).'/../bootstrap/functional.php');
require_once(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(4, new lime_output_color());

$authors = Doctrine::getTable('Author')->findAll();
$t->is(count($authors), 2);

$author = new Author();

// Accessor overriding
$author->setName('Jonathan H. Wage');
$author->save();

$t->is($author->getName(), $author->name);

// Propel style accessors
$t->is($author->getId(), 1);

// Make sure we still have only 2 authors
$authors = Doctrine::getTable('Author')->findAll();
$t->is(count($authors), 2);