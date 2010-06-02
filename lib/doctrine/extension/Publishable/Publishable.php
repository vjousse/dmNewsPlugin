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
    $this->hasColumn('started_at', 'timestamp');
    $this->hasColumn('ended_at', 'timestamp');
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
   * isPublishable checks if the item is publishable or not according to a timestamp
   * if no timestamp is passed as an argument, current time() will be used
   * 
   * @param int $time a timestamp or null (current time)
   * @access public
   * @return bool true if it's publishable according to the $time else false
   */
  public function isPublishable($time=null) {

    if(is_null($time))
      $time = time();
      
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
   * not publishable according to a timestamp or if the timestamp is null, according
   * to the current time()
   * 
   * @param int $time 
   * @param mixed $order Order by to add to the qyery object (or not)
   * @access public
   * @return void
   */
  public function getUnPublishableQueryTableProxy($time=null,$order=null) {

    if(is_null($time))
      $time = time();

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

    if(is_null($time))
      $time = time();

    $q = $this->getInvoker()
        ->getTable()
        ->createQuery('p')
        ->select('p.*,t.*')
        ->leftJoin('p.Translation t')
        ->where('started_at IS NULL OR started_at <= ?',array(date('Y-m-d', $time)))
        ->andWhere('ended_at IS NULL OR ended_at >= ?',array(date('Y-m-d', $time)))
        ->orderBy('created_at desc');

    if(!is_null($order))
      $q->orderBy($order);

    return $q;
  }

}
