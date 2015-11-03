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
$data = JRequest::get('post');
$chans = $this->chans;
	//echo "<pre>";var_dump($chans);die();
if(is_array($chans)) { $n = count($chans); } else { $n = 0; }
$k = 0;
	
$user = JFactory::getUser();
$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_adagency&controller=adagencyGeo&task=saveOrderAjax&tmpl=component';
JHtml::_('sortablelist.sortable', 'geochannels', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
JHtml::_('behavior.tooltip');
$helper = new adagencyAdminHelper();
?>


<form action="index.php" name="adminForm" id="adminForm" method="post">	
	 <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('ADAG_GEOCH'); ?>
				</h2>
                <a class="modal publisher-video pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="http://www.ijoomla.com/redirect/adserver/geo/channel.htm">
                    <img src="components/com_adagency/images/icon_video.gif" class="video_img" />
                    <?php echo JText::_("AD_VIDEO"); ?>   
                </a>
            </div>
        </div>
	
		<div id="filter-bar" class="row-fluid">            
    			<div class="span12">                
    				<div class="filter-search btn-group pull-left">                    
    					<label for="search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>                    
    					<input type="text" id="filter_search" name="search_channel" value="<?php 
							if(isset($data['search_channel'])) { echo $data['search_channel']; }
							elseif(isset($_SESSION['search_channel'])) { echo $_SESSION['search_channel']; }
						?>" />                
    					</div>                                                
    					<div class="btn-group pull-left hidden-phone">                    
    						<button class="btn adag_tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>                    
    						<button class="btn adag_tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>                
    					</div> 
    					<div class="btn-group pull-right hidden-phone">	                
    						<?php echo JText::_('VIEWADSTATUS');?>&nbsp;&nbsp <select name="public" onchange="document.adminForm.submit();"><option <?php if(!isset($this->p_filter)||($this->p_filter=='YN') || ($this->p_filter == '')) { echo 'selected="selected"';} ?> value='YN'><?php echo JText::_('ADAG_SEL_STS');?></option><option <?php if(isset($this->p_filter)&&($this->p_filter=='Y')) { echo 'selected="selected"';} ?> value='Y'><?php echo strtolower(JText::_('Approve'));?></option><option <?php if(isset($this->p_filter)&&($this->p_filter=='N')) { echo 'selected="selected"';} ?> value='N'><?php echo strtolower(JText::_('Unapprove'));?></option></select>
            				<?php echo $this->ul; ?>          
    					</div>	      
    					<div class="btn-group pull-right hidden-phone">
		                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
		                    <?php echo $this->pagination->getLimitBox(); ?>
		                </div>	         	
    			</div>    
    	</div>    
    <div class="row-fluid">
            <div class="span12">
            	<div id="editcell">
                <table class="table table-striped table-bordered" id="geochannels">	
				<thead>
					<tr>
						<th>
				        	<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				        </th>
				        <th width="5">
							<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
							<span class="lbl"></lbl>
						</th>
						<th width="20">
							<?php echo JText::_('ADAG_ID');?>
						</th>
				        <th>
							<?php echo JText::_('ADAG_NAME');?>
						</th>
				        <th>
							<?php echo JText::_('ADAG_CREATED_BY');?>
						</th>
				        <th>
							<?php echo JText::_('ADAG_CREATED_DATE');?>
						</th>
				         <th>
							<?php echo JText::_('VIEWADSTATUS');?>
						</th>                
					</tr>
				</thead>
				<tbody>
				
				<?php 
					for ($i = 0; $i < $n; $i++):
						$chan =& $this->chans[$i];
						$id = $chan->id;
						$checked = JHTML::_('grid.id', $i, $id);
						$link = JRoute::_("index.php?option=com_adagency&controller=adagencyGeo&task=edit&cid[]=".$id);		
						//$published = JHTML::_('grid.published', $zone, $i );
						$canCheckin = $user->authorise('core.manage',     'com_checkin') || $chan->checked_out == $userId || $chan->checked_out == 0;
						$canChange  = $user->authorise('core.edit.state', 'com_adagency.adagencyAds.') && $canCheckin;
				?>
					<tr class="row<?php echo $k;?>"> 
					    <td>
							<span class="sortable-handler active" style="cursor: move;">
				                <i class="icon-menu"></i>
				            </span>
				            <input type="text" class="width-20 text-area-order " value="<?php echo $chan->ordering; ?>" size="5" name="order[]" style="display:none;">
				        </td>
				        <td>
					       	<?php echo $checked;?>
					       	 <span class="lbl"></lbl>
						</td>		
						<td>
					       	<?php echo $id;?>
						</td>		
					    <td>
					     	<a href="<?php echo $link;?>" ><?php echo $chan->name;?></a>
						</td>		
				        <td>
				        	<?php 
								if($chan->from == 'F') { $chan->from = ' ['.JText::_('ADAG_FRONTEND').']'; } else { $chan->from = ' ['.JText::_('ADAG_BACKEND').']'; } 
								if($chan->advertiser_id != 0) { echo $chan->uname." (<a href='index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=".$chan->uid."'>".$chan->username."</a>)".$chan->from; } elseif(isset($chan->username)&&($chan->username != NULL)) { echo $chan->uname.' ('.$chan->username.')'.$chan->from; } else { echo '-'.$chan->from;}
							?>
				        </td>
				        <td>
				        	<?php //if($chan->banner_id) { echo "<a href='".$chan->blink."'>".$chan->bname."</a>"; } else { echo '-';}
								if(isset($chan->created)) {
									echo $helper->formatime($chan->created, $this->params);
								} else { echo "&nbsp;"; }
							?>
				        </td>
				        <td>
				        	<?php
				            	if(isset($chan->public)&&($chan->public == 'Y')){
									echo '<i class="fa fa-check" style="cursor: pointer;" onclick="document.getElementById(\'c2p\').value = \''.$id.'\';submitbutton(\'unpublish\');" alt="Approve" title="Approve" ></i>';
								} else {
									echo '<i class="fa fa-ban" style="cursor: pointer;" onclick="document.getElementById(\'c2p\').value = \''.$id.'\';submitbutton(\'publish\');"  alt="Unapprove" title="Unapprove"></i>';
								}
							?>
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
<input type="hidden" name="chan" value="" id="c2p" />
<input type="hidden" name="option" value="com_adagency" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="adagencyGeo" />
</form>