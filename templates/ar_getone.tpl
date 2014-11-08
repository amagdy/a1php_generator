<div dir="rtl">
<?=$project['primary_key']?>: {$<?=$project['model']?>.<?=$project['primary_key']?>} <br/>
<?
$arr_fields = array_keys($project['arr_fields_names']);
$count_fields = count($arr_fields);
if ($arr_fields) {
	for ($i = 0; $i < $count_fields; $i++) {
		?>
<?=$project['arr_fields_names'][$arr_fields[$i]]['ar_friendly_name']?>:	{$<?=$project['model']?>.<?=$arr_fields[$i]?>} <br/>
<?
	}
}
?>
</div>
