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
	ADAG(function(){
			ADAG('#upgrade_button').click(function(){
				var ok = true;
				ADAG('.adsTable tr:gt(0)').each(function(index){
    				if((ADAG(this).find('.addColumn input:checked').length>0)&&(ADAG(this).find('.w145[value=0]').length>0)){
        				ok = false;
    				}
				});
				if(!ok) {
					alert("<?php echo JText::_("ADAG_CHOOSE_ZONE_EACH_AD"); ?>");
				} else {
					Joomla.submitbutton('upgradecamp');
				}
		}).find('td').css('border','none').end().prev().find('td').css('border','none');
	});
</script>
