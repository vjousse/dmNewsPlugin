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

  public function addNews($startDate=null,$endDate=null)
  {
    $news = new DmNews();
    $news->started_at=$startDate;
    $news->ended_at=$endDate;
    $news->save();

    return $news;
  }
}
