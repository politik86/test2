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
	$n = count ($this->camps);
	$lists = $this->lists;
	$params = $this->params;
	$configs = $this->configs;
	require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
	
	$user = JFactory::getUser();
	$listDirn = "asc";
	$listOrder = "ordering";
	$saveOrderingUrl = 'index.php?option=com_adagency&controller=adagencyCampaigns&task=saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
	
	JHtml::_('behavior.modal');
	JHtml::_('bootstrap.modal');
	$helper = new adagencyAdminHelper();
	$helperView = new adagencyAdminViewadagencyCampaigns();
	$document = JFactory::getDocument();
	$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
	
	$from = JRequest::getVar("from", "");
	$active = JRequest::getVar("active", "");
?>

<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton){
		if(pressbutton == "add"){
			if(eval(document.getElementById("cb0"))){
				document.getElementById("campaign-light").style.display = "";
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

<script language="javascript" type="text/javascript">
	function campaignDetails(campaign_id){
		
		var url = "index.php?option=com_adagency&controller=adagencyCampaigns&task=details&cid[]="+campaign_id+"&format=raw&tmpl=component";
		var req = new Request.HTML({
			method: 'get',
			url: url,
			data: { 'do' : '1' },
			//update: $("modal-body"),
			onComplete: function(response){
				document.getElementById("modal-body").empty().adopt(response);
			}
		}).send();
	}
</script>

<form action="index.php" name="adminForm" id="adminForm" method="post">
	<div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWTREECAMPAIGNS'); ?>
				</h2>
            </div>
            <a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674032">
	            <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
	            <?php echo JText::_("COM_ADAGENCY_VIDEO_CAMPAIGNS_SETTINGS"); ?>   
        	</a>
        </div>
	
		<div id="filter-bar" class="row-fluid">            
    			<div class="span12">                
    				<div class="filter-search btn-group pull-left">                    
    					<label for="search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>                    
    					<input type="text" id="filter_search" name="search_campaign" value="<?php if(isset($_SESSION['search_campaign'])) echo $_SESSION['search_campaign'];?>" />   
    					</div>                                                
    					<div class="btn-group pull-left hidden-phone">                    
    						<button class="btn adag_tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>                    
    						<button class="btn adag_tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>                
    					</div> 
    					<div class="btn-group pull-right hidden-phone">	                
    						<?php echo JText::_('VIEWAD_STATUS_SEARCH');?>
							<select name="campaign_status" onChange="document.adminForm.submit()">
								<option value="YN" <?php if (!isset($_SESSION['campaign_status']) || $_SESSION['campaign_status'] == 'YN') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_SEARCH_SELECT'); ?></option>
								<option value="Y" <?php if (isset($_SESSION['campaign_status']) && $_SESSION['campaign_status'] == 'Y') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_STATUS_SEARCH_APPROVED');?></option>
								<option value="N" <?php if (isset($_SESSION['campaign_status']) && $_SESSION['campaign_status'] == 'N') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_STATUS_SEARCH_DISSAPPROVED');?></option>
								<option value="P" <?php if (isset($_SESSION['campaign_status']) && $_SESSION['campaign_status'] == 'P') echo ' selected="selected" ';?>><?php echo JText::_('ADAG_PENDING');?></option>				
							</select>		     
    					</div>	      
		                <div class="btn-group pull-right hidden-phone">
		                    <?php echo JText::_('VIEWAD_SEARCH_ADVERTISER');?>
							<?php echo $lists['advertiser_id'];	?>	
		                </div>
		                <div class="btn-group pull-right hidden-phone">
		                     <?php echo JText::_('VIEWORDERSPACKAGE');?>&nbsp;
            				 <?php echo $lists['packs']; ?>
		                </div>  
		                 <div class="btn-group pull-right hidden-phone">
		                     <?php echo JText::_('NEWADZONE');?>&nbsp;
            				 <?php echo $lists['zones']; ?>
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
        <table class="table table-striped table-bordered" id="campaignList">	
            <thead>
                    <th width="5">
                        <input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
                        <span class="lbl"></lbl>
                    </th>
                    <th width="20">
                        <?php echo JText::_('ID'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('AD_CMP_CMPNAME'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('AD_INFO'); ?>
                    </th>
                    <th>
						<?php echo JText::_('AD_CMP_CMPPACK');?>
					</th>
                    <th>
                        <?php echo JText::_('AD_CMPACTIVE')."?"; ?>
                    </th>
                    <th>
                        <?php echo JText::_('AD_STATUS'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('VIEWTREEADS'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('AD_MANAGE_ADS'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('VIEWADACTION'); ?>
                    </th>
            </thead>
            
            <tbody>

        <?php 
        
            for ($i = 0; $i < $n; $i++):
                $camp = $this->camps[$i];
				$id = $camp->id;
                $checked = JHTML::_('grid.id', $i, $id);
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid[]=".$id);
                $published = $helperView->approve($camp, $i );
            
                $expired=0;
				if(($camp->type=="cpm"  || $camp->type=="pc") && $camp->quantity < 1){
					$expired=1;
				}
				
				if($camp->type=="fr" || $camp->type=="in"){
					$datan = date("Y-m-d H:i:s");
					if($datan > $camp->validity && $camp->validity != "0000-00-00 00:00:00"){
						$expired=1;
					}
				}
				
				if($from == "stats"){
					if($active == "Y"){
						$offset = JFactory::getApplication()->getCfg('offset');
						$jnow = JFactory::getDate('now', $offset);
						$current_date = $jnow->toSql(true);
						
						if($expired == 1 && $camp->status != "-1"){
							continue;
						}
						elseif((strtotime($camp->start_date) > strtotime($current_date)) && $camp->status != "-1"){
							continue;
						}
						elseif($camp->status == "1"){
							// do nothing
						}
						elseif($camp->status == "0"){
							continue;
						}
						elseif($camp->status == "-1"){
							continue;
						}
					}
					elseif($active == "N"){
						$offset = JFactory::getApplication()->getCfg('offset');
						$jnow = JFactory::getDate('now', $offset);
						$current_date = $jnow->toSql(true);
						
						if($expired == 1 && $camp->status != "-1"){
							// do nothing
						}
						elseif((strtotime($camp->start_date) > strtotime($current_date)) && $camp->status != "-1"){
							// do nothing
						}
						elseif($camp->status == "1"){
							continue;
						}
						elseif($camp->status == "0"){
							// do nothing
						}
						elseif($camp->status == "-1"){
							// do nothing
						}
					}
				}
        ?>
                <tr class="camp<?php echo $k;?>"> 
					<td>
                        <?php echo $checked;?>
                        <span class="lbl"></lbl>
                    </td>		
            
                    <td>
                        <?php echo $id;?>
                    </td>		
                    
                    <td>
                    	<a href="<?php echo $link; ?>" ><?php echo $camp->name;?></a>
                    </td>
                    
                    <td>
                    	<a data-toggle="modal" href="#campaign-details" onclick="javascript:campaignDetails(<?php echo intval($camp->id); ?>);">
                        	<i class="icon-info"></i>
                        </a>
                        
                        <div id="campaign-details" class="modal fade in" style="display: none;">
                            <div class="modal-header">
                            	<h2>
                            		<?php echo JText::_("ADAG_ABOUT_CAMPAIGN"); ?>
                                </h2>
                            </div>
                            
                            <div id="modal-body" class="modal-body">
                            </div>
                            
                            <div class="modal-footer">
                                <a href="#" class="btn" data-dismiss="modal" id="close-modal-btn">Close</a>
                            </div>
                        </div>
                    </td>
                    
                    <td>
        			<?php
                    	echo "<a href='index.php?option=com_adagency&controller=adagencyPackages&task=edit&cid[]=".$camp->package_id."'>".$camp->description."</a>";
					?>
        			</td>
            
            		<td>
						<?php
							$offset = JFactory::getApplication()->getCfg('offset');
							$jnow = JFactory::getDate('now', $offset);
							$current_date = $jnow->toSql(true);
							
							if($expired == 1 && $camp->status != "-1"){
								echo '<span class="label label-default campaign-status">
										'.JText::_("AD_CMPEXPIRED").'
									  </span>';
							}
							elseif((strtotime($camp->start_date) > strtotime($current_date)) && $camp->status != "-1"){
								echo '<span class="label label-warning campaign-status">
										'.JText::_("ADAG_START").": ".$helper->formatime($camp->start_date, $configs->params['timeformat']).'
									  </span>';
							}
							elseif($camp->status == "1"){
								echo '<span class="label label-success campaign-status">
										<a href="index.php?option=com_adagency&controller=adagencyCampaigns&task=pause&cid='.$camp->id.'" style="color:green" >'.JText::_("AD_CMPACTIVE").'</a>
									  </span>';
							}
							elseif($camp->status == "0"){
								echo '<span class="label label-warning campaign-status">
										<a href="index.php?option=com_adagency&controller=adagencyCampaigns&task=unpause&cid='.$camp->id.'" style="color:red">'.JText::_("AD_CMPPAUSED").'</a>
									  </span>';
							}
							elseif($camp->status == "-1"){
								echo '<span class="label label-important campaign-status">'.JText::_("ADAGA_DELETED").'</span>';
							}
						?>
                    </td>
                    
                    <td>
						<?php
							jimport('joomla.html.html.bootstrap');
							
								if($camp->approved == "Y" && $camp->status != "-1"){
									echo '<span class="adag_tip">
	                    					<i class="fa fa-check"></i>
											<span>'.JText::_("AD_APPROVED").'</span>
										</span>';
								}
								elseif($camp->approved == "N" && $camp->status != "-1"){
									echo '<span class="adag_tip">
	                    					<i class="fa fa-ban"></i>
											<span>'.JText::_("ADAG_DECLINED").'</span>
										</span>';
								}
								elseif($camp->approved == "P" && $camp->status != "-1"){
									echo '<span class="adag_tip">
	                    					<i class="fa fa-clock-o"></i>
											<span>'.JText::_("ADAG_PENDING").'</span>
										</span>';
								}
							
						?>
                    </td>
                    
                    <td>
						<?php
                        	if($camp->cnt){
								echo '<a href="index.php?option=com_adagency&controller=adagencyAds&cid[]='.$id.'" class="campaign-ads">'.$camp->cnt.'</a>';
                        	}
							else{
								echo '<p class="text-error campaign-ads">'.$camp->cnt.'</p>';
							}
						?>
                    </td>
                    
                    <td width="15%">
                    	
                    	<div class="btn-group">
                            <?php
								$btn_class = "";
								if($camp->cnt == 0){
									$btn_class = "btn-danger";
								}
								if($camp->status =="-1") $disabled = " disabled ";
								else $disabled = " enabled ";
							?>
                            <button class="btn <?php echo $btn_class; ?> dropdown-toggle" <?php echo $disabled; ?>  data-toggle="dropdown">
                            	<?php echo JText::_("AD_MANAGE_ADS"); ?>
                            	<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                            	<li><?php echo '<a href="index.php?option=com_adagency&controller=adagencyAds&task=add&camp_id='.$id.'">'.JText::_("ADAG_ADD_NEW_ADS").'</a>'; ?></li>
                                <li><?php echo '<a href="'.$link.'">'.JText::_("ASSIGN_EXISTING_ADS").'</a>'; ?></li>
                            </ul>
                        </div>
                    </td>
                    
                    <td>
                    	<div class="btn-group">
                            <button class="btn dropdown-toggle" <?php echo $disabled; ?> data-toggle="dropdown">
                            	<?php echo JText::_("VIEWADACTION"); ?>
                            	<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                            	<li><?php echo '<a href="'.$link.'">'.JText::_("AD_EDIT").'</a>'; ?></li>
                            	<?php
                            	if($expired == 0){
									$offset = JFactory::getApplication()->getCfg('offset');
									$jnow = JFactory::getDate('now', $offset);
									$current_date = $jnow->toSql(true);
									
									if($camp->status == 1 && strtotime($camp->start_date) < strtotime($current_date)){
								?>
                                		<li><?php echo '<a href="index.php?option=com_adagency&controller=adagencyCampaigns&task=pause&cid='.$camp->id.'">'.JText::_("AD_CMPPAUSE").'</a>'; ?></li>
								<?php
                                	}
								}
								
								?>
                            </ul>
                        </div>
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
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="adagencyCampaigns" />
    <input type="hidden" name="old_limit" value="<?php echo JRequest::getVar("limitstart"); ?>" />
</form>

<div id="light-modal-background" style="display:none;" onclick="javascript:closePopUp('campaign-light'); return false;">&nbsp;</div>

<div class="modal alert-light" id="campaign-light" style="display:none;">
    <p style="font-size: 17px; font-weight: bold; text-align: justify;"><?php echo JText::_("COM_ADAGENCY_ONE_CAMPAIGN"); ?></p>
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
    
    <a style="background: url(components/com_adagency/images/closebox.png) no-repeat scroll center center transparent; width: 30px; height: 30px; z-index: 70000; opacity: 1; position: absolute; top: -15px; right: -20px;" class="closeDOMWindow" id="close_domwin" href="#" onclick="javascript:closePopUp('campaign-light'); return false;" style="font-size:14px;"></a>
</div>