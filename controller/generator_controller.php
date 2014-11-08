<?
/**
@file generator_controller.php
*/
require_once(PHP_ROOT . "lib/parent_controller.php");
require_once(PHP_ROOT . "lib/parent_model.php");
require_once(PHP_ROOT . "lib/CreateZipFile.inc.php");
require_once(PHP_ROOT . "model/generator.php");
class generator_controller extends parent_controller
{
//-----------------------------------------------------
	function generator_controller () {
		global $__out;
		$this->parent_controller();
		if (!$_SESSION['project']) $this->go_to_url(HTML_ROOT);
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
	function main () {
	
	}
//-----------------------------------------------------
	function controller () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="' . $project['controller'] . '_controller.php"');
		require_once(PHP_ROOT . "templates/controller.php");
		exit();
		return true;
	}
//-----------------------------------------------------			
	function model () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="' . $project['model'] . '.php"');
		require_once(PHP_ROOT . "templates/model.php");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_en_add () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="add.tpl"');
		require_once(PHP_ROOT . "templates/en_add.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_ar_add () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="add.tpl"');
		require_once(PHP_ROOT . "templates/ar_add.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_en_edit () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="edit.tpl"');
		require_once(PHP_ROOT . "templates/en_edit.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_ar_edit () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="edit.tpl"');
		require_once(PHP_ROOT . "templates/ar_edit.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_en_getall () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="getall.tpl"');
		require_once(PHP_ROOT . "templates/en_getall.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_ar_getall () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="getall.tpl"');
		require_once(PHP_ROOT . "templates/ar_getall.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_en_search () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="search.tpl"');
		require_once(PHP_ROOT . "templates/en_search.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_ar_search () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="search.tpl"');
		require_once(PHP_ROOT . "templates/ar_search.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_en_getone () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="getone.tpl"');
		require_once(PHP_ROOT . "templates/en_getone.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------
	function view_ar_getone () {
		global $__in, $__out;
		$project = $_SESSION['project'];
		header('Content-Disposition: attachment; filename="getone.tpl"');
		require_once(PHP_ROOT . "templates/ar_getone.tpl");
		exit();
		return true;
	}
//-----------------------------------------------------	
	function all () {
		global $__in, $__out;
		$parent_model = new parent_model();
		$project = $_SESSION['project'];
		$parent_model->rmdir_r(PHP_ROOT . "uploads/code/");
		$parent_model->mkdir_r(PHP_ROOT . "uploads/code/view/en/default/" . $project['controller']);
		$parent_model->mkdir_r(PHP_ROOT . "uploads/code/view/ar/default/" . $project['controller']);
		$parent_model->mkdir_r(PHP_ROOT . "uploads/code/uploads/" . $project['model']);
		$parent_model->mkdir_r(PHP_ROOT . "uploads/code/controller/");
		$parent_model->mkdir_r(PHP_ROOT . "uploads/code/model/");
		
		ob_start();
		require_once(PHP_ROOT . "templates/controller.php");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/controller/" . $project['controller'] . "_controller.php");
		
		require_once(PHP_ROOT . "templates/model.php");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/model/" . $project['model'] . ".php");
				
		require_once(PHP_ROOT . "templates/en_add.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/en/default/" . $project['controller'] . "/add.tpl");
		
		require_once(PHP_ROOT . "templates/ar_add.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/ar/default/" . $project['controller'] . "/add.tpl");
		
		require_once(PHP_ROOT . "templates/en_edit.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/en/default/" . $project['controller'] . "/edit.tpl");
		
		require_once(PHP_ROOT . "templates/ar_edit.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/ar/default/" . $project['controller'] . "/edit.tpl");
		
		require_once(PHP_ROOT . "templates/en_getall.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/en/default/" . $project['controller'] . "/getall.tpl");
		
		require_once(PHP_ROOT . "templates/ar_getall.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/ar/default/" . $project['controller'] . "/getall.tpl");
		
		require_once(PHP_ROOT . "templates/en_search.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/en/default/" . $project['controller'] . "/search.tpl");
		
		require_once(PHP_ROOT . "templates/ar_search.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/ar/default/" . $project['controller'] . "/search.tpl");
		
		require_once(PHP_ROOT . "templates/en_getone.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/en/default/" . $project['controller'] . "/getone.tpl");
				
		require_once(PHP_ROOT . "templates/ar_getone.tpl");
		$text = ob_get_contents();
		ob_clean();
		$parent_model->string_to_file($text, PHP_ROOT . "uploads/code/view/ar/default/" . $project['controller'] . "/getone.tpl");
		
		ob_end_clean();
		
		$arr_validation = $project['validation'];
		$english = array();
		$arabic = array();
		if (is_array($arr_validation)) {
			while (list($field, $field_validations) = each($arr_validation)) {
				//$error = Inflector::underscore($validation['en_msg']);
				if (is_array($field_validations)) {
					while (list(, $validation) = each($field_validations)) {
						$error = Inflector::underscore($validation['en_msg']);
						$english[$error] = $validation['en_msg'];
						$arabic[$error] = $validation['ar_msg'];
					}
					reset($field_validations);
				}
			}
			reset($arr_validation);
		}
		
		$__out['project'] = $project;
		$__out['PHP_ROOT'] = PHP_ROOT;
		$__out['en_error_msgs'] = $english;
		$__out['ar_error_msgs'] = $arabic;
	}
//-----------------------------------------------------
	function download_zip () {
		chdir("uploads/");
		$zip_filename = $_SESSION['project']['table'] . ".zip";
		
		$createZipFile = new CreateZipFile();

		//Code toZip a directory and all its files/subdirectories
		$createZipFile->zipDirectory("code/", "code/");

		$fd = fopen($zip_filename, "wb");
		$out = fwrite($fd, $createZipFile->getZippedfile());
		fclose($fd);
		$createZipFile->forceDownload($zip_filename);
		@unlink($zip_filename);
		chdir("..");
		exit();
	}
//-----------------------------------------------------
}
?>
