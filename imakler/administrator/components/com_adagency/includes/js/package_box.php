<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>

<script language="javascript" type="text/javascript">
		<!--

		function change_package(packtype){

			if(packtype=='cpm')
				{
					document.getElementById('typepc').style.display = 'none';
					document.getElementById('typefr').style.display = 'none';
					document.getElementById('typecpm').style.display = '';
				}

			if(packtype == 'fr')
				{
					document.getElementById('typecpm').style.display = 'none';
					document.getElementById('typepc').style.display = 'none';
					document.getElementById('typefr').style.display = '';
				}

			if(packtype == 'pc')
				{
					document.getElementById('typecpm').style.display = 'none';
					document.getElementById('typepc').style.display = '';
					document.getElementById('typefr').style.display = 'none';
				}
		}


		function getSelectedValue2( frmName, srcListName ) {
		var form = eval( 'document.' + frmName );
		var srcList = form[srcListName];

		i = srcList.selectedIndex;
			if (i != null && i > -1) {
				return srcList.options[i].value;
			} else {
				return null;
			}
		}
		function IsNumeric(sText){
		var ValidChars = "0123456789.";
   		var IsNumber=true;
  		var Char;
 		for (i = 0; i < sText.length && IsNumber == true; i++) {
      		Char = sText.charAt(i);
      		if (ValidChars.indexOf(Char) == -1)  { IsNumber = false; }
	    }
	    return IsNumber;
     	}

function ajax_addpackage () {
//
var form = document.adminForm;

if (document.getElementById('quantity').value <= 0 || !IsNumeric(document.getElementById('quantity').value)) {
	alert( "<?php echo JText::_("JS_INSERT_PACKQUANT");?>" );
	return false;
}

description = form['description'].value;

if (form['description'].value == "") {
	alert( "<?php echo JText::_("JS_INSERT_PACKDESC");?>" );
	return false;
}

if (document.getElementById('not_free').checked==true){
	if (form['cost'].value <= 0)
		{
			alert( "<?php echo JText::_("JS_INSERT_PRICE");?>" );
			return false;
		}
	if (!IsNumeric(form['cost'].value))
		{
			alert( "<?php echo JText::_("JS_INSERT_PRICE");?>" );
			return false;
		}
}

pack_description = form['pack_description'].value;
type = form['type'].value;
quantity = document.getElementById('quantity').value;

if ( document.getElementById('not_free').checked == true)
	{
		free = 0;
		cost = form['cost'].value;
	}
else
	{
		free = 1;
		cost = 0;
	}

if ( document.getElementById('selzone1').checked == true)
	{
		selzone = 1;
		selected_zones = document.getElementById('allzones').value;
	}
else
	{
		selzone = 0;
		selected_zones = document.getElementById('boxchecked_id').value;
	}

amount = 0;
duration = '';

if(type == 'fr')
	{
		amount = document.getElementById('amount').value ;
		duration = document.getElementById('duration').value ;
	}

var url = 'index.php?option=com_adagency&controller=adagencyPackages&task=add_package_from_modal&tmpl=component&format=raw';

new Ajax.Request(url, {
  method: 'post',
  async: false,
  parameters: 'description='+description+'&pack_description='+pack_description+'&type='+type+'&quantity='+quantity+'&free='+free+'&cost='+cost+'&selected_zones='+selected_zones+'&amount='+amount+'&duration='+duration+'&advert_id='+<?php echo $_GET['advert_id'];?>,
  asynchronous: 'true',
  onSuccess: function(transport) {

		/*
		if(transport.responseText == 'err11');
			{
				alert('Username exists and he is already Advertiser!');
				return false;
			}
		*/

		to_be_replaced = parent.document.getElementById('to_be_replaced_p');
		//to_be_replaced.innerHTML += '&nbsp; Package Added';
		to_be_replaced.innerHTML = transport.responseText;
window.parent.setTimeout('document.getElementById("close_domwin").click()', 5);

  },
  onCreate: function()
  {
	//alert('Element added!');
  }
}

);

//window.parent.setTimeout('window.parent.document.getElementById("sbox-window").close()', 1);

//window.parent.document.getElementById("sbox-window").close();

return true;

}


function str_replace(haystack, needle, replacement) {
	var temp = haystack.split(needle);
	return temp.join(replacement);
}


function isChecked(id, isitchecked){

	if (isitchecked == true){
		//document.adminForm.boxchecked.value++;
		document.adminForm.boxchecked_id.value = document.adminForm.boxchecked_id.value + id + '|';
	}
	else {
			my_tasks = document.getElementById('boxchecked_id').value;

			var my_tasks_array=my_tasks.split("|");
			var part_num=0;
			var new_task_array = '';

				while (part_num < my_tasks_array.length-1)
				 {
					if(my_tasks_array[part_num]!=id)
						{
							new_task_array = new_task_array+my_tasks_array[part_num]+'|';
						}
				  part_num+=1;
				  }
			document.getElementById('boxchecked_id').value = new_task_array;
		//needle = id + ',';
		//document.adminForm.boxchecked_id.value = str_replace(document.adminForm.boxchecked_id.value, needle, '');
	}
}


		-->
		</script>
