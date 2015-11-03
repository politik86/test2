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
	var regexp = /[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/
	<?php //if($advertiser->aid<1) {}?>
	if ((pressbutton=='storeExistent')||(pressbutton=='save_graybox')) {
		if (form['fullname'].value == "") {
			alert("<?php echo JText::_("JS_INSERT_NAME2");?>");
			return false;
		}
		if (form['email'].value == "") {
			alert("<?php echo JText::_("JS_INSERT_EMAIL2");?>");
			return false;
		}
        <?php if (isset($configs->show) && (in_array('phone',$configs->show))
                && isset($configs->mandatory) && (in_array('phone',$configs->mandatory))) { ?>
		if (form['telephone'].value == "") {
			alert( "<?php echo JText::_("ADAG_JS_INSERT_PHONE");?>" );
			return false;
        }
        <?php } ?>
        <?php if (isset($configs->show) && (in_array('url',$configs->show))
                && isset($configs->mandatory) && (in_array('url',$configs->mandatory))) { ?>
		if ((form['website'].value == "")||(form['website'].value == "http://")) {
			alert( "<?php echo JText::_("ADAG_JS_INSERT_WEB");?>" );
			return false;
		}
        <?php } ?>
		<?php // Checking for company fields if mandatory

		if(isset($configs->show)&&(in_array('company',$configs->show))&&isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) {?>
		if (form['company'].value == "") {
			alert( "<?php echo JText::_("JS_INSERT_COMPANY");?>" );
			return false;
		}
		if (form['description'].value == "") {
			alert( "<?php echo JText::_("ADAG_JS_CMP_DSC");?>" );
			return false;
		}
		<?php } // End checking for company fields ?>

		<?php // Checking for address fields if mandatory

		if(isset($configs->show)&&(in_array('address',$configs->show))&&isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) {?>
		if (form['address'].value == "") {
			alert( "<?php echo JText::_("ADAG_JS_ADR");?>" );
			return false;
		}
		if (form['country'].value == "") {
			alert( "<?php echo JText::_("JS_SELECT_COUNTRY");?>" );
			return false;
		}
		if (form['zip'].value == "") {
			alert( "<?php echo JText::_("ADAG_JS_ZIP");?>" );
			return false;
		}
		<?php } // End checking for address ?>

		submitform( pressbutton );
	}
	else
		submitform( pressbutton );
}
</script>
