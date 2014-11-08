<?
/**
@file controller.php
@class controller
This class main function is request that takes the request array from any type of client and responds with the array of response after calling the appropriate subcontroller and action.
*/
require_once(dirname(__FILE__) . "/parent_controller.php");
class controller extends parent_controller
{
//-----------------------------------------------------------------------------
	function controller() {
		$this->parent_controller();		
	}
//--------------------------------------------------------------------------------
	/**
	Initiates a request and returns the output.
	Calls the right controller and calls the function needed from it.
	It uses the global __in array of request params containing the controler and the action and any other parameters.
	It also uses the global __out an array to add to then return in the output of the request.
	@return tru or false and changes the input and output arrays.
	@access Public.
	*/
	function request ()
	{
		$this->before_request();
		global $__in, $__out;
		$__in['controller'] = strtolower($__in['controller']);
		$__in['action'] = strtolower($__in['action']);
		
		//-------------------------------------------------------------
		if (DEBUG == true) {
			if($_GET['noview']=="yes") {
				print "### Input:\n---------------\n";
				print_r($__in);
			}
		}
		//-------------------------------------------------------------
		$__out = array_merge($__out, $__in);
		// include the class file from the same directory of the main controller.
		if (!file_exists(dirname(__FILE__) . "/../controller/" . $__in['controller'] . "_controller.php")) {
			if (DEBUG) {
				trigger_error("Could not find file : " . dirname(__FILE__) . "/../controller/" . $__in['controller'] . "_controller.php", 256);
			} else {
				return $this->redirect(array("controller" => "errors", "action" => "page_not_found"));
			}
		}
		
		require_once(dirname(__FILE__) . "/../controller/" . $__in['controller'] . "_controller.php");
		
		if (!class_exists($__in['controller'] . "_controller")) {
			if (DEBUG) {
				trigger_error("Could not find class : " . $__in['controller'] . "_controller", 256);
			} else {
				return $this->redirect(array("controller" => "errors", "action" => "page_not_found"));
			}
		}
			
		// make a new instance of the ubcontroller needed.
		eval("\$subcontroller = new " . $__in['controller'] . "_controller();");
		
		if (!is_callable(array(&$subcontroller, $__in['action']))) {
			if (DEBUG) {
				trigger_error("Cannot call method " . $__in['controller'] . "_controller::" . $__in['action'] . "().", 256);
			} else {
				return $this->redirect(array("controller"=>"errors", "action"=>"page_not_found"));
			}
		}
		// calling the method.
		call_user_func(array(&$subcontroller, $__in['action']));
		$this->after_request();
		return true;
	}	// end function request().
//------------------------------------------------------------------------------------------------------------------------------
	function before_request () {
	
	}
//------------------------------------------------------------------------------------------------------------------------------	
	function after_request () {
		global $__out;
		if(is_array($__out['project'])){
			if (is_array($_SESSION['project'])) {
				$__out['project'] = array_merge($_SESSION['project'], $__out['project']);
			}
		} else {
			$__out['project'] = $_SESSION['project'];
		}
	}
//------------------------------------------------------------------------------------------------------------------------------
}	// end class controller.
?>
