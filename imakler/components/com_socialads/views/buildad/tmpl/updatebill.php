<?php
/**
 *  @package    Social Ads
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');
JHtmlBehavior::framework();

?>
<script type="text/javascript">
	/*
function billSubmitform(pressbutton)
{
		 if (pressbutton == 'billCancel')
		 {
			 window.close()
		 //document.sa_adminForm.task.value = pressbutton;
		 }
	 	//document.adminForm.submit();
	} */
</script>
<div class="techjoomla-bootstrap">
<form method="post" name="sa_adminForm" id="sa_adminForm" class="form-horizontal form-validate" onsubmit="return validateForm();">
	<div id="ads_mainwrapper" class="row-fluid form-horizontal">

		<legend id="qtc_billin" >
			<?php echo JText::_('ADS_BILLIN')?>&nbsp;<small><?php //echo JText::_('ADS_BILLIN_DESC')?></small>
		</legend>

		<?php
		$socialadshelper = new socialadshelper();
		$buildform = $socialadshelper->getViewpath('buildad','billform');

		ob_start();
			include($buildform);
			$html = ob_get_contents();
		ob_end_clean();

		echo $html;
		?>
	</div><!-- END qtc_mainwrapper  -->

	<div class="form-actions">
		<button type="submit" class="btn btn-success" title="<?php echo JText::_('ADS_UPDATE_BILLIN_DETAILS'); ?>" >
				<?php echo JText::_('ADS_UPDATE_BILLIN_DETAILS'); ?>
		</button>
		<button class="btn btn-inverse" onclick="window.parent.SqueezeBox.close();" title="<?php echo JText::_('ADS_CANCEL_BILLIN_DETAILS'); ?>"><?php echo JText::_('ADS_CANCEL_BILLIN_DETAILS'); ?></button>

		<input type="hidden" name="option" value="com_socialads">
		<input type="hidden" name="task" value="billUpdate">
		<input type="hidden" name="view" value="buildad">
		<input type="hidden" name="controller" value="buildad">


	</div>
</form>
</div>
