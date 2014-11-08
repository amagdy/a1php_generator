<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Creates an HTML border according to the design specified by the designer

@param assign assigns the border html to a template variable
@param title text title
@param width 100% by default
@param height none by default
*/
function smarty_block_border($params, $content, &$smarty)
{
	$output = "";
	$assign = $params['assign'];
	unset($params['assign']);
	$width = ($params['width'] ? $params['width']: "100%");
	$height = ($params['height'] ? " height=\"" . $params['height'] . "\"" : "");
	
	$output = "<table$height width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	$output .= "	<tr>";
	$output .= "		<td colspan=\"3\">";
	$output .= "			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	$output .= "				<tr>";
	$output .= "					<td class=\"table_title_left\"></td>";
	$output .= "					<td colspan=\"3\" class=\"table_title_middle\" align=\"center\">";
	$output .= $params['title'];
	$output .= "					</td>";
	$output .= "					<td class=\"table_title_right\"></td>";
	$output .= "				</tr>";
	$output .= "			</table>";
	$output .= "		</td>";
	$output .= "	</tr>";
	$output .= "	<tr>";
	$output .= "		<td class=\"td_border\">";
	$output .= $content;
	$output .= "		</td>";
	$output .= "	</tr>";
	$output .= "</table>";	

	if ($assign) {
		$smarty->assign($assign, $output);
		return "";
	} else {
		return $output;
	}
}
?>
