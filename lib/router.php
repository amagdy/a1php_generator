<?
/**
Used to define routes within the site.
You can add as many routs as you wish to this function but note their order.
@param the requiest_uri server variable or the link to be parsed.
@return an associative array of request parameters.

reads the url and decides the request of that url.
@param String $arg_str_path the url of the page.
@return Array of current request.
*/
function route ($arg_str_path, $arg_use_friendly_urls=ENABLE_FRIENDLY_URLS) {
	global $__in, $__out;
	$str_url = strtolower(substr($arg_str_path, strlen(HTML_ROOT))); 
	
	// use friendly Links
	if ($arg_use_friendly_urls) {
		$index = strpos($str_url, "?");
		$url = $str_url;
		if ($index > 0)
			$url = substr($str_url, 0, $index);

	}
	
	$m = array();
	if ($str_url == "admin" || $str_url == "admin/") 
		return array("controller" => "user", "action" => "login");		
	if (preg_match("/^([a-z0-9_]+)\/([a-z0-9_]+)\/([0-9]+)\.html.*$/", $str_url, $m)) 
		return array("controller" => $m[1], "action" => $m[2], "id" => (int)$m[3]);
	if (preg_match("/^([a-z0-9_]+)\/([a-z0-9_]+)\/?.*$/", $str_url, $m)) 
		return array("controller" => $m[1], "action" => $m[2]);
	if (preg_match("/^([a-z0-9_]+)\/?.*$/", $str_url, $m)) 
		return array("controller" => $m[1], "action" => "index");

	return array("controller" => "project", "action" => "index");
}	// end function route().

if(empty($_GET))
{
	$_SERVER['QUERY_STRING'] = preg_replace('#^.*\?#','',$_SERVER['REQUEST_URI']);
	parse_str($_SERVER['QUERY_STRING'], $_GET);
}
$__in = array_merge($_COOKIE, $_GET, $_POST);
$__in = array_merge(route($_SERVER['REQUEST_URI']), $__in);
/**< Stop post repetition */
if ($_SESSION['__POST_REPITITION_STOPPER_TIMESTAMP'] && $_SESSION['__POST_REPITITION_STOPPER_TIMESTAMP'] == $_POST['__POST_REPITITION_STOPPER_TIMESTAMP']){
	$__in['action'] = 'index';	
}
if($_POST['__POST_REPITITION_STOPPER_TIMESTAMP']) $_SESSION['__POST_REPITITION_STOPPER_TIMESTAMP'] = $_POST['__POST_REPITITION_STOPPER_TIMESTAMP'];
if ($_GET['t']) {
	if ($_GET['t'] == $_SESSION['t']) {
		$__in['action'] = 'index';
	} else {
		$_SESSION['t'] = $_GET['t'];
	}
}
?>
