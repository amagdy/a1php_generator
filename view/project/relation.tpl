<h3>Relations</h3>
<table border="1" id="tbl">
	<tr>
		<td>
			Field Name
		</td>
		<td>
			Relation Text
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
{foreach from=$relations key="field" item="text"}
	<tr>
		<td>
			{$field}
		</td>
		<td>
			{$text}
		</td>
		<td>
			<a class="mylink" href="{make_link controller="project" action="delete_relation" field=$field}">Delete</a>
		</td>
		<td>
			
		</td>
	</tr>
{/foreach}
</table>
<br/><br/>
<b><a href="{make_link controller="project" action="add_relation"}" class="mylink">Add a new Relation</a></b>
