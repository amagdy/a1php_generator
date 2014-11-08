<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
multiplies the numbers in the parameters and prints the output or assigns it to a variable.
@param all numbers that are needed to be multiplied
*/
function smarty_function_times($arg_arr_params){
	$assign = $arg_arr_params['assign'];
	unset($arg_arr_params['assign']);
	$return_value = 1;
	if (is_array($arg_arr_params)) {
		while (list(,$v) = each($arg_arr_params)) {
			$return_value *= $v;
		}
	}
	if ($assign) {
		$smarty->assign($assign, $return_value);
		return "";
	} else {
		return $return_value;
	}
}
?>
