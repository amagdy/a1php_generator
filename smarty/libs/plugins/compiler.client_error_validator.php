<?php
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Creates a client side onblur validator for the field with the specified id.
@author Ahmed Magdy <admin@a1works.com>.
@param field_name the name of the form field to be validated (username, password, ....).
@param field_id the html tag id that if specified client side validation is enabled (user_username, user_password, ...).
@param validation the way of validation (email, username, number, int, required).
@param format the format used to validate a string.
@param min the minimum value of the integer to be validated.
@param max the maximum value of the integer to be validated.
@param error_text the error text message that is written if there is any error.
@return the span to show in place of the validator tag.
*/
function smarty_compiler_client_error_validator($tag_attrs, &$compiler)
{
	$_params = $compiler->_parse_attrs($tag_attrs);
	
	if (!isset($_params['field_id'])) {
		$compiler->_syntax_error("client_error_validator: missing 'field_id' parameter", E_USER_WARNING);
	        return;
	}
	if (!isset($_params['error_text'])) {
		$compiler->_syntax_error("client_error_validator: missing 'error_text' parameter", E_USER_WARNING);
	        return;
	}
        $arr_validations = array("'email'", "'username'", "'number'", "'int'", "'required'");
        if (isset($_params['validation'])) {
                if (!in_array($_params['validation'], $arr_validations)) {
                        $compiler->_syntax_error("client_error_validator: Invalid value for the 'validation' parameter it should be any of these (email, username, number, int, required)", E_USER_WARNING);
	                return;
                }
        }
	if ((!isset($_params['validation'])) && (!isset($_params['format']))) {
		$compiler->_syntax_error("client_error_validator: You must specify the 'validation' parameter or the 'format' parameter.", E_USER_WARNING);
	        return;
	}
	$output = "<span id=\"<?=$_params[field_name]?>_validator\" class=\"error_validator\"></span>";
	$output .= "
<script language='javascript'>

	document.getElementById('<?=$_params[field_id]?>').onblur = function () {
		var field = document.getElementById('<?=$_params[field_id]?>');
		var validator = document.getElementById('<?=$_params[field_name]?>_validator');
		validator.innerHTML = \"\";
		";

	if ($_params['validation'] == "'numeric'" || $_params['validation'] == "'int'") if ($_params['min']) $output .= "
		if (field.value < <?=$_params[min]?>) {
			validator.innerHTML = \"<?=$_params[error_text]?>\";
			return false;
		}
	";
	if ($_params['validation'] == "'numeric'" || $_params['validation'] == "'int'") if ($_params['max']) $output .= "
		if (field.value > <?=$_params[max]?>) {
			validator.innerHTML = \"<?=$_params[error_text]?>\";
			return false;
		}
	";

	if ($_params['validation'] == "'email'") {
		$output .= "
		if (/^[\w\.-]{2,}@[\w\.-]{2,}\.[\w\.]{2,}\$/.test(field.value)) {
			return true;
		} else {
			validator.innerHTML = \"<?=$_params[error_text]?>\";
			return false;
		}
		";
	} elseif ($_params['validation'] == "'username'") {
		$output .= "
		if (/^[\w]{3,50}\$/.test(field.value)) {
			return true;
		} else {
			validator.innerHTML = \"<?=$_params[error_text]?>\";
			return false;
		}
		";
	} elseif ($_params['validation'] == "'number'") {
		$output .= "
		if (/^[\d]+(\.[\d]+)?\$/.test(field.value)) {
			return true;
		} else {
			validator.innerHTML = \"<?=$_params[error_text]?>\";
			return false;
		}
		";

	} elseif ($_params['validation'] == "'int'") {
		$output .= "
		if (/^[\d]+\$/.test(field.value)) {
			return true;
		} else {
			validator.innerHTML = \"<?=$_params[error_text]?>\";
			return false;
		}
		";

	} elseif ($_params['validation'] == "'required'") {
		$output .= "
		if (field.value) {
			return true;
		} else {
			validator.innerHTML = \"<?=$_params[error_text]?>\";
			return false;
		}
		";

	} else {
		if ($_params['format']) {
			$output .= "
		if (/<?=$_params[format]?>/.test(field.value)) {
			return true;
		} else {
			validator.innerHTML = \"<?=$_params[error_text]?>\";
			return false;
		}
		";
		}
	}
	
	$output .= "
	};
	

</script>
	";


    return "?>" . $output . "<?";
}

/* vim: set expandtab: */

?>
