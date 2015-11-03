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

<script type="text/javascript" language="javascript">
function changetype() {
	var form=document.adminForm;
	if (form['type'].value=="Click Detail") document.getElementById("breackdown").style.visibility="hidden";
	else document.getElementById("breackdown").style.visibility="visible";
	return;
}

Joomla.submitbutton = function (pressbutton) {
	var form = document.adminForm;
		// do field validation
	if ((pressbutton == 'creat')) {
		if((document.getElementById('start_date').value != document.getElementById('start_date2').value) || (document.getElementById('end_date').value != document.getElementById('end_date2').value)) {
			document.getElementById('adag_datepicker').value = 1;
		}
		submitform( pressbutton );
	}
}

function adagsetdate(x) {
	if(document.getElementById('tfa_adag').value>=0){
		var tfa = document.getElementById('tfa_adag').value;
		var current = new Date();
		var past = new Date();
		var endDate = new Array();
		var startDate = new Array();

		if(x == 3) { current.setDate(current.getDate()-1);}

		endDate['month'] = current.getMonth() + 1;
		endDate['day'] = current.getUTCDate();
		endDate['year'] = current.getUTCFullYear();
		if (endDate['day']<10) { endDate['day'] = "0" + endDate['day']; }
		if (endDate['month']<10) { endDate['month'] = "0" + endDate['month']; }

		if(x == 3) { past.setDate(past.getDate()-1); }
		else if(x == 4) { past.setDate(past.getDate()-7); }
		else if(x == 5) { past.setMonth(past.getMonth()-1); }
		else if(x == 6) { past.setFullYear(past.getUTCFullYear()-1); }
		else if(x == 7) { past.setFullYear(past.getUTCFullYear()-10); }

		startDate['month'] = past.getMonth() + 1;
		startDate['day'] = past.getUTCDate();
		startDate['year'] = past.getUTCFullYear();
		if (startDate['day']<10) { startDate['day'] = "0" + startDate['day']; }
		if (startDate['month']<10) { startDate['month'] = "0" + startDate['month']; }


		if(tfa == 0) {
			var fullEndDate = endDate['year'] + "-" + endDate['month'] + "-" + endDate['day'];
			var fullStartDate = startDate['year'] + "-" + startDate['month'] + "-" + startDate['day'];
		} else if(tfa == 1) {
			var fullEndDate = endDate['month'] + "/" + endDate['day'] + "/" + endDate['year'];
			var fullStartDate = startDate['month'] + "/" + startDate['day'] + "/" + startDate['year'];
		} else if(tfa == 2) {
			var fullEndDate = endDate['day'] + "-" + endDate['month'] + "-" + endDate['year'];
			var fullStartDate = startDate['day'] + "-" + startDate['month'] + "-" + startDate['year'];
		} else if(tfa == 3) {
			var fullEndDate = endDate['year'] + "-" + endDate['month'] + "-" + endDate['day'];
			var fullStartDate = startDate['year'] + "/" + startDate['month'] + "/" + startDate['day'];
		} else if(tfa == 4) {
			var fullEndDate = endDate['month'] + "/" + endDate['day'] + "/" + endDate['year'];
			var fullStartDate = startDate['month'] + "/" + startDate['day'] + "/" + startDate['year'];
		} else if(tfa == 5) {
			var fullEndDate = endDate['day'] + "-" + endDate['month'] + "-" + endDate['year'];
			var fullStartDate = startDate['day'] + "-" + startDate['month'] + "-" + startDate['year'];
		}
	}
	//var fullDate = startDate['month'] + "/" + startDate['day'] + "/" + startDate['year'];
	document.getElementById('end_date').value = fullEndDate;
	document.getElementById('end_date2').value = fullEndDate;
	document.getElementById('start_date').value = fullStartDate;
	document.getElementById('start_date2').value = fullStartDate;
}
	<?php if(!isset($_POST['tfa'])) { ?>
		window.onload = function(){
			adagsetdate(5);
		}
	<?php
	} else {
	?>
	window.onload = function(){
		document.getElementById('end_date2').value = document.getElementById('end_date').value;
		document.getElementById('start_date2').value = document.getElementById('start_date').value;
	}
	<?php }	?>
</script>
