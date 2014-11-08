{make_link controller="project" action="open_project" assign="form_action"}
{form action=$form_action}
Saved Projects: 
{html_options output=$arr_files values=$arr_files selected=$project.file_name name="project[file_name]"}
{error_validator field_name="file_name"}
<br/>
<input type="submit" value="Open"/>
{/form}
