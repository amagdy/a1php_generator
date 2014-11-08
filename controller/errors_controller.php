<?
/**
@file errors_controller.php
*/
require_once(dirname(__FILE__) . "/../lib/parent_controller.php");
class errors_controller extends parent_controller
{
//-----------------------------------------------------
	function home_controller () {
		$this->parent_controller();
	}
//-----------------------------------------------------
	function permission_denied () {
		return true;
	}
//-----------------------------------------------------
	function page_not_found () {
		return true;
	}
}
?>
