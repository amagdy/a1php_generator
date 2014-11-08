<?
/**
@file project_controller.php
*/
require_once(PHP_ROOT . "lib/parent_controller.php");
require_once(PHP_ROOT . "model/generator.php");

class project_controller extends parent_controller
{
//-----------------------------------------------------
	function project_controller () {
		global $__out;
		$this->parent_controller();
		$gen = new generator();
		$__out['is_ready_to_generate'] = $gen->is_project_ready_for_generate();
		
	}
//-----------------------------------------------------	
	function index()
	{
		global $__out;
		$__out['action'] = "main";
		$this->main();
	}	// end function index().
//-----------------------------------------------------			
	function clear_generated_code () {
		global $__in, $__out;
		$gen = new generator();
		$gen->delete_folder(PHP_ROOT . "uploads/code/");
		$gen->create_skeleton(PHP_ROOT . "uploads/");
		return $this->redirect(array("action" => "main"), "Generated Code Deleted");
	}
//-----------------------------------------------------		
	function main () {
		global $__in, $__out;
		$gen = new generator();
	}
//-----------------------------------------------------		
	function new_project () {
		global $__in, $__out;
		$gen = new generator();
		$gen->new_project();
		return $this->redirect(array("action" => "select_db"));
	}
//-----------------------------------------------------		
	function save_project () {
		global $__in, $__out;
		$gen = new generator();
		if($gen->save_project()) {
			add_info("Project saved successfully");
		}
		return $this->redirect(array("action" => "main"));
	}
//-----------------------------------------------------		
	function save_close () {
		global $__in, $__out;
		$gen = new generator();
		if($gen->save_close()) {
			add_info("Project saved successfully");
		}
		return $this->redirect(array("action" => "main"));
	}
//-----------------------------------------------------	
	function open_project () {
		global $__in, $__out;
		$gen = new generator();
		if ($__in['__is_form_submitted']) {
			if ($gen->open_project($__in['project']['file_name'])) {
				return $this->redirect(array("action" => "main"), "Opened Project successfully");
			} else {
				$__out['project']['file_name'] = $__in['project']['file_name'];
			}
		}
		$__out['arr_files'] = $gen->get_saved_projects();
	}
//-----------------------------------------------------		
	function select_db () {
		global $__in, $__out;
		$gen = new generator();
		if ($__in['__is_form_submitted']) {
			if($gen->set_db($__in['project']['db_name'])) {
				return $this->redirect(array("action" => "select_table"));
			}
		}
		$__out['databases'] = $gen->get_databases();
	}
//-----------------------------------------------------
	function select_table () {
		global $__in, $__out;
		$gen = new generator();
		if ($__in['__is_form_submitted']) {
			if($gen->set_table($__in['project']['table'])) {
				return $this->redirect(array("action" => "set_model_controller"));
			}
		}
		$__out['tables'] = $gen->get_tables();
	}
//-----------------------------------------------------		
	function set_model_controller () {
		global $__in, $__out;
		$gen = new generator();
		if ($__in['__is_form_submitted']) {
			if ($gen->set_model_controller($__in['project']['model'], $__in['project']['controller'], $__in['project']['primary_key'])) {
				return $this->redirect(array("action" => "set_appearance"));
			}
		}
		$__out['fields'] = $gen->get_table_fields($_SESSION['project']['table']);
		
		if (!$_SESSION['project']['model'] && !$_SESSION['project']['controller']) {
			$__out['project']['model'] = $__out['project']['controller'] = Inflector::singularize($_SESSION['project']['table']);
			$__out['project']['primary_key'] = $gen->get_table_primary_key($_SESSION['project']['table']);
		}
	}
//-----------------------------------------------------
	/**
	Set fields appearnce in getall and add and edit fields
	*/
	function set_appearance () {
		global $__in, $__out;
		$gen = new generator();
		$arr_fields = $gen->get_table_fields($_SESSION['project']['table']);
		if ($__in['__is_form_submitted']) {
			if ($gen->set_appearance($__in['project']['getall_fields'], $__in['project']['add_fields'], $__in['project']['edit_fields'])) {
				return $this->redirect(array("action" => "set_fields_names"));
			} else {
				$__out['project']['getall_fields'] = $gen->get_selected_fields($arr_fields, $__in['project']['getall_fields']);
				$__out['project']['add_fields'] = $gen->get_selected_fields($arr_fields, $__in['project']['add_fields']);
				$__out['project']['edit_fields'] = $gen->get_selected_fields($arr_fields, $__in['project']['edit_fields']);
			}
		} else {
			if ($_SESSION['project']['getall_fields'] && $_SESSION['project']['add_fields'] && $_SESSION['project']['edit_fields']) {
				$__out['project']['getall_fields'] = $gen->get_selected_fields($arr_fields, $_SESSION['project']['getall_fields']);
				$__out['project']['add_fields'] = $gen->get_selected_fields($arr_fields, $_SESSION['project']['add_fields']);
				$__out['project']['edit_fields'] = $gen->get_selected_fields($arr_fields, $_SESSION['project']['edit_fields']);
			} else {
				$arr_string_fields = $gen->get_string_fields($_SESSION['project']['table']);
				$__out['project']['getall_fields'] = $gen->get_selected_fields($arr_fields, $arr_string_fields);
				$__out['project']['add_fields'] = $gen->get_selected_fields($arr_fields, $arr_string_fields);
				$__out['project']['edit_fields'] = $gen->get_selected_fields($arr_fields, $arr_string_fields);
			}
		}
		return true;
	}
//-----------------------------------------------------		
	function set_fields_names () {
		global $__in, $__out;
		$gen = new generator();
		$__out['arr_html_controls'] = array();
		$__out['arr_html_controls']['none'] = "--- None ---";
		$__out['arr_html_controls']['text'] = "Text Box";
		$__out['arr_html_controls']['password'] = "Password";
		$__out['arr_html_controls']['textarea'] = "Textarea";
		$__out['arr_html_controls']['rte'] = "R T E";
		$__out['arr_html_controls']['select'] = "Select";
		$__out['arr_html_controls']['checkbox'] = "Checkbox";
		$__out['arr_html_controls']['radio'] = "Radio";
		$__out['arr_html_controls']['file'] = "File";
		$__out['arr_html_controls']['pic'] = "Picture";
		$__out['arr_html_controls']['date'] = "Date";
		$__out['arr_html_controls']['time'] = "Time";
		$__out['arr_html_controls']['datetime'] = "Date and Time";
		
		if ($__in['__is_form_submitted']) {
			if($gen->set_fields_names($__in['project']['arr_fields_names'])) {
				return $this->redirect(array("action" => "set_defaults"));
			} else {
				$__out['arr_fields_names'] = $gen->get_fields_names_for_out($__in['project']['arr_fields_names']);
			}
		} else {
			$__out['arr_fields_names'] = array();
			$arr_saved_fields = array();
			if ($_SESSION['project']['project_flags'] & 0x10) {
				$arr_saved_fields = $gen->get_fields_names_for_out($_SESSION['project']['arr_fields_names']);
			}
			$count_saved = count($arr_saved_fields);
			for ($i = 0; $i < count($_SESSION['project']['add_fields']); $i++) {
				$field_name = $_SESSION['project']['add_fields'][$i];
				$__out['arr_fields_names'][$i]['field_name'] = $field_name;
				$__out['arr_fields_names'][$i]['en_friendly_name'] = Inflector::humanize($field_name);
				$__out['arr_fields_names'][$i]['ar_friendly_name'] = $__out['arr_fields_names'][$i]['en_friendly_name'];
				if ($count_saved) {
					for ($j = 0 ; $j < $count_saved; $j++) {
						if ($__out['arr_fields_names'][$i]['field_name'] == $arr_saved_fields[$j]['field_name']) {
							$__out['arr_fields_names'][$i]['en_friendly_name'] = $arr_saved_fields[$j]['en_friendly_name'];
							$__out['arr_fields_names'][$i]['ar_friendly_name'] = $arr_saved_fields[$j]['ar_friendly_name'];
							$__out['arr_fields_names'][$i]['control'] = $arr_saved_fields[$j]['control'];
							break;
						}
					}
				}
			}
			
		}

		return true;
	}
//-----------------------------------------------------
	function set_defaults () {
		global $__in, $__out;
		$gen = new generator();
		if ($__in['__is_form_submitted']) {
			if($gen->set_defaults($__in['project']['arr_defaults'])) {
				return $this->redirect("validation");
			}
		}
		
		$__out['arr_fields_names'] = $gen->get_table_fields($_SESSION['project']['table']);
		$__out['project']['arr_defaults'] = $gen->get_saved_defaults();
		return true;
	}
//-----------------------------------------------------		
	function validation () {
		global $__in, $__out;
		if ($_SESSION['project']['project_flags'] & 0x40) {
			$__out['validation'] = $_SESSION['project']['validation'];
		} else {
			return $this->redirect("add_validation");
		}
		return true;
	}
//-----------------------------------------------------
	function add_validation () {
		global $__in, $__out;
		$gen = new generator();
		$__out['arr_fields_names'] = $gen->get_fields_names_from_session();
		$__out['arr_validation_types'] = array("presence" => "Required", "int" => "Integer", "number" => "Number", "uniq" => "Unique", "email" => "Email", "username" => "Username", "function" => "Function", "format" => "Format");
		if ($__in['__is_form_submitted']) {
			if ($gen->add_validation ($__in['project']['field_name'], $__in['project']['validation_type'], $__in['project']['validation_value'])) {
				return $this->redirect("validation", "added_successfully");
			}
		}
		return true;
	}
//-----------------------------------------------------
	function delete_validation () {
		global $__in, $__out;
		$gen = new generator();
		if ($gen->delete_validation ($__in['field'], $__in['index'])) {
			return $this->redirect("validation", "deleted_successfully");
		}
		return true;
	}
//-----------------------------------------------------
	function set_error_messages () {
		global $__in, $__out;
		$gen = new generator();
		$__out['validation'] = $_SESSION['project']['validation'];
		if ($__in['__is_form_submitted']) {
			if ($gen->set_error_messages($__in['errors'])) {
				add_info("Error Messages have been set successfully");
			}
			$__out['validation'] = $_SESSION['project']['validation'];
		} else {
			$gen->suggest_error_messages($__out['validation']);
		}
		
		return true;
	}
//-----------------------------------------------------
	function add_search () {
		global $__in, $__out;
		$gen = new generator();
		$__out['arr_fields_names'] = $gen->get_table_fields($_SESSION['project']['table']);//, "");
		$__out['arr_search_operators'] = array("like" => "LIKE", "eq" => "=", "gt" => "&gt;", "lt" => "&lt;", "ge" => "&gt;=", "le" => "&lt;=");
		if ($__in['__is_form_submitted']) {
			if ($gen->add_search ($__in['search']['field_name'], $__in['search']['operator'])) {
				return $this->redirect("search", "added_successfully");
			}
		}
		return true;
	}
//-----------------------------------------------------
	function delete_search () {
		global $__in, $__out;
		$gen = new generator();
		if ($gen->delete_search($__in['field'], $__in['index'])) {
			return $this->redirect("search", "deleted_successfully");
		}
		return true;
	}
//-----------------------------------------------------
	function search () {
		global $__in, $__out;
		$gen = new generator();
		$__out['search'] = $gen->get_search_for_output();
		$__out['arr_search_operators'] = array("like" => "LIKE", "eq" => "=", "gt" => "&gt;", "lt" => "&lt;", "ge" => "&gt;=", "le" => "&lt;=");
		return true;
	}
//-----------------------------------------------------		
	function add_relation () {
		global $__in, $__out;
		$gen = new generator();
		$__out['arr_fields_names'] = $gen->get_table_fields($_SESSION['project']['table']);
		$tables = $__out['arr_tables'] = $gen->get_tables();
		if (is_array($tables)) {
			while (list(, $tbl) = each($tables)) {
				$class_name = Inflector::singularize($tbl);
				$__out['arr_methods'][] = "\$$class_name = new $class_name();\n\$__out['arr_%s'] = \$$class_name" . "->get_all_assoc(\"id\", \"name\");\n";
			}
		}
		if ($__in['__is_form_submitted']) {
			if ($gen->add_relation ($__in['relation']['field_name'], $__in['relation']['text'])) {
				return $this->redirect("relation", "added_successfully");
			}
		}
		return true;
	}
//-----------------------------------------------------
	function delete_relation () {
		global $__in, $__out;
		$gen = new generator();
		if ($gen->delete_relation($__in['field'])) {
			return $this->redirect("relation", "deleted_successfully");
		}
		return true;
	}
//-----------------------------------------------------
	function relation () {
		global $__in, $__out;
		$gen = new generator();
		$__out['relations'] = $gen->get_relations_for_output();
		return true;
	}
//-----------------------------------------------------

//-----------------------------------------------------

//-----------------------------------------------------
}
?>
