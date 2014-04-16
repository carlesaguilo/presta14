$(document).ready(function(){
	hideMSCOptions($('#id_group_type').val());
	$('#id_group_type').change(function(){
		hideMSCOptions($(this).val());
	});
});
function populate_attrs2()
{
	var attr_group = $('#attribute_group2');
	if (!attr_group)
		return;
	var attr_name = $('#attribute2');
	var number = $('#attribute_group2').val() ? $('#attribute_group2').val() : 0;

	if (!number)
	{
		attr_name.find('option').remove();
		$('#attribute2').append(new Option('---', 0, true, true));
		return;
	}

	var list = attrs2[number];
	
	$('#attribute2').find('option').remove();
	for(i = 0; i < list.length; i += 2)
		$('#attribute2').append(new Option(list[i + 1], list[i], true, true));
}
function hideMSCOptions(type)
{
	if(type==0)
	{
		$('#divcategory').hide();
		$('#divattribute').show();
		
	}
	else if(type==1)
	{
		$('#divcategory').show();
		$('#divattribute').hide();
	}
	
}