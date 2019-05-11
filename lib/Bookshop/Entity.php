<?php

namespace Bookshop;

interface IData {
  public function getId() : int;
}

/**
 * Entity
 * 
 * 
 * @package    
 * @subpackage 
 * @author     John Doe <jd@fbi.gov>
 */
class Entity extends BaseObject implements IData {

  private $id;

  public function __construct(int $id) {
    $this->id = intval($id);
  }

  /**
   * getter for the private parameter $id
   *
   * @return int
   */
  public function getId() : int {
    return $this->id;
  }

}