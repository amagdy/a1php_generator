<?
require_once(PHP_ROOT . "lib/parent_model.php");
class generator extends parent_model {

	function generator () {
		$this->parent_model();
	}
//------------------------------------------------------------------------------------------------------------------------------
	/**
	Gets available databases on this server
	*/
	function get_databases () {
		$link = @mysql_connect(DB_HOST, DB_USER, DB_PASS);
		$result = mysql_query("SHOW DATABASES", $link);
		$arr_dbs = array();
		if ($result) {
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if ($row['Database'] != 'information_schema' && $row['Database'] != 'mysql') $arr_dbs[] = $row['Database'];
			}
			mysql_free_result($result);
		}
		return $arr_dbs;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function get_tables() {
		return $this->result_array_to_one_array($this->db_select("SHOW TABLES"));
	}
//------------------------------------------------------------------------------------------------------------------------------
	function get_table_fields ($arg_table_name) {
		$arr_fields = $this->get_table_fields_info($arg_table_name);
		return array_keys($arr_fields);
	}
//------------------------------------------------------------------------------------------------------------------------------
	function is_type_number ($arg_data_type) {
		if (eregi("^int|bigint|tinyint|double|decimal|float|smallint|mediumint|timestamp|year.*$", $arg_data_type)) {
			return true;
		} else {
			return false;
		}
	}
//------------------------------------------------------------------------------------------------------------------------------
	function get_table_fields_info ($arg_table_name) {
		if(!$arg_table_name){	// if $arg_table_name
			add_error("Invalid table name");
			return false;
		}	// end if $arg_table_name
		$arr_fields_info = array();
		$str_query = "DESC " . $arg_table_name;
		$arr_desc = $this->db_select($str_query);
		if (is_array($arr_desc)) {
			while (list(, $info) = each($arr_desc)) {
				$arr['field_name'] = $info['Field'];
				$arr['number'] = $this->is_type_number($info['Type']);
				$arr['string'] = !$arr['number'];
				$arr['not_null'] = ($info['Null'] == "NO" ? true : false);
				if (isset($info['Default']) && $info['Default'] !== "") {
					$arr['default'] = $info['Default'];
				}
				$arr['primary_key'] = ($info['Key'] == "PRI" ? true : false);
				$arr['unique'] = ($info['Key'] == "UNI" ? true : false);
				$arr_fields_info[$info['Field']] = $arr;
			}
		}
		return $arr_fields_info;
	}	// end function	
//------------------------------------------------------------------------------------------------------------------------------
	function delete_folder ($arg_folder) {
		$this->edit_dir_name($arg_folder);
		if(!$arg_folder){return false;}	// if the folder name is not valid then return false
		if (!file_exists($arg_folder)){return false;}		// if the folder does not exists then return false
		if (!is_dir($arg_folder)){return false;}		// if it is not a folder then return false
		$dir_folder = opendir($arg_folder);		// open the folder 
		while (false !== ($file = readdir($dir_folder))){		// while folder
			if (is_dir($file)){
				delete_folder($arg_folder);
			} else {
				@unlink($arg_folder . $file);
			}	// if it is a directory then delete it with its contents
		}	// end while
		closedir($dir_folder);							// close the folder
		rmdir($arg_folder);
	}
//------------------------------------------------------------------------------------------------------------------------------
	function create_skeleton ($arg_containing_folder) {
		$this->edit_dir_name($arg_containing_folder);
		$arg_containing_folder .= "code/";
		mkdir($arg_containing_folder);
		mkdir($arg_containing_folder . "controller/");
		mkdir($arg_containing_folder . "model/");
		mkdir($arg_containing_folder . "view/");
		mkdir($arg_containing_folder . "view/en/");
		mkdir($arg_containing_folder . "view/ar/");
		mkdir($arg_containing_folder . "view/en/default/");
		mkdir($arg_containing_folder . "view/ar/default/");
	}
//------------------------------------------------------------------------------------------------------------------------------
	function save_project ($arg_validate=true) {
		if (!$_SESSION['project']) {
			if ($arg_validate) add_error("There is no project to save");
			return false;
		}
		if (!$_SESSION['project']['table']) {
			if ($arg_validate) add_error("Your project cannot be saved because it does not have a table");
			return false;
		}
		$str = "<?\n";
		$str .= $this->array_to_string($_SESSION['project'], '$project');
		$str .= "\n?>";
		$this->string_to_file($str, PHP_ROOT . "uploads/project/" . $_SESSION['project']['table'] . ".php", true);
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function save_close () {
		if ($this->save_project()) {
			session_unregister("project");
			return true;
		} else {
			return false;
		}
	}	
//------------------------------------------------------------------------------------------------------------------------------
	function is_project_ready_for_generate () {
		if (($_SESSION['project']['project_flags'] & 0xDF) >= 0x1F) {
			return true;
		} else {
			return false;
		}
	}
//------------------------------------------------------------------------------------------------------------------------------
	function new_project () {
		$this->save_project(false);
		$_SESSION['project'] = array();
		$_SESSION['project']['project_flags'] = 0;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function set_db ($arg_db_name) {
		if (!$arg_db_name) {
			add_error("Please select a valid Database", array(), "db_name");
			return false;
		}
		global $__DB_NAME;
		$__DB_NAME = $arg_db_name;
		$_SESSION['project']['db_name'] = $arg_db_name;
		$_SESSION['project']['project_flags'] = $_SESSION['project']['project_flags'] | 1;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function set_table ($arg_table_name) {
		if (!$arg_table_name) {
			add_error("Please select a valid Table", array(), "table");
			return false;
		}
		$_SESSION['project']['table'] = $arg_table_name;
		$_SESSION['project']['project_flags'] = $_SESSION['project']['project_flags'] | 2;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function get_saved_projects () {
		$arr_files = $this->list_files_in_folder(PHP_ROOT . "uploads/project/");
		$arr_projects = array();
		if (is_array($arr_files)) {
			while (list(, $file) = each($arr_files)) {
				if (ereg("\.php$", $file)) {
					$arr_projects[] = $file;
				}
			}
		}
		return $arr_projects;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function open_project ($arg_file_name) {
		if (!$arg_file_name) {
			add_error("Could not open project invalid File");
			return false;
		}
		if (!file_exists(PHP_ROOT . "uploads/project/" . $arg_file_name)) {
			add_error("Could not open project invalid File");
			return false;
		}
		require_once(PHP_ROOT . "uploads/project/" . $arg_file_name);
		$_SESSION['project'] = $project;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function set_model_controller ($arg_model, $arg_controller, $arg_primary_key) {
		if (!$arg_model || !$arg_controller) {
			add_error("Please add a valid model and controller");
			return false;
		}
		$_SESSION['project']['model'] = $arg_model;
		$_SESSION['project']['controller'] = $arg_controller;
		$_SESSION['project']['primary_key'] = $arg_primary_key;
		$_SESSION['project']['project_flags'] |= 0x4;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function get_table_primary_key ($arg_table_name) {
		$arr_fields_info = $this->get_table_fields_info($arg_table_name);
		if (is_array($arr_fields_info)) {
			while (list($field_name, $obj_field) = each($arr_fields_info)) {
				if ($obj_field->primary_key) return $field_name;
			}
		}
		return "";
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function get_string_fields ($arg_table_name) {
		$arr_fields_info = $this->get_table_fields_info($arg_table_name);
		$arr_fields = array();
		if (is_array($arr_fields_info)) {
			while (list($field_name, $obj_field) = each($arr_fields_info)) {
				if ($obj_field['string']) $arr_fields[] = $field_name;
			}
		}
		return $arr_fields;
	}
//------------------------------------------------------------------------------------------------------------------------------		
	function get_non_primary_key_fields ($arg_table_name) {
		$arr_fields_info = $this->get_table_fields_info($arg_table_name);
		$arr_fields = array();
		if (is_array($arr_fields_info)) {
			while (list($field_name, $obj_field) = each($arr_fields_info)) {
				if (!$obj_field->primary_key) {
					$arr_fields[] = $field_name;
				}
			}
		}
		return $arr_fields;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function set_appearance ($arg_arr_getall_fields, $arg_arr_add_fields, $arg_arr_edit_fields) {
		if (!$arg_arr_getall_fields) {
			add_error("Please select at least one field to appear in the getall page");
			return false;
		}
		if (!$arg_arr_add_fields) {
			add_error("Please select at least one field to appear in the add form");
			return false;
		}
		if (!$arg_arr_edit_fields) {
			add_error("Please select at least one field to appear in the edit form");
			return false;
		}
		$_SESSION['project']['getall_fields'] = $arg_arr_getall_fields;
		$_SESSION['project']['add_fields'] = $arg_arr_add_fields;
		$_SESSION['project']['edit_fields'] = $arg_arr_edit_fields;
		$_SESSION['project']['project_flags'] |= 0x8;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function get_selected_fields ($arg_arr_fields, $arg_arr_selected_fields) {
		$arr_output = array();
		if (!is_array($arg_arr_selected_fields)) $arg_arr_selected_fields = array();
		if (is_array($arg_arr_fields)) {
			while (list(,$field_name) = each($arg_arr_fields)) {
				$arr_output[$field_name] = (in_array($field_name, $arg_arr_selected_fields) ? true : false);
			}
		}
		return $arr_output;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function set_fields_names ($arg_arr_fields) {
		if (!is_array($arg_arr_fields)) {
			add_error("Invalid array of fields names and controls");
			return false;
		}
		$error = false;
		$arr_fields_names = array();
		while (list($name, $info) = each($arg_arr_fields)) {
			$arr_fields_names[$name]['field_name'] = $name;
			if (!$info['en_friendly_name']) {
				add_error("Invalid English Friendly Name for field: [%s]", array($name));
				$error = true;
				continue;
			}
			if (!$info['ar_friendly_name']) {
				$info['ar_friendly_name'] = $info['en_friendly_name'];
			}
			if ($info['control'] == "none") {
				add_error("Invalid HTNL Control for field: [%s]", array($name));
				$error = true;
				continue;
			}
			$arr_fields_names[$name] = $info;
		}
		if ($error) return false;
		$_SESSION['project']['arr_fields_names'] = $arr_fields_names;
		$_SESSION['project']['project_flags'] |= 0x10;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function get_fields_names_for_out ($arr_fields) {
		$out_fields = array();
		if (is_array($arr_fields)) {
			$i = 0;
			while (list($name, $info) = each($arr_fields)) {
				$out_fields[$i] = $info;
				$i++;
			}
		}
		return $out_fields;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function set_defaults ($arg_arr_defaults) {
		$_SESSION['project']['arr_defaults'] = $arg_arr_defaults;
		$_SESSION['project']['project_flags'] |= 0x20;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function get_saved_defaults () {
		if ($_SESSION['project']['project_flags'] & 0x20) return $_SESSION['project']['arr_defaults'];
		return array();
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function add_validation ($arg_field_name, $arg_type, $arg_value) {
		$arr = array();
		$arr['type'] = $arg_type;
		if (($arg_type == "function" || $arg_type == "format") && !$arg_value) {
			add_error("please_specify_a_value", array(), "validation_value");
			return false;
		}
		if ($arg_type == "function") {
			$arr['function'] = $arg_value;
		} else if ($arg_type == "format") {
			$arr['format'] = $arg_value;
		}
		$_SESSION['project']['validation'][$arg_field_name][] = $arr;
		$_SESSION['project']['project_flags'] |= 0x40;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function get_fields_names_from_session () {
		$arr = array();
		$arr_fields = $_SESSION['project']['arr_fields_names'];
		if (is_array($arr_fields)) {
			while (list(, $row) = each($arr_fields)) {
				$arr[$row['field_name']] = $row['field_name'];
			}
		}
		return $arr;
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function delete_validation ($arg_field_name, $arg_index) {
		if (!$_SESSION['project']['validation'] || !$_SESSION['project']['validation'][$arg_field_name] || !$_SESSION['project']['validation'][$arg_field_name][$arg_index]) {
			add_error("No Validation to Delete");
			return false;
		}
		unset($_SESSION['project']['validation'][$arg_field_name][$arg_index]);
		if (!$_SESSION['project']['validation'][$arg_field_name]) unset($_SESSION['project']['validation'][$arg_field_name]);
		if (!$_SESSION['project']['validation']) {
			$_SESSION['project']['project_flags'] &= ~0x40;
		}
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function suggest_error_messages (&$arg_arr_validation) {
		if (!is_array($arg_arr_validation) || !$arg_arr_validation) {
			$arg_arr_validation = array();
			return;
		}
		// suggest messages
		while (list($field_name) = each($arg_arr_validation)) {
			while (list($index) = each($arg_arr_validation[$field_name])) {
				$type = $arg_arr_validation[$field_name][$index]['type'];
				if ($arg_arr_validation[$field_name][$index]['en_msg']) continue;
				$en_field_name = $_SESSION['project']['arr_fields_names'][$field_name]['en_friendly_name'];
				$ar_field_name = $_SESSION['project']['arr_fields_names'][$field_name]['ar_friendly_name'];
				if ($type == "presence") {                    
					$arg_arr_validation[$field_name][$index]['en_msg'] = "Please enter the " . $en_field_name;
					$arg_arr_validation[$field_name][$index]['ar_msg'] = "من فضلك أدخل " . $ar_field_name;
				} elseif ($type == "int") {
					$arg_arr_validation[$field_name][$index]['en_msg'] = "Invalid Number: " . $en_field_name . " should be an integer number.";
					$arg_arr_validation[$field_name][$index]['ar_msg'] = "رقم غير صحيح: " . $ar_field_name . " لا بد أن يكون رقم صحيح";
				} elseif ($type == "number") {
					$arg_arr_validation[$field_name][$index]['en_msg'] = "Invalid Number";
					$arg_arr_validation[$field_name][$index]['ar_msg'] = "رقم غير صحيح";
				} elseif ($type == "uniq") {
					$arg_arr_validation[$field_name][$index]['en_msg'] = $en_field_name . " already exists";
					$arg_arr_validation[$field_name][$index]['ar_msg'] = $ar_field_name . " موجود بالفعل";
				} elseif ($type == "email") {
					$arg_arr_validation[$field_name][$index]['en_msg'] = "Invalid Email Address";
					$arg_arr_validation[$field_name][$index]['ar_msg'] = "البريد الإلكترونى غير صحيح";
				} elseif ($type == "username") {
					$arg_arr_validation[$field_name][$index]['en_msg'] = "Invalid Username";
					$arg_arr_validation[$field_name][$index]['ar_msg'] = "إسم المستخدم غير صحيح";
				} elseif ($type == "function") {
					$arg_arr_validation[$field_name][$index]['en_msg'] = "Invalid " . $en_field_name;
					$arg_arr_validation[$field_name][$index]['ar_msg'] = $ar_field_name . " غير صحيح";
				} elseif ($type == "format") {
					$arg_arr_validation[$field_name][$index]['en_msg'] = "Invalid " . $en_field_name;
					$arg_arr_validation[$field_name][$index]['ar_msg'] = $ar_field_name . " غير صحيح";
				}
			}
			reset($arg_arr_validation[$field_name]);
		}
		reset($arg_arr_validation);
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function set_error_messages ($arg_arr_msgs) {
		if (!is_array($arg_arr_msgs) || !$arg_arr_msgs) {
			add_error("No Error Messages to add");
			return false;
		}
		$is_error = false;
		while (list($field_name, $arr) = each($arg_arr_msgs)) {
			while (list($index, $msgs) = each($arr)) {
				if (!$msgs['en_msg']) {
					add_error("Please fill error message", array(), $field_name . "_" . $index);
					$is_error = true;
				}
				$_SESSION['project']['validation'][$field_name][$index]['en_msg'] = $msgs['en_msg'];
				$_SESSION['project']['validation'][$field_name][$index]['ar_msg'] = $msgs['ar_msg'];
			}
		}
		return !$is_error;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function get_search_for_output () {
		if (!$_SESSION['project']['search']) return array();
		return $_SESSION['project']['search'];
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function add_search ($arg_field_name, $arg_operator) {
		$arr = array();
		$_SESSION['project']['search'][$arg_field_name][] = $arg_operator;
		$_SESSION['project']['project_flags'] |= 0x80;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function delete_search ($arg_field_name, $arg_index) {
		if (!$_SESSION['project']['search'] || !$_SESSION['project']['search'][$arg_field_name] || !$_SESSION['project']['search'][$arg_field_name][$arg_index]) {
			add_error("No Search Criteria to Delete");
			return false;
		}
		unset($_SESSION['project']['search'][$arg_field_name][$arg_index]);
		if (!$_SESSION['project']['search'][$arg_field_name]) unset($_SESSION['project']['search'][$arg_field_name]);
		if (!$_SESSION['project']['search']) {
			$_SESSION['project']['project_flags'] &= ~0x80;
		}
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function get_relations_for_output () {
		if (!$_SESSION['project']['relation']) return array();
		return $_SESSION['project']['relation'];
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function add_relation ($arg_field_name, $arg_text) {
		$arr = array();
		$_SESSION['project']['relation'][$arg_field_name] = $arg_text;
		$_SESSION['project']['project_flags'] |= 0x100;
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------
	function delete_relation ($arg_field_name) {
		if (!$_SESSION['project']['relation'] || !$_SESSION['project']['relation'][$arg_field_name]) {
			add_error("No Relation to Delete");
			return false;
		}
		unset($_SESSION['project']['relation'][$arg_field_name]);
		if (!$_SESSION['project']['relation']) {
			$_SESSION['project']['project_flags'] &= ~0x100;
		}
		return true;
	}
//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

//------------------------------------------------------------------------------------------------------------------------------	

}
?>
