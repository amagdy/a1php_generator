<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Creates a date time picker
@param name 
@param time 
@param start_year 
@param end_year 
@param format 
@param clear_link_name 
@param show_time 
@param assign 
*/
function smarty_function_date_time_picker ($arg_arr_params, &$smarty) {
	$assign = $arg_arr_params['assign'];
	unset($arg_arr_params['assign']);
	
	$arr_js_params = array();
	if (is_int($arg_arr_params['time'])) {
		$arr_js_params['date'] = date("Y-m-d H:i:s", $arg_arr_params['time']);
	} elseif (preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}(\s[0-9]{2}:[0-9]{2}:[0-9]{2})?$/", $arg_arr_params['time'])) {
		$arr_js_params['date'] = $arg_arr_params['time'];
	}
	
	if ($arg_arr_params['show_time']) $arr_js_params['show_time'] = $arg_arr_params['show_time'];
	
	$clear_link_name = ($arg_arr_params['clear_link_name'] ? $arg_arr_params['clear_link_name'] : "Clear");
	
	$input_field_name = $arg_arr_params['name'];
	$input_field_id = join("_", split("\[", str_replace("]", "", $input_field_name)));
	$button_id = "button_" . $input_field_id;
	
	$arr_js_params['inputField'] = $input_field_id;
	$arr_js_params['button'] = $button_id;
	
	if ($arg_arr_params['format']) $arr_js_params['format'] = $arg_arr_params['format'];
	
	if ($arg_arr_params['start_year']) {
		if (is_int($arg_arr_params['start_year']) && ($arg_arr_params['start_year'] >= 1900 && $arg_arr_params['start_year'] <= 2100)) {
			$arr_js_params['start_year'] = $arg_arr_params['start_year'];
		}
	}
	if ($arg_arr_params['end_year']) {
		if (is_int($arg_arr_params['end_year']) && ($arg_arr_params['end_year'] >= 1900 && $arg_arr_params['end_year'] <= 2100)) {
			$arr_js_params['end_year'] = $arg_arr_params['end_year'];
		}
	}
	
	global $__date_time_pickers_count;
	if (!$__date_time_pickers_count) {
		$output = "<script type=\"text/javascript\" src=\"" . HTML_ROOT . "view/scripts/js/calendar.js\"></script>
<style type=\"text/css\"> @import url(\"" . HTML_ROOT . "view/scripts/js/calendar-system.css\"); </style>";
		$__date_time_pickers_count = 1;
	} else {
		$output = "";
		$__date_time_pickers_count++;
	}
	$output .= "
<input type=\"text\" name=\"" . $input_field_name . "\" id=\"" . $input_field_id . "\" value=\"" . ($arr_js_params['date'] ? $arr_js_params['date'] : "") . "\" readonly=\"readonly\"/>
<input type=\"button\" value=\"...\" id=\"" . $button_id . "\" />
<a href=\"#\" onClick=\"document.getElementById('" . $input_field_id . "').value='';return false;\" class=\"calendar_clear_link\">" . $clear_link_name . "</a>
<script type=\"text/javascript\">
Calendar.setup({
";
	while (list($k, $v) = each($arr_js_params)) {
		if (is_numeric($v)) {
			$output .= "$k : $v,\n";
		} else {
			$output .= "$k : \"$v\",\n";
		}
	}
	$output .= "
align : \"Tr\"
});
</script>
";
	if ($assign) {
		$smarty->assign($assign, $output);
		return "";
	} else {
		return $output;
	}
}
?>
