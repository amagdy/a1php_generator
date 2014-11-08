<script>
{literal}
	function show_val (drop_down) {
		var val = document.getElementById('val');
		if (drop_down.value == "function" || drop_down.value == "format") {
			val.style.display = "";
		} else {
			val.style.display = "none";
		}
	}
{/literal}
</script>
<h3>Add a Validation</h3>
{make_link controller="project" action="add_validation" assign="form_action"}
{form action=$form_action}
<table>
	<tr>
		<td>Field Name</td>
		<td>		
			{html_options name="project[field_name]" options=$arr_fields_names selected=$project.field_name}
		</td>
	</tr>
	<tr>
		<td>Validation Type</td>
		<td>		
			<select name="project[validation_type]" onchange="show_val(this);">
				{html_options options=$arr_validation_types selected=$project.validation_type}
			</select>
		</td>
	</tr>
	<tr{if $project.validation_type != "format" && $project.validation_type != "function"} style="display: none"{/if} id="val">
		<td>Value</td>
		<td>		
			<input type="text" name="project[validation_value]" value="{$project.validation_value}"/> {error_validator field_name="validation_value"}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="  Add Validation  "/>
		</td>
	</tr>
</table>
{/form}
