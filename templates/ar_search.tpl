<script>
{literal}
function confirm_delete(mylink){
	document.getElementById('delyes').href = mylink.href;
	document.getElementById('div_confirm').style.display = '';
	return false;
}
function select_unselect_checkbox(checkbox) {
	if (checkbox.checked == true) {
		select_all_none(true);
	} else {
		select_all_none(false);
	}
}
function hide_confirm_delete() {
	document.getElementById('div_confirm').style.display = 'none';
	return false;
}

function confirm_delete_many(){
	document.getElementById('div_confirm_del_many').style.display = '';
	return false;
}
function hide_confirm_delete_many() {
	document.getElementById('div_confirm_del_many').style.display = 'none';
	return false;
}

function select_all_none(bool_all) {
	var arr_checkboxes = document.getElementsByName('arr_ids[]');
	var i;
	for (i=0; i<arr_checkboxes.length; i++) {
		arr_checkboxes[i].checked = bool_all;
	}
	return false;
}
{/literal}
</script>
<div id="div_confirm" style="display:none" dir="rtl">
	<table width="100%" cellpadding="4" cellspacing="0" border="0" class="confirm_table">
		<tr>
			<td colspan="2" align="center" class="confirmmsg">
				تأكيد
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				هل أنت متأكد أنك تريد حذف هذا العنصر؟
			</td>
		</tr>	
		<tr>
			<td align="center">
				<a class="mylink" href="" id="delyes">نعم</a>
			</td>
			<td align="center">
				<a class="mylink" href="#" onClick="javascript:return hide_confirm_delete();">لا</a>
			</td>		
		</tr>	
	</table>
</div>
<div id="div_confirm_del_many" style="display:none" dir="rtl">
	<table width="100%" cellpadding="4" cellspacing="0" border="0" class="confirm_table">
		<tr>
			<td colspan="2" align="center" class="confirmmsg">
				تأكيد
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				هل أنت متأكد أنك تريد حذف العناصر المختارة؟
			</td>
		</tr>	
		<tr>
			<td align="center">
				<a class="mylink" href="#" onclick="javascript:document.getElementById('form1').submit(); return false;">نعم</a>
			</td>
			<td align="center">
				<a class="mylink" href="#" onClick="javascript:return hide_confirm_delete_many();">لا</a>
			</td>		
		</tr>	
	</table>
</div>
{make_link controller="<?=$project['controller']?>" action="search" assign="form_action"}
{form action=$form_action method="get"}
<?
if ($project['search']) {
	while (list($field_name, $arr) = each($project['search'])) {
		$count = count($arr);
		for ($i = 0; $i < $count; $i++) {
			?>

<?=$project['arr_fields_names'][$field_name]['en_friendly_name']?>: <input type="text" name="<?=$project['model']?>_search[<?=$field_name?>_<?=$arr[$i]?>]" value="{$<?=$project['model']?>_search.<?=$field_name?>_<?=$arr[$i]?>}"/><br/><?
		}
	}
	reset($project['search']);
}
?>

<input type="submit" value="  Search  "/>
{/form}

{make_link controller="<?=$project['controller']?>" action="delete_many" assign="form_action"}
{form id="form1" action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4" dir="rtl">
	<tr class="table_title"> 
		<?
		$count_getall_fields = count($project['getall_fields']);
		for ($i = 0; $i < $count_getall_fields; $i++) {
		?>

		<td>
			<div align="center">
				<?=$project['arr_fields_names'][$project['getall_fields'][$i]]['ar_friendly_name']?>
				
				<a href="{make_link controller="<?=$project['controller']?>" action="search" __orderby=<?=$project['getall_fields'][$i]?> __desc="no" __is_form_submitted=$__is_form_submitted array=$<?=$project['model']?>_search_link}"><img src="{$HTML_ROOT}view/images/up_arrow.png" border="0"/></a>
				<a href="{make_link controller="<?=$project['controller']?>" action="search" __orderby=<?=$project['getall_fields'][$i]?> __desc="yes" __is_form_submitted=$__is_form_submitted array=$<?=$project['model']?>_search_link}"><img src="{$HTML_ROOT}view/images/down_arrow.png" border="0"/></a>
			</div>
		</td><?
		}
		?>

		<td>عرض</td>
		<td>تعديل</td>
		<td>حذف</td>
		<td width="10%" align="center"><input type="checkbox" onchange="javascript:select_unselect_checkbox(this);"/></td>
	</tr>
	{section name=<?=$project['model']?>id loop=$arr_<?=$project['table']?>}
	<tr bgcolor="{cycle values="#EEEEEE,#DDDDDD"}"><?
		for ($i = 0; $i < $count_getall_fields; $i++) {
			?>
			
		<td><div align="center"><?
			if ($project['arr_fields_names']['name']['control'] == "checkbox") {
				?>{if $arr_<?=$project['table']?>[<?=$project['model']?>id].<?=$project['getall_fields'][$i]?>}-{else}Yes{/if}<?
			} else {
				?>{$arr_<?=$project['table']?>[<?=$project['model']?>id].<?=$project['getall_fields'][$i]?>}<?
			}
			?></div></td><?
		}
		?>

		<td><div align="center"><a class="mylink" href="{make_link controller="<?=$project['controller']?>" action="getone" id=$arr_<?=$project['table']?>[<?=$project['model']?>id].<?=$project['primary_key']?>}">عرض</a></div></td>
		<td><div align="center"><a class="mylink" href="{make_link controller="<?=$project['controller']?>" action="edit" id=$arr_<?=$project['table']?>[<?=$project['model']?>id].<?=$project['primary_key']?>}"><img border="0" src="{$HTML_ROOT}view/images/icon_edit.png"/> تعديل</a></div></td>
		<td><div align="center"><a class="mylink" href="{make_link controller="<?=$project['controller']?>" action="delete" id=$arr_<?=$project['table']?>[<?=$project['model']?>id].<?=$project['primary_key']?>}" onClick="javascript:return confirm_delete(this);"><img border="0" src="{$HTML_ROOT}view/images/icon_delete.gif"/> حذف</a></div></td>
		<td width="10%" align="center"><input type="checkbox" name="arr_ids[]" value="{$arr_<?=$project['table']?>[<?=$project['model']?>id].<?=$project['primary_key']?>}"/></td>
	</tr>
	{/section}
</table>
<div class="div_text" align="right" dir="rtl"><a href="#" onclick="javascript:return confirm_delete_many();" class="mylink"><img border="0" src="{$HTML_ROOT}view/images/icon_delete.gif"/> حذف</a></div>
<div class="div_text" dir="rtl">الصفحة:
		{foreach key=k item=v from=$<?=$project['model']?>.__paging_pages}
			{if $v}
			<a href="{make_link controller="<?=$project['controller']?>" action="search" __orderby=$__orderby __desc=$__desc __is_form_submitted=$__is_form_submitted array=$<?=$project['model']?>_search_link array2=$v}" class="mylink">{$k}</a>
			{else}
			<b><u><font size="2">{$k}</font></u></b>
			{/if}
		{/foreach}
</div>
{/form}
