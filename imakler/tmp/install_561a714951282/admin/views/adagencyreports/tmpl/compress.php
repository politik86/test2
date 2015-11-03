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


$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/jquery.jscrollpane.css');
$document->addScript('components/com_adagency/js/jquery.mousewheel.js');
$document->addScript('components/com_adagency/js/jquery.jscrollpane.min.js');

require_once('components/com_adagency/includes/js/compress.php');

?>

<div class="current">
    <span id="compress_adag"><?php echo JText::_('ADAG_COMPRESS_IN_PROGRESS'); ?></span>
    <img id="comp_load" style="width: 15px;vertical-align: bottom;" src="<?php  echo JURI::root() . 'components/com_adagency/images/loading.gif'; ?>"  />
    <div id="adagency_log">
        <p id="loginfo">
        </p>
    </div>
</div>
