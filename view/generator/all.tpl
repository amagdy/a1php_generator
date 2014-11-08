<h1>Congratulations Code is generated Successfully</h1>
Copy the contents of the folder <b>{$PHP_ROOT}uploads/code/</b> to the folder of your project or <a href="{make_link controller="generator" action="download_zip"}">Click Here</a> to download a ZIP file of the generated module.


<br/><br/><br/>
<b>Add these links to the admin layout</b>
<br/>
<textarea cols="150" rows="7">
	
<br/><br/>
<a href="#" style="text-decoration:none" onClick="javascript:return hide_show('{$project.model}_div');"><font class="bold_text"><span id="{$project.model}_div_span">+</span> {$project.model} Management</font></a>
<div id="{$project.model}_div" style="margin-left:15px; margin-top:7px; display:none;">	
	<a class="mylink" href="{ldelim}make_link controller="{$project.controller}" action="add"{rdelim}">Add a {$project.model}</a><br/>
	<a class="mylink" href="{ldelim}make_link controller="{$project.controller}" action="getall"{rdelim}">Get All {$project.table}</a><br/>
	<a class="mylink" href="{ldelim}make_link controller="{$project.controller}" action="search"{rdelim}">Search for a {$project.model}</a><br/>
</div>
</textarea>



<br/><br/><br/>
<b>Add this to the end of the English translation array</b>
<br/>
<textarea cols="150" rows="7">
{foreach from=$en_error_msgs key=k item=v}$arr_lang['{$k}'] = "{$v|escape}";
{/foreach}
</textarea>



<br/><br/><br/>
<b>Add this to the end of the Arabic translation array</b>
<br/>
<textarea cols="150" rows="7">
{foreach from=$ar_error_msgs key=k item=v}$arr_lang['{$k}'] = "{$v|escape}";
{/foreach}
</textarea>



