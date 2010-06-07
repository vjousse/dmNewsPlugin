<?php

require_once(getcwd() .'/config/ProjectConfiguration.class.php');

require_once(dm::getDir().'/dmCorePlugin/test/unit/helper/dmUnitTestHelper.php');

class dmNewsUnitTestHelper extends dmUnitTestHelper
{
  protected
  $limeTest;
  
  public function setLimeTest(lime_test $t)
  {
    $this->limeTest = $t;
  }
}
