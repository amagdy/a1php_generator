{make_link controller="project" action="set_fields_names" assign="form_action"}
{form action=$form_action}
	<table border="1">
		<tr>
			<td>
				Field Name
			</td>
			<td>
				English Friendly Name
			</td>
			<td>
				Arabic Friendly Name
			</td>
			<td>
				HTML Control
			</td>
		</tr>
		{section loop=$arr_fields_names name="field"}
		<tr>
			<td>
				{$arr_fields_names[field].field_name}
				<input type="hidden" name="project[arr_fields_names][{$arr_fields_names[field].field_name}][field_name]" value="{$arr_fields_names[field].field_name}"/>
			</td>
			<td>
				<input type="text" name="project[arr_fields_names][{$arr_fields_names[field].field_name}][en_friendly_name]" value="{$arr_fields_names[field].en_friendly_name}"/>
			</td>
			<td>
				<input type="text" name="project[arr_fields_names][{$arr_fields_names[field].field_name}][ar_friendly_name]" value="{$arr_fields_names[field].ar_friendly_name}"/>
			</td>
			<td>
				<select name="project[arr_fields_names][{$arr_fields_names[field].field_name}][control]">
				{html_options options=$arr_html_controls selected=$arr_fields_names[field].control}
				</select>
			</td>
		</tr>
		{/section}
	</table>
	<input type="submit" value="Next"/>
{/form}
