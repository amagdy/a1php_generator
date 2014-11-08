<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Creates a validator span for a certain field name
It searches the $__error array for any error that has the same fieldname and display it.
@param field_name the name of the form field to be validated.
@return the span to show in place of the validator tag.
*/
function smarty_function_error_validator ($arg_arr_params, &$smarty) {
	global $__errors;
	if (!$arg_arr_params['field_name']) return "";
	$output = "<span id=\"" . $arg_arr_params['field_name'] . "_validator\" class=\"error_validator\"></span>";
	reset($__errors);
	if (is_array($__errors)) {
		while (list(,$entry) = each($__errors)) {
			if ($entry['field_name'] == $arg_arr_params['field_name']) {
				$output = "<span id=\"" . $arg_arr_params['field_name'] . "_validator\" class=\"error_validator\">" . $smarty->translate($entry['error_msg']) . "</span>";
			}
		}
	}
	reset($__errors);
	return $output;
}
?>
