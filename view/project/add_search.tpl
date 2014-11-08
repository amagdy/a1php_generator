<h3>Add a Search Criterion</h3>
{make_link controller="project" action="add_search" assign="form_action"}
{form action=$form_action}
<table>
	<tr>
		<td>Field Name</td>
		<td>		
			{html_options name="search[field_name]" output=$arr_fields_names values=$arr_fields_names selected=$search.field_name}
		</td>
	</tr>
	<tr>
		<td>Search Operator</td>
		<td>	
			{html_options name="search[operator]" options=$arr_search_operators selected=$search.operator}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="  Add Search  "/>
		</td>
	</tr>
</table>
{/form}
