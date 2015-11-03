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
JHtml::_('behavior.tooltip');
	$script = "
		Joomla.submitbutton = function (pressbutton) {
			if(pressbutton == 'cancel') {
				document.getElementById('option1').checked = false;
				document.getElementById('option2').checked = false;
			}
			submitform(pressbutton);
		}
	";
	$document = JFactory::getDocument();
	$document->addScriptDeclaration($script);
    $document->addStyleSheet('components/com_adagency/css/joomla16.css');
    $document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
	$get_data = JRequest::get('get');
?>

<style>
	.page-content{
		margin:20px;
	}
</style>

<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
			<div class="row-fluid">
	            <div class="span12 pull-right">
	            	<h2 class="pub-page-title">
						<?php echo JText::_('ADAG_ADV_WIZ'); ?>
					</h2>
	            </div>
      		</div>
       <div class="well"><strong><?php echo JText::_('ADAG_WIZ_CHOS');?></strong></div>
	 	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_WIZ_ADV'); ?> </label>
			<div class="controls">
				<input type="radio" id="option1" name="task" value="edit" checked="checked">
				<span class="lbl"></span>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_WIZ_ADV_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_WIZ_EXI'); ?> </label>
			<div class="controls">
				<input type="radio" id="option2" name="task" value="temp">
				<span class="lbl"></span>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_WIZ_EXI_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>

		<?php if(isset($get_data['tmpl'])&&($get_data['tmpl'] == 'component')){ ?>
		<input type="submit" class="btn btn-primary" value="<?php echo JText::_('ADAG_NEXT');?>" onclick="submitbutton('next')" />
		<input type="hidden" name="tmpl" value="component" />
		<?php } ?>
 		<input type="hidden" name="option" value="com_adagency" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="controller" value="adagencyAdvertisers" />
</form>
