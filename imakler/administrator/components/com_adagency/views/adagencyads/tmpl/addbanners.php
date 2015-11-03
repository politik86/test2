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

	$document = JFactory::getDocument();
	$item_id = $this->itemid;
	if($item_id != 0) { $Itemid = "&Itemid=".intval($item_id); } else { $Itemid = NULL; }
	$item_id_cpn = $this->itemid_cpn;
    if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".intval($item_id_cpn); } else { $Itemid_cpn = NULL; }
	
	$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
	
    $configs = $this->configs;
	$root = JURI::root();
	$url=JURI::base()."components/com_adagency/includes/css/ad_agency.css";
	$iconsaddr = JURI::base()."components/com_adagency/images/";
	$document->addStyleSheet($url);
	JHtml::_('behavior.framework',true);
	
    $document->addStyleSheet('components/com_adagency/includes/css/adagency_template.css');
	require_once(JPATH_BASE . "/components/com_adagency/includes/js/ads.php");
	$type = $this->type;
?>
<div id="add_ads">
<?php
	if(isset($this->wiz)) {
		echo "<div class='row-fluid alert alert-message'>".JText::_('ADAG_PENDING_ADS2')."</div>";
	}
			$my	=  JFactory::getUser();
			$return_url = base64_encode("index.php?option=com_adagency".$Itemid);
			$ads = $this->getModel("adagencyConfig")->getItemid('adagencyads');
	        $adv = $this->getModel("adagencyConfig")->getItemid('adagencyadvertisers');
	        $cmp = $this->getModel("adagencyConfig")->getItemid('adagencycampaigns');
	        $rep = $this->getModel("adagencyConfig")->getItemid('adagencyreports');
	        $ord = $this->getModel("adagencyConfig")->getItemid('adagencyorders');

		    if($ads != 0) { $Itemid_ads = "&Itemid=" . intval($ads); } else { $Itemid_ads = NULL; }
		    if($adv != 0) { $Itemid_adv = "&Itemid=" . intval($adv); } else { $Itemid_adv = NULL; }
		    if($cmp != 0) { $Itemid_cmp = "&Itemid=" . intval($cmp); } else { $Itemid_cmp = NULL; }
		    if($ord != 0) { $Itemid_ord = "&Itemid=" . intval($ord); } else { $Itemid_ord = NULL; }
		    if($rep != 0) { $Itemid_rep = "&Itemid=" . intval($rep); } else { $Itemid_rep = NULL; }
?>
<div id="adg_toolbar" class="adg_row">
			<div class="adg_cell span12">
				<div> 
					<div class="clearfix">
						<ul>
							<li><a href="<?php echo $cpn_link;?>"><i class="fa fa-home"></i><?php echo JText::_('ADG_DASH'); ?></a></li>
				            <li><a  href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=<?php echo intval($my->id) . $Itemid_adv;?>"><i class="fa fa-user"></i><?php echo JText::_('ADG_PROF'); ?></a></li>
				            <li class="adg_active"><a href="index.php?option=com_adagency&controller=adagencyAds<?php echo $Itemid_ads; ?>"><i class="fa fa-bars"></i><?php echo JText::_('ADG_ADS'); ?></a></li>
				            <li><a href="index.php?option=com_adagency&controller=adagencyOrders<?php echo $Itemid_ord; ?>"><i class="fa fa-shopping-cart"></i><?php echo JText::_('ADG_ORDERS'); ?></a></li>
				            <li><a href="index.php?option=com_adagency&controller=adagencyReports<?php echo $Itemid_rep; ?>"><i class="fa fa-bar-chart-o"></i><?php echo JText::_('ADG_REPORTS'); ?></a></li>
				            <li><a href="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo $Itemid_cmp; ?>"><i class="fa fa-calendar-o"></i><?php echo JText::_('ADG_CAMP'); ?></a></li>
				            <li><a href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><i class="fa fa-sign-out"></i><?php echo JText::_('ADG_LOGOUT'); ?></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>	
<div class="page-title">
		<h2><?php echo JText::_('ADAG_ADDNEWAD'); ?> </h2>
</div>

<div class="adg_row">
	<div class="adg_cell span12">
		<div><div>
		<?php
			echo "<div class='row-fluid adag_top_bottom_spacer'>".JText::_('ADAG_SELADTYPE');
			if($type == 'banner'){
				echo "<a class='ad-padding-left' href='index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid."'> << ".JText::_('ADAG_BKTOADS')."</a>";
			}
			echo "</div>";
		?>
		
        <form class="form-horizontal clearfix adg_row" method="post" name="adminForm" id="adminForm">
			<?php
            if($type == 'banner'){
                if($configs->allowstand) {
                    $link = JRoute::_('index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]=0'.$Itemid);
                    echo "<div class='adag_bannersfa pull-left span12 adg_cell clearfix'>";
                    echo 	'<div style="float:left; display: table-cell;">';
                    echo 		"<a href='".$link."'><i class='fa fa-picture-o adg_ico_img'></i></a> ";
                    echo 	'</div>';
                    echo 	'<div style="display: table-cell;">';
                    echo 		"<a id='adag_bannersfa_title6' href='".$link."'>".JText::_('JAS_STANDART')."</a>";
                    echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_STANDARD')."</div>";
                    echo 	'</div>';
                    echo '</div>';
                }
    
                if($configs->allowswf) {
                    $link = JRoute::_('index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]=0'.$Itemid);
                    echo "<div class='adag_bannersfa pull-left span12 adg_cell clearfix'>";
                    echo 	'<div style="float:left; display: table-cell;">';
                    echo 		"<a href='".$link."'><i class='fa fa-flash adg_ico_img'></i></a> ";
                    echo 	'</div>';
                    echo 	'<div style="display: table-cell;">';
                    echo 		"<a id='adag_bannersfa_title7' href='".$link."'>".JText::_('JAS_FLASH')."</a>";
                    echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_FLASH')."</div>";
                    echo 	'</div>';
                    echo '</div>';
                }
    
                if($configs->allowadcode) {
                    $link = JRoute::_('index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=0'.$Itemid);
                    echo "<div class='adag_bannersfa pull-left span12 adg_cell clearfix'>";
                    echo 	'<div style="float:left; display: table-cell;">';
                    echo 		"<a href='".$link."'><i class='fa fa-building-o adg_ico_img'></i></a> ";
                    echo 	'</div>';
                    echo 	'<div style="display: table-cell;">';
                    echo 		"<a id='adag_bannersfa_title7' href='".$link."'>".JText::_('JAS_AFFILIATE')."</a>";
                    echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_ADV')."</div>";
                    echo 	'</div>';
                    echo '</div>';
                }
            }
            else{
                if(($configs->allowstand)||($configs->allowswf)||($configs->allowadcode)) {
                    $link = JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=addbanners&type=banner'.$Itemid);
                    echo "<div class='adag_bannersfa pull-left span6 adg_cell '>";
                    echo 	'<div style="float:left; display: table-cell;">';
                    echo 		"<a href='".$link."'><i class='fa fa-desktop adg_ico_img'></i></a> ";
                    echo 	'</div>';
                    echo 	'<div style="display: table-cell;">';
                    echo 		"<a id='adag_bannersfa_title1' href='".$link."'>".JText::_('ADAG_BANNERSFA')."</a>";
                    echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_BANNER')."</div>";
                    echo 	'</div>';
                    echo '</div>';
                }
    
                if($configs->allowpopup) {
                    $link = JRoute::_('index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid[]=0'.$Itemid);
                    echo "<div class='adag_bannersfa pull-left span6  adg_cell '>";
                    echo 	'<div style="float:left; display: table-cell;">';
                    echo 		"<a href='".$link."'><i class='fa fa-square adg_ico_img'></i></a> ";
                    echo 	'</div>';
                    echo 	'<div style="display: table-cell;">';
                    echo 		"<a id='adag_bannersfa_title2' href='".$link."'>".JText::_('JAS_POPUP')."</a>";
                    echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_POPUP')."</div>";
                    echo 	'</div>';
                    echo '</div>';
                }
    
                if($configs->allowtxtlink) {
                    $link = JRoute::_('index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=0'.$Itemid);
                    echo "<div class='adag_bannersfa pull-left span6  adg_cell '>";
                    echo 	'<div style="float:left; display: table-cell;">';
                    echo 		"<a href='".$link."'><i class='fa fa-font adg_ico_img'></i></a> ";
                    echo 	'</div>';
                    echo 	'<div style="display: table-cell;">';
                    echo 		"<a id='adag_bannersfa_title3' href='".$link."'>".JText::_('JAS_TEXT_LINK')."</a>";
                    echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_TEXTAD')."</div>";
                    echo 	'</div>';
                    echo '</div>';
                }
   
    
                if($configs->allowfloat) {
                    $link = JRoute::_('index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid[]=0'.$Itemid);
                    echo "<div class='adag_bannersfa pull-left span6  adg_cell '>";
                    echo 	'<div style="float:left; display: table-cell;">';
                    echo 		"<a href='".$link."'><i class='fa fa-chain-broken adg_ico_img'></i></a> ";
                    echo 	'</div>';
                    echo 	'<div style="display: table-cell;">';
                    echo 		"<a id='adag_bannersfa_title5' href='".$link."'>".JText::_('JAS_FLOATING')."</a>";
                    echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_FLOATING')."</div>";
                    echo 	'</div>';
                    echo '</div>';
                }
                 if($configs->allowtrans) {
                    $link = JRoute::_('index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid[]=0'.$Itemid);
                    echo "<div class='adag_bannersfa pull-left span6  adg_cell '>";
                    echo 	'<div style="float:left; display: table-cell;">';
                    echo 		"<a href='".$link."'><i class='fa fa-arrows-h adg_ico_img'></i></a> ";
                    echo 	'</div>';
                    echo 	'<div style="display: table-cell;">';
                    echo 		"<a id='adag_bannersfa_title4' href='".$link."'>".JText::_('JAS_TRANSITION')."</a>";
                    echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_TRANSITION')."</div>";
                    echo 	'</div>';
                    echo '</div>';
                }
            }
            ?>
		</form>
		<input style="" type="button" class="btn" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_BACK'); ?>" />
	</div>
	</div></div>
</div>
</div>