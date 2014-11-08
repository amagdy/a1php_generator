<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Creates a link from an array of parameters
@param params is the array of parameters passed through the smarty tag. The params can contain assoicative arrays that would be added to the parameters.
@return the link uri.
*/
function smarty_function_make_link ($arg_arr_params, &$smarty) {
		if ($arg_arr_params['t']) {
			$arg_arr_params['t'] = microtime();
		}
		$assign = $arg_arr_params['assign'];
		unset($arg_arr_params['assign']);
		if (!is_array($arg_arr_params)) return HTML_ROOT;
		while (list($k, $v) = each($arg_arr_params)) {
			if (is_array($v)) {
				while (list($sub_key, $sub_value) = each($v)) {
					$arg_arr_params[$sub_key] = $sub_value;
				}
				unset($arg_arr_params[$k]);
			}
		}
		reset ($arg_arr_params);
		$str_link = HTML_ROOT . ($arg_arr_params['controller'] ? $arg_arr_params['controller'] . "/" : "") . ($arg_arr_params['action'] ? $arg_arr_params['action'] . "/" : "") . (isset($arg_arr_params['id']) ? $arg_arr_params['id'] . ".html" : "");
		$arr_uri = array();
		while (list($k, $v) = each($arg_arr_params)) {
			if ($k != "controller" && $k != "action" && $k != "id") {
				$arr_uri[] = urlencode($k) . "=" . urlencode($v);
			}
		}
		$str_link .= ($arr_uri ? "?" . join("&", $arr_uri) : "");
		if ($assign) {
			$smarty->assign($assign, $str_link);
			return "";
		} else {
			return $str_link;
		}
		
}
?>
