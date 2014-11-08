<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Draws an RTE control on the page
@param name the rte control name
@param width
@param height
@param assign assigns the output text to a template variable
*/
function smarty_block_rte($params, $content, &$smarty)
{
	$output = "";
	$m = array();
	if (preg_match("/\[([a-z0-9_]+)\]$/i", $params['name'], $m)) {
		$params['oldname'] = $params['name'];
		$params['name'] = $m[1];
	}
	$assign = $params['assign'];
	unset($params['assign']);
	if (!$params['width']) $params['width'] = 520;
	if (!$params['height']) $params['height'] = 400;
	$output .= "
		<script language=\"JavaScript\" type=\"text/javascript\" src=\"" . HTML_ROOT . "view/scripts/rte/html2xhtml.js\"></script>
		<script language=\"JavaScript\" type=\"text/javascript\" src=\"" . HTML_ROOT . "view/scripts/rte/richtext_compressed.js\"></script>
		<script language=\"JavaScript\" type=\"text/javascript\">
		<!--
		
		initRTE(\"" . HTML_ROOT . "view/scripts/rte/images/\",  \"" . HTML_ROOT . "view/scripts/rte/\", \"\", false);
		//-->
		</script>
		";

	if($content){
		//returns safe code for preloading in the RTE
		$tmpString = $content;
		
		//convert all types of single quotes
		$tmpString = str_replace(chr(145), chr(39), $tmpString);
		$tmpString = str_replace(chr(146), chr(39), $tmpString);
		$tmpString = str_replace("'", "&#39;", $tmpString);
		
		//convert all types of double quotes
		$tmpString = str_replace(chr(147), chr(34), $tmpString);
		$tmpString = str_replace(chr(148), chr(34), $tmpString);
	//	$tmpString = str_replace("\"", "\"", $tmpString);
		
		//replace carriage returns & line feeds
		$tmpString = str_replace(chr(10), " ", $tmpString);
		$tmpString = str_replace(chr(13), " ", $tmpString);
		
		$content = $tmpString;
	}		// end if arg_text

	$output .= "
		<script language=\"JavaScript\" type=\"text/javascript\">
		<!--
		var " . $params['name'] . " = new richTextEditor('" . $params['name'] . "');
		" . $params['name'] . ".html = '" . $content . "';
		
		" . $params['name'] . ".cmdFormatBlock = true;
		" . $params['name'] . ".cmdFontName = true;
		" . $params['name'] . ".cmdFontSize = true;
		" . $params['name'] . ".cmdIncreaseFontSize = true;
		" . $params['name'] . ".cmdDecreaseFontSize = true;
		
		" . $params['name'] . ".cmdBold = true;
		" . $params['name'] . ".cmdItalic = true;
		" . $params['name'] . ".cmdUnderline = true;
		" . $params['name'] . ".cmdStrikethrough = true;
		" . $params['name'] . ".cmdSuperscript = true;
		" . $params['name'] . ".cmdSubscript = true;
		
		" . $params['name'] . ".cmdJustifyLeft = true;
		" . $params['name'] . ".cmdJustifyCenter = true;
		" . $params['name'] . ".cmdJustifyRight = true;
		" . $params['name'] . ".cmdJustifyFull = true;
		
		" . $params['name'] . ".cmdInsertHorizontalRule = true;
		" . $params['name'] . ".cmdInsertOrderedList = true;
		" . $params['name'] . ".cmdInsertUnorderedList = true;
		
		" . $params['name'] . ".cmdOutdent = true;
		" . $params['name'] . ".cmdIndent = true;
		" . $params['name'] . ".cmdForeColor = true;
		" . $params['name'] . ".cmdHiliteColor = true;
		" . $params['name'] . ".cmdInsertLink = true;
		" . $params['name'] . ".cmdInsertImage = false;
		" . $params['name'] . ".cmdInsertSpecialChars = true;
		" . $params['name'] . ".cmdInsertTable = true;
		" . $params['name'] . ".cmdSpellcheck = true;
		
		" . $params['name'] . ".cmdCut = true;
		" . $params['name'] . ".cmdCopy = true;
		" . $params['name'] . ".cmdPaste = true;
		" . $params['name'] . ".cmdUndo = true;
		" . $params['name'] . ".cmdRedo = true;
		" . $params['name'] . ".cmdRemoveFormat = true;
		" . $params['name'] . ".cmdUnlink = true;
		
		" . $params['name'] . ".toggleSrc = true;
		
		" . $params['name'] . ".build();
		
		if (int_rte_count == 0) {
			addfun(document.getElementById('hdn" . $params['name'] . "').form, function () {updateRTEs();});
			int_rte_count++;
		}";
		if ($params['oldname']) {
			$output .= "
		addfun(document.getElementById('hdn" . $params['name'] . "').form, function () {document.getElementById('hdn" . $params['name'] . "').name='" . $params['oldname'] . "';});
		";
		}
		$output .= "
		//-->
		</script>
		";
	return ($assign ? $smarty->assign($assign, $output) : $output);
}
?>
