<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Assigns the content block to a template variable
@param assign the variable name which the content will be assigned to (MUST).
*/
function smarty_block_block_assign($params, $content, &$smarty)
{
	$output = "";
	$assign = $params['assign'];
	unset($params['assign']);
	if (!$assign) {
		trigger_error("block_assign: You must specify the assign parameter.", 256);
		return;
	}
	$smarty->assign($assign, $content);
}
?>
