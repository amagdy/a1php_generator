<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Creates a form with the default method as post and adds a post repetition timestamp to stop repetition of post.

@param assign assigns the form html to a template variable
@param action sets the form action.
@param any other params can be put as form params
*/
function smarty_block_form($params, $content, &$smarty)
{
	$output = "";
	$assign = $params['assign'];
	unset($params['assign']);

	$output .= "
	<form";
	$params['name'] = ($params['name'] ? $params['name'] : $params['id']);
	$params['id'] = ($params['id'] ? $params['id'] : $params['name']);

	if (!$params['method']) $params['method'] = "post";
        while (list($k, $v) = each($params)) {
            if ($v) $output .= " $k=\"$v\"";
	}
	$output .= ">";
	if($params['method'] == "post") {
		$output .= "\n<input type=\"hidden\" name=\"__POST_REPITITION_STOPPER_TIMESTAMP\" value=\"" . microtime() . "\"/>";
	}
	$output .= "\n<input type=\"hidden\" name=\"__is_form_submitted\" value=\"yes\"/>\n<script>var int_rte_count = 0;</script>";
	$output .= "\n" . $content;
	$output .= "\n</form>";
	if ($assign) {
		$smarty->assign($assign, $output);
		return "";
	} else {
		return $output;
	}
}
?>
