<?
/**
@file clssmarty.php
@class clssmarty
A class that inherits Smarty (http://smarty.php.net) template class and displays the view of the site.
*/
require_once(SMARTY_DIR . 'Smarty.class.php');
class clssmarty extends Smarty{
	/**
	The constructor
	defines the template directory and some modifiers.
	@access Public.
	*/
	function clssmarty() {
		$this->Smarty();
		$this->template_dir = "view/";
		$this->compile_dir = 'smarty/templates_c/';
		$this->config_dir = 'smarty/configs/';
		$this->cache_dir = 'smarty/cache/';
		$this->caching = false;
        $this->register_modifier("TT", array(&$this, "translate"));
		$this->register_modifier("text_to_html", array(&$this, "text_to_html"));
		$this->register_modifier("safe_rte", array(&$this, "safe_rte"));
	}
//-------------------------------------------------------------
	/**
	safe_rte
	A custom smarty modifier made to cleanup RTE content from Javascripts to protect the site against XSS attacks.
	@param String $arg_rte_text the text to be cleaned up.
	@return String the cleaned value.
	@access Public.
	*/
	function safe_rte ($arg_rte_text)
	{
		$arg_rte_text = str_ireplace("<script", "&lt;script", $arg_rte_text);
		return preg_replace("/(<[^>]+\s+on)([a-z]+)(\s*=[^>]+>)/i", "\\1 \\2\\3", $arg_rte_text);
	}
//-------------------------------------------------------------
	/**
	Translates a language entry key to the current language.
	@param String $arg_str_msg the key to be translated.
	@param Array $arg_arr_params any optional extra parameters to be put in the translated text.
	@return String (Translation).
	@access Public.
	*/
	function translate($arg_str_msg="", $arg_arr_params=array()){
		return vsprintf($arg_str_msg, $arg_arr_params);
	}	// end function translate
//-------------------------------------------------
	/**
	A modifier used when displaying text written by a user in an HTML context to protect the site against XSS attacks. 
	It works by changing all HTML tags to text so that it will not be executed as HTML.
	@param String $arg_str_text the text to be cleaned.
	@return String the cleaned text.
	@access Public.
	*/
	function text_to_html($arg_str_text){	// to get text from DB and put it in an HTML page
	   $arg_str_text = trim(htmlspecialchars($arg_str_text));
	   $arg_str_text = str_replace("\n", "<br/>\n", $arg_str_text);
	   $arg_str_text = str_replace("  ", " &nbsp; ", $arg_str_text);
	   return $arg_str_text;
	}	// end function text_to_html
//-----------------------------------------------------------------------------
	/**
	Decides the file of the layout to be displayed and the helper to be used then displays it and assigns all variables in $__out to smarty variables.
	@access Public.
	*/
	function display_index() {
		global $__out;
		$__out['document'] = $__out['controller'] . "/" . $__out['action'] . ".tpl";		/**< decide the document variable which is the name of the tpl file shown in the center of the current template like login.tpl or register.tpl which is decided by te current language, theme, and action. */
		
		if (DEBUG == true  && $_GET['noview'] == "yes") 		// if there is a view
		{
			print "\n\n\n\n### Output:\n---------------\n";
			print_r($__out);
			print "\n\n\n\n### Error:\n---------------\n";
			print $str_error;
			return;
		}
		$this->assign($__out);
		$this->display("layouts/index.tpl");
	}	
}
?>
