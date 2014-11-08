<h3>Validation</h3>
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
			&nbsp;
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
			<a class="mylink" href="{make_link controller="project" action="delete_validation" field=$field index=$index}">Delete</a>
		</td>
		<td>
			
		</td>
	</tr>
	{/foreach}
{/foreach}
</table>
<br/><br/>
<b><a href="{make_link controller="project" action="add_validation"}" class="mylink">Add a new Validation</a></b>
