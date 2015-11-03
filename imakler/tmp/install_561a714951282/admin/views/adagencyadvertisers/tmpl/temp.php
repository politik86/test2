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
$get_data = JRequest::get('get');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
?>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/temp.php"); ?>

<?php
	$tmp = JRequest::getVar("tmpl", "");
	if($tmp == "component"){
?>
		<style>
			.page-content{
				margin:20px;
			}
		</style>
<?php
	}
?>

<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	 <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('ADAG_CHS_USR2'); ?>
				</h2>
            </div>
      </div>
      
      <div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_USER'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" maxlength="255" size="40" name="username" value="<?php
				if(isset($_SESSION['temp_user'])) { echo $_SESSION['temp_user']; } ?>" <?php
				if(isset($_SESSION['temp_user'])&&($_SESSION['temp_user'] != NULL)) {
						echo ' style = "border: 1px solid #FF0000;" ';
				} ?> />
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_USER_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>

    <input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="task" value="existing" />
	<input type="hidden" name="controller" value="adagencyAdvertisers" />
    <?php
		if(isset($get_data['tmpl'])&&($get_data['tmpl'] == 'component')){
			echo '<input type="hidden" name="tmpl" value="component" />';
			echo '<input type="submit" class="btn btn-primary" value="'.JText::_('ADAG_NEXT').'" onclick="submitbutton(\'existing\')" />';
		}
	?>
</form>
