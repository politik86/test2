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

<script  language="javascript" type="text/javascript">

function getSelectedValue2( frmName, srcListName ) {
    var form = eval( 'document.' + frmName );
    var srcList = form[srcListName];
    //alert(srcList);

    i = srcList.selectedIndex;
    if (i != null && i > -1) {
        return srcList.options[i].value;
    } else {
        return null;
    }
}

function UploadImage() {
    if (getSelectedValue2('adminForm','advertiser_id') < 1) {
        alert( "<?php echo JText::_('JS_SELECT_ADV');?>" );
    }
    else {
        var fileControl = document.adminForm.image_file;
        var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
        if (thisext != ".swf" && thisext != ".SWF")
            { alert('<?php echo JText::_('JS_INVALIDSWF');?>');
              return false;
            }
        if (fileControl.value) {
            //alert('here');
            document.adminForm.task.value = 'upload';
            return true;
            //submitbutton('upload');
        }
        return false;
    }
    return false;
}

</script>
