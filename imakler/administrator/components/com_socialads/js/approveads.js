function selectstatus(appid,ele)
{
	//document.forms['adminForm'].id.value = appid;
	var selInd=ele.selectedIndex;

	var status =ele.options[selInd].value;
	document.getElementById('hidid').value = appid;
	document.getElementById('hidstat').value = status;
	
	submitbutton('save');
   	return;
}
