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
Joomla.submitbutton = function (pressbutton) {
	var form = document.adminForm;
	if(pressbutton == 'existing') {
		if (form['username'].value == "") {
			alert( "<?php echo JText::_("JS_INSERT_USERNAME2");?>" );
			return false;
		}
		submitform( pressbutton );
	} else {
		submitform( pressbutton );
	}
}
</script>
