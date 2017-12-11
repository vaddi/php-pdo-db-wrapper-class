<?php

require_once( __DIR__ . '/../config.php' );
require_once( __DIR__ . '/iDatabase.php' );
require_once( __DIR__ . '/Database_Error.php' );
require_once( __DIR__ . '/Database_MySQL.php' );
require_once( __DIR__ . '/Database_SQLite.php' );

class Database implements iDatabase {
	
	public $connected = false;
	
	public $_query = null;
	
	private $_db = null;	// Database Object
	private $_stmt;				// Database Statements
	
	// constructor
	public function __construct( $class = null ) {
		if( $class === null ) return;
		$className = __CLASS__ . "_" . $class;
		if( class_exists( $className ) ) {
			// TODO Build connectionstring for each DB type
			// see here for more https://stackoverflow.com/a/18236124/5208166
			if( $class === "MySQL" ) {
				$this->_db = new $className( MYSQL_USER, MYSQL_PASS, MYSQL_NAME, MYSQL_HOST, MYSQL_PORT );
			} else if( $class === 'SQLite' ) {
				$this->_db = new $className( SQLITE_TYPE, SQLITE_FILE );
			}
			
			if( ! is_object( $this->_db ) ) {
				// No Object, use Error Class
				$eclass = __CLASS__ . '_Error';
				$this->_db = new $eclass();
				$this->_db->error = $error = 'Error:  ' . __FILE__ . ' in line ' . __LINE__ . '. Instance of class ' . $className . ' is no Object.' . "\n";
				//error_log( TIMESTAMP . $error, 3, LOG );
			}
		} else {
			// Class not found, use Error Class
			$eclass = __CLASS__ . '_Error';
			$this->_db = new $eclass();
			$this->_db->error = $error = 'Error:  ' . __FILE__ . ' in line ' . __LINE__ . '. Cannot create instance of class "' . $className . "\", class not exsist.\n";
			//error_log( TIMESTAMP . $error, 3, LOG );
		}
		if( $this->_db->error !== null ) {
			echo $this->_db->error;
			return;
		}
		if( $this->connection() ) {
			$this->connected = true;
		}
	}
	
	// close DB connection
	public function close() {
		if( $this->_db ) $this->_db = null;
		$this->connected = false;
	}
	
	// verifying database connection
	public function connection() {
		if( $this->_db === null ) {
			return false;
		} else {
			return $this->_db->connection();
		}
	}
	
	// return the database object
	public function getDb() {
		return $this->_db;
	}
	
	// prepare query
	public function query( $query ) {
		try {
			$this->_stmt = $this->_db->prepare( $query );
			$this->_query = (string) $query;
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
	}
	
	// bind parameter 
	public function bind( $param, $value, $type = null ) {
    try {
    	if( is_null( $type ) ) {
		    switch( true ) {
		      case is_int( $value ):
		        $type = PDO::PARAM_INT;
		        break;
		      case is_bool( $value ):
		        $type = PDO::PARAM_BOOL;
		        break;
		      case is_null( $value ):
		        $type = PDO::PARAM_NULL;
		        break;
		      default:
		        $type = PDO::PARAM_STR;
		    }
		  }
		  $this->_stmt->bindValue( $param, $value, $type );
    } catch( PDOException $e ) {
    	throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
    }
	}
	
	// execute statement
	public function execute() {
		try {
			return $this->_stmt->execute();
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
	}
	
	// get result as array
	public function resultset() {
		$result = null;
		try {
			$result = $this->_stmt->fetchAll( PDO::FETCH_ASSOC );
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}
		
	// get result as object
	public function resultObj() {
    $result = null;
		try {
			$result = $this->_stmt->fetchAll( PDO::FETCH_OBJ );
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}
	
	// get a single result
	public function single() {
		$result = null;
		try {
			$result = $this->_stmt->fetch( PDO::FETCH_ASSOC );
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}
	
	// get the amount of rows
	public function rowCount( $table = null ) {
		$result = null;
		try {
			$query = "SELECT COUNT(*) as count FROM " . $table;
			$this->_db->query( $query );
			$this->_db->execute();
			$result = $this->_db->resultset()[0]['count'];
			//$result = $this->_db->rowCount();
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}
	
	// get the last insert id
	public function lastInsertId( $table = null ) {
		$result = null;
		try {
			if( $this->_stmt !== null ) {
				if( strpos( 'INSERT INTO', $this->_query ) === false ) {
					$result = (int) $this->_db->lastInsertId( $table );
				}
			}
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}
	
	// Transactions
	public function beginTransaction() {
		return $this->_db->beginTransaction();
	}
	
	public function endTransaction() {
		return $this->_db->commit();
	}
	public function cancelTransaction() {
		return $this->_db->rollBack();
	}
	
	// Debuging
	public function debugDumpParams() {
		$result = null;
		try {
			if( $this->_stmt !== null ) {
				$result = $this->_stmt->debugDumpParams();
			}
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}
	
	public function queryString() {
    $result = null;
		try {
			if( $this->_stmt !== null ) {
				$result = $this->_db->queryString();
			}
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}
	
	public function errorInfo() {
    $result = null;
		try {
			if( $this->_stmt !== null ) {
				$result = $this->_stmt->errorInfo();
			}
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}
	
}

?>

