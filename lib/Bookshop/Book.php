<?php
namespace Bookshop;
/**
 * Book
 * 
 * 
 * @extends Entity
 * @package    
 * @subpackage 
 * @author     John Doe <jd@fbi.gov>
 */
class Book extends Entity {
/**
 *
 * @var integer 
 */
  private $categoryId;
  /**
   *
   * @var string 
   */
  private $title;
  /**
   *
   * @var string
   */
  private $author;
  /**
   *
   * @var double 
   */
  private $price;

  
  /**
   * 
   * @param integer $id
   * @param integer $categoryId
   * @param string  $title
   * @param string  $author
   * @param float   $price
   *
   * constructor has no return type
   *
   */
  public function __construct(int $id, int $categoryId, string $title, string $author, float $price)  {
    parent::__construct($id);
    $this->categoryId = intval($categoryId); // eigentlich obsolet
    $this->title = $title;
    $this->author = $author;
    $this->price = floatval($price); // eigentlich obsolet
  }

  /**
   * getter for the private parameter $categoryId
   *
   * @return integer
   */
  public function getCategoryId() : int {
    return $this->categoryId;
  }

  /**
   * getter for the private parameter $title
   *
   * @return string
   */
  public function getTitle() : string {
    return $this->title;
  }

  /**
   * getter for the private parameter $author
   *
   * @return string
   */
  public function getAuthor() :  string {
    return $this->author;
  }

  /**
   * getter for the private parameter $price
   *
   * @return float string
   */
  public function getPrice() : float {
    return $this->price;
  }

}