<?php

require_once(dirname(__FILE__).'/helper/dmNewsUnitTestHelper.php');


$helper = new dmNewsUnitTestHelper();
$helper->bootFast();

$t = new lime_test();
$helper->setLimeTest($t);

$helper->clearRecords('dmNews');

$t->comment('Testing Publishable behaviour');

$news = $helper->addNews();
$t->ok($news->isAlwaysPublishable(),'By default, a news should always be Publishable');
$t->ok($news->isPublishable(),'By default, a news should be Publishable');


$currentTime=time();

$news->started_at=date('Y-m-d H:i:s',$currentTime+5*60);
$t->ok(!$news->isPublishable($currentTime),'News should not be publishable if it starts 5 minutes after current time');

$news->started_at=date('Y-m-d H:i:s',$currentTime-5*60);

$t->ok($news->isPublishable($currentTime),'News should be publishable if it starts 5 minutes before current time');


//Check unpublishable query
$unPublishableNews = $helper->addUnPublishableNews();

$t->ok(!$unPublishableNews->isPublishable(),'News with ended_at in the past should not be publishable');

$unPublishableQuery = dmDb::table('DmNews')->getUnPublishableQuery();
$t->is(count($unPublishableQuery->fetchArray()),1,'There should be only one unpublishable query at this time');

$unPublishableNews = $helper->addUnPublishableNews();
$t->is(count($unPublishableQuery->fetchArray()),2,'There should be two unpublishable query at this time');


//Check publishable query
$publishableQuery = dmDb::table('DmNews')->getPublishableQuery();
$t->is(count($publishableQuery->fetchArray()),1,'There should be only one publishable query at this time');

$helper->addNews();

$publishableQuery = dmDb::table('DmNews')->getPublishableQuery();
$t->is(count($publishableQuery->fetchArray()),2,'There should be two publishable query at this time');
