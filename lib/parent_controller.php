<?
/**
This class is the parent of all controller classes
@class parent_controller.
*/
require_once(dirname(__FILE__) . "/object.php");
class parent_controller extends object{
	/**
	parent_controller 
	*/
	function parent_controller () {
		$this->object();
	}
//---------------------------------------------------
	/**
	Redirects the browser to another page.
	Shows a language specific message and redirects to the given url.
	@param arg_str_url the url to redirect to.
	@access Public.
	*/
	function go_to_url ($arg_str_url="") {
		if(!$arg_str_url) return false;
		header("Location: " . $arg_str_url);
		exit();
	}
//----------------------------------------------------
	/**
	Reorder the $_FILES array.
	*/
	function reordered_FILES() {
		$arr_files = array();
		if (is_array($_FILES)) {
			while (list($k1, $v1) = each($_FILES)) {
				if (is_array($v1)) {
					while (list($k2, $v2) = each($v1)) {
						if (is_array($v2)) {
							while (list($k3, $v3) = each($v2)) {
								$arr_files[$k1][$k3][$k2] = $v3;
							}
						}
					}
				}
			}
		}
		return $arr_files;
	}
//---------------------------------------------------
	/**
	Redirects to the given request and Adds an information message to the global array of information $__info.
	@param arg_arr_request the array that contains the request parameteres (Controller, action ...).
	@param arg_str_info_msg [Optional] the message that is shown to the user.
	@param arg_arr_info_params [Optional] any extra parameters added to the info message if the message text has placeholders (Like %s or %d).
	@param arg_type [Optional] The type of the message that can be ("info" or "warning") which decides the style of the information shown.
	@return the output of the new request.
	*/
	function redirect ($arg_arr_request, $arg_str_info_msg="", $arg_arr_info_params=array(), $arg_type="info") {
		global $__in, $__controller;
		if (!is_array($arg_arr_request)) {
			$arr_req = split("/", $arg_arr_request);
			$arg_arr_request = array();
			if (count($arr_req) == 1) {
				$arg_arr_request['action'] = $arr_req[0];
			} else if (count($arr_req) == 2) {
				$arg_arr_request['controller'] = $arr_req[0];
				$arg_arr_request['action'] = $arr_req[1];
			}
		}
		if ($arg_str_info_msg) add_info($arg_str_info_msg, $arg_arr_info_params, $arg_type);
		if (!$arg_arr_request['controller']) $arg_arr_request['controller'] = $__in['controller'];
		$_SESSION['__GET_REPITITION_STOPPER_OLD'] = $__in;
		$_SESSION['__GET_REPITITION_STOPPER_NEW'] = $arg_arr_request;
		$__in = $arg_arr_request;
		return $__controller->request();
	}
}
?>
