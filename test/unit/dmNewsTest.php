<?php
require_once(getcwd() .'/config/ProjectConfiguration.class.php');

require_once(dm::getDir().'/dmCorePlugin/test/unit/helper/dmUnitTestHelper.php');

$t = new lime_test(0, new lime_output_color());

$t->comment('1) Testing Publishable methods');
