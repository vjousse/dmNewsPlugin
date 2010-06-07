<?php

require_once(dirname(__FILE__).'/helper/dmNewsUnitTestHelper.php');

$helper = new dmNewsUnitTestHelper();
$helper->bootFast();

$t = new lime_test();

$t->comment('Testing Publishable behaviour');

