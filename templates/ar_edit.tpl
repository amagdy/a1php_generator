{make_link controller="<?=$project['controller']?>" action="edit" <?=$project['primary_key']?>=$<?=$project['model']?>.<?=$project['primary_key']?> assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4" dir="rtl">
<?
if (is_array($project['edit_fields'])) {
	while (list(, $field) = each($project['edit_fields'])) {
?>
  <tr>
    <td><?=$project['arr_fields_names'][$field]['ar_friendly_name']?></td>
    <td>
<?
if ($project['arr_fields_names'][$field]['control'] == "text") {
?>
	<input type="text" name="<?=$project['model']?>[<?=$field?>]" id="<?=$project['model']?>_<?=$field?>" value="{$<?=$project['model']?>.<?=$field?>}"/>
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "password") {
?>
	<input type="password" name="<?=$project['model']?>[<?=$field?>]" id="<?=$project['model']?>_<?=$field?>" value="{$<?=$project['model']?>.<?=$field?>}"/>
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "textarea") {
?>
	<textarea name="<?=$project['model']?>[<?=$field?>]" id="<?=$project['model']?>_<?=$field?>">{$<?=$project['model']?>.<?=$field?>}</textarea>
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "rte") {
?>
	{rte name="<?=$project['model']?>[<?=$field?>]"}{$<?=$project['model']?>.<?=$field?>}{/rte}
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "select") {
?>
	<select name="<?=$project['model']?>[<?=$field?>]" id="<?=$project['model']?>_<?=$field?>">
		{html_options options=$arr_<?=$field?> selected=$<?=$project['model']?>.<?=$field?>}
	</select>
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "checkbox") {
?>
	<input type="checkbox" name="<?=$project['model']?>[<?=$field?>]" id="<?=$project['model']?>_<?=$field?>" value="1"{if $<?=$project['model']?>.<?=$field?>} checked="checked"{/if}/>
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "radio") {
?>
	{html_radios name="<?=$project['model']?>[<?=$field?>]" options=$arr_<?=$field?> selected=$<?=$project['model']?>.<?=$field?>}
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "file") {
?>
	{file uniq=true field="<?=$project['model']?>[<?=$field?>]" value=$<?=$project['model']?>.<?=$field?>}
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "pic") {
?>
	{file image=true uniq=true field="<?=$project['model']?>[<?=$field?>]" value=$<?=$project['model']?>.<?=$field?>}
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "date") {
?>
	{date_time_picker name="<?=$project['model']?>[<?=$field?>]" time=$<?=$project['model']?>.<?=$field?> clear_link_name="Clear" show_time=false}
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "time") {
?>
	{time_picker name="<?=$project['model']?>[<?=$field?>]" time=$<?=$project['model']?>.<?=$field?>}
<?
} elseif ($project['arr_fields_names'][$field]['control'] == "datetime") {
?>
	{date_time_picker name="<?=$project['model']?>[<?=$field?>]" time=$<?=$project['model']?>.<?=$field?> clear_link_name="Clear" show_time=true}
<?
}
?>
	{error_validator field_name="<?=$field?>"}
    </td>
  </tr>
<?
	}
	reset($project['edit_fields']);
}
?>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  حفظ  "/></td>
  </tr>
</table>
{/form}
