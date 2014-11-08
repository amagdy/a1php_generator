<?
/**
@phpversion 5.1
@file parent_model.php
@class parent_model
This class is the parent class of almost all model classes. It implements many functions that are used in the children classes.
*/
require_once(dirname(__FILE__) . "/" . DB_ENGINE . ".php");	// it requires object class too
class parent_model extends object{
	// Private
	private $__DB;							/**< Private Member: DB Object to connect to database and run queries. */
	private $__properties = array();		/**< Private Member: The internal array of properties, used to save database output for the current object. */
	private $__offset_var_name = "offset";	/**< Public Member: The offset variable name as used in the url of the page. */

	// protected 
	protected $__private_keys = array();	/**< Protected Member: The names of properties that are removed from the properties array when using the method secure_output(). */	
	protected $__primary_key = "id";		/**< Protected Member: The name of the table field that is the primary key of the table. Used in some functions. */
	protected $__table;						/**< Protected Member: The table name of the current class if any. */
	protected $__is_error = false;			/**< Protected Member: a variable set by the validation function to state that there was an error in validation. It is get and cleared by the method $this->is_error().*/
	/** Protected Member: An array of validation settings for the current table fields. 
	It should be filled on the constructor of the child (used) class. And it should contain the following fields.
	"field_name" => array(array(
			"type" => "the type of validation can be any of the following (format, number, int, presence, uniq, email, username, function)"
			"function" => "The name of the custom method used for validation. Used only if type=function"
			"format" => "regular expression (used only if type=format)"
			"error_msg" => "The error message to be shown to the user."
		)),
	"another_field_name" => array(...)
	*/
	protected $__validation = array();		
	
	// public
	public $__page_limit = 0;				/**< Public Member: The number of records shown per page. */	
	public $__offset = 0;					/**< Public Member: Paging offset: is the record where this page starts at. */	
	public $__next_link = array();			/**< Public Member: The array that forms the url of the next page when paging is enabled. */
	public $__previous_link = array();		/**< Public Member: The array that forms the url of the previous page when paging is enabled. */
	public $__paging_pages = array();		/**< Public Member: The array of paging pages filled when set_paging() is called and then db_select() is called. */
//---------------------------------------------------
	/**
	Constructor
	Creates the database object to be used later.
	@param arg_str_dbprofile the database profile from which the database host, databasename, username and password are extracted.
	@see db::__construct().
	@access Public.
	*/
	public function parent_model ($arg_str_dbprofile=DEFAULT_DBPROFILE) {
		$this->object();
		$this->__DB = &db::get_connection($arg_str_dbprofile);
	}	// end constructor
//---------------------------------------------------
	/**
	Creates an update statement from an associative array of fields
	@param $arg_arr_fields the associative array of fields
	@param [$arg_primary_key_field_name] the primary key field if it is not specified the primary key is taken from $this->__primary_key
	@return boolean true if updated successfully.
	*/
	public function auto_update ($arg_arr_fields, $arg_primary_key_field_name="") {
		if (!is_array($arg_arr_fields)) return false;
		$arg_primary_key_field_name = ($arg_primary_key_field_name ? $arg_primary_key_field_name : $this->__primary_key);
		// check for sql injection
		$arg_arr_fields = $this->__DB->escape_array($arg_arr_fields);
		$primary_key_value = $arg_arr_fields[$arg_primary_key_field_name];
		unset($arg_arr_fields[$arg_primary_key_field_name]);
		
		// if there are no fields to update then return false
		if (!$arg_arr_fields) return false;
		
		$str_update_query = "UPDATE `" . $this->__table . "` SET ";
		$i = 0;
		while (list($field, $value) = each($arg_arr_fields)) {
			if ($i > 0) $str_update_query .= ", ";
			$str_update_query .= "`" . $field . "`='" . $value . "'";
			$i++;
		}
		$str_update_query .= " WHERE `" . $arg_primary_key_field_name . "`='" . $primary_key_value . "'";

		return $this->__DB->update($str_update_query);
	}
//---------------------------------------------------
	/**
	Deletes a record from the current table by its primary key field
	@param [$arg_id] the value of the primary key field (if not specified then it should be specified as an attribute to the $this)
	@return returns true on successful delete and false otherwise
	*/
	public function delete ($arg_id=0) {
		if (!$this->__table) {
			trigger_error("No table given", 256);
			return false;
		}
		if (!$this->__primary_key) {
			trigger_error("No Primary Key given", 256);
			return false;
		}
		$id = $this->__primary_key;
		$this->$id = ($arg_id ? $arg_id : $this->$id);
		
		if (!$this->is_id($this->$id)) {
			add_error("could_not_find_entry");
			return false;
		}
			
		return $this->db_delete("DELETE FROM `" . $this->__table . "` WHERE `" . $id . "`=%d", array($this->$id));
	}
//---------------------------------------------------	
	public function __save () {
		if (!$this->__table) {
			trigger_error("Model table name is not specified please set the \$this->__table variable in your model constructor.", 256);
			return false;
		}
		// if the object has the primary key field set then
		if ($this->__properties[$this->__primary_key]) {	// update the record
			return $this->auto_update($this->__properties);
		} else {		// else insert
			unset($this->__properties[$this->__primary_key]);
			return $this->auto_insert($this->__properties, $this->__table);
		}
	}
//---------------------------------------------------	
	/**
	Overiding method
	If a method starting with get_one_by_ or get_all_by_ and that is not found in the current class or any of its parents is called,
	This method finds the field name at the end of the method and finds the required records from the current table
	This Function only works for PHP 5.1 and later
	
	@phpversion 5.1
	@param $arg_function_name the function being called
	@param $arg_arr_params the paramters sent to this function
	@return the return value of the called method or false in case there is an error
	*/
	function __call ($arg_function_name, $arg_arr_params) {
		if (!$this->__table) {
			trigger_error("Model table name is not specified", 256);
			return false;
		}
		$arr_matches = array();
		if (substr($arg_function_name, 0, 11) == "get_one_by_") {
			$field_name = str_replace("get_one_by_", "", $arg_function_name);
			return $this->db_select_one_row("SELECT * FROM `" . $this->__table . "` WHERE `" . $field_name . "`='%s'", $arg_arr_params); 
			
		} elseif (substr($arg_function_name, 0, 11) == "get_all_by_") {
			$field_name = str_replace("get_all_by_", "", $arg_function_name);
			return $this->db_select("SELECT * FROM `" . $this->__table . "` WHERE `" . $field_name . "`='%s'", $arg_arr_params); 
			
		} elseif (preg_match("/^get_([a-z0-9_]+)_by_([a-z0-9_]+)$/i", $arg_function_name, $arr_matches)) {
			return $this->db_get_one_value("SELECT `" . $arr_matches[1] . "` FROM `" . $this->__table . "` WHERE `" . $arr_matches[2] . "`='%s'", $arg_arr_params); 
			
		} elseif (preg_match("/^set_all_([a-z0-9_]+)$/i", $arg_function_name, $arr_matches)) {
			return $this->db_update("UPDATE `" . $this->__table . "` SET `" . $arr_matches[1] . "`='%s'", $arg_arr_params);
			
		} elseif (preg_match("/^set_([a-z0-9_]+)$/i", $arg_function_name, $arr_matches)) {
			if ($this->__properties[$this->__primary_key]) {
				return $this->db_update("UPDATE `" . $this->__table . "` SET `" . $arr_matches[1] . "`='%s' WHERE `" . $this->__primary_key . "`='%s'", array($arg_arr_params[0], $this->__properties[$this->__primary_key]));
			} else {
				trigger_error("You cannot call " . $arg_function_name . "() when you have no primary key value in this object.", 256);
				return false;
			}
		} else {
			trigger_error("Call to an Undefined function : " . __CLASS__ . "->" . $arg_function_name, 256);
			return false;
		}
	}
//---------------------------------------------------	
	/**
	Returns whether there was an error in the fields that has been validated recently 
	and resets the error flag to false so that we can restart validation on other fields.
	@return boolean true if there was an error and false otherwise.
	*/
	public function is_error () {
		$bool_error = $this->__is_error;
		$this->__is_error = false;
		return $bool_error;
	}
//---------------------------------------------------
	/**
	Checks if a field value is valid or not
	It validates the value against the field validator arrays.
	@param $arg_field_name the name of the field to be validated 
	@param $arg_value the value to be validated
	@return returns true if valid and false otherwise
	*/
	public function is_field_valid ($arg_field_name, $arg_value) {
		$arr_field_validations = $this->__validation[$arg_field_name];
		if (is_array($arr_field_validations)) {
			while (list(, $arr_validation) = each($arr_field_validations)) {
				if (!$this->run_validation ($arg_field_name, $arg_value, $arr_validation)) {
					$this->__is_error = true;
					add_error($arr_validation['error_msg'], array($arg_value), $arg_field_name);
					return false;
				}
			}	// end while listing arg_arr_fields
		}
		return true;
	}
//---------------------------------------------------
	/**
	Magic function to set object properties
	@phpversion 5.1
	@param $arg_property_name the property name of the object
	@param $arg_value the value to be put for the property
	*/
	function __set($arg_property_name, $arg_value) {
		if ($this->is_field_valid($arg_property_name, $arg_value)) {
        	$this->__properties[$arg_property_name] = $arg_value;
        }
    }
//---------------------------------------------------
	/**
	Magic function to get an object property value
	@phpversion 5.1
	@param $arg_property_name the property name of the object
	*/
    function __get($arg_property_name) {
		return $this->__properties[$arg_property_name];
    }
//---------------------------------------------------
	/**
	Magic function to check if an object property is set
	@phpversion 5.1
	@param $arg_property_name the property name of the object
	*/
    function __isset($arg_property_name) {
        return isset($this->__properties[$arg_property_name]);
    }
//---------------------------------------------------
	/**
	Magic function to unset object properties
	@phpversion 5.1
	@param $arg_property_name the property name of the object
	*/
    function __unset($arg_property_name) {
        unset($this->__properties[$arg_property_name]);
    }
//---------------------------------------------------
	/**
	Changes an array to This Object.
	Copies an array to __properties of the current object.
	@param arg_arr_vars associative array of variables to be copied to __properties.
	@param $arg_bool_skip_validation to skip validation
	@see __set().
	@see array_to_object().
	@see this_to_array().
	@see objects_to_array().
	@see array_to_objects().
	@access Public.
	*/	
	function array_to_this ($arg_arr_vars, $arg_bool_skip_validation=false) {
		if (is_array($arg_arr_vars)) {
			while (list($k, $v) = each($arg_arr_vars)) {
				if ($arg_bool_skip_validation) {
					// skip the validation phase for optimization
					$this->__properties[$k] = $v;
				} else {
					$this->$k = $v;
				}
			}
		}
	}	// end function array_to_this
//---------------------------------------------------
	/**
	Changes Array to an Object.
	Creates an object of the type of the current class and copies an array to __properties of the this object.
	@param arg_arr_vars associative array of variables to be copied to __properties.
	@return An object of the current class type.
	@see array_to_this().
	@see this_to_array().
	@see objects_to_array().
	@see array_to_objects().
	@access Public.
	*/	
	function array_to_object ($arg_arr_vars) {
		if(is_array($arg_arr_vars))
		{	
			$obj = NULL;
			eval("\$obj = new " . get_class($this) . "();");
			$obj->array_to_this($arg_arr_vars);
			return $obj;
		}else{	// else if there is no array
			return false;
		}	// end if array
	}	// end function array_to_object
//---------------------------------------------------
	/**
	Changes This object to an Array.
	returns __properties and can also return all public properties in the same array.
	@param arg_bool_all boolean value to indicate whether to return all public properties in the array or not.
	@return Array of properties.
	@see array_to_this().
	@see array_to_object().
	@see objects_to_array().
	@see array_to_objects().
	@access Public.
	*/	
	function this_to_array  ($arg_bool_all=true) {
		$arr_vars = $this->__properties;
		if ($arg_bool_all) {
			$arr = get_object_vars($this);
			if (is_array($arr)) {
				while(list($k, $v) = each($arr)){
					$arr_vars[$k] = $v;
				}
			}
		}
		return $arr_vars;
	} 	// end function this_to_array
//---------------------------------------------------
	/**
	Changes An array of objects to a multidimensional array.
	returns the properties of an object in an array for each object in the given array of objects.
	@param arr_objects Array of objects.
	@param arg_bool_all boolean value to indicate whether to return all public properties in the array or not.
	@return Multidimentional array of properties of objects.
	@see array_to_this().
	@see array_to_object().
	@see this_to_array().
	@see array_to_objects().
	@access Public.
	*/
	function objects_to_array ($arr_objects, $arg_bool_all=false) {
		if(is_array($arr_objects)){
			while(list($key, $obj) = each($arr_objects)){
				$arr_arrays[$key] = $obj->this_to_array($arg_bool_all);
			}	// end while 
		}	// end if array of objects
		return $arr_arrays;		
	}	// end function objects_to_array
//---------------------------------------------------
	/**
	Changes a multidimensional array to an array of objects.
	Copies each row in a multidimensional array to __properties of an object of the same type of the current class.
	@param arg_arr_vars multidimensional array of associative arrays of variables to be copied to __properties.
	@return An array of objects.
	@see array_to_this().
	@see array_to_object().
	@see this_to_array().
	@see objects_to_array().
	@access Public.
	*/
	function array_to_objects ($arg_arr_vars) {
		if(!is_array($arg_arr_vars)) return false;
		while(list(,$arr_row) = each($arg_arr_vars)){	// while array of vars
			$obj = $this->array_to_object($arr_row);
			$arr_objects[] = $obj;
		}	// end while array
		return (is_array($arr_objects) ? $arr_objects : false);
	}	// getAllElements
//----------------------------------------------------------
	/**
	Enables or Disables paging.
	If the arg_int_page_limit is 0 then paging is disabled and if it is bigger than 0 paging is enabled and the page size is set to the value of arg_int_page_limit.
	The offset variable name may be changed to facilitate paging in a page that has more than one paging context.
	@param arg_int_page_limit the number of rows to show per page.
	@param arg___offset_var_name the name of the REQUEST variable that holds the holds the current paging offset.
	@see db_select().
	@access Public.
	*/
	function set_paging ($arg_int_page_limit=0, $arg_offset_var_name="offset") {
		if ($arg_int_page_limit < 0) $arg_int_page_limit = 0;
		$this->__page_limit = $arg_int_page_limit;
		$this->__offset_var_name = $arg_offset_var_name;
	}
//---------------------------------------------------
	/**
	Builds a query.
	Builds a query from a query string with place holders and an array of variables. It cleans the array of variables to secure it against SQL injection then puts the variables of the array in the place holders.
	@param arg_str_query The string of the query with its place holders.
	@param arg_arr_variables the array of variables that are cleaned then put in the place holders of the query.
	@see db_select().
	@see db_update().
	@see db_delete().
	@access Public.
	*/
	function query_builder ($arg_str_query, $arg_arr_variables) {
		if(!$arg_str_query){return "";}
		if(!$arg_arr_variables){return $arg_str_query;}
		$this->array_to_db($arg_arr_variables);	// cleanup the array
		$arg_str_query = vsprintf($arg_str_query, $arg_arr_variables);	// build the query
		return $arg_str_query;
	}	// end function query_builder
//---------------------------------------------------
	/**
	Automatic insert or replace.
	Inserts or replaces a row by taking only the table name and the associative array of field names and field values.
	It gets the types of table fields first then decides whether to put single quotes around the values or not.
	@param arg_arr_variables 
	@param arg_str_table_name
	@param arg_str_command
	@return Last insert id or Boolean value.
	
	@see db::insert().
	@see auto_replace().
	@see auto_insert().
	@access Private.
	*/
	function auto_insert_replace ($arg_arr_variables, $arg_str_table_name="", $arg_str_command="INSERT"){	// returns insert id
		$arg_str_table_name = ($arg_str_table_name ? $arg_str_table_name : $this->__table);
		if(!$arg_str_table_name){		// if there is not table name passed to the function
			trigger_error("No Table to Use in Insert", 256);
			return false;
		}
		
		$this->array_to_db($arg_arr_variables);

		// create the INSERT statement	
		if(!is_array($arg_arr_variables)) {
			trigger_error("Nothing to insert in table <" . $arg_str_table_name . "> ", 256);
			return false;
		}
		$str_fields = "";	// the filed names
		$str_values = "";	// the field values
		$str_fields = "`" . join("`, `", array_keys($arg_arr_variables)) . "`";
		$str_values = "'" . join("', '", array_values($arg_arr_variables)) . "'";
		$str_sql = $arg_str_command . " INTO " . $arg_str_table_name . " (" . $str_fields . ") VALUES (" . $str_values . ")";
		return $this->__DB->insert($str_sql);
	}	// end function insert
//------------------------------------------------------------
	/**
	Automatic replace in DB.
	Calls auto_insert_replace() with the last parameter as "REPLACE".
	@see auto_insert_replace().
	@access Public.
	*/
	function auto_replace ($arg_arr_variables, $arg_str_table_name="") {
		return $this->auto_insert_replace($arg_arr_variables, $arg_str_table_name, "REPLACE");
	}	// end function insert
//------------------------------------------------------------
	/**
	Automatic insert in DB.
	Calls auto_insert_replace() with the last parameter as "INSERT".
	@see auto_insert_replace().
	@access Public.
	*/
	function auto_insert ($arg_arr_variables, $arg_str_table_name="") {
		return $this->auto_insert_replace($arg_arr_variables, $arg_str_table_name, "INSERT");
	}	// end function insert
//------------------------------------------------------------
	/**
	Insert or replace in db using a normal query.
	Takes a query with place holders and an array of variables to be put in the place holders. 
	Used for optimized environments because auto insert runs an extra query.
	@param arg_str_query string select statement with %%s place holders.
	@param arg_arr_variables array of variables to be put in the %%s place holders of the arg_str_query.
	@return Last insert id or Boolean value.
	@see query_builder().
	@see db::insert().
	@see auto_insert_replace().
	@access Public.
	*/
	function db_insert ($arg_str_query="", $arg_arr_variables=array()) {
		$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		return $this->__DB->insert($arg_str_query);
	}
//------------------------------------------------------------
	/**
	Selects rows from db.
	Builds the select query and selects a number of rows from db and frees the result. 
	It also supports paging by reading the instance variables int_page_limit, int_offset, 
	__offset_var_name and filling the instance variables arr_next_link and arr_previous_link. 
	If int_page_limit instance variable is more than 0 paging is enabled. It also cleans up the 
	result by removing escape slashes from it.
	
	@param $arg_str_query String select statement with %%s place holders.
	@param $arg_arr_variables Array of variables to be put in the %%s place holders of the arg_str_query.
	@param $arg_old_mysql_count_word String optional defaults to COUNT(*) and is only used when MYSQL4_OR_LATER equals false.
	@param $arg_old_mysql_count_sql String optional the sql statement of the count query. To enable it you have to put the value of $arg_old_mysql_count_word as "" (only used in suphisticate queries that cannot be just treated with COUNT(*) or any other key words in its place or that needs editing to other places in the source query).
	$arg_old_mysql_count_sql_params Array optional only specifies when $arg_old_mysql_count_word = "" and $arg_old_mysql_count_sql has a query in it.
	@return Multidimensional array of database rows (we can call it result array).
	
	@see db::select().
	@see query_builder().
	@access Public.
	*/
	function db_select ($arg_str_query, $arg_arr_variables=array(), $arg_old_mysql_count_word="COUNT(*)", $arg_old_mysql_count_sql="", $arg_old_mysql_count_sql_params=array()) {
		global $__in;
	        $arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
	        if ($this->__page_limit > 0) {        // if paging is enabled
			// paging part #################
			$this->__offset = $__in[$this->__offset_var_name];
			if(!$this->__offset) $this->__offset = 0;
			if($this->__offset < 0) $this->__offset = 0;
			

			if (MYSQL4_OR_LATER) {
				/**< mysql 4.1 and later. */
				$arg_str_query = preg_replace("/^select[\s]+/i", "SELECT SQL_CALC_FOUND_ROWS ", $arg_str_query);     
				$arg_str_query .= " LIMIT " . $this->__offset . ", " . $this->__page_limit;
				$arr_result = $this->__DB->select($arg_str_query);
				$int_count = $this->db_get_one_value("SELECT FOUND_ROWS()");
			} else {
				/**< mysql 3.2 */
				if ($arg_old_mysql_count_word) {
					$arg_str_count_query = preg_replace("/^select[\s]+.* FROM /i", "SELECT " . $arg_old_mysql_count_word . " FROM ", $arg_str_query);     
				} else if ($arg_old_mysql_count_sql){
					$arg_str_count_query = $this->query_builder($arg_old_mysql_count_sql, $arg_old_mysql_count_sql_params);
				} else {
					trigger_error("You did not specify a Count query for the sql statement when you specified that MySQL Version is less than 4.1", 256);
				}
				$arg_str_query .= " LIMIT " . $this->__offset . ", " . $this->__page_limit;
				$arr_result = $this->__DB->select($arg_str_query);
				$int_count = $this->db_get_one_value($arg_str_count_query);
			}			
			// make the $arr_pages
			$pages_count = ceil($int_count / $this->__page_limit);
			if ($this->__offset < 1) {
				$current_page = 1;
			} else {
				$current_page = intval($this->__offset / $this->__page_limit) + 1;
			}
			for ($i=1; $i <= $pages_count; $i++) {
				if ($i == $current_page) {
					$this->__paging_pages[$i] = array();
				} else {
					$this->__paging_pages[$i] = array($this->__offset_var_name => (($i * $this->__page_limit)-$this->__page_limit));
				}
			}
			
			// next and previous
			if($int_count > ($this->__offset + $this->__page_limit)){        // if there are still records in the database then there must be a next link
				$int_next_offset = $this->__offset + $this->__page_limit;
				$this->__next_link = array($this->__offset_var_name => $int_next_offset);
			}else{
				$this->__next_link = array();
			}    // end if next link

			if($this->__offset > 0){    // ifthe offset is bigger than 0 this means that there is previous link
				$int_previous_offset = $this->__offset - $this->__page_limit;
				$this->__previous_link = array($this->__offset_var_name => $int_previous_offset);
			}else{
				$this->__previous_link = array();
			}    // end if previous link
			// end paging part #################
		} else {	// if paging is not enabled
			$arr_result = $this->__DB->select($arg_str_query);
		}    // end if paging enabled
		return $arr_result;
    }    // end function db_select
//---------------------------------------------------
		/**
	Selects one row from db.
	Does the same like db_select but reutrns the first row only of the recordset.
	
	@param arg_str_query string select statement with %%s place holders.
	@param arg_arr_variables array of variables to be put in the %%s place holders of the arg_str_query.
	@return An array of one rows (the first row of the result array).
	
	@see db_select().
	
	@access Public.
	*/
	function db_select_one_row ($arg_str_query, $arg_arr_variables=array()) {
		$arr_wanted_row = array();
		$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		return $this->__DB->select_one_row($arg_str_query);
	}
//---------------------------------------------------
	/**
	Updates a row or more in db.
	Builds the update query and updates the database and can return the affected rows and the matched rows of a sql statement. This function by default returns true or false.
	@param arg_str_query string update statement with %%s place holders.
	@param arg_arr_variables array of variables to be put in the %%s place holders of the arg_str_query.
	@param arg_bool_return_affected_rows Boolean true/false whether to return the number of affected rows or not.
	@param arg_return_matched_rows Boolean true/false whether to return the number of matched rows or not.
	@return Mixed (Array(int_affected_rows, int_matched_rows) or int_affected_rows only, or int_matched_rows only, or true).
	@see db::update().
	@see query_builder().
	@access Public.
	*/
	function db_update ($arg_str_query="", $arg_arr_variables=array(), $arg_bool_return_affected_rows=false, $arg_return_matched_rows=false){	// returns true or false
		$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		$int_rows_affected = $this->__DB->update($arg_str_query, $arg_bool_return_affected_rows);
		if ($arg_bool_return_affected_rows) {
			$arr_return[] = $int_rows_affected;
		}
		$int_matched_rows = $this->__DB->int_matched_rows;
		if ($arg_return_matched_rows == true) {
			$arr_return[] = $int_matched_rows;
		}
		if (count($arr_return) == 2) {
			return $arr_return;
		} elseif (count($arr_return) == 1) {
			return $arr_return[0];
		} else {
			if ($int_matched_rows) {
				return true;
			} else {
				return false;
			}
		}
	}	// end function dbUpdate
//---------------------------------------------------
	/**
	Deletes a row or more from db.
	Builds the delete query and deletes rows from the database and can return the affected rows. This function by default returns true or false.
	@param arg_str_query string delete statement with %%s place holders.
	@param arg_arr_variables array of variables to be put in the %%s place holders of the arg_str_query.
	@param arg_bool_return_affected_rows Boolean true/false whether to return the number of affected rows or not.
	@return Mixed int_affected_rows or boolean.
	@see db::delete().
	@see query_builder().
	@access Public.
	*/
	function db_delete ($arg_str_query="", $arg_arr_variables=array(), $arg_bool_return_affected_rows=false){	// return true or false
		$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		return $this->__DB->delete($arg_str_query, $arg_bool_return_affected_rows);
	}	// end function delete
//---------------------------------------------------
	/**
	Returns one value only.
	Builds a select statement and selects the first field in the first row of the output.
	@param arg_str_query string select statement with %%s place holders.
	@param arg_arr_variables array of variables to be put in the %%s place holders of the arg_str_query.
	@return Scalar value String or integer or float.
	@see db::get_one_value().
	@see query_builder().
	@access Public.
	*/
	function db_get_one_value ($arg_str_query="", $arg_arr_variables=array()){	// return one value
		if($arg_arr_variables)$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		return $this->__DB->get_one_value($arg_str_query);
	}	// end function db_get_one_value
//---------------------------------------------------
	/**
	Array to DB.
	Cleans array values from sql injection.
	@param arg_arr_vars an array of variables to be cleaned. It is passed by reference.
	@return The cleaned array.
	@see query_builder().
	@see db::escape_array().
	@access Public.
	*/
	function array_to_db (&$arg_arr_vars){	// cleans the array to be put in the db
		$arg_arr_vars = $this->__DB->escape_array($arg_arr_vars);
		return $arg_arr_vars;
	}	// end function array_to_db
//------------------------------------------------------------------------
	/**
	Sends mail using mail().
	Uses mail() to send an email to arg_str_to_email email address from arg_str_from_email using SMTP with no authentication. This function makes sure that the sender and receiver emails are in a right format first. It can also send mails in html or text format.
	@param arg_str_to_email The receiver email address.
	@param arg_str_from_email the sender email address.
	@param arg_str_from_name the sender full or nick name.
	@param arg_str_subject the message subject.
	@param arg_str_message the message body.
	@param arg_bool_html boolean whether this message should be sent in html format or not.
	@param arg_str_other_headers Any extra headers that the developer may need to add.
	@return Boolean wheteher the mail has been sent or not. 
	@see is_email().
	@access Public.
	*/
	function send_mail ($arg_str_to_email, $arg_str_from_email, $arg_str_from_name="", $arg_str_subject="", $arg_str_message="", $arg_bool_html=false, $arg_str_other_headers=""){	// a function that sends mail with headers
		$return = true;
		if(!$this->is_email($arg_str_to_email)){add_error("invalid_receiver_email"); $return = false;}		// If the receiver email address is wrong return false
		if(!$this->is_email($arg_str_from_email)){add_error("invalid_sender_email"); $return = false;}		// if the sender email address is wrong return false
		if(!$return) return false;
		//	$arg_str_message = text_to_html($arg_str_message);
		// make up the header
		$header = "MIME-Version: 1.0\r\n";
		if($arg_bool_html){	// if the mail is sent as html
			$header .= "Content-type: text/html; charset=windows-1256\r\n";
		}else{		// else if the email is sent as clear text
			$header .= "Content-type: text/plain; charset=windows-1256\r\n";
		}	// end if HTML
		$header .= "Organization: " . SITE_NAME . "\r\n";
		//$header .= "Content-Transfer-encoding: 8bit\r\n";
		$header .= "To: " . $arg_str_to_email . "\r\n";
		$header .= "From: " . $arg_str_from_name . " <" . $arg_str_from_email . ">\r\n";
		$header .= "Reply-To: " . $arg_str_from_name . " <" . $arg_str_from_email . ">\r\n";
		$header .= "Message-ID: <" . md5(uniqid(time())) . "@{$_SERVER['SERVER_NAME']}>\r\n";
		$header .= "Return-Path: " . $arg_str_from_email . "\r\n"; 
		$header .= "X-Priority: 1\r\n"; 
		$header .= "X-MSmail-Priority: High\r\n";
		$header .= "X-Mailer: Microsoft Office Outlook, Build 11.0.5510\r\n"; //hotmail and others dont like PHP mailer.
		$header .= "X-MimeOLE: Produced By Microsoft MimeOLE V6.00.2800.1441\r\n";
		$header .= "X-Sender: " . $arg_str_from_email . "\r\n";
		$header .= "X-AntiAbuse: This is an email sent from - " . $arg_str_from_name . " - to " . $arg_str_to_email . ".\r\n";
		$header .= "X-AntiAbuse: Servername - {$_SERVER['SERVER_NAME']}\r\n";
		$header .= "X-AntiAbuse: User - " . $arg_str_from_email . "\r\n";
		$header .= $arg_str_other_headers;
		
		if ($_SERVER['REMOTE_ADDR'] != "127.0.0.1") $mail_sent = @mail($arg_str_to_email, $arg_str_subject, $arg_str_message, $header);
		if($mail_sent==1){
			return true;
		}else{	// else if the mail is not sent
			add_error("couldnot_send_mail");
			return false;
		}	// end if mail is send
	}	// end function send_mail
//---------------------------------------------------
	/**
	HTML to Text.
	Changes html encoded text to its original form of text by using html_entity_decode which is the oposite of htmlspecialchars(). for example changing &nbsp; to a space and &gt; to > and so on.
	@param arg_str_text the text to be changed from html to normal text. This parameter is passed by reference to ease its use.
	@return string the changed text.
	@see text_to_html().
	@access Public.
	*/
	function html_to_text (&$arg_str_text){		// to Change the HTML to Text
	   $arg_str_text = str_replace(" &nbsp; ", "  ", $arg_str_text);
	   $arg_str_text = trim(html_entity_decode($arg_str_text));
	   return $arg_str_text;
	}	// end function html_to_text
//---------------------------------------------------
	/**
	Text to DB.
	Used to escape user input to secure the application against SQL Injection.
	@param arg_str_text the text to be cleaned. it is passed by reference for convenience.
	@return String the cleaned text.
	@see array_to_db().
	@access Public.
	*/
	function text_to_db (&$arg_str_text){
		$arg_str_text = $this->__DB->escape($arg_str_text);
		return $arg_str_text;
	}	// end function text_to_db
//---------------------------------------------------
	/**
	Text to HTML.
	Prepares text to be shown in the page as html by changing html tags using htmlspecialchars. This function is used to disable Cross platform attacks (XSS). So please try to use it whenever you want to show text on the html page.
	It also replaces new lines with html line breaks and spaces to &nbsp;
	@param arg_str_text the rte text that should be cleaned. It is passed by reference to make it easier to use.
	@return string the cleaned rte text.
	@access Public.
	*/		
	function text_to_html (&$arg_str_text){	// to get text from DB and put it in an HTML page
	   $arg_str_text = trim(htmlspecialchars($arg_str_text));
	   $arg_str_text = str_replace("\n", "<br>" . "\n", $arg_str_text);
	   $arg_str_text = str_replace("  ", " &nbsp; ", $arg_str_text);
	   return $arg_str_text;
	}	// end function text_to_html
//---------------------------------------------------
	/**
	Checks if a number is a valid table id.
	checks the id and if it is not valid returns false. It also makes sure that the id is an integer.
	@param arg_int_id the id to be checked. it is passed by reference to change it to its integer value within the same function. 
	@return Boolean whether the id is valid or not.
	@access Public.
	*/
	function is_id (&$arg_int_id) {
		if(!is_numeric($arg_int_id)){return false;}
		$arg_int_id = intval($arg_int_id);
		if($arg_int_id < 1){return false;}
		return true;
	}	//	is_id
//---------------------------------------------------	
	/**
	Is Email address correct.
	Checks an email then returns true if the email has the correct format and false if not
	@param arg_str_email  an email address to be checked against a regular expression to check its format.
	@return Boolean whether the email address is correct or not.
	@see send_mail().
	@access Public.
	*/
	function is_email (&$arg_str_email) 	{
		$arg_str_email = trim(strtolower($arg_str_email));
		if(!$arg_str_email){return false;}
		return eregi("^([a-z0-9_\.\-]){2,}(\@){1}([a-z0-9_\-]){2,}(\.){1}([a-z0-9_\.\-]){2,}$", $arg_str_email);
	}	// end function
//---------------------------------------------------	
	/**
	Checks the validity of a username.
	Checks the username if it is empty or less than 3 characters or more than 50 characters or has invalid characters then it is invalid
	@param arg_str_username the username to be checked.
	@return Boolean whether the username is correct or not.
	@access Public.
	*/
	function is_username (&$arg_str_username) {
		$arg_str_username = trim(strtolower($arg_str_username));
		if(!$arg_str_username){return false;}
		return ereg("(^(([0-9a-zA-Z_-]){3,50})$)", $arg_str_username);
	}     // end function
//---------------------------------------------------
	/**
	List the files in a folder.
	Lists all files found in a certain folder or lists only the files that start with a certain string.
	@param arg_str_folder_name the folder name that will be searched.
	@param arg_str_files_starting_with if sepecified lists only the files starting with its value.
	@return Array of file names.
	@see delete_files_in_folder().
	@access Public.
	*/
	function list_files_in_folder ($arg_str_folder_name, $arg_str_files_starting_with=""){	// a function that takes a folder name and returns an array of all the files in that folder (not recursive)
		$arr_files = array();
		if(!$arg_str_folder_name){return false;}	// if the folder name is not valid then return false
		if (!file_exists($arg_str_folder_name)){return false;}		// if the folder does not exists then return false
		if (!is_dir($arg_str_folder_name)){return false;}		// if it is not a folder then return false
		$dir_folder = opendir($arg_str_folder_name);		// open the folder 
		while ($file = readdir($dir_folder)){		// while folder
			if (is_dir($file)){continue;}	// if it is a directory then do nothing and continue to the next while loop
			if($arg_str_files_starting_with){		// if a condition is put that the files start with a certain text
				if(ereg("^" . $arg_str_files_starting_with, $file)){$arr_files[] = $file;}		// if it is a file add it to the array of files
			}else{
				$arr_files[] = $file;		// if it is a file add it to the array of files
			}		// end if a condition is put
		}	// end while
		closedir($dir_folder);							// close the folder
		if(count($arr_files) < 1){		// if array of files is empty
			return false;		// if the array is empty then return false
		}else{
			return $arr_files;		// return the array of files if it is not empty
		}	// end if array of files is empty
	}	// end function list_files_in_folder
//---------------------------------------------------
	/**
	List the Folders in a folder.
	Lists all folders found in a certain folder or lists only the folders that start with a certain string.
	@param arg_str_folder_name the folder name that will be searched.
	@param arg_str_folder_starting_with if sepecified lists only the folders starting with its value.
	@return Array of folder names.
	@see list_files_in_folder().
	@access Public.
	*/
	function list_folders_in_folder ($arg_str_folder_name, $arg_str_folder_starting_with=""){	// a function that takes a folder name and returns an array of all the folders in that folder (not recursive)
		$arr_folders = array();
		if(!$arg_str_folder_name){return false;}	// if the folder name is not valid then return false
		if (!file_exists($arg_str_folder_name)){return false;}		// if the folder does not exists then return false
		if (!is_dir($arg_str_folder_name)){return false;}		// if it is not a folder then return false
		$dir_folder = opendir($arg_str_folder_name);		// open the folder 
		while ($folder = readdir($dir_folder)){		// while folder
			if(is_dir($folder)){
				if(($folder == ".") && ($folder == "..")) continue;
				if($arg_str_folder_starting_with)	// if folders starting with
				{
					if(ereg("^" . $arg_str_folder_starting_with, $folder))
					{
						$arr_folders[] = $folder;
					}
				}
				else	// else if all folders
				{
					$arr_folders[] = $folder;
				}		// end if arg_str_folder_starting_with
			}	// end if dir
		}	// end while
		closedir($dir_folder);							// close the folder
		return $arr_folders;
	}	// end function list_folders_in_folder
//---------------------------------------------------
	/**
	Delete the files in a folder.
	Deletes all files found in a certain folder or deletes only the files that start with a certain string.
	@param arg_str_folder_name the folder name that will be searched.
	@param arg_str_files_starting_with if sepecified deletes only the files starting with its value.
	@return Boolean whether the files are deleted or not.
	@see list_files_in_folder().
	@access Public.
	*/
	function delete_files_in_folder ($arg_str_folder_name, $arg_str_files_starting_with=""){	// a function that takes a folder name and returns an array of all the files in that folder (not recursive)
		$this->edit_dir_name($arg_str_folder_name);
		$arr_files = $this->list_files_in_folder($arg_str_folder_name, $arg_str_files_starting_with);
		
		if(is_array($arr_files)){
			while(list(, $str_fileName) = each($arr_files)){
				@unlink($arg_str_folder_name . $str_fileName);
			}
		}
		return true;
	}	// end function delete_files_in_folder
//---------------------------------------------------
	/**
	Makes sure the directory name ends with / and replaces any backslashes with normal slashes.
	@param arg_str_dir_name the directory name to be checked. It is passed by reference for convenience.
	@return the formatted directory name.
	@access Public.
	*/
	function edit_dir_name (&$arg_str_dir_name) {		// if the folder name does not end with a / the slash is adde
		$arg_str_dir_name = str_replace("\\", "/", $arg_str_dir_name);
		if(strrpos($arg_str_dir_name, "/") != (strlen($arg_str_dir_name)-1)){	// if the dir name ends with slash
			$arg_str_dir_name .= "/";
		}	// end if dir name
		return $arg_str_dir_name;
	}	// end 	function editFolderName
//---------------------------------------------------
	/**
	Makes a thumbnail from a picture.
	Makes a thumbnail image from the picture and gives it the new name or just resizes the picture if the arg_str_thumbnail_name is left empty.
	@param arg_str_file_name the filename of the picture to be resized.
	@param arg_str_picture_dir the foldername of the picture and the thumbnail.
	@param arg_str_thumbnail_name the name of the file of the thumbnail picture if left empty the arg_str_file_name original picture will be resized with the same name.
	@param arg_int_max_width The maximum width allowed for the thumbnail if left 0 this means width will not be resized.
	@param arg_int_max_height The maximum height allowed for the thumbnail if left 0 this means height will not be resized.
	@return Boolean whether the operation occured or not.
	@see upload_picture().
	@access Public.
	*/
	function make_thumbnail ($arg_str_file_name, $arg_str_picture_dir, $arg_str_thumbnail_name="", $arg_int_max_width=0, $arg_int_max_height=0) {
		if(!$arg_str_file_name){return false;}
		if(!$arg_str_picture_dir){return false;}
		$this->edit_dir_name($arg_str_picture_dir);
		if(!$arg_str_thumbnail_name){$arg_str_thumbnail_name = $arg_str_file_name;}
		
		// get the command suffix from the pic extension
		$path = pathinfo($arg_str_file_name);
		if((strtolower($path['extension'])=="jpg") || (strtolower($path['extension'])=="jpeg")){
			$commandsuffix = "jpeg";
		}elseif(strtolower($path['extension'])=="png"){
			$commandsuffix = "png";
		}elseif(strtolower($path['extension'])=="gif"){
			$commandsuffix = "gif";
		}else{	// else if not a supported format of pictures
			return false;
		}	// end if extension
		
		// get the size of thenew thumbnail
		$original_size = getimagesize($arg_str_picture_dir . $arg_str_file_name);

		$old_size = $original_size;
		$new_size = $old_size;
		
		if($arg_int_max_width!=0){
			if($old_size[0] > $arg_int_max_width){
				// resize width and height acording to the new width which is the maximum width
				$new_size[0] = $arg_int_max_width;
				$new_size[1] = (int)(($new_size[0]/$old_size[0]) * $old_size[1]);
			}	// check the width
		}
		$old_size = $new_size;		// so the new size now will be old because the picture may be resized again
		if($arg_int_max_height!=0){
			if($old_size[1] > $arg_int_max_height){
				// resize width and height acording to the new height which is the maximum height
				$new_size[1] = $arg_int_max_height;
				$new_size[0] = (int)(($new_size[1]/$old_size[1]) * $old_size[0]);
			}	// check the height
		}	// end if
		
		// make sure if there will be a change
		if(($original_size[0]==$new_size[0]) && ($original_size[1]==$new_size[1])){return true;}
		$img_big_pic = NULL;
		eval("\$img_big_pic = imagecreatefrom" . $commandsuffix . "('" . $arg_str_picture_dir . $arg_str_file_name . "');");
		$img_small_pic = imagecreatetruecolor($new_size[0], $new_size[1]);
		imagecopyresampled($img_small_pic, $img_big_pic, 0, 0, 0, 0, $new_size[0], $new_size[1], $original_size[0], $original_size[1]);
		
		// save the thumbnail to disk
		eval("image" . $commandsuffix . "(\$img_small_pic, '" . $arg_str_picture_dir . $arg_str_thumbnail_name . "', 80);");

		// destroy the unwanted resources
		imagedestroy($img_big_pic);
		imagedestroy($img_small_pic);
		return true;
	}	// end function make_thumbnail
//---------------------------------------------------
	/**
	Uploads a file to the server.
	Uploads a file to a certain folder on the server. In most cases the folder should be given the permission 755. The file uploaded is moved and renamed to a new generated name that starts with arg_str_prefix.
	If arg_str_replaced_file_name is specified the file that has the replaced file name will be removed.
	arg_str_prefix can be used as id_ to differenciate files and is easily differenciated using list_files_in_folder().
	@param arg_file the file input to be uploaded like $_FILES['myfile'].
	@param arg_str_upload_dir the folder where the file will be moved (in most cases need to have 755 permissions rwx).
	@param arg_str_prefix The string that the new filename should start with if any.
	@param arg_str_replaced_file_name the filename of the file that we want to remove on this upload process if any.
	@return the uploaded generated filename or false on failure.
	@see list_files_in_folder().
	@see upload_picture().
	@access Public.
	*/
	function upload_file ($arg_file, $arg_str_upload_dir, $arg_str_prefix="", $arg_str_replaced_file_name="") {
		$matches = array();
		if (!preg_match('/\.(exe|zip|rar|tar.bz2|tar|tar\.gz|gz|bz2|ace|iso|nrg|daa|dmg|pdf|psd|jpeg|jpg|gif|png|bmp|tiff|doc|rtf|txt|sql|xls|csv|ppt|pps|wav|mp3|rm|ram|avi|dat|wmv|mpg|mpeg|ra|a4v|a4m|arm|mp4|ape|ogg|flac|vqf)$/i', $arg_file['name'], $matches)) {
			add_error("extension_not_allowed");
			return false;
		}
		$ext = $matches[1];
		//$file_withno_ext = eregi_replace($ext, "", $arg_file['name']);;
		
		if (!$arg_str_upload_dir) return false;
		$this->edit_dir_name($arg_str_upload_dir);
		if ($arg_file['name']){	// if there is a file given to the function
			$str_new_file_name = uniqid($arg_str_prefix) . "." . $ext;	// combine the prefix and the unique id and extension to get the new file name
			if (move_uploaded_file($arg_file['tmp_name'], $arg_str_upload_dir . $str_new_file_name)){	// rename and move the file to the right folder after it is uploaded
				chmod($arg_str_upload_dir . $str_new_file_name, 0644);	// change permissions if possible
				if($arg_str_replaced_file_name != ""){	// if the old file name is specified
					if (file_exists($arg_str_upload_dir . $arg_str_replaced_file_name)) {
						unlink($arg_str_upload_dir . $arg_str_replaced_file_name);
					} else {
						add_error("file_not_found", array($arg_str_replaced_file_name, $arg_str_upload_dir)); 
					}
				}	// end if
				return $str_new_file_name;
			}else{		// if the file could not be uploaded
				add_error("couldnot_upload_file", array($arg_file['name'], $arg_str_upload_dir)); 
				return false;
			}	// end if
		}else{	//else if there is no file given to the function
			add_error("no_file_to_upload"); 
			return false;
		}	// end if
	}	// end function upload_file
//---------------------------------------------------
	/**
	Uploads a picture and resizes it if needed.
	It is a combination of the functions upload_file(). and make_thumbnail(). 
	It first makes sure that the file extension is a known picture extension. 
	Then it uploads the picture with upload_file() and uses the new file name 
	returned by this function in make_thumbnail() to resize the uploaded picture.
	@param arg_file the file input to be uploaded like $_FILES['mypicture'].
	@param arg_str_upload_dir the folder where the file will be moved (in most cases need to have 755 permissions rwx).
	@param arg_str_prefix The string that the new filename should start with if any.
	@param arg_str_replaced_file_name the filename of the file that we want to remove on this upload process if any.
	@param arg_int_max_width The maximum width allowed for the thumbnail if left 0 this means width will not be resized.
	@param arg_int_max_height The maximum height allowed for the thumbnail if left 0 this means height will not be resized.
	@return the uploaded generated filename or false on failure.
	@see upload_file().
	@see make_thumbnail().
	@access Public.
	*/
	function upload_picture ($arg_file, $arg_str_upload_dir, $arg_str_prefix="", $arg_str_replaced_file_name="", $arg_int_max_width=100, $arg_int_max_height=200) {
		// upload the fil of the picture
		$arr_pathinfo = pathinfo($arg_file['name']);
		if((strtolower($arr_pathinfo['extension'])!="jpg") && (strtolower($arr_pathinfo['extension'])!="jpeg") && (strtolower($arr_pathinfo['extension'])!="png")){add_error("not_a_picture"); return false;}
		$str_new_file_name = $this->upload_file($arg_file, $arg_str_upload_dir, $arg_str_prefix, $arg_str_replaced_file_name);
		if(!$str_new_file_name){return false;}
		// resize the uploaded image
		if($this->make_thumbnail($str_new_file_name, $arg_str_upload_dir, "", $arg_int_max_width, $arg_int_max_height)){
			return $str_new_file_name;
		}else{
			return false;
		}
	}	// end function upload_picture
//---------------------------------------------------
	/**
	Returns a one dimensional array from an array of arrays (result array).
	Searches the arrays in the result array for the arg_str_key_field and puts its value in a one dimensional array. If there is no arg_str_key_field the first field is taken instead.
	@param arg_arr_array The results array to loop on.
	@param arg_str_key_field the key field name to put in the one dimensional array. If not supplied the first field is taken
	@return one dimensional array.
	@access Public.
	*/
	function result_array_to_one_array ($arg_arr_array, $arg_str_key_field=""){		// one array like getting all the ids of a table
		if(!is_array($arg_arr_array))return array();
		$arr_output = array();
		while(list(,$row) = each($arg_arr_array)){
			if ($arg_str_key_field) {
				$arr_output[] = $row[$arg_str_key_field];
			} else {
				list(,$value) = each($row);
				$arr_output[] = $value;
			}
		}
		return $arr_output;
	}
//---------------------------------------------------
	/**
	Gets the real folder path from its relative path.
	@param arg_str_relative_folder_name the relative path of the given directory.
	@return string directory absolute path.
	@access Public.
	*/
	function get_real_folder ($arg_str_relative_folder_name=""){
		$arr_pathinfo = pathinfo($_SERVER['PHP_SELF']);
		$rootDir = $_SERVER['DOCUMENT_ROOT'] . $arr_pathinfo['dirname'];		// sepecify the root directory on the file system
		$this->edit_dir_name($rootDir);
		$mydir = $rootDir . $arg_str_relative_folder_name;
		$this->edit_dir_name($mydir);
		return $mydir;
	}
//---------------------------------------------------
	/**
	Associative Array from Result Array (Array of Arrays).
	Takes the keys from which it will get the key and value of the associative array and loops on an array of arrays (result array) to build the associative array.
	@param arg_arr_objects_array the array of objects to loop on.
	@param arg_str_key_field the property name of the key field of the result associative array.
	@param arg_str_value_field the property name of the value field of the result associative array.
	@return Associative Array (one dimensional) of key, value.
	@see assoc_array_from_objects_array().
	@access Public.
	*/
	function assoc_array_from_result_array ($arg_arr_result_array, $arg_str_key_field, $arg_str_value_field){
		if(!is_array($arg_arr_result_array)){return array();}
		if(!$arg_str_key_field){return array();}
		if(!$arg_str_value_field){return array();}
		$arr_assoc = array();
		while(list(,$row) = each($arg_arr_result_array)){
			if((array_key_exists($arg_str_key_field, $row)) && (array_key_exists($arg_str_value_field, $row))){
				$arr_assoc[$row[$arg_str_key_field]] = $row[$arg_str_value_field];
			}
		}
		return $arr_assoc;
	}
//---------------------------------------------------
	/**
	Associative Array from Objects Array.
	Takes the names of the properties from which it will get the key and value of the associative array and loops on an objects array to build the associative array.
	@param arg_arr_objects_array the array of objects to loop on.
	@param arg_str_key_field the property name of the key field of the result associative array.
	@param arg_str_value_field the property name of the value field of the result associative array.
	@return Associative Array (one dimensional) of key value.
	@see assoc_array_from_result_array().
	@access Public.
	*/
	function assoc_array_from_objects_array ($arg_arr_objects_array, $arg_str_key_field, $arg_str_value_field){
		if(!is_array($arg_arr_objects_array)){return array();}
		if(!$arg_str_key_field){return array();}
		if(!$arg_str_value_field){return array();}
		$arr = array();
		while(list(,$obj) = each($arg_arr_objects_array)){
			if($obj->$arg_str_key_field && $obj->$arg_str_value_field){
				$arr[$obj->$arg_str_key_field] = $obj->$arg_str_value_field;
			}
		}
		return $arr;
	}
//---------------------------------------------------------------------
	/**
	Removes private data from properties array.
	@param arg_arr_all_properties Array of properties if not supplied it reads from $this->__properties.
	@param arg_arr_private_keys Array of forbidden property names that should be removed from arg_arr_all_properties. If not supplied it is taken from $this->__private_keys.
	@return Associative Array of clean propertiesto be sent to the view.
	@access Public.
	*/
	function secure_output ($arg_arr_all_properties=array(), $arg_arr_private_keys=array()) {
		if(!$arg_arr_all_properties) $arg_arr_all_properties = $this->__properties;
		if(!$arg_arr_all_properties) return $arg_arr_all_properties;
		if (!$arg_arr_private_keys) $arg_arr_private_keys = $this->__private_keys;
		if (!$arg_arr_private_keys) return $arg_arr_all_properties;
		while (list(,$key) = each($arg_arr_private_keys)) {
			unset($arg_arr_all_properties[$key]);
		}
		return $arg_arr_all_properties;
	}	// end function secure_output
//---------------------------------------------------------------------
	/**
	Gets all the rows of a certain table or the rows that applies a certain condition. 
	It can also select certain fields only.
	Example usage: $obj->find_all("mytable", array("username"=>"ahmed", "age"=>"27"), array("name", "domain")) runs the sql "SELECT `name`, `domain` FROM `mytable` WHERE `username`='ahmed' AND `age`='27'"
	@param arg_str_table_name the table to select all rows from.
	@param arg_arr_conditions is the associative array of field_name and value from which the conditions is taken.
	@param arg_arr_required_fields the fields to select. * if not specified.
	@return a result array of table rows.
	@see db_select().
	@access Public.
	*/
	function find_all ($arg_str_table_name, $arg_arr_conditions=array(), $arg_arr_required_fields=array()) {
		$arg_str_table_name = ($arg_str_table_name ? $arg_str_table_name : $this->__table);
		if (!$arg_str_table_name) {
			trigger_error("No table given", 256);
			return false;
		}
		if (!$arg_arr_required_fields) {		// if there are no required fields
			$str_sql = "SELECT * FROM " . $arg_str_table_name;
		} else {	// if there are required fields
			$str_sql = "SELECT `" . join("`, `", $this->array_to_db($arg_arr_required_fields)) . "` FROM " . $arg_str_table_name;
		}	// end if there are no required fields
		if ($arg_arr_conditions) {
			while (list($str_field_name, $fieldvalue) = each($arg_arr_conditions)) {
				$arr_where[] = "`" . $str_field_name . "`='" . $fieldvalue . "'";
			}
			$str_sql .= " WHERE " . join(" AND ", $arr_where);
		}
		return $this->db_select($str_sql);
	}	// end function find_all
//---------------------------------------------------------------------
	/**
	Returns all records in the current table
	@see __call
	*/
	public function get_all () {
		return $this->db_select("SELECT * FROM " . $this->__table);
	}
//---------------------------------------------------------------------
	/**
	Gets the first rows of a certain table that follows a certain condition. 
	It can also select certain fields only.
	Example usage: $obj->find_one("mytable", array("username"=>"ahmed", "password"=>"123"), array("name", "domain")) runs the sql "SELECT `name`, `domain` FROM `mytable` WHERE `name`='ahmed' AND `password`=>'123' LIMIT 1"
	@param arg_str_table_name the table to select all rows from.
	@param arg_arr_conditions is the associative array of field_name and value from which the conditions is taken.
	@param arg_arr_required_fields the fields to select. * if not specified.
	@return an associative array representing one table row.
	@see db_select_one().
	@access Public.
	*/
	function find_one ($arg_str_table_name, $arg_arr_conditions=array(), $arg_arr_required_fields=array()) {
		$arg_str_table_name = ($arg_str_table_name ? $arg_str_table_name : $this->__table);
		if (!$arg_str_table_name) {
			trigger_error("No table given", 256);
			return false;
		}

		if (!$arg_arr_required_fields) {		// if there are no required fields
			$str_sql = "SELECT * FROM " . $arg_str_table_name;
		} else {	// if there are required fields
			$str_sql = "SELECT `" . join("`, `", $this->array_to_db($arg_arr_required_fields)) . "` FROM " . $arg_str_table_name;
		}	// end if there are no required fields
		if ($arg_arr_conditions) {
			while (list($str_field_name, $fieldvalue) = each($arg_arr_conditions)) {
				$arr_where[] = "`" . $str_field_name . "`='" . $fieldvalue . "'";
			}
			$str_sql .= " WHERE " . join(" AND ", $arr_where);
		}
		$str_sql .= " LIMIT 1";
		return $this->db_select_one($str_sql);
	}	// end function find_one
//---------------------------------------------------------------------
	/**
	Checks if the value of a certain field or fields is unique in the same table.
	It needs the table name and the primary key to make sure the required fields are unique.
	If it does not find the primary key value then this is a new entry (Like in the add method) so it won't check using the primary key.
	@param arg_arr_fields The associative array of field names and field values to validate.
	@param arg_str_table_name The table name to be checked. If it is empty, it takes its value from $this->__table.
	@param arg_str_primary_key_field_name The field name of the primary key of the current table if empty takes its value from $this->__primary_key.
	@return true if valid and false if not valid.
	@see db_get_one_value().
	@access Public.	
	*/
	public function is_uniq ($arg_field_name, $arg_value, $arg_str_table_name="", $arg_str_primary_key_field_name="") {
		$arg_str_table_name = ($arg_str_table_name ? $arg_str_table_name : $this->__table);
		if (!$arg_str_table_name) {
			trigger_error("No table given", 256);
			return false;
		}
		$arg_str_primary_key_field_name = ($arg_str_primary_key_field_name ? $arg_str_primary_key_field_name : $this->__primary_key);
		if (!$arg_str_primary_key_field_name) {
			trigger_error("No Primary Key given", 256);
			return false;
		}
		if(!$arg_field_name) {
			trigger_error("No Field name to validate", 256);
			return false;
		}
		
		$str_sql = "SELECT `" . $arg_str_primary_key_field_name . "` FROM `" . $arg_str_table_name . "` WHERE `" . $arg_field_name . "`='" . $this->__DB->escape($arg_value) . "'" . ($this->$arg_str_primary_key_field_name ? " AND `" . $arg_str_primary_key_field_name . "`!='" . $this->$arg_str_primary_key_field_name . "'" : "");
		return ($this->db_get_one_value($str_sql) ? false : true);
	}
//---------------------------------------------------------------------
	/**
	This method assigns a function to be the validation function of a certain field.
	This function is created by the developer and set as a public method of the current class.
	The validation function must have the name of the field to be validated as the first param and the field value to be validated as the second param and it must return true or false.
	Example:

	@param $value the value to be validated
	function is_number_big ($value) {
		if ($value <= 1000) {
			return false;
		}
		return true;
	}
	
	Example calling register_validation_function:
	$this->register_validation_function("bid", "is_number_big");
	*/
	function register_validation_function ($arg_field_name, $arg_function_name, $arg_error_msg) {
		$arr = array();
		$arr['type'] = "function";
		$arr['function'] = $arg_function_name;
		$arr['error_msg'] = $arg_error_msg;
		$this->__validation[$arg_field_name][] = $arr;
	}
//---------------------------------------------------------------------	
	public function add_validation ($arg_field_name, $arg_type, $arg_error_msg, $arg_function="", $arg_format="") {
		$arg_type = strtolower($arg_type);
		$arr_types = array("presence", "int", "number", "uniq", "email", "username", "function", "format");
		
		if (!in_array($arg_type, $arr_types)) {
			trigger_error("Invalid type of validation: [" . $arg_type . "] The validation type must be one of these: " . join(", ", $arr_types), 256);
			return false;
		}
		
		if (!$arg_error_msg) {
			trigger_error("Please write an error messgae for the validation of: [" . $arg_field_name . "]", 256);
			return false;
		}
		
		if (!$this->__validation[$arg_field_name]) $this->__validation[$arg_field_name] = array();
		
		$arr_new_validation = array("type" => $arg_type, "error_msg" => $arg_error_msg);
		if ($arg_type == "format") {
			if (!$arg_format) {
				trigger_error("Please specify the format regular expression param for the validation of: [" . $arg_field_name . "]", 256);
				return false;
			} else {
				$arr_new_validation['format'] = $arg_format;
			}
		} else if ($arg_type == "function") {
			if (!$arg_function) {
				trigger_error("Please specify the function name param for the validation of: [" . $arg_field_name . "]", 256);
				return false;
			} else {
				$arr_new_validation['function'] = $arg_function;
			}
		}
		$this->__validation[$arg_field_name][] = $arr_new_validation;
		return true;
	}	
//----------------------------------------------------------------------------
	/**
	Runs the appropriate validation Function for the supplied field name.
	@param arg_str_field_name The field name of the database field to be validated.
	@param arg_value The value of the field to be validated.
	@param arg_arr_validation_settings The validation settings, which is a sub array taken from $this->__validation for this field only.
	@return true or false according to the return value of the validation function called.
	@see is_field_valid()
	*/
	function run_validation ($arg_field_name, $arg_value, $arg_arr_validation_settings) {
		if ($arg_arr_validation_settings['type'] == "presence") {	// if validation type
			return ($arg_value ? true : false);
		} elseif ($arg_arr_validation_settings['type'] == "format") {
			return preg_match($arg_arr_validation_settings['format'], $arg_value);
		} elseif ($arg_arr_validation_settings['type'] == "int") {
			return ereg('^[0-9]+$', $arg_value);
		} elseif ($arg_arr_validation_settings['type'] == "number") {
			return is_numeric($arg_value);
		} elseif ($arg_arr_validation_settings['type'] == "email") {
			return $this->is_email($arg_value);
		} elseif ($arg_arr_validation_settings['type'] == "username") {
			return $this->is_username($arg_value);
		} elseif ($arg_arr_validation_settings['type'] == "uniq") {
			return $this->is_uniq ($arg_field_name, $arg_value);
		} elseif ($arg_arr_validation_settings['type'] == "function") {
			return call_user_func(array(&$this, $arg_arr_validation_settings['function']), $arg_value);
		}	// end if validation type
	}
//---------------------------------------------------------------------
	/**
	Loops on the array of fields and calidates them all against the array of $this->__validation
	Then returns true or false according to the overall output of all validation methods.
	@param $arg_arr_fields associative array of field names and values to be validated.
	@return true or false indicating whether all fields are valid or not.
	*/
	function are_fields_valid ($arg_arr_fields=array()) {
		if (!$arg_arr_fields || !is_array($arg_arr_fields)) return false;
		while (list($field_name, $value) = each($arg_arr_fields)) {
			if (!$this->is_field_valid($field_name, $value)) return false;
		}
		return true;
	}	// end function
//-------------------------------------------------------------------------------------
	/**
	Converts an array to its string representation so that it could be saved in a php file and included later.
	@param arg_arr_input the input array.
	@param arg_output_array_name the name of the output array.
	@return returns a string representation for the input array.
	*/
	function array_to_string($arg_arr_input, $arg_output_array_name='$arr'){
		if(!is_array($arg_arr_input)) return false;
		$str_var = "";
		if($arg_output_array_name) $str_var .= $arg_output_array_name . " = array();		// Start Array\n";
		$j=0;
		while ( list($key, $value) = each($arg_arr_input) ) {
			if(is_array($value)){			
				if (is_numeric($key)) {				
					$str_var .= $this->array_to_string($value, $arg_output_array_name . "[" . $key . "]");
				} else {
					$str_var .= $this->array_to_string($value, $arg_output_array_name . "['" . $key . "']");
				}
			}else{
				if (is_numeric($value)) {
					$str_var .= $arg_output_array_name . "['" . $key . "'] = " . $value . ";\n";
				} else {
					$value = ereg_replace('"', '\"', $value);
					$value = str_replace('$', '\$', $value);
					$str_var .= $arg_output_array_name . "['" . $key . "'] = \"" . $value . "\";\n";
				}
			}
		}
		return $str_var;
	}
//-------------------------------------------------------------------------------------
	/**
	Saves a string to a file
	@param arg_string the string to be saved to the file.
	@param arg_filename the file name of the string to be saved.
	@param arg_bool_backup if true and there is a file with the same name it is renamed to be saved as a backup.
	@return true if saved and false if not saved.
	*/
	function string_to_file($arg_string, $arg_filename, $arg_bool_backup=false){
		if(!$arg_filename) return false;
		if ($arg_bool_backup) {
			if(file_exists($arg_filename)){	// if the file exists then take a backup of it
				rename($arg_filename, $arg_filename . uniqid("."));
			}	// end if file exists
		}
		$fp = fopen($arg_filename, 'w');
		if (flock($fp, LOCK_EX)) { // do an exclusive lock
			fwrite($fp, $arg_string);
			flock($fp, LOCK_UN); // release the lock
		}		
		fclose($fp);
		return true;
	}
//------------------------------------------------------------------------
	public function rmdir_r ($path) {
		if (!$path || !is_string($path)) return false;
		if (!is_dir($path)) return false;
		if (!file_exists($path)) return false;		// if the folder does not exists then return false		
		if ($path{strlen($path)-1} != '/') $path .= '/';
		$dir_folder = opendir($path);			// open the folder 
		while ($file_entry = readdir($dir_folder)){		// while folder
			if(is_dir($path . $file_entry)){
				if(($file_entry == '.') || ($file_entry == '..')) continue;
				$this->rmdir_r($path . ($path{strlen($path)-1} == '/' ? '' : '/') . $file_entry);
			} else {	// file
				unlink($path . ($path{strlen($path)-1} == '/' ? '' : '/') . $file_entry);
			}	// end if dir
		}	// end while
		closedir($dir_folder);	
		rmdir($path);
		return true;
	}
//------------------------------------------------------------------------
	/**
	creates a directory recursively (creates its parent if it does not exist)
	@param $path the directory path to be created
	@return true on success and false on failure
	*/
	public function mkdir_r ($path) {
		if (!$path) return false;
		if ($path{strlen($path)-1} != '/') $path .= '/';
		$parent = dirname($path);
		if (!file_exists($parent)) {
			if (!$this->mkdir_r($parent)) return false;			
		}
		if (!@mkdir($path)) {
			return false;
		}
		return true;
	}
//------------------------------------------------------------------------
}	// end class parent_model
?>
