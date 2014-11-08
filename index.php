<?
require_once("config.php");		/**< include the site configuration that is edited by the coder. */
require_once("lib/inc.php");
set_error_handler('error_handler');		/**< set the custom error handler function . */

session_start();

if (get_magic_quotes_gpc()) {
	print "<h1>Please make sure \"magic_quotes_gpc = Off\" in your php.ini file and restart your web server</h1>";
}
require_once("lib/router.php");
require_once("lib/controller.php");
require_once(PHP_ROOT . "lib/inflector.php");

$__controller = new controller();
$__controller->request();		// handle request

$__out['__errors'] = $__errors;		/**< Add the $__errors array to the array of output ($__out) to be shown on the template when displayed. */
$__out['__info'] = $__info;			/**< Add the $__info array to the array of output ($__out) to be shown on the template when displayed. */

//-------------------------------------------------------------
// view handling
require_once("lib/clssmarty.php");
$template = new clssmarty();
$template->display_index();		/**< assign the $__out variables to the template and display the main tpl of the current language and current theme and current group. */
?>
