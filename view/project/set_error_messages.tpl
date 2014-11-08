<h3>Set Error Messages</h3><br/>
{make_link controller="project" action="set_error_messages" assign="form_action"}
{form action=$form_action}
<table border="1" id="tbl">
	<tr>
		<td>
			Field Name
		</td>
		<td>
			Validation Type
		</td>
		<td>
			Validation Value
		</td>
		<td>
			English Error Message
		</td>
		<td>
			Arabic Error Message
		</td>
	</tr>
{foreach from=$validation key="field" item="arr"}
{foreach from=$arr key="index" item="row"}
	<tr>
		<td>
			{$field}
		</td>
		<td>
			{$row.type}
		</td>
		<td>
			{if $row.type == "function"}
				{$row.function}
			{elseif $row.type == "format"}
				{$row.format}
			{else}
				&nbsp;
			{/if}
		</td>
		<td>
			<input type="text" name="errors[{$field}][{$index}][en_msg]" value="{$row.en_msg}"/>
			{error_validator field_name=$field|cat:"_"|cat:$index}
		</td>
		<td>
			<input type="text" name="errors[{$field}][{$index}][ar_msg]" value="{$row.ar_msg}"/>
		</td>
	</tr>
	{/foreach}
{/foreach}
</table>
<center><input type="submit" value="  Set Error Messages  "/></center>
{/form}
<a href="{make_link controller="project" action="add_validation"}" class="mylink">Add a new Validation</a>
