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

	$k = 0;
	$n = count ($this->advertisers);
	require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
	
	
$user = JFactory::getUser();
$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=saveOrderAjax&tmpl=component';
JHtml::_('sortablelist.sortable', 'advertiserlist', 'adminForm', strtolower($listDirn), $saveOrderingUrl);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('behavior.modal');
$helper = new adagencyAdminHelper();
$helperView = new adagencyAdminViewadagencyAdvertisers();
$loggeduser = JFactory::getUser();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton){
		if(pressbutton == "wizard"){
			if(eval(document.getElementById("cb0")) && eval(document.getElementById("cb1"))){
				document.getElementById("advertiser-light").style.display = "";
				document.getElementById("light-modal-background").style.display = "";
				return false;
			}
		}
		submitform(pressbutton);
	}
	
	function closePopUp(modal_id){
		document.getElementById(modal_id).style.display = "none";
		document.getElementById("light-modal-background").style.display = "none";
	}
</script>

<form action="index.php" name="adminForm" id="adminForm" method="post">
	<div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWTREEADVERTISERS'); ?>
				</h2>
            </div>
            <a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674003">
			    <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
			    <?php echo JText::_("COM_ADAGENCY_VIDEO_ADVERTISERS_SETTINGS"); ?>   
			</a>
       </div>
	
		<div id="filter-bar" class="row-fluid">            
    			<div class="span12">                
    				<div class="filter-search btn-group pull-left">                    
    					<label for="search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>                    
    					<input type="text" id="filter_search" name="search_advertiser" value="<?php if(isset($_SESSION['search_advertiser'])) echo $_SESSION['search_advertiser'];?>" />   
    					</div>                                                
    					<div class="btn-group pull-left hidden-phone">                    
    						<button class="btn adag_tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>                    
    						<button class="btn adag_tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>                
    					</div> 
    					<div class="btn-group pull-right hidden-phone">	                
    						<?php echo JText::_('VIEWAD_STATUS_SEARCH');?>
							<select name="advertiser_status" onChange="document.adminForm.submit()">
								<option value="YN" <?php if (!isset($_SESSION['advertiser_status']) || $_SESSION['advertiser_status'] == 'YN') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_SEARCH_SELECT');?></option>
								<option value="Y" <?php if (isset($_SESSION['advertiser_status']) && $_SESSION['advertiser_status'] == 'Y') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_STATUS_SEARCH_APPROVED');?></option>
								<option value="N" <?php if (isset($_SESSION['advertiser_status']) && $_SESSION['advertiser_status'] == 'N') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_STATUS_SEARCH_DISSAPPROVED');?></option>
								<option value="P" <?php if (isset($_SESSION['advertiser_status']) && $_SESSION['advertiser_status'] == 'P') echo ' selected="selected" ';?>><?php echo JText::_('ADAG_PENDING');?></option>
							</select>     
    					</div>	      
		                <div class="btn-group pull-right hidden-phone">
		                    <?php echo JText::_('ADAG_MBR_STS');?>
							<select name="advertiser_enable" onChange="document.adminForm.submit()">
								<option value="-1" <?php if (!isset($_SESSION['advertiser_enable']) || $_SESSION['advertiser_enable'] == '-1') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_SEARCH_SELECT'); ?></option>
								<option value="0" <?php if (isset($_SESSION['advertiser_enable']) && $_SESSION['advertiser_enable'] == '0') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_STATUS_SEARCH_ENABLED');?></option>
								<option value="1" <?php if (isset($_SESSION['advertiser_enable']) && $_SESSION['advertiser_enable'] == '1') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_STATUS_SEARCH_DISABLED');?></option>
							</select>
		                </div>
		                <div class="btn-group pull-right hidden-phone">
		                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
		                    <?php echo $this->pagination->getLimitBox(); ?>
		                </div>	       	
    			</div>    
    	</div>    
<div class="row-fluid">
<div class="span12">
<div id="editcell" >
<table class="table table-striped table-bordered" id="advertiserlist">
<thead>
		<th>
        	<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
        </th>
        <th width="5">
			<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
			 <span class="lbl"></lbl>
		</th>
	        <th width="20">
			<?php echo JText::_('VIEWADVERTISERID');?>
		</th>
		<th>
			<?php echo JText::_('VIEWADVERTISERCONTACT');?>
		</th>
		<th>
			<?php echo JText::_('VIEWADVERTISERAPPROVED');?>
		</th>

		<th>
			<?php echo JText::_('ADAG_USER');?>
		</th>

		<th>
			<?php echo JText::_('VIEWADVERTISERENABLED');?>
		</th>
		<th>
			<?php echo JText::_('VIEWADVERTISEREMAIL');?>
		</th>

		<th>
			<?php echo JText::_('ADAG_DATEADD');?>
		</th>

		<th>
			<?php echo JText::_('VIEWADVERTISERCAMPAIGNS');?>
		</th>
</thead>

<tbody>

<?php
	$uid = 0;
	for ($i = 0; $i < $n; $i++):
	$advertiser = $this->advertisers[$i];
	$id = $advertiser->aid;
	$uid = $advertiser->id;
	$checked = JHTML::_('grid.id', $i, $id);
	$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=".$uid);
	$cmplink = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&advertiser_id=".$id);
	$published = $helperView->approve($advertiser, $i );
	$blocked = $helperView->block($advertiser, $i );
	
	$canCheckin = $user->authorise('core.manage',     'com_checkin') || $advertiser->checked_out == $userId || $advertiser->checked_out == 0;
	$canChange  = $user->authorise('core.edit.state', 'com_adagency.adagencyAds.') && $canCheckin;
?>
	<tr class="row<?php echo $k;?>">
	    <td>
			<span class="sortable-handler active" style="cursor: move;">
                <i class="icon-menu"></i>
            </span>
            <input type="text" class="width-20 text-area-order " value="<?php echo $advertiser->ordering; ?>" size="5" name="order[]" style="display:none;">
        </td>
        
        <td>
	     	    	<?php echo $checked;?>
	     	    	 <span class="lbl"></lbl>
		</td>

	     	<td>
	     	    	<?php echo $id;?>
		</td>
	     	<td>
	     	    	<a href="<?php echo $link;?>" ><?php echo $advertiser->name;?></a>
		</td>
		<td>
	     	    	<?php echo $published;?>
		</td>

		<td>
					<?php echo "<a target='_blank' href='index.php?option=com_users&task=user.edit&id=".intval($advertiser->user_id)."'>".$advertiser->username."</a>";?>
		</td>

		<td>
	     	    	<?php //echo $blocked;
						$states	= array(
							1	=> array(
								'unblock',
								'',
								'',
								'',
								true,
								'unpublish',
								'unpublish'
							),
							0	=> array(
								'block',
								'',
								'',
								'',
								true,
								'publish',
								'publish'
							),
						);
						$self = $loggeduser->id == $advertiser->id;
						echo JHtml::_('jgrid.state', $states, $advertiser->block, $i, '', !$self);
					?>
		</td>
		<td>
	     	    	<?php echo $advertiser->email;?>
		</td>

		<td>
					<?php echo $helper->formatime($advertiser->registerDate, @$this->params['timeformat']); ?>
		</td>

		<td>
	     	    	<a href="<?php echo $cmplink;?>" ><?php echo $advertiser->count;?></a>
		</td>
	</tr>


<?php
		$k = 1 - $k;
	endfor;
?>
</tbody>
</table>
<?php echo $this->pagination->getListFooter(); ?>
</div>
</div>
</div>

<input type="hidden" name="option" value="com_adagency" />
<input type="hidden" name="id" value="<?php echo $uid;?>"/>
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="adagencyAdvertisers" />
<input type="hidden" name="old_limit" value="<?php echo JRequest::getVar("limitstart"); ?>" />
</form>

<div id="light-modal-background" style="display:none;" onclick="javascript:closePopUp('advertiser-light'); return false;">&nbsp;</div>

<div class="modal alert-light" id="advertiser-light" style="display:none;">
    <p style="font-size: 17px; font-weight: bold; text-align: justify;"><?php echo JText::_("COM_ADAGENCY_ONE_ADVERTISER"); ?></p>
    <br />
    <br />
    <p class="pagination-centered">
        <form id="prform" name="prform" target="_blank" style="margin:0px; text-align: center;" method="post" action="http://www.ijoomla.com/index.php?option=com_digistore&controller=digistoreCart&task=add&pid[0]=81&cid[0]=81" onsubmit="return prodformsubmit4a60cb04c1341();">
            <input name="qty" value="1" type="hidden" />
            <input name="pid" id="product_id" value="81" type="hidden" />
            <input name="Button" type="submit" class="btn btn-warning" value="Buy Pro" />
        </form>
    </p>
    <br />
    <br />
    <p class="pagination-centered">
        <a href="http://adagency.ijoomla.com/pricing/" style="font-size:18px; text-decoration:underline;" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
    </p>
    
    <a style="background: url(components/com_adagency/images/closebox.png) no-repeat scroll center center transparent; width: 30px; height: 30px; z-index: 70000; opacity: 1; position: absolute; top: -15px; right: -20px;" class="closeDOMWindow" id="close_domwin" href="#" onclick="javascript:closePopUp('advertiser-light'); return false;" style="font-size:14px;"></a>

</div>
