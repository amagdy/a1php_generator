<? 
/**
PHP 5
@class db
Database abstract class.
This class manages the raw connection and methods that access the DB. 
*/
require_once(dirname(__FILE__) . "/object.php");
class db extends object
{
	// private
	var $CONN = NULL;		/**< Private Member: The connection identifier of the database */
	var $str_dbfile;		/**< Private Member: Database server name or IP address */
	var $str_dbprofile;		/**< Private Member: Database Profile used only in displaying errors to avoid shown information about the database */
	var $int_matched_rows = 1;	/**< the number of matched rows in the last update command set to 1 because it could not be got in sqlite and it must be set so that it does not cause errors. */
	
	private static $arr_db_objects = array();	/**< hash of db objects. */
	
	public static function &get_connection() {
		$db_obj = &$arr_db_objects[0];
		if (!$db_obj) {
			$db_obj = new db();
			$arr_db_objects[0] = $db_obj;
		}
		return $db_obj;
	}
	
	/**
	db Constructor.
	@param arg_str_dbprofile the database profile from which the database host, databasename, username and password are extracted.
	@see __destruct().
	@access Public.
	*/
	private function __construct() 	
	{		
		$this->object();
		
		if (!$_SESSION['project']['db_file']) {
			return;
		}
		$this->str_dbfile = $_SESSION['project']['db_file'];
		$this->str_dbprofile = $_SESSION['project']['db_file'];
		
		if(!class_exists('PDO')) {
			trigger_error("PDO is not supported", 256);
		} else {
    		$this->connect();
    	}
	}	// end __construct()
//---------------------------------------------------
	/**
	Close Database connection
	Called when the developer wants to close the connection or when the page closes.
	@see __construct().
	@access Public.
	*/
	public function __destruct() {
		$this->CONN = null;
	}	// end __destruct()
//---------------------------------------------------
	/**
	connects to the database.
	connects to database and returns true if connected.
	@return A Boolean value.
	@see check_connect().
	@access Private.
	*/	
	
	private function connect() {
	    $this->CONN = new PDO('sqlite:' . $this->str_dbfile); 
		if (!$this->CONN) {
			$tempErrorInfo = $this->CONN->errorInfo();
			trigger_error($tempErrorInfo[2]. "\nCould Not Connect using DB Profile: ". $this->str_dbprofile, 256);
			return false;
	    }
	    return true;
	}	// end function connect()
//---------------------------------------------------
	/**
	checks connection.
	Checks to see if there is a connection if not it reconnects to database with the saved settings. 
	This function is called at the beginning of each function that accesses the database in this class.
	@see connect().
	@access Private.
	*/
	private function check_connect(){
		if(!$this->CONN){
			$this->connect();
		}
	}
//---------------------------------------------------
	/**
	Escapes a string to be put in a query.
	@access Public.
	*/
	public function escape ($arg_string) {
		if (function_exists('sqlite_escape_string')) {
			$res = sqlite_escape_string($string);
		} else {
			$res = str_replace("'", "''", $string);
		}
		return $res;
	}
//---------------------------------------------------
	/**
	Escapes an array of strings to be put in a query.
	@access Public.
	*/
	public function escape_array($arg_arr_string)	{
		if (!is_array($arg_arr_string)) return array();
		if (function_exists('sqlite_escape_string')) {
			while (list($key, $value) = each($arg_arr_string)) {				
				$arg_arr_string[$key] = sqlite_escape_string($value);
			}
		} else {
			while (list($key, $value) = each($arg_arr_string)) {				
				$value = str_replace("'", "''", $value);
				$value = str_replace("\"", "\"\"", $value);
				$arg_arr_string[$key] = $value;
			}
		}
		return $arg_arr_string;
	}
//---------------------------------------------------	
	/**
	Inserts a row in the database.
	Inserts a row in the database and returns the last insert id if exists. 
	Can also be used with replace.
	@param arg_str_query string insert statement.
	@return true/false or last insert id.
	@access Public.
	*/
	public function insert($arg_str_query) {
		$this->check_connect();
		if(!$arg_str_query) {
			trigger_error("No query to run", 256);
			return false;
		}
		$sth = @$this->CONN->prepare($arg_str_query);
		if ($sth === false) {
		   $tempErrorInfo = $this->CONN->errorInfo();
		   trigger_error($tempErrorInfo[2]. "\nError While Running Query : [ " . $arg_str_query . " ] \nOn DB Profile: " . $this->str_dbprofile, 256);
		   return false;			
		}
		if (!$sth->execute()) {
		   $tempErrorInfo = $this->CONN->errorInfo();
		   trigger_error($tempErrorInfo[2]. "\nError While Running Query : [ " . $arg_str_query . " ] \nOn DB Profile: " . $this->str_dbprofile, 256);
		   return false;			
		}
		$int_insert_id = $this->CONN->lastInsertId();
		$sth->closeCursor();
		if ($int_insert_id) {
		   return $int_insert_id;	// return the insert id of this query
		} else {
		   return true;
		}
	}	// end insert()
//---------------------------------------------------
	/**
	Selects a row or more from database in an array of arrays.
	@param arg_str_query string select statement.
	@return A result array
	@access Public.
	*/
	public function select($arg_str_query) {
		$sth = @$this->CONN->prepare($arg_str_query);
		if ($sth === false)
		{
		   $tempErrorInfo = $this->CONN->errorInfo();
		   trigger_error($tempErrorInfo[2]. "\nError While Running Query : [ " . $arg_str_query . " ] \nOn DB Profile: " . $this->str_dbprofile, 256);
		   return array();
		}
		$sth->execute();		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$sth->closeCursor();
		return $result;
	}	// end select()
//---------------------------------------------------
	/**
	Selects one row from database in an array.
	@param $arg_str_query string select statement.
	@return A one row array
	@access Public.
	*/
	public function select_one_row($arg_str_query) {
		$sth = @$this->CONN->prepare($arg_str_query);		
		if ($sth === false) {
		   $tempErrorInfo = $this->CONN->errorInfo();
		   trigger_error($tempErrorInfo[2]. "\nError While Running Query : [ " . $arg_str_query . " ] \nOn DB Profile: " . $this->str_dbprofile, 256);
		   return array();
		}
		$sth->execute();		
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		$sth->closeCursor();
		return $result;
	}	// end select()
//---------------------------------------------------
	/**
	Updates a row or more.
	Updates a row or more in the database and returns true or false.
	@param $arg_str_query string update statement.
	@param $arg_bool_return_affected_rows boolean value with the default value of false if true the function returns the number pf affected rows by this update statement (is not supported on sqlite).
	@return true or false
	@access Public.
	*/
	public function update($arg_str_query, $arg_bool_return_affected_rows=false) {
		$this->check_connect();
		if (!$arg_str_query) {
			trigger_error("No query to run.", 256);
			return false;
		}
		$affected_rows = $this->CONN->exec($arg_str_query);
		if ($affected_rows === false) {
			$tempErrorInfo = $this->CONN->errorInfo();
			trigger_error($tempErrorInfo[2]. "\nError While Running Query : [ " . $arg_str_query . " ] \nOn DB Profile: " . $this->str_dbprofile, 256);
			return false;
		}			
		if ($arg_bool_return_affected_rows) {
			return $affected_rows;		
		} else {
			return true;
		}
	}	// end function update
//---------------------------------------------------
	/**
	Deletes a row or more.
	Deletes a row or more from the database and returns the affected rows.
	@param arg_str_query string delete statement.
	@return Affected rows.
	@access Public.
	*/
	public function delete($arg_str_query) {
		$this->check_connect();
		if(!$arg_str_query) {
			trigger_error("No query to run", 256);
			return false;
		}
		$affected_rows = @$this->CONN->exec($arg_str_query);
		if ($affected_rows === false)			
		{
			$tempErrorInfo = $this->CONN->errorInfo();
			trigger_error($tempErrorInfo[2]. "\nError While Running Query : [ " . $arg_str_query . " ] \nOn DB Profile: " . $this->str_dbprofile, 256);
			return false;
		}
		return 1;
	}	// end function delete
//---------------------------------------------------
	/**
	Selects one value.
	Selects one value from db used in things like getting count or maximum or a username.
	@param arg_str_query string select statement.
	@return scalar value.
	@access Public.
	*/
	public function get_one_value($arg_str_query)	{
		$this->check_connect();
		if(!$arg_str_query){
			trigger_error("No query to run", 256);
			return false;
		}
		$sth = @$this->CONN->prepare($arg_str_query);
		if ($sth === false)
		{
			$tempErrorInfo = $this->CONN->errorInfo();
			trigger_error($tempErrorInfo[2]. "\nError While Running Query : [ " . $arg_str_query . " ] \nOn DB Profile: " . $this->str_dbprofile, 256);
			return false;
		}
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_NUM);
		$sth->closeCursor();
		return $result[0];
	}	// end get_one_value
//---------------------------------------------------
	/**
	Performs a query on the database and return a result resource
	@param $arg_str_query string query.
	@return the result resource
	@access Public.
	*/
	public function raw_query($arg_str_query, $arg_int_result_type=SQLITE_ASSOC) {
		$this->check_connect();
		if(!$arg_str_query) {
			trigger_error("No query to run", 256);
			return false;
		}
		try {
			if (is_object($this->resId)) $this->resId->closeCursor();
			if (substr(trim($sqlString), -1) != ';') $sqlString .= ';';
			if (preg_match('/^\s*(insert|replace|update|delete|create|drop|alter)\s/i', $sqlString)) {
				$resId = $this->CONN->exec($sqlString);
			} else {
				$resId = $this->CONN->query($sqlString);
			}
			return $resId;
	    } catch(PDOException $e) {
			trigger_error($e->getMessage(). "\nError While Running Query : [ " . $arg_str_query . " ] \nOn DB Profile: " . $this->str_dbprofile, 256);
			return false;
	    }
	}	// end function other
}	// class DB
?>
