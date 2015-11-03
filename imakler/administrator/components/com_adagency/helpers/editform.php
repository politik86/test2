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

$czones = $this->czones;
$czones_select = $this->czones_select;
$campaigns_zones = $this->campaigns_zones;

$configs = $this->configs;
$current = $this->channel;
$banners_camps=$this->these_campaigns;
if (!$banners_camps) { $banners_camps = array(); }
$data = $this->data;
$realimgs = '';
$camps = $this->camps;
$czones = $this->czones;
$lists = $this->lists;
$advertiser_id = $this->advertiser_id;
$_row=$this->ad;
$ad = $this->ad;
$editor1  = & JFactory::getEditor();
$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

$document = & JFactory::getDocument();
$url = JURI::base()."components/com_adagency/includes/css/ad_agency.css";
$document->addStyleSheet($url);
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
include_once(JPATH_BASE."/components/com_adagency/includes/js/transition.php");
require_once('components/com_adagency/helpers/geo_helper.php');
if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
    include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
}
include_once(JPATH_BASE."/components/com_adagency/includes/js/transition_geo.php");

$nullDate = 0;
if (!isset($_row->parameters['ad_code'])) $_row->parameters['ad_code']='';


$cpanel_home = "";
if(!class_exists('Mobile_Detect')){
	require_once(JPATH_BASE . "/components/com_adagency/helpers/Mobile_Detect.php");
}
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');	

if($deviceType == "computer"){
	$cpanel_home .= "<div class='row cpanelimg'>";
	$cpanel_home .= 	"<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
	$cpanel_home .= 	"<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
	$cpanel_home .= "</div>";
}
else{
	$cpanel_home .= '<div class="row-fluid adag_medium_title">
						<div class="cpanelimg span12 pagination-centered clearfix">
							<img src="'.JURI::root()."components/com_adagency/includes/css/home2.png".'" />
							<a href="/j30/index.php?option=com_adagency&amp;controller=adagencyCPanel&amp;Itemid=111">'.JText::_('ADAG_ADV_DASHB').'</a>
						</div>
					 </div>';
}

?>
		<div class="ijadagencytransition" id="adagency_container">
            <p id="hidden_adagency">
                <a id="change_cb">#</a><br />
                <a id="close_cb">#</a>
            </p>
        </div>
		
        <div class="componentheading" id="ijadagencypopup">
			<?php
                echo $cpanel_home;
                $class = "";
                if($deviceType != "computer"){
                    $class = 'class="adag_medium_title"';
                }
            ?>
            <h1 <?php echo $class; ?> ><?php echo JText::_('AD_TRANSITION'); ?></h1>
        </div>

<form class="form-horizontal" action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid='.$_row->id);?>" method="post" name="adminForm" id="adminForm">

	<div class="control-group hidden-phone hidden-tablet">
        <div>
            <span class="agency_subtitle"><?php echo  JText::_('NEWADDETAILS');?></span>
        </div>
    </div>

	<div class="control-group">
        <label for="transition_title" class="control-label"><?php echo JText::_('NEWADTITLE');?><font color="#ff0000">*</font></label>
        <div class="controls">
            <input type="text" id="transition_title" name="title" value="<?php if ($_row->title!="") {echo $_row->title;} else {echo "";} ?>">
        </div>
    </div>
    
    <div class="control-group">
        <label for="transition_description" class="control-label"><?php echo JText::_('NEWADDESCTIPTION');?></label>
        <div class="controls">
            <input type="text" name="description" id="transition_description"  value="<?php if ($_row->description!=""){echo $_row->description;} else {echo "";} ?>" />
        </div>
    </div>
    
    <div class="control-group">
        <label for="image_file" class="control-label"><?php echo JText::_('NEWADCONTENTAD');?><font color="#ff0000">*</font></label>
        <div class="controls">
            <?php
                if($deviceType == "computer"){
                    echo $editor1->display( 'transitioncode', ''.stripslashes($_row->parameters['ad_code']),'100%', '300px', '20', '60', false);
                }
                else{
                    echo '<textarea name="transitioncode" style="height:180px;">'.stripslashes($_row->parameters['ad_code']).'</textarea>';
                }
            ?>
        </div>
    </div>
    
    <?php 
		if($configs->allow_add_keywords == 1){
	?>
			<div class="control-group">
                <label for="keywords" class="control-label"><?php echo JText::_('ADAG_KEYWORDS');?></label>
                <div class="controls">
                	<input type="text" id="keywords" name="keywords" value="<?php if ($_row->keywords != ""){echo $_row->keywords;} else {echo "";} ?>" >
                    <br/>
                    <span class="ad_small_text">
                    	<?php echo JText::_("ADAG_ENTER_KEYWORDS"); ?>
                    </span>
                </div>
            </div>
	<?php
		}
	
        output_geoform($advertiser_id);
        require_once(JPATH_BASE."/administrator/components/com_adagency/helpers/jomsocial.php");
        JomSocialTargeting::render_front($_row->id);	
    ?>
    
    <?php
		if(isset($camps)&&(count($camps)>0)){
			$i=0;
	?>
			<div class="control-group">
				<div>
					<span class="agency_subtitle"><?php echo  JText::_('ADD_NEWADCMPS');?></span>
				</div>
			</div>
	<?php
		if($deviceType == "computer"){
	?>		
			<table class="table table-striped" width="100%" id="affiliateCampaigns">
				<tr style="color:#FFFFFF !important;">
					<th style="background-color:#999999 !important;">
					</th>
					<th style="background-color:#999999 !important;">
						<?php echo JText::_("CONFIGCMP"); ?>
					</th>
					<th style="background-color:#999999 !important;">
						<?php echo JText::_("ADAG_ZONES_SIZES"); ?>
					</th>
					<th style="background-color:#999999 !important;">
						<?php echo JText::_("ADAG_ON_WHICH_ZONE"); ?>
					</th>
				</tr>
	<?php
			
			$displayed = array();
			foreach ($camps as $camp) {
				$style = "";
				$style2 = ""; 
				if(!isset($czones_select[$camp->id])){
					$style = 'style="display:none;"';
					$style2 = 'display:none;';
				}
				
				if(in_array($camp->id, $displayed)){
					continue;
				}
				$displayed[] = $camp->id;
				$i++;
	?>
				<tr>
					<td class="check_camp">
						<input type="hidden" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>|no|" />
						<input class="formField camp<?php echo $camp->id; ?>" type="checkbox" <?php
						if(in_array($camp->id,$banners_camps)){
							echo 'checked="checked"';
						}
						?>id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
					</td>
					
					<td>
						<label><?php echo $camp->name; ?></label>
					</td>
					
					<td style="width:40%">
						<?php
							if(isset($czones[$camp->id])){
								foreach($czones[$camp->id] as $czone){
									$zone_width = $czone["width"];
									$zone_height = $czone["height"];
									$ad_width = $ad->width;
									$ad_height = $ad->height;
									
									if(trim($zone_width) != "" && trim($zone_height) != ""){
										if(trim($zone_width) < trim($ad_width) && trim($zone_height) < trim($ad_height)){
											unset($campaigns_zones[$camp->id][$czone["zoneid"]]);
										}
									}
								}
							}
										
							if(isset($campaigns_zones[$camp->id]) && count($campaigns_zones[$camp->id]) > 0){
								echo implode("<br/>", $campaigns_zones[$camp->id]);
							}
						?>
					</td>
					
					<td align="left" class="check_ad">
						<?php
							$ok = FALSE;
							
							if(isset($czones[$camp->id])){
								foreach($czones[$camp->id] as $czone){
									$zone_width = $czone["width"];
									$zone_height = $czone["height"];
									$ad_width = $ad->width;
									$ad_height = $ad->height;
									
									$params = $czone["adparams"];
									$params = unserialize($params);
									
									if(trim($zone_width) != "" && trim($zone_height) != "" && isset($params["transition"])){
										if(trim($zone_width) == trim($ad_width) && trim($zone_height) == trim($ad_height)){
											$ok = TRUE;
											break;
										}
									}
									elseif(trim($zone_width) == "" && trim($zone_height) == "" && isset($params["transition"])){
										$ok = TRUE;
										break;
									}
								}
							}
						
							if($ok){
								echo $czones_select[$camp->id];
							}
							else{
								echo '<span class="label label-important">'.JText::_("ADAG_SIZE_OF_AD_UPLOADED")." (".$_row->width." x ".$_row->height." px) ".JText::_("ADAG_NOT_SUPPORTED_BY_THIS_CAMPAIGN").'</span>';
							}
						?>
					</td>
				</tr>
<?php
			}
?>
			</table>
           
		<?php
			}// if compunter
        	else{
				//if phone
		?>
        		<table class="table table-striped" width="100%" id="affiliateCampaigns">
        <?php
        			$displayed = array();
					foreach ($camps as $camp) {
						$style = "";
						$style2 = ""; 
						if(!isset($czones_select[$camp->id])){
							$style = 'style="display:none;"';
							$style2 = 'display:none;';
						}
						
						if(in_array($camp->id, $displayed)){
							continue;
						}
						$displayed[] = $camp->id;
						$i++;
		?>
        				<tr>
                            <td class="check_camp">
                            	
                                		<div class="span1 grid1">
                                            <input type="hidden" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>|no|" />
                                            <input class="formField camp<?php echo $camp->id; ?>" type="checkbox" <?php
                                            if(in_array($camp->id,$banners_camps)){
                                                echo 'checked="checked"';
                                            }
                                            ?>id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
										</div>
                                        <div class="span11 grid11">
                                    		<label><?php echo $camp->name; ?></label>
											<?php
                                                $ok = FALSE;
                                                
                                                if(isset($czones[$camp->id])){
													foreach($czones[$camp->id] as $czone){
														$zone_width = $czone["width"];
														$zone_height = $czone["height"];
														$ad_width = $ad->width;
														$ad_height = $ad->height;
														
														$params = $czone["adparams"];
														$params = unserialize($params);
														
														if(trim($zone_width) != "" && trim($zone_height) != "" && isset($params["transition"])){
															if(trim($zone_width) == trim($ad_width) && trim($zone_height) == trim($ad_height)){
																$ok = TRUE;
																break;
															}
														}
														elseif(trim($zone_width) == "" && trim($zone_height) == "" && isset($params["transition"])){
															$ok = TRUE;
															break;
														}
													}
												}
                                            
                                                if($ok){
                                                    echo $czones_select[$camp->id];
                                                }
                                                else{
                                                    echo '<span class="label label-important">'.JText::_("ADAG_SIZE_OF_AD_UPLOADED")." (".$_row->width." x ".$_row->height." px) ".JText::_("ADAG_NOT_SUPPORTED_BY_THIS_CAMPAIGN").'</span>';
                                                }
                                            ?>
										</div> 
                            </td>
						</tr>
        <?php			
					}
		?>
                </table>
        <?php
			}
		?>  
            
<?php
		}
    ?>

		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="media_type" value="Transition" />
		<input type="hidden" name="id" value="<?php echo $_row->id;?>" />
		<input type="hidden" name="parameters['border']" value="<?php echo @$_row->parameters['border'];?>" />
		<input type="hidden" name="parameters['border_color']" value="<?php echo @$_row->parameters['border_color'];?>" />
		<input type="hidden" name="parameters['bg_color']" value="<?php echo @$_row->parameters['bg_color'];?>" />
		<input type="hidden" name="controller" value="adagencyTransition" />
        <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
       
        
        <?php
		$new_class = "";
		if($deviceType != "computer"){
			$new_class = "pagination-centered clearfix";
		}
	?>
	
		<div class="control-group">
			<div class="controls">
				<div class="<?php echo $new_class; ?>">
					<input class="btn" type="button" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_BACK'); ?>" />
					<input style="margin-left: 5px;" class="btn btn-warning" type="button" value="<?php echo JText::_("AD_SAVE");?>" onclick="Joomla.submitbutton('save');">
				</div>
			</div>
		</div>
        
</form>