<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Creates a time_picker field
must have a name parameter and can have any other attribute that can be added to the text input tag
@param name 
@param time   timestamp or time data type from DB 00:00:00
*/
function smarty_function_time_picker ($arg_arr_params, &$smarty) {
	$name = $arg_arr_params['name'];
	if (!$name) {
		$smarty->trigger_error("time_picker: must supply the name attribute");
		return "";
	}
	$time = $arg_arr_params['time'];
	unset($arg_arr_params['time']);
	if (!$time) {
		$time = "00:00:00";
	} elseif (is_int($time)) {
		$time = date("H:i:s", $time);
	} elseif (eregi("^[0-9]{2}:[0-9]{2}:[0-9]{2}$", $time)) {
		// $time is as it is
	} else {
		$time = "00:00:00";
	}
	$arg_arr_params['value'] = $time;
	
	$id = str_replace("[", "_", str_replace("]", "", $name));
	$arg_arr_params['id'] = $id;
	
	global $__TIME_PICKER_APPEARANCE_TIMES;
	if (!$__TIME_PICKER_APPEARANCE_TIMES) {
		$__TIME_PICKER_APPEARANCE_TIMES = 1;
		$output = "<script src='" . HTML_ROOT . "view/scripts/js/timepicker.js'></script>";
	} else {
		$__TIME_PICKER_APPEARANCE_TIMES++;
		$output = "";
	}
	$output .= "<input type='text' autocomplete='off'";
	while (list($k, $v) = each($arg_arr_params)) {
		$output .= " $k=\"$v\"";
	}
	$output .= " />";
	$output .= "<script>timepicker('$id');</script>";
	return $output;
}
?>
