<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );	
JHtml::_('behavior.formvalidation');
$document = JFactory::getDocument();
//$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css'); 	
//$document->addStyleSheet(JUri::base().'components/com_socialads/css/helper.css'); 
//require_once(JPATH_SITE.DS."components".DS."com_socialads".DS."views".DS."buildad".DS."tmpl".DS."default_camp.php");
$user = JFactory::getUser();
?>
<div class="techjoomla-bootstarp">
<?php
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
if($this->pricecamp==0) 
				{
						$mode =  JText::_('PAY_IMP');
				}
				else if($this->pricecamp==1)
				{
						$mode = JText::_('PAY_CLICK');
				}
				else if($this->pricecamp==3)
				{
						$mode = JText::_('SELL_THROUGH');
				}
			

?>
<script>

</script>



<!--body onload="myFunction()"-->


	
	
		<table class="table table-striped">
				<tr >
					<td><?php echo JText::_('CAMPAIGN'); ?></td>
					<td><div id="ncamp"><?php echo $this->ncamp; ?></div></td>
				</tr>
				<tr >
					<td><?php echo JText::_('SA_MODE'); ?></td>
					<td><div id="modecamp"><?php echo $mode ?></div></td>
				
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
				
			<div class="form-actions">
				<button id="buy" type="button" class="btn btn-success" onclick="submitbutton('save')"><?php echo JText::_('SAVE_ACTIVATE');?></button>
					<button id="edit" type="button" class="btn btn-warning" onclick="submitbutton('editad');"><?php echo JText::_('SHOWAD_EDIT');?></button>
					 <button id="draft" type="button" class="btn btn-info" onclick="submitbutton('draft');"><?php echo JText::_('SHOWAD_DRAFT');?></button>
			</div>		
		
		
	
</div><!--AKEEBA ENDS-->

