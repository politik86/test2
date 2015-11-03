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

<script type="text/javascript">
	/*up2*/
	ADAG(function(){
		ADAG('#upgrade_button').click(function(){
			var ok = true;
			ADAG('.selectable').each(function(){
    			if(ADAG(this).find('input:checked').length == 0) {
					ok = false;
				}
			});
			if(!ok) {
				alert("<?php echo JText::_('ADAG_CHOOSE_ZONE_EACH_PK'); ?>");
			} else {
				Joomla.submitbutton('upgradepack');
			}
		}).find('td').css('border','none').end().prev().find('td').css('border','none');
	});
</script>
