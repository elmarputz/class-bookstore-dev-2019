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

      self::$__connection = new \PDO($type . ':host=' . $host . ';dbname=' . $name . ';charset=utf8', 
        $user, $pass);
    }
    return self::$__connection;

  }


  private static function query($connection, $query, $parameters = array()) {
    $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    try {
      $statement = $connection->prepare($query);
      $i = 1;
      foreach ($parameters as $param) {
        if (is_int($param)) {
          $statement->bindValue($i, $param, \PDO::PARAM_INT);
        }
        if (is_string($param)) {
          $statement->bindValue($i, $param, \PDO::PARAM_STR);
        }
        $i++;
      }
      $result = $statement->execute(); 
    }
    catch (\Exception $e) {
      die ($e->getMessage());
    }

    return $statement;
 
  }

  private static function lastInsertId ($connection) {
    return $connection->lastInsertId();
  }

  private static function fetchObject ($cursor) {
    return $cursor->fetchObject();
  }


  private static function close($cursor) {
    return $cursor->closeCursor();
  }

  private static function closeConnection($connection) {
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

  /**
	 * get the books per search term
	 *
	 * note: search via LIKE
	 *
	 * @param string $term search term: book title string match
	 *
	 * @return array of Book-items
	 */
	public static function getBooksForSearchCriteria($term) {
		$books = array();
		$con   = self::getConnection();
		$term  = $con->real_escape_string($term); /* !!! */
		$res   = self::query($con, "
      SELECT id, categoryId, title, author, price 
      FROM books 
      WHERE title LIKE '%" . $term . "%';
            ");
		while ($book = self::fetchObject($res)) {
			$books[] = new Book($book->id, $book->categoryId, $book->title, $book->author, $book->price);
		}
		self::close($res);
		self::closeConnection($con);

		return $books;
	}

	/**
	 * get the books per search term – paginated set only
	 *
	 * @param string  $term       search term: book title string match
	 * @param integer $offset     start at the nth item
	 * @param integer $numPerPage number of items per page
	 *
	 * @return array of Book-items
	 */
	public static function getBooksForSearchCriteriaWithPaging($term, $offset, $numPerPage) {
		$con = self::getConnection();
		//query total count
		$term       = $con->real_escape_string($term); /* !!! */
		$res        = self::query($con, "
      SELECT COUNT(*) AS cnt 
      FROM books 
      WHERE title LIKE '%" . $term . "%';
        ");
		$totalCount = self::fetchObject($res)->cnt;
		self::close($res);
		//query books to return
		$books      = array();
		$offset     = intval($offset); /* !!! */
		$numPerPage = intval($numPerPage); /* !!! */
		$res        = self::query($con, "
      SELECT id, categoryId, title, author, price 
      FROM books 
      WHERE title LIKE '%" . $term . "%' 
      LIMIT " . $offset . ", " . $numPerPage . ";
        ");
		while ($book = self::fetchObject($res)) {
			$books[] = new Book($book->id, $book->categoryId, $book->title, $book->author, $book->price);
		}
		self::close($res);
		self::closeConnection($con);

		return new PagingResult($books, $offset, $totalCount);
	}

	/**
	 * get the User item by id
	 *
	 * @param integer $userId uid of that user
	 *
	 * @return User | false
	 */
	public static function getUserById(int $userId) {
		$user   = null;
		$con    = self::getConnection();
		$userId = intval($userId); /* !!! */
		$res    = self::query($con, "
      SELECT id, userName, passwordHash 
      FROM users
      WHERE id = " . $userId . ";
        ");
		if ($u = self::fetchObject($res)) {
			$user = new User($u->id, $u->userName, $u->passwordHash);
		}
		self::close($res);
		self::closeConnection($con);

		return $user;
	}

	/**
	 * get the User item by name
	 *
	 * @param string $userName name of that user - must be exact match
	 *
	 * @return User | false
	 */
	public static function getUserByUserName(string $userName) {
		$user     = null;
		$con      = self::getConnection();
		$userName = $con->real_escape_string($userName); /* !!! */
		$res      = self::query($con, "
      SELECT id, userName, passwordHash 
      FROM users 
      WHERE userName = '" . $userName . "';
        ");
		if ($u = self::fetchObject($res)) {
			$user = new User($u->id, $u->userName, $u->passwordHash);
		}
		self::close($res);
		self::closeConnection($con);

		return $user;
	}

  public static function createOrder (int $userId, array $bookIds, 
      string $nameOnCard, string $cardNumber) : int {
        
        $con  = self::getConnection();
        self::query($con, 'BEGIN;');
        
        $nameOnCard = $con->real_escape_string($nameOnCard);
        $cardNumber = $con->real_escape_string($cardNumber);

        self::query($con, "
          INSERT INTO orders (
            userId, 
            creditCardNumber,
            creditCardHolder
          ) VALUES (
            " . $userId .", 
            '" . $cardNumber . "',
            '" . $nameOnCard . "'
          );
        ");

        $orderId = intval(self::lastInsertId($con));
        foreach ($bookIds as $bookId) {
          self::query($con, "
            INSERT INTO orderedbooks (
              orderId, 
              bookId
            ) VALUES (
              " . $orderId . ", 
              " . $bookId .");  
          ");
        }  
        self::query($con, 'COMMIT;');
        self::closeConnection($con);

        return $orderId;

      }


}