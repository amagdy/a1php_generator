{make_link controller="project" action="select_table" assign="form_action"}
{form action=$form_action}
	Select Table: 
	{html_options output=$tables values=$tables name="project[table]" selected=$project.table}
	{error_validator field_name="table"}
	<br/>
	<input type="submit" value="Next"/>
{/form}
