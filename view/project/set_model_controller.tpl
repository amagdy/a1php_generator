{make_link controller="project" action="set_model_controller" assign="form_action"}
{form action=$form_action}
	Model:
	<input type="text" name="project[model]" value="{$project.model}"/>
	<br/><br/>
	Controller:
	<input type="text" name="project[controller]" value="{$project.controller}"/>
	<br/><br/>
	Primary Key:
	{html_options output=$fields values=$fields name="project[primary_key]" selected=$project.primary_key}
	{error_validator field_name="primary_key"}
	<br/><br/>
	<input type="submit" value="Next"/>
{/form}
