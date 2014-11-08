{make_link controller="project" action="select_db" assign="form_action"}
{form action=$form_action}
	Select Database: 
	{html_options output=$databases values=$databases name="project[db_name]" selected=$project.db_name}
	{error_validator field_name="db_name"}
	<br/>
	<input type="submit" value="Next"/>
{/form}
