<html>
<head>
<title>PHP Generator 2.0</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="{$HTML_ROOT}view/images/style.css" rel="stylesheet"/>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="10" marginheight="10">
<table cellpadding="10" cellspacing="0" border="0" width="100%" height="100%">
	<tr>
		<td width="180" valign="top">
			<b>- <u>Project</u></b><br/>	
			&nbsp; <a href="{make_link controller="project" action="new_project"}" class="mylink">New</a><br/>
			&nbsp; <a href="{make_link controller="project" action="open_project"}" class="mylink">Open</a><br/>
			&nbsp; <a href="{make_link controller="project" action="save_project"}" class="mylink">Save</a><br/>
			&nbsp; <a href="{make_link controller="project" action="save_close"}" class="mylink">Save & Close</a><br/>
			
			<br/><br/>
			
			<b>- <u>Wizard</u></b><br/>
			&nbsp; <a href="{make_link controller="project" action="select_db"}" class="mylink">Select DB</a><br/>
			&nbsp; {if $project.project_flags & 0x1}<a href="{make_link controller="project" action="select_table"}" class="mylink">Select Table</a>{else}Select Table{/if}<br/>
			&nbsp; {if $project.project_flags & 0x2}<a href="{make_link controller="project" action="set_model_controller"}" class="mylink">Model & Controller</a>{else}Model & Controller{/if}<br/>
			&nbsp; {if $project.project_flags & 0x4}<a href="{make_link controller="project" action="set_appearance"}" class="mylink">Appearance</a>{else}Appearance{/if}<br/>
			&nbsp; {if $project.project_flags & 0x8}<a href="{make_link controller="project" action="set_fields_names"}" class="mylink">Fields Names</a>{else}Fields Names{/if}<br/>
			&nbsp; {if $project.project_flags & 0x10}<a href="{make_link controller="project" action="set_defaults"}" class="mylink">Set Defaults</a>{else}Set Defaults{/if}<br/>
			
			&nbsp; {if $project.project_flags & 0x20}<a href="{make_link controller="project" action="validation"}" class="mylink">Validation</a>{else}Validation{/if}<br/>
			&nbsp; {if $project.project_flags & 0x40}<a href="{make_link controller="project" action="set_error_messages"}" class="mylink">Error Messages</a>{else}Error Messages{/if}<br/>
			&nbsp; {if $project.project_flags & 0x20}<a href="{make_link controller="project" action="search"}" class="mylink">Search</a>{else}Search{/if}<br/>
			&nbsp; {if $project.project_flags & 0x20}<a href="{make_link controller="project" action="relation"}" class="mylink">Relations</a>{else}Relations{/if}<br/>
			
			<br/><br/>

			<b>- <u>Generate</u></b><br/>
			{if $is_ready_to_generate}
			&nbsp; <a href="{make_link controller="generator" action="all"}" class="mylink">Generate All Module</a><br/>
			<br/>
			&nbsp; <a href="{make_link controller="generator" action="controller"}" class="mylink">Controller</a><br/>
			&nbsp; <a href="{make_link controller="generator" action="model"}" class="mylink">Model</a><br/>
			<br/>
			&nbsp; <b>English View</b><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_en_add"}" class="mylink">Add</a><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_en_edit"}" class="mylink">Edit</a><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_en_getall"}" class="mylink">Get All</a><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_en_search"}" class="mylink">Search</a><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_en_getone"}" class="mylink">Get One</a><br/>
			<br/>
			&nbsp; <b>Arabic View</b><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_ar_add"}" class="mylink">Add</a><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_ar_edit"}" class="mylink">Edit</a><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_ar_getall"}" class="mylink">Get All</a><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_ar_search"}" class="mylink">Search</a><br/>
			&nbsp; <a href="{make_link controller="generator" action="view_ar_getone"}" class="mylink">Get One</a><br/>
			{/if}
			
		</td>
		<td valign="top" style="padding: 0px;width: 1px;" bgcolor="#CCCCCC"></td>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			{if $__info}
			{section loop=$__info name=inf}
			<tr>
				<td colspan="2" align="center" class="{$__info[inf].type}msg">
					{$__info[inf].info_msg|TT:$__info[inf].info_params}
				</td>
			</tr>
			{/section}  
			{/if}
			{if $__errors}
			{section loop=$__errors name=err}
			<tr>
				<td colspan="2" align="center" class="errormsg">
					{$__errors[err].error_msg|TT:$__errors[err].error_params}
				</td>
			</tr>
			{/section}  
			{/if}
			</table>
			{include file=$document}
		</td>
	</tr>
</table>		
</body>
</html>
