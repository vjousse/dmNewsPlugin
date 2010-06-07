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

  public function clearRecords($table=null)
  {

    if(is_null($table))
    {
      return false;
    }

    if ($this->limeTest)
    {
      $this->limeTest->diag('Clearing ' . $table . ' records');
    }

    return dmDb::table($table)->createQuery()->delete()->execute();
  }

  public function addNews($startDate=null,$endDate=null)
  {
    $news = new DmNews();
    $news->started_at=$startDate;
    $news->ended_at=$endDate;
    $news->save();

    return $news;
  }

  public function addUnPublishableNews()
  {
    $news = new DmNews();
    $news->ended_at='1970-01-01';
    $news->save();

    return $news;
  }
}
