<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');


$user =JFactory::getUser();
?>
<div class="techjoomla-bootstrap">
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

// pause icon
    if(version_compare(JVERSION, '3.0', 'lt')) {
                                       $sa_icon_pause=" icon-pause ";
                               }
                               else
                               { // for joomla3.0
                                       $sa_icon_pause=" icon-checkbox-partial   ";
                               }
                              
?>

<script type="text/javascript">
	
		
	function check_status(cid,id){
		
		jQuery.ajax({   
	
		url: '?option=com_socialads&controller=campaign&task=change_status&campid='+cid,
		type: 'GET',
		
		success: function(response) {
		
		
		if(response==1)
		{
				document.getElementById(id).innerHTML="<i class='<?php echo $sa_icon_pause; ?> icon-white'></i>Pause";
		}
		else
		{
				document.getElementById(id).innerHTML="<i class='icon-play '></i>Start";
		}
		
		}
	});	
		}
		
		//check if campaign selected to create edit or delete ad
/*		function check_selected()
		{
			var i=0;
			var sel_Value	= '';
			 jQuery('.camp_check').each(function() {
			
				if(this.checked)
				{
						i++;
						sel_Value = this.value;
				}	
				})

			return i;
		}
*/		
		//create ad in a particular campaign
		function create_ad_camp(){
			//var i = check_selected();
			var i=0;
			var sel_Value	= '';
			 jQuery('.camp_check').each(function() {
			
				if(this.checked)
				{
						i++;
						sel_Value = this.value;
				}	
				})
			if(i > 1 || i==0)
			{
					alert('<?php echo JText::_('SA_SELECT_CAMP'); ?>');
					return false;
			}
			
				window.location.href = "index.php?option=com_socialads&view=buildad&campid="+sel_Value;
			
		}		
		
		
		//delete campaign 
		function delete_ad_camp()
		{
			//var i = check_selected();
			var i=0;
			var sel_Value	= '';
			 jQuery('.camp_check').each(function() {
			
				if(this.checked)
				{
						i++;
						sel_Value = this.value;
				}	
				})
			if(i==0)
			{
				alert('<?php echo JText::_('SA_SELECT_CAMP_DELET'); ?>');
				return false;
			}	
			 var result=confirm('<?php echo JText::_('DELET_COMFIRM'); ?>');
			 if(result==true)
			{
			document.adminForm.task.value="deletecampaign";
			document.adminForm.submit();
			}
		}		
			
		//function to edit singl campaign
		function edit_ad_camp(){
				//var i = check_selected();
			var i=0;
			var sel_Value	= '';
			 jQuery('.camp_check').each(function() {
			
				if(this.checked)
				{
						i++;
						sel_Value = this.value;
				}	
				})
				if(i > 1 || i==0)
					{
							alert('<?php echo JText::_('SA_SELECT_CAMP'); ?>');
							return false;
					}
				window.location.href = "index.php?option=com_socialads&view=campaign&edit=edit&campid="+sel_Value;
		}	

</script>



				
			
		<form action="" method="post" name="adminForm"	id="adminForm">
				
				<?php
                               if(version_compare(JVERSION, '3.0', 'lt')) {
                                       $sa_icon_edit=" icon-edit ";
                               }
                               else
                               { // for joomla3.0
                                       $sa_icon_edit=" icon-pencil-2 ";
                               }                                                        
                       ?>        
						
			<div class="sa-campaign-toolbar" id="sa-campaign-toolbar">
				<button type="button" style="" class="btn btn-success" name="add_ads" onclick="create_ad_camp()"><i class="icon-plus icon-white"></i><?php echo JText::_('CREATE_AD'); ?></button>
				<button type="button" style="" class="btn btn-warning" name="edit_camp" onclick="edit_ad_camp()"><i class="<?php echo $sa_icon_edit; ?> icon-white"></i><?php echo JText::_('EDIT_CAMP'); ?></button>
				<button type="button" style="" class="btn btn-danger" name="delete_ads" onclick="delete_ad_camp()"><i class="icon-trash icon-white"></i><?php echo JText::_('DELETE'); ?></button>
			</div>
			<div class="table-responsive">	
				<table class="table ">
					<thead>
						<tr>
							<th></th>
							<th><?php echo JHtml::_( 'grid.sort', JText::_('CAMP_NAME'), 'c.campaign', $this->lists['order_Dir'], $this->lists['order']); ?></th>
							<th><?php echo JHtml::tooltip(JText::_('NO_OF_ADS'), '','', JText::_('NO_OF_ADS'));?></th>
							<th><?php  echo JHtml::tooltip(JText::_('NO_CLICKS'), '','', JText::_('CLICKS'));  ?></th>
							<th><?php  echo JHtml::tooltip(JText::_('NO_IMPRESSIONS'), '','', JText::_('IMPRESSIONS')); ?></th>
							<th class="hidden-tablet hidden-phone"><?php echo JHtml::tooltip(JText::_('DESC_CLICK_THROUGH_RATIO'), JText::_('CLICK_THROUGH_RATIO'), '', JText::_('CLICK_THROUGH_RATIO'));?></th>
							<th><?php echo JHtml::_( 'grid.sort', JText::_('DAILY_BUDGET'), 'c.daily_budget', $this->lists['order_Dir'], $this->lists['order']); ?></th>
							<th><?php echo JHtml::tooltip(JText::_('ACTION'), '','', JText::_('ACTION')); ?></th>
						</tr>
					</thead>	
		
						
					
				
				<?php
				
				$i=0;
				//print_r($this->list); die('adasd');
				foreach($this->list as $key)
				{	
					$i++;
					//$v=JText::_('START'); 
					?>
						<tr>
							
							<td>
							<input id="crt_ad<?php echo $i; ?>" type="checkbox" class="camp_check" name="camp_name[]" value="<?php echo $key->camp_id; ?>"  />
							</td>
							<td>
								<?php echo $key->campaign; ?>
							</td>
							<td>
							<a href="index.php?option=com_socialads&view=managead&ad_camp_id=<?php echo $key->camp_id; ?>" title="<?php echo JText::_('CLICK_TO_VIEW'); ?>"><?php echo $key->no_of_ads; ?></a>
							</td>
							<td>
							<?php echo $key->clicks; ?>
							</td>
							<td>
							<?php echo $key->imp; ?>
							</td>
							<td class="hidden-tablet hidden-phone">
							<?php echo $key->ctc; ?>
							</td>
							<td>
							<?php echo $key->daily_budget; ?>
							</td>
							<td>
								<?php if($key->status==0) {?>
								 
								<div><button type="button" id="<?php echo $i; ?>" name="status" class="btn btn-primary" onclick="check_status(<?php echo $key->camp_id.",".$i; ?>)" ><i class="icon-play icon-white"></i><?php echo JText::_('START'); ?></button></div>
								
								<?php }else {?>
								
								<div><button type="button" id="<?php echo $i; ?>" name="status" class="btn btn-primary" onclick="check_status(<?php echo $key->camp_id.",".$i; ?>)" ><i class="<?php echo $sa_icon_pause; ?> icon-white"></i><?php echo JText::_('PAUSE'); ?></button></div>
								
								<?php } ?>
							</td>
						</tr>
				<?php }

				?>
				
				
	
				
				
				<input type="hidden" name="option" value="com_socialads" />
				<input type="hidden" name="view" value="campaign" /> 
				<input type="hidden" name="task" value="" /> 
				<input type="hidden" name="layout" value="default" /> 	
				<input type="hidden" name="list" value="list" /> 	
				<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
				</table>
			</div>
	</form>		
</div>

