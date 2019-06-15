<?php 
namespace Data;

use Bookshop\Category;
use Bookshop\Book;
use Bookshop\User;


class DataManager implements IDataManager {

   
  private static $__connection;

  private static function getConnection() {
    if (!isset(self::$__connection)) {
      
      $type = 'mysql';
      $host = 'localhost';
      $name = 'fh_scm4_bookshop';
      $user = 'root';
      $pass = '';

      self::$__connection = new \mysqli($host, $user, $pass, $name);
      if (mysqli_connect_errno()) {
        die ('unable to connect to database');
      }
    }
    return self::$__connection;

  }


  private static function query($connection, $query) {
    $res = $connection->query($query);
    if (!$res) {
      die ("Error in query \"" . $query . "\": " . $connection->error); 
    }
    return $res;
  }

  private static function lastInsertId ($connection) {
    return mysqli_insert_id($connection);
  }

  private static function fetchObject ($cursor) {
    return $cursor->fetch_object();
  }


  private static function close($cursor) {
    return $cursor->close();
  }

  private static function closeConnection($connection) {
    $connection->close();
    self::$__connection = null;
  }



  public static function getCategories() : array {
    $categories = array();
    $con = self::getConnection();
    $res = self::query($con, "
      SELECT id, name 
      FROM categories;
    ");

    while ($cat = self::fetchObject($res)) {
      $categories[] = new Category($cat->id, $cat->name);
    }

    self::close($res);
    self::closeConnection($con);

    return $categories;



  }

  public static function getBooksByCategory (int $categoryId) : array {
    $books = array();
    $con = self::getConnection();
    $categoryId = intval($categoryId);
    $res = self::query($con, "
      SELECT id, categoryId, title, author, price 
      FROM books 
      WHERE categoryId = ". $categoryId . ";
    ");
    while ($book = self::fetchObject($res)) {
      $books[] = new Book ($book->id, $book->categoryId, $book->title, $book->author, $book->price);
    }

    self::close($res);
    self::closeConnection($con);
    return $books;

  }

  public static function getUserById (int $userId) {
     
 
  }
  public static function getUserByUserName(string $userName) {
   
  }

  public static function createOrder (int $userId, array $bookIds, 
      string $nameOnCard, string $cardNumber) : int {
     
      }


}