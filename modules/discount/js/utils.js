function getDiscountForm()
{
	if (document.forms)
		return (document.forms['FormCustomersDiscount']);
	else
		return (document.FormCustomersDiscount);
}
function deleteDiscount(id)
{
	if(confirm("Veuillez confirmer la suppression.")) {
		var form = getDiscountForm();
		form.elements['DiscountToEditDelete'].value = id;
		form.elements['actionForm'].value = 'delete';
		form.submit();
	}
}
function editDiscount(id)
{
	var form = getDiscountForm();
	form.elements['DiscountToEditDelete'].value = id;
	form.elements['actionForm'].value = 'edit';
	form.submit();
}
function updateDiscount(id)
{
	var form = getDiscountForm();
	form.elements['DiscountToEditDelete'].value = id;
	form.elements['actionForm'].value = 'update';
	form.submit();
}