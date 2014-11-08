<script>
{literal}
function generate_relation_text () {
	document.getElementById('relation_text').value = document.getElementById('relation_tbl').value.replace("%s", document.getElementById('relation_field_name').value);;
}
{/literal}
</script>
<h3>Add a Relation</h3>
{make_link controller="project" action="add_relation" assign="form_action"}
{form action=$form_action}
<table>
	<tr>
		<td>Field Name</td>
		<td>		
			<select name="relation[field_name]" id="relation_field_name" onchange="generate_relation_text();">
			{html_options output=$arr_fields_names values=$arr_fields_names selected=$relation.field_name}
			</select>
		</td>
	</tr>
	<tr>
		<td>Relation Table</td>
		<td>	
			<select id="relation_tbl" onchange="generate_relation_text();">
				<option value="">--- None ---</option>
				<option value="$__out['arr_%s'] = array(&quot;&quot; => &quot;&quot;);">Array()</option>
				{html_options output=$arr_tables values=$arr_methods}
			</select>
		</td>
	</tr>
	<tr>
		<td>Relation Text</td>
		<td>
			<textarea name="relation[text]" id="relation_text" cols="90" rows="5"></textarea><br/> &nbsp; Make sure your code ends with a semicolon ;
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="  Add Relation  "/>
		</td>
	</tr>
</table>
{/form}
