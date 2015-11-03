<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');

$input=JFactory::getApplication()->input;
          //$post=$input->post;
$document=JFactory::getDocument();
//$document->addScript(JUri::root().'media://techjoomla_strapper/js/akeebajq.js');
//$document->addScript(JUri::root().'components/com_socialads/css/socialads.css');
$document->addStyleSheet(JUri::root().'components/com_socialads/css/campaign.css');
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");


	require_once(JPATH_COMPONENT . DS . 'helper.php');
	$socialadshelper = new socialadshelper();
	$init_balance = $socialadshelper->getbalance();
	if($init_balance!=NULL && $init_balance !=1.00)    // HARDCODED FOR NOW.......
	{
		$itemid	= $socialadshelper->getSocialadsItemid('payment');
		$not_msg	= JText::_('MIM_BALANCE');
		$not_msg	= str_replace('{clk_pay_link}','<a href="'.JRoute::_('index.php?option=com_socialads&view=payment&Itemid='.$itemid).'">'.JText::_('SA_CLKHERE').'</a>', $not_msg);
		JError::raiseNotice( 100, $not_msg );
	}

$user =JFactory::getUser();
?>
<div class="techjoomla-bootstrap">
<?php
if (!$user->id)
{
	?>
	<div class="alert alert-block">
	<?php echo JText::_('BUILD_LOGIN'); ?>
	</div>
	</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	return false;
}

if ($socialads_config['select_campaign']=='0')
{
	?>
	<div class="alert alert-block">
	<?php echo JText::_('AD_NO_AUTH_SEE'); ?>
	</div>
	</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
return false;
}
$camp_name='';
$dailybudget='';
$campaign_id='';
$flag=0;
$list_show = "  active ";
$camp_show = "  ";
if($input->get('list','','STRING')=='list')
{
	$list_show = " active  ";
	$camp_show = "  ";
}
elseif($input->get('edit','','STRING')=='edit')
{
	$list_show = "   ";
	$camp_show = "  active";
	//fetch campgian info if edit campiagn
	$camp_info = $this->camp_info;
	//print_r($camp_info); die('asda');
	$camp_name = $camp_info[0]->campaign;
	$dailybudget = $camp_info[0]->daily_budget;
	$campaign_id = $camp_info[0]->camp_id;
	$flag=1; //to hide + button when editing
}

?>
<script>

function checkvalid(rClass,mini)
{

		var amt = document.getElementsByClassName('campaign_amount');
			for(var i = 0; i < amt.length; i++) {
			   var campaign_amount = amt[i].value;

			   if(parseFloat(campaign_amount) < parseFloat(mini))
				{
				   //alert('Please enter Correct Campaign Daily Budget');
				   alert('<?php echo JText::sprintf('SA_DAILY_CAMP_BUDGET',$socialads_config['camp_currency_daily'].' '.$socialads_config["currency"]) ?>');
				   //document.getElementsByClassName('campaign_name').focus();
				   return false;
				}

		}

		return;
}

function addClone(rId,rClass)
{

	var lastexistsElemetid = techjoomla.jQuery("."+rClass+":last").attr('id');
	var pre	=	lastexistsElemetid.replace(rClass, "");
	var current = pre;
	current++;

	var removeButton="<div class='span2 com_socialads_remove_button' id=com_socialads_remove_button"+pre+"'>";
	removeButton+="<label class='control-label'> Add button</label><div class='controls'>";
	removeButton+="<button class='btn btn-small btn-danger' type='button' id='remove"+pre+"'";
	removeButton+="onclick=\"removeClone('com_socialads_repeating_block"+pre+"','com_socialads_repeating_block');\" title=\"<?php echo JText::_('COM_SOCIALADS_REMOVE_TOOLTIP');?>\" >";
	removeButton+="<i class=\"icon-minus-sign icon-white\"></i></button>";
	removeButton+="</div></div><div class='clerfix'></div>";
	var newElem=techjoomla.jQuery('#'+rId+pre).clone().attr('id',rId+current);

	newElem.find('#camp_name'+pre).attr('id', 'camp_name'+current);
	newElem.find('label[for="camp_name'+pre+'"]').attr('for', 'camp_name'+current);
	newElem.find('#camp_amount'+pre).attr('id', 'camp_amount'+current);
	newElem.find('label[for="camp_amount'+pre+'"]').attr('for', 'camp_amount'+current);

	newElem.find('#com_socialads_add_button'+pre).attr('id', 'com_socialads_add_button'+current);
	techjoomla.jQuery('#'+rId+pre +"  #com_socialads_add_button"+pre).remove();
	techjoomla.jQuery('#'+rId+pre +"  .clerfix").remove();
	techjoomla.jQuery('#'+rId+pre).append(removeButton);
	techjoomla.jQuery('#'+rId+pre).after(newElem);

}

       function removeClone(rId,rClass){
               jQuery('#'+rId).remove();
       }
</script>
<?php
   //newly added for JS toolbar inclusion
   if(file_exists(JPATH_SITE . DS .'components'. DS .'com_community') and $socialads_config['show_js_toolbar']==1)
   {
	require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'toolbar.php');
	$toolbar    = CFactory::getToolbar();
	$tool = CToolbarLibrary::getInstance();

	?>
<style>
   <!--
      div#proimport-wrap #community-wrap { margin: 0;padding: 0; }
      div#proimport-wrap #community-wrap { min-height: 45px !important; }
      -->
</style>
<script src="<?php echo JUri::root().'components/com_community/assets/bootstrap/bootstrap.min.js'; ?>" type="text/javascript"></script>
<div id="proimport-wrap">
   <div id="community-wrap">
      <?php	echo $tool->getHTML();	?>
   </div>
</div>
<!-- end of proimport-wrap div -->
<?php
   }
   //eoc for JS toolbar inclusion
?>



	<div class="page-header">
		<h2><?php echo JText::_('CAMPAIGN');?></h2>
	</div>
	<ul class="nav nav-tabs" id="myTab">
		<li class="<?php echo $list_show; ?>" ><a href="#list" data-toggle="tab"><?php echo JText::_('SA_LIST');?></a></li>
		<li class="<?php echo $camp_show; ?>" ><a href="#camp" data-toggle="tab"><?php echo ($flag==0) ? JText::_('SA_CREATE_NEW_CAMP') :  JText::_('SA_EDIT_CAMP');?></a></li>
	</ul>
	<div class="tab-content">
			<div class="tab-pane <?php  echo $camp_show;  ?> " id="camp">
					<!--<form action="" method="post" name="createcamp" id="createcamp" enctype="multipart/form-data"
                       class="form-horizontal form-validate" onsubmit="return validateForm();">-->
					<form method="post" name="camp_form" id="camp_form" class="form-validate" enctype="multipart/form-data" value=""  >
							<div id="socialads_container" class="socialads_container row-fluid">
								<!--<div class="control-group">-->
									<div class="com_socialads_repeating_block" id="com_socialads_repeating_block1">
										<div class="span4">
											<label class="control-label" for="camp_name1" title="<?php echo JText::_('CAMPAIGN_NAME');?>" >
												<?php echo JText::_('CAMPAIGN_NAME');?>
											</label>
											<div class="controls">
												<input type="text" id="camp_name1" class="required campaign_name" name="camp_name[]"  value="<?php echo $camp_name; ?>">
											</div>
										</div>
										<div class="span4">
											<label class="control-label" for="camp_amount1" title="<?php echo JText::_('AMOUNT');?>" >
												<?php echo JText::_('DAILY_BUDGET');?>
											</label>
											<div class="controls">
												 <div class="input-append">
													<input type="text" class="required input-mini campaign_amount validate-numeric" id="camp_amount1" name="camp_amount[]" value="<?php echo $dailybudget; ?>" onchange="ad_checkforalpha(this,'46','<?php echo JText::_('ADS_ENTER_NUMERICS'); ?>')">
													 <span class="add-on"><?php echo $socialads_config["currency"]; ?></span>
												</div>
											</div>
										</div>
										<?php if($flag==0){ ?>
											<div class="span2 com_socialads_add_button" id="com_socialads_add_button1">
												<label class="control-label"> Add button</label>
												<div class="controls">
													<button class="btn btn-small btn-success" type="button" id='add' onclick="addClone('com_socialads_repeating_block','com_socialads_repeating_block');" title='<?php echo JText::_('COM_SOCIALADS_ADD_TOOLTIP');?>'>
														<i class="icon-plus-sign icon-white"></i>
													</button>
												</div>
											</div>
									<?php } ?>
										<div class="clerfix"></div>
									</div>
								 <!--</div>-->
							 </div>


					<div class="form-actions">
							<input type="submit" class="btn btn-primary" name="submit"  value="<?php echo JText::_('SUBMIT')?>" onclick="return checkvalid('socialads_container','<?php echo $socialads_config['camp_currency_daily']; ?>')"/>
					</div>
							<input type="hidden" name="option" value="com_socialads"/>
							<input type="hidden" name="controller" value="campaign"/>
							<input type="hidden" name="task" value="add_camp"/>
						<?php if($flag==1){ ?>
							<input type="hidden" name="campid" value="<?php echo $campaign_id; ?>" >
						<?php } ?>
					</form>
			</div><!--!ST LAYOUT-->
			<div class="tab-pane <?php  echo $list_show;  ?> " id="list">
					<?php echo $this->loadTemplate('list'); ?>
			</div><!--2ND LAYOUT-->
		</div><!--TAB ENDS-->
 </div><!--AKKEEBA ENDS-->



