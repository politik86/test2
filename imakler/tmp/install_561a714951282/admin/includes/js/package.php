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

Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    var ok=1;
    if (pressbutton=='save') {
        if(form['type'].value != 'fr' && form['type'].value != 'in'){
            if (form['quantity'].value <= 0 || !IsNumeric(form['quantity'].value))
                {
                    alert( "<?php echo JText::_("JS_INSERT_PACKQUANT");?>" );
                    ok=0;
                }
        }
        if (form['description'].value == "")
            {
                alert( "<?php echo JText::_("JS_INSERT_PACKDESC");?>" );
                ok=0;
            }
        if (document.getElementById('not_free').checked==true){
            if (form['cost'].value <= 0)
                {
                    alert( "<?php echo JText::_("JS_INSERT_PRICE");?>" );
                    ok=0;
                }
            if (!IsNumeric(form['cost'].value))
                {
                    alert( "<?php echo JText::_("JS_INSERT_PRICE");?>" );
                    ok=0;
                }
        }
		
		if(ADAG('.zlocations:checked ').length == 0){
			/*alert("<?php echo JText::_("JS_ASSIGN_ZONE");?>");
			ok=0;*/
		}
		
        if(ok==1){
			submitform(pressbutton);
		}
    }
    else
        submitform( pressbutton );
}
-->
</script>

<!-- <script type="text/javascript"> -->
<!--     window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); }); -->
<!--     window.addEvent('domready', function() { -->
<!--  -->
<!--     SqueezeBox.initialize({}); -->
<!--  -->
<!--     $$('a.modal').each(function(el) { -->
<!--         el.addEvent('click', function(e) { -->
<!--             new Event(e).stop(); -->
<!--             SqueezeBox.fromElement(el); -->
<!--         }); -->
<!--     }); -->
<!-- }); -->
<!-- </script> -->
