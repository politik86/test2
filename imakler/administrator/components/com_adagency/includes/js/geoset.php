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

Joomla.submitbutton = function (pressbutton) {
    if((pressbutton == 'savesettings')||(pressbutton == 'applysettings')) {
        if(ADAG('#cityloc').val() == '') {
            alert('<?php echo JText::_('ADAG_GEO_JS_CF');?>');
            return false;
        }
        submitform(pressbutton);
    }
    submitform(pressbutton);
}
ADAG(document).ready(function(){
    var country = ADAG('#allowcountry');
    ADAG('#countryopts input').each(function(index){
        ADAG(this).click(function(){
            if((country.prop('checked') == true)&&(!ADAG('#countryopts input:checked').length)) {
                country.removeProp('checked');
                //or country.get(0).checked = false;
            } else {
                var everywhere = ADAG('#c1');
                country.prop('checked','true');
                //alert(index);
                if(index > 0) {
                    everywhere.prop('checked','true');
                }
            }
            if((index == 0)&&(ADAG(this).get(0).checked == false)&&(ADAG('#countryopts input:checked').length<=1)) {
                ADAG('#countryopts input:checked').each(function(){
                    this.checked = false;
                });
                country.removeProp('checked');
            }
        });
    });
    country.click(function(){
        if(ADAG(this).prop('checked') == true) {
            ADAG('#c1').prop('checked','true');
        } else {
            ADAG('#countryopts input:checked').each(function(){
                this.checked = false;
            });
        }
    });
});

</script>
