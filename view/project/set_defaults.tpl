{literal}
<script language="javascript">
	function change_default (field, dropdown) {
		document.getElementById(field).value = dropdown.value;
	}
</script>
{/literal}
{make_link controller="project" action="set_defaults" assign="form_action"}
{form action=$form_action}
<table border="1">
	<tr>
		<td>
			Field Name
		</td>
		<td>
			Default Value Type
		</td>
		<td>
			Default Value
		</td>
	</tr>
{foreach key=index item=field_name from=$arr_fields_names}
	<tr>
		<td>
			{$field_name}
		</td>
		<td>
			<select onchange="change_default('txt_{$field_name}', this);">
				<option value=""> -- Any -- </option>
				<option value="NOW()"> Current Date Time </option>
				<option value="ip2long($_SERVER['REMOTE_ADDR'])"> Current IP </option>
				<option value="function()"> Function </option>
				<option value="$variable"> Variable </option>
				<option value="CONSTANT"> Constant </option>
				<option value="&quot;&quot;"> String </option>
				<option value="0"> Number </option>
				<option value="$this->()"> Method </option>
				<option value="$this->"> Property </option>
			</select>
		</td>
		<td>
			
			<input type="text" size="70" id="txt_{$field_name}" name="project[arr_defaults][{$field_name}]" value="{$project.arr_defaults[$field_name]|escape}"/>
		</td>
	</tr>
{/foreach}
	<tr>
		<td colspan="3" align="center">
			<input type="submit" value="  Next  "/>
		</td>
	</tr>
</table>	
{/form}

