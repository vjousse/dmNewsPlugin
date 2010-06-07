<?php
/**
 * Behavior for adding publishable features to your models
 * Is an item publishable according to a timestamp, start and end date
 *
 * @package     Doctrine
 * @subpackage  Template
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 * @author      Vincent Jousse <vincent.jousse@devorigin.fr>
 */
class Doctrine_Template_Publishable extends Doctrine_Template {

/**
 * Set table definition for Publishable behavior
 *
 * @return void
 */
  public function setTableDefinition() {
    $this->hasColumn('started_at', 'datetime');
    $this->hasColumn('ended_at', 'datetime');
  }


  /**
   * isAlwaysPublishable checks if an item is always publishable
   * no start date and no end date
   * 
   * @access public
   * @return void
   */
  public function isAlwaysPublishable() {
    return (is_null($this->getInvoker()->ended_at) && is_null($this->getInvoker()->started_at));
  }
  
  /**
   * isPublishable checks if the item is publishable or not according to a date time
   * if no date time is passed as an argument, current time() will be used
   * 
   * @param int $dateTime a date or null (current time)
   * @access public
   * @return bool true if it's publishable according to the $time else false
   */
  public function isPublishable($dateTime=null) {

    $time = $this->getTime($dateTime);

    $object = $this->getInvoker();
    
    $endTime = strtotime($object->ended_at);
    $startTime = strtotime($object->started_at);

    return (
      is_null($object->started_at) && is_null($object->ended_at) ||
      $endTime >= $time && is_null($object->started_at) ||
      $startTime <= $time && is_null($object->ended_at) ||
      $endTime >= $time && $startTime <= $time);
    
  }

/**
 * Table proxy method
 *
 */

  /**
   * getAlwaysPublishableQueryTableProxy returns a query fetching items
   * which are always publishable (start and end date are null) 
   * 
   * @access public
   * @return Doctrine_Query the query object 
   */
  public function getAlwaysPublishableQueryTableProxy() {

    $q = $this->getInvoker()
        ->getTable()
        ->createQuery('p')
        ->select('p.*')
        ->where('started_at IS NULL')
        ->andWhere('ended_at IS NULL');


    return $q;
  }

  /**
   * getUnPublishableQueryTableProxy returns a query fetching items which are
   * not publishable according to a timestamp (or a datetime) or if the timestamp is null, according
   * to the current time()
   * 
   * @param int $time 
   * @param mixed $order Order by to add to the query object (or not)
   * @access public
   * @return void
   */
  public function getUnPublishableQueryTableProxy($time=null,$order=null) {

    $time = $this->getTime($time);

    $q = $this->getInvoker()
        ->getTable()
        ->createQuery('p')
        ->select('p.*')
        ->where('ended_at IS NOT NULL and ended_at < ?',array(date('Y-m-d H:m:i', $time)))
        ->orWhere('started_at IS NOT NULL and started_at > ?',array(date('Y-m-d H:m:i', $time)));

    if(!is_null($order))
      $q->orderBy($order);

    return $q;
  }

  /**
   * getPublishableQueryTableProxy returns a query fetching items wich are publishable
   * according to a timestamp or if the timestamp is null, accorind to the current time() 
   * 
   * @param mixed $time 
   * @param mixed $order 
   * @access public
   * @return void
   */
  public function getPublishableQueryTableProxy($time=null,$order=null) {

    $time = $this->getTime($time);

    $q = $this->getInvoker()
        ->getTable()
        ->createQuery('p')
        ->select('p.*')
        ->where('started_at IS NULL OR started_at <= ?',array(date('Y-m-d H:m:i', $time)))
        ->andWhere('ended_at IS NULL OR ended_at >= ?',array(date('Y-m-d H:m:i', $time)))
        ->orderBy('created_at desc');

    if(!is_null($order))
      $q->orderBy($order);

    return $q;
  }


  /**
   * getTime always returns a timestamp
   * if $date is null, it returns the current TS
   * if $date is a timestamp, it does nothing and returns it
   * if strtotime($date) returns true, it returns the result of strtotime 
   * 
   * @param mixed $date null, a timestamp or a date 'Y-m-d H:i:s' 
   * @access public
   * @return void
   */
  public function getTime($date=null)
  {
    if(is_null($date))
    {
      
      return time();
    }

    //Can't parse as a date, perhaps a timestamp ?
    //return as it is
    if(!($time=strtotime($date)))
    {
      return $date;
    }
    else
    {
      return $time;
    }


  }

}
