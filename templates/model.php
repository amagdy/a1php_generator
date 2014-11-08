<? print "<?\n"; ?>
/**
@file <?=$project['model']?>.php
@class <?=$project['model']?>

@author Ahmed Magdy <a.magdy@a1works.com>
*/
require_once(PHP_ROOT . "lib/parent_model.php");
class <?=$project['model']?> extends parent_model 
{
//------------------------------------------------------------------------------
	/**
	Constructor
	@param $arg_<?=$project['primary_key']?> [optional] default 0 primary key used to fetch a row from the database and wrap it into an object if it is not specified an empty object is returned
	*/
	public function __construct ($arg_<?=$project['primary_key']?>=0) 
	{
		$this->parent_model();
		$this->__table = "<?=$project['table']?>";
		$this->__primary_key = "<?=$project['primary_key']?>";
		$this->__allowed_fields = array("<?=join('", "', array_keys($project['arr_fields_names']))?>");
<?
if (is_array($project['validation']) && $project['validation']) {
?>

		// validation
<?
	// loop on fields
	while (list($field, $arr_validations) = each($project['validation'])) {
		// first loop presence
		while (list(, $validation) = each($arr_validations)) {
			$error = Inflector::underscore($validation['en_msg']);
			if ($validation['type'] == "presence") {
				?>
		$this->add_validation("<?=$field?>", "presence", "<?=$error?>");
<?
			}
		}
		reset($arr_validations);
		
		// second loop not uniq
		while (list(, $validation) = each($arr_validations)) {
			$error = Inflector::underscore($validation['en_msg']);
			if ($validation['type'] != "uniq" && $validation['type'] != "presence") {
				if ($validation['type'] == "format") {
					?>
		$this->add_validation("<?=$field?>", "format", "<?=$error?>", "", "<?=$validation['format']?>");
<?
				}elseif ($validation['type'] == "function") {
					?>
		$this->add_validation("<?=$field?>", "function", "<?=$error?>", "<?=$validation['function']?>");
<?
				} else {
					?>
		$this->add_validation("<?=$field?>", "<?=$validation['type']?>", "<?=$error?>");
<?
				}
			}
		}
		reset($arr_validations);
		
		// third loop uniq
		while (list(, $validation) = each($arr_validations)) {
			if ($validation['type'] == "uniq") {
				?>
		$this->add_validation("<?=$field?>", "uniq", "<?=$error?>");
<?
			}
		}
		reset($arr_validations);
	}
	reset($project['validation']);
}
if (is_array($project['search']) && $project['search']) {
	?>
		
		// search criteria
<?
	while (list($field, $search_operators) = each($project['search'])) {
		if (is_array($search_operators)) {
			while (list(, $operator) = each($search_operators)) {
				?>
		$this->__add_search("<?=$field?>", "<?=$operator?>");
<?
			}
			reset($search_operators);
		}
	}
	reset($project['search']);
}
?>
		if ($arg_<?=$project['primary_key']?>) $this->array_to_this($this->get_one_by_<?=$project['primary_key']?>($arg_<?=$project['primary_key']?>), true);
		
	}	// end constructor
//------------------------------------------------------------------------------
	/**
	Adds a new <?=$project['model']?> and returns the insert id of the inserted record
	@param array $arr_properties the array of all properties to be added
	@return long
        @throws ValidationException
	*/
	public function add (array $arr_properties) 
	{
		$arr_properties = $this->filter_allowed_fields($arr_properties);
		$arr_properties = $this->filter_required_fields($arr_properties);
		$this->array_to_this($arr_properties);

<?
		if (is_array($project['arr_defaults'])) {
			while (list($field, $default) = each($project['arr_defaults'])) {
				if (isset($default) && $default != "") {
					?>
		$this-><?=$field?> = <?=$default?>;
<?
				}
			}
			reset($project['arr_defaults']);
		}
		?>

		if ($this->is_error()) throw new ValidationException($this);
		return $this->__save();
	}
//------------------------------------------------------------------------------
	/**
	Edit <?=$project['model']?> information and returns true if it was updated successfully.
	@param array $arr_properties the array of all properties to be updated
	@return boolean
        @throws Exception
        @throws ValidationException
	*/
	public function edit (array $arr_properties) 
	{
		if (!$this->is_id($this-><?=$project['primary_key']?>)) throw new Exception("Invalid <?=$project['primary_key']?>");
		$arr_properties = $this->filter_allowed_fields($arr_properties);
		$arr_properties = $this->filter_required_fields($arr_properties);
		$this->array_to_this($arr_properties);
		if ($this->is_error()) throw new ValidationException($this);
		return $this->__save();
	}
//------------------------------------------------------------------------------	
//------------------------------------------------------------------------------	
//------------------------------------------------------------------------------	
//------------------------------------------------------------------------------	
//------------------------------------------------------------------------------
	/**
	 * Returns all records in the current table
	 * @param String $arg_order_by_field
	 * @param String $arg_ascendingly
	 * @return array
	 *
	public function get_all ($arg_order_by_field='', $arg_ascendingly=true) {
		return $this->get_all_by(array(), $this->__table, $arg_order_by_field, $arg_ascendingly);
	}
	*/
//------------------------------------------------------------------------------
	/**
	 * Returns an array containing the database record that has this <?=$project['primary_key']?>
	 * @param long $arg_<?=$project['primary_key']?> the <?=$project['primary_key']?> of the record to get
	 * @return array the record of database table
	 * @throws Exception
	 * @throws ValidationException
	 *
	public function get_one_by_<?=$project['primary_key']?> ($arg_<?=$project['primary_key']?>) {
		return $this->get_one_row_by(array('<?=$project['primary_key']?>' => $arg_<?=$project['primary_key']?>));
	}
	*/
//------------------------------------------------------------------------------
	/**
	Deletes a <?=$project['model']?> from the <?=$project['table']?> table.
	This object must be initialized and must have a value in the $this-><?=$project['primary_key']?> field.
	@return boolean
	@throws Exception
        *
	public function delete($arg_<?=$project['primary_key']?>) 
	{
		if (!$this->is_id($arg_<?=$project['primary_key']?>)) throw new Exception("Invalid <?=$project['primary_key']?>");
		return $this->db_delete("DELETE FROM <?=$project['table']?> WHERE <?=$project['primary_key']?>='%s'", array($arg_<?=$project['primary_key']?>));
	}
        */
//------------------------------------------------------------------------------
	/**
	Deletes many <?=$project['table']?>.	
	@param arg_arr_<?=$project['primary_key']?>s	array of <?=$project['model']?> <?=$project['primary_key']?>s to be deleted.	
	@return true on success or false on failure if the the array if <?=$project['primary_key']?>s is not correct
        *
	public function delete_many (array $arg_arr_<?=$project['primary_key']?>s) 
	{
                if (!$arg_arr_<?=$project['primary_key']?>s) {
			$this->add_validation_error("no_elements_selected");
			throw new ValidationException($this);
		}
                while (list (, $<?=$project['primary_key']?>) = each ($arg_arr_<?=$project['primary_key']?>s)) {
			try {
				$this->delete($<?=$project['primary_key']?>);
			} catch (Exception $ex) {}
		}
		return true;
	}
        */
//------------------------------------------------------------------------------
}		// end class <?=$project['model']?>.

