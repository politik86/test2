<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
JHtml::_('behavior.formvalidation');
$document = JFactory::getDocument();

// get ad preview
// get payment HTML
//JLoader::import('showad',JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
JLoader::import('showad', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');

$showadmodel = new socialadsModelShowad();

$preview = $showadmodel->getAds($ad_id);
$this->preview = $preview

?>

<div class="techjoomla-bootstarp ad_reviewAdmainContainer">
<?php
$user = JFactory::getUser();

if (!$user->id)
{
	?>
	<div class="alert alert-success">
		<?php echo JText::_('BUILD_LOGIN'); ?>
	</div>

</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	return false;
}

require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
if($AdPreviewData->ad_payment_type==0)
{
	$mode =  JText::_('PAY_IMP');
}
else if($AdPreviewData->ad_payment_type==1)
{
	$mode = JText::_('PAY_CLICK');
}
else if($AdPreviewData->ad_payment_type==3)
{
	$mode = JText::_('SELL_THROUGH');
}


?>
<script>

</script>

	<form action="" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="" >
		<fieldset class="sa_fieldset">
			<legend class="hidden-desktop"><?php echo JText::_('COM_SOCIALADS_REVIEW_AD'); ?></legend>
		<!-- for ad detail and preview -->
		<div class=" row-fluid show-grid">
			<!--ad detai start -->
			<div class="span6 well">
				<h4><?php echo JText::_('ADS_PAYMENT_REVIEW');?></h4>
				<table class="table table-striped">
					<tr >
						<td><?php echo JText::_('CAMPAIGN'); ?></td>
						<td><div id="ncamp"><?php echo $AdPreviewData->campaign; ?></div></td>
					</tr>
					<tr >
						<td><?php echo JText::_('SA_MODE'); ?></td>
						<td><div id="modecamp"><?php echo $mode; ?></div></td>

					</tr>
					<tr >
						<?php /*if($socialads_config['bidding']==1 && $this->bid_value)
						{ ?>
						<td><?php echo JText::_('BID_VALUE'); ?></td>
						<td>
							<div id="bid"><?php echo $this->bid_value; echo " "; echo JText::_('USD'); ?></div>

						</td>
					<?php  }*/ ?>
					</tr>
				</table>

			</div>
			<div class="span6">
				<?php echo $this->preview; ?>
			</div>
		</div>


		<input type="hidden" name="option" value="com_socialads"/>
		<input type="hidden" name="controller" value="buildad"/>
		<input type="hidden" name="task" value=""/>


		<div class="form-actions">
			<button id="buy" type="button" class="btn btn-success" onclick="submitbutton('activateAd')"><?php echo JText::_('SAVE_ACTIVATE');?></button>
			 <button id="draft" type="button" class="btn btn-info" onclick="submitbutton('draftAd');"><?php echo JText::_('SHOWAD_DRAFT');?></button>
		</div>
		</fieldset>
	</form>

</div><!--AKEEBA ENDS-->


<script type="text/javascript">

	function submitbutton(pressbutton)
	{
		var form = document.previewAd;
		submitform(pressbutton);
	}
</script>
