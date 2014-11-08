{make_link controller="project" action="set_appearance" assign="form_action"}
{form action=$form_action}
	<table>
		<tr>
			
			<td>
				<table border="1">
					<tr bgcolor="#AABBCC">
						<td><b>Field Name</b></td>
						<td><b>Appears in Get All Page</b></td>
					</tr>
					{foreach from=$project.getall_fields key="field_name" item="selected"}
					<tr bgcolor="{cycle values="#FFFFFF,#DDDDDD"}">
						<td><b>{$field_name}</b></td>
						<td><input type="checkbox" name="project[getall_fields][]" value="{$field_name}"{if $selected} checked{/if}/></td>
					</tr>
					{/foreach}
				</table>
			</td>
			<td>
				<table border="1">
					<tr bgcolor="#009990">
						<td><b>Field Name</b></td>
						<td><b>Appears in Add Form</b></td>
					</tr>
					{foreach from=$project.add_fields key="field_name" item="selected"}
					<tr bgcolor="{cycle values="#FFFFFF,#DDDDDD"}">
						<td><b>{$field_name}</b></td>
						<td><input type="checkbox" name="project[add_fields][]" value="{$field_name}"{if $selected} checked{/if}/></td>
					</tr>
					{/foreach}
				</table>
			</td>
			<td>
				<table border="1">
					<tr bgcolor="#09990">
						<td><b>Field Name</b></td>
						<td><b>Appears in Edit Form</b></td>
					</tr>
					{foreach from=$project.edit_fields key="field_name" item="selected"}
					<tr bgcolor="{cycle values="#FFFFFF,#DDDDDD"}">
						<td><b>{$field_name}</b></td>
						<td><input type="checkbox" name="project[edit_fields][]" value="{$field_name}"{if $selected} checked{/if}/></td>
					</tr>
					{/foreach}
				</table>
			</td>


			
		</tr>
	</table>
	<input type="submit" value="Next"/>
{/form}
