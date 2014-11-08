<h3>Search Criteria</h3>
<table border="1" id="tbl">
	<tr>
		<td>
			Field Name
		</td>
		<td>
			Search Operator
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
{foreach from=$search key="field" item="arr"}
{foreach from=$arr key="index" item="operator"}
	<tr>
		<td>
			{$field}
		</td>
		<td>
			{$arr_search_operators.$operator}
		</td>
		<td>
			<a class="mylink" href="{make_link controller="project" action="delete_search" field=$field index=$index}">Delete</a>
		</td>
		<td>
			
		</td>
	</tr>
	{/foreach}
{/foreach}
</table>
<br/><br/>
<b><a href="{make_link controller="project" action="add_search"}" class="mylink">Add a new Search Criterion</a></b>
