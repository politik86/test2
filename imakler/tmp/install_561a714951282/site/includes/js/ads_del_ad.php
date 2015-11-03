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

<script type="text/javascript" language="JavaScript">
	function Change(i) {
		boxchecked = document.getElementById("boxchecked").value;
		
		if(boxchecked != 0){
			if(boxchecked == 1){
				if (confirm('<?php echo JText::_("ADAG_SURE_DELETE_AD");?>')){
					document.adminForm.sid.value = i;
					document.adminForm.task.value = "remove";
					document.adminForm.submit();
					return true; 
				}
				else { 
					return;
				}
			}
			else{
				if (confirm('<?php echo JText::_("ADAG_SURE_DELETE_ADS");?>')){
					document.adminForm.sid.value = i;
					document.adminForm.task.value = "remove";
					document.adminForm.submit();
					return true; 
				}
				else { 
					return;
				}
			}
			
		}
		else{
			alert('<?php echo JText::_("ADAG_MAKE_SELECTION_FIRST"); ?>');
			return false;
		}
	}	
</script>