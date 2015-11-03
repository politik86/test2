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
function UploadImage() {
    var fileControl = document.adminForm.image_file;
    var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
            if (thisext != ".jpeg" && thisext != ".jpg" && thisext != ".gif" && thisext != ".png" && thisext != ".JPEG" && thisext != ".JPG" && thisext != ".GIF" && thisext != ".PNG")
                { alert('<?php echo "Only jpg, gif and png allowed!";?>');
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
</script>
