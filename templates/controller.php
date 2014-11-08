<? print "<?php\n"; ?>
/**
@file <?=$project['controller']?>_controller.php
@class <?=$project['controller']?>_controller

@author Ahmed Magdy <a.magdy@a1works.com>
*/
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/<?=$project['model']?>.php");

class <?=$project['controller']?>_controller extends object implements controller
{
	/**
	 * Constructor
	 * @global array $__out
	 */
	public function __construct ()
	{
		$this->object();
<?
		if (is_array($project['relation'])) {
			?>

		global $__out;
<?
			while (list($field, $relation) = each($project['relation'])) {
				?>
		<?=$relation?>

<?
			}
			reset($project['relation']);
		}
?>
	}
//------------------------------------------------------------------------------------------------------	
	/**
	 * Adds a new <?=$project['model']?>
         * @global array $__in
	 * @global array $__out
	 * @return boolean
         * @throws Exception
	 */
	public function add ()
	{
		global $__in, $__out;
		if ($__in['__is_form_submitted']) {		// if form is submitted
			try {
                                $<?=$project['model']?> = new <?=$project['model']?>();
				$<?=$project['model']?>->add($__in['<?=$project['model']?>']);
				return dispatcher::redirect("getall", "added_successfully");
			} catch (ValidationException $ex) {
				$ex->publish_errors();
				$__out['<?=$project['model']?>'] = $__in['<?=$project['model']?>'];
				return false;
			} catch (Exception $ex) {
				throw $ex;
			}
		} else {	// if form is not submitted
			$__out['<?=$project['model']?>'] = array();
			return true;
		}	// end if form submitted
	}	// end function add
//------------------------------------------------------------------------------------------------------	
	/**
	 * Edits an existing <?=$project['model']?>
         * @global array $__in
	 * @global array $__out
	 * @return boolean
         * @throws Exception
	 */
	public function edit ()
	{
		global $__in, $__out;
                if ($__in['__is_form_submitted']) {		// if form is submitted
			try {
				$<?=$project['model']?> = new <?=$project['model']?>($__in['<?=$project['primary_key']?>']);
				$<?=$project['model']?>->edit($__in['<?=$project['model']?>']);
				return dispatcher::redirect("getall", "updated_successfully");
			} catch (ValidationException $ex) {
				$ex->publish_errors();
				$__out['<?=$project['model']?>'] = $__in['<?=$project['model']?>'];
				$__out['<?=$project['model']?>']['<?=$project['primary_key']?>'] = $__in['<?=$project['primary_key']?>'];
			} catch (Exception $ex) {
				throw $ex;
			}
                } else {	// if form is not submitted
			$<?=$project['model']?> = new <?=$project['model']?>($__in['<?=$project['primary_key']?>']);
			$__out['<?=$project['model']?>'] = $<?=$project['model']?>->this_to_array();
                }	// end if form submitted
                return true;
	}	// end action edit
//------------------------------------------------------------------------------------------------------	
	/**
	 * Deletes one <?=$project['model']?>
         * @global array $__in
	 * @global array $__out
	 * @return boolean
         * @throws Exception
	 */
	public function delete ()
        {
		global $__in, $__out;
		try {
			$<?=$project['model']?> = new <?=$project['model']?>();
			$<?=$project['model']?>->delete($__in['<?=$project['primary_key']?>']);
			return dispatcher::redirect("getall", "deleted_successfully");
		} catch (ValidationException $ex) {
			$ex->publish_errors();
			return dispatcher::redirect("getall");
		} catch (Exception $ex) {
			throw $ex;
		}
	}	// end action delete
//------------------------------------------------------------------------------------------------------	
	/**
	 * Deletes many <?=$project['table']?>.
	 * @global array $__in
	 * @global array $__out
	 * @return boolean
	 */
	public function delete_many ()
	{
		global $__in, $__out;
		try {
			$<?=$project['model']?> = new <?=$project['model']?>();
			$<?=$project['model']?>->delete_many($__in['arr_<?=$project['primary_key']?>s']);
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} catch (ValidationException $ex) {
			$ex->publish_errors();
			return dispatcher::redirect(array("action"=>"getall"));
		} catch (Exception $ex) {
			throw $ex;
		}
	}	// end action delete_many	
//------------------------------------------------------------------------------------------------------
	/**
	 * Gets all <?=$project['table']?>
         * @global array $__in
	 * @global array $__out
	 * @return boolean
         * @throws Exception
	 */
	public function getall ()
	{
		global $__in, $__out;
		try {
			$<?=$project['model']?> = new <?=$project['model']?>();
			$<?=$project['model']?>->set_paging(25);
			$__out['arr_<?=$project['table']?>'] = $<?=$project['model']?>->get_all($__in['__orderby'], ($__in['__desc'] == 'yes' ? false : true));
                        $__out['<?=$project['model']?>'] = $<?=$project['model']?>->this_to_array();
		} catch (ValidationException $ex) {
			$ex->publish_errors();
		} catch (Exception $ex) {
			throw $ex;
		}
		return true;
	}	// end action getall
//------------------------------------------------------------------------------------------------------
	/**
	 * Gets one <?=$project['model']?>.
         * @global array $__in
	 * @global array $__out
	 * @return boolean
         * @throws Exception
	 */
	public function getone () 
	{
		global $__in, $__out;
		try {
			$<?=$project['model']?> = new <?=$project['model']?>($__in['<?=$project['primary_key']?>']);
			$__out['<?=$project['model']?>'] = $<?=$project['model']?>->this_to_array();
		} catch (ValidationException $ex) {
			$ex->publish_errors();
		} catch (Exception $ex) {
			throw $ex;
		}
		return true;
	}
//------------------------------------------------------------------------------------------------------
	/**
	 * Searches the database and returns the results in the same form as the getall form.
         * @global array $__in
	 * @global array $__out
	 * @return boolean
	 */
	public function search () 
	{
		global $__in, $__out;
		try {
			$<?=$project['model']?> = new <?=$project['model']?>();
			$<?=$project['model']?>->set_paging(25);
			$__out['arr_<?=$project['table']?>'] = $<?=$project['model']?>->__search($__in['<?=$project['model']?>_search'], $__in['__orderby'], ($__in['__desc'] == 'yes' ? false : true));
		} catch (ValidationException $ex) {
			$ex->publish_errors();
		} catch (Exception $ex) {
			throw $ex;
		}
		$__out['<?=$project['model']?>_search_link'] = array("<?=$project['model']?>_search" => $__in['<?=$project['model']?>_search']);
		$__out['<?=$project['model']?>'] = $<?=$project['model']?>->this_to_array();
		return true;
	}
//------------------------------------------------------------------------------------------------------
	/**
	 * Redirects to getall.
	 * @return boolean
	 */
	public function index ()
	{
		return dispatcher::redirect("getall");
	}
//------------------------------------------------------------------------------------------------------
}	// end class <?=$project['controller']?>_controller


