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
Joomla.submitbutton = function (pressbutton) {
    adv = document.getElementById('advertiser_id').value;
    pack = document.getElementById('package_id').value;
    cost = document.getElementById('cost').value;
    ok = 1;
    if(pressbutton == 'savenew')
        {
            if(adv == 0)
                {
                    alert ('<?php echo JText::_('JS_SEL_ADVERTISER'); ?>');
                    ok = 0;
                }
            if (pack == 0)
                {
                    alert ('<?php echo JText::_('JS_SEL_PACKAGE'); ?>');
                    ok = 0;
                }
            if ((cost < 0) || (IsNumeric(cost)==false))
                {
                    alert ('<?php echo JText::_('JS_INSERT_AMOUNT'); ?>');
                    ok = 0;
                }
            if (ok==1) { submitform( pressbutton ); }
        }
    else
        submitform( pressbutton );
}
-->
</script>
