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
defined( '_JEXEC' ) or die( 'Restricted access' );// JHTML::_('behavior.tooltip');
// JHTML::_('behavior.modal');
    $order = $this->packages;
    $k = 0;
    $n = count ($order);
    $my =JFactory::getUser();
    $configs = $this->configs;
    $overview = stripslashes($configs->overviewcontent);
    $currencydef = trim($configs->currencydef," ");
    $advertiserid = $this->advertiserid;
    $showZoneInfo = $this->showZoneInfo;
    $itemid = $this->itemid;
	$showPackages = true;
    if($itemid->adv != 0) { $Itemid_adv = "&Itemid=" . intval($itemid->adv); } else { $Itemid_adv = NULL; }
    $Itemid = $Itemid_adv;
    if($itemid->cpn != 0) { $Itemid_cpn = "&Itemid=" . intval($itemid->cpn); } else { $Itemid_cpn = NULL; }
    if($itemid->cmp != 0) { $Itemid_cmp = "&Itemid=" . intval($itemid->cmp); } else { $Itemid_cmp = NULL; }
    if($itemid->ads != 0) { $Itemid_ads = "&Itemid=" . intval($itemid->ads); } else { $Itemid_ads = NULL; }
    if($itemid->pkg != 0) { $Itemid_ads = "&Itemid=" . intval($itemid->pkg); } else { $Itemid_pkg = NULL; }
	
	$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
	$document = JFactory::getDocument();
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
    $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
    $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adag_tip.css");
	$document->addScript(JURI::root()."components/com_adagency/includes/js/graybox.js");
	$document->addScriptDeclaration('
		ADAG(function(){
			ADAG(\'.cpanelimg\').click(function(){
				document.location = "'.JURI::root()."index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn . '";
			});
        });
    ');
	
	$replace_package_with = '
<h3>'.JText::_('VIEWPACKAGE_LIST_PACKAGES').'</h3><div id="package_wrapper">
	<div class="adg_row hidden-phone">
	<div class="adg_cell span12 adg_box">
	<div>
	<div>
	<div class="adg_row ">
	    <div class="adg_cell span4"><div><div><h4>
			'.JText::_('VIEWPACKAGEDESC').'</h4>
		</div></div></div>';
	if($showZoneInfo) {
		$replace_package_with .='<div class="adg_cell span4"><div><div><h4>
				'.JText::_('ADAG_ZONE_INFO').'
			</h4></div></div></div>';
	}
	$replace_package_with .='<div class="adg_cell span4"><div><div><h4>
			'.JText::_('VIEWPACKAGEPRICE').'
		</h4></div></div></div>
	</div>
	</div>
	</div>
	</div>
	</div>';

	for ($i = 0; $i < $n; $i++)
	{
		$order = $this->packages[$i];
		$order->adparams = @unserialize($order->adparams);
		$id = $order->tid;
		$order->zones = str_replace("All Zones",JText::_('ADAG_ALL_ZONES'), $order->zones);
		if(isset($order->visibility)&&($order->visibility==0)) { continue; }
		$checked = JHTML::_('grid.id', $i, $id);

		$link = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0" . $Itemid_cmp);

		$validity_in = '';
		if ($order->type=="fr" || $order->type=="in") { if ($order->validity!="") {
					$validity = explode("|", $order->validity, 2);
					$validity[1] = ($validity[1]=="day") ? JText::_('VIEWPACKAGE_DAY') : (($validity[1]=="week") ? JText::_('VIEWPACKAGE_WEEK') : (($validity[1]=="month") ? JText::_('VIEWPACKAGE_MONTHS') : (($validity[1]=="year") ? JText::_('VIEWPACKAGE_YEARS') : ""))) ;
					$validity_in = $validity[0]."<br />".$validity[1]; } } else { $validity_in = $order->quantity; }
					if ($order->type == 'cpm')
						$validity_in = $validity_in.'<br />'.JText::_('AGENCYIMPRESSIONS');
					elseif($order->type == 'pc') $validity_in =  $validity_in.'<br />'.JText::_('AGENCYCLICKS');

		if ($order->type == 'cpm') {
            $tooltip_in ='&nbsp;
                <span class="adag_tip">
                    <img align="top"  src="components/com_adagency/images/tooltip.png" border="0" />
                    <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPM') . '</span>
                </span>';
		} elseif($order->type == 'pc') {
            $tooltip_in ='&nbsp;
                <span class="adag_tip">
                    <img align="top"  src="components/com_adagency/images/tooltip.png" border="0" />
                    <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPC') . '</span>
                </span>';
		} else {
            $tooltip_in ='&nbsp;
                <span class="adag_tip">
                    <img align="top"  src="components/com_adagency/images/tooltip.png" border="0" />
                    <span>' . JText::_('VIEWPACKAGE_TOOLTIP_FR') . '</span>
                </span>';
		}

	 	$button_value = JText::_('VIEWPACKAGE_BUY');
		if($order->cost > 0) {
			$params_conf = unserialize($configs->params);
			$currency_price = 0;
			if(isset($params_conf['currency_price'])){
				$currency_price = $params_conf['currency_price'];
			}
			
			if($currency_price == 0){
				$price_in =  JText::_("ADAG_C_".$currencydef).$order->cost;
			}
			else{
				$price_in =  $order->cost.JText::_("ADAG_C_".$currencydef);
			}
		} else {
			$price_in = JText::_('VIEWPACKAGEFREE');
			$button_value = JText::_('ADAG_START');
		}

        if( $my->id && (isset($advertiserid) && $advertiserid > 0) )  {
            $link_in = $link ;
        } else if ($my->id &&  (!isset($advertiserid) || (isset($advertiserid) && $advertiserid == 0) ) )  {
            $link_in = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&user=reg&cid=' . $order->tid . $Itemid_adv;
        } else if (!$my->id)  {
            $link_in = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=register' . $Itemid_adv;
        }


	$desc_stuff='<p>
                	<strong>'.JText::_('ADAG_SLOTS').'</strong> '.$order->banners*$order->banners_cols.' ('.$order->banners . " " . JText::_('ADAG_ROWS').' , '.$order->banners_cols." ".JText::_('ADAG_COLS').') <br />
                    <strong>'.JText::_('ADAG_ROTATION').':</strong>';
	if($order->rotatebanners == '0') { $desc_stuff .= JText::_('ADAG_NO');} else { $desc_stuff .= JText::_('ADAG_YES'); }
	$desc_stuff .= ' <br />';
	if(isset($order->adparams['width']) && ($order->adparams['width'] != '') &&($order->adparams['height'] != '') && isset($order->adparams['height'])) {
		$desc_stuff .= '<strong>'.JText::_('VIEWADSIZE').':</strong> '.$order->adparams['width'].' x '.$order->adparams['height'].' '.JText::_('ADAG_WIDTH_X_HEIGHT').' <br />';
	} else {
		$desc_stuff .= "<strong>".JText::_("ADAG_ANYSIZE")."</strong><br />";
	}
	$desc_stuff .= '<strong>'.JText::_('VIEWADTYPE').':</strong>';
	$before = false;
	if(isset($order->adparams['standard']) || isset($order->adparams['affiliate']) || isset($order->adparams['flash'])){
		$desc_stuff .= JText::_("VIEW_CAMPAIGN_MEDIA_BANNERS").": ";
		if(isset($order->adparams['standard'])) { $desc_stuff .= JText::_('VIEWTREEADDSTANDARD'); $before = true; }
		if(isset($order->adparams['affiliate'])) {
			if($before) { $desc_stuff .= ", "; }
			$desc_stuff .= JText::_('VIEWTREEADDADCODE');
			$before = true;
		}
		if(isset($order->adparams['flash'])) {
			if($before) { $desc_stuff .= ", "; }
			$desc_stuff .= JText::_('VIEWTREEADDFLASH');
		}
	} elseif(isset($order->adparams['textad'])){
		$desc_stuff .= JText::_('VIEWTREEADDTEXTLINK');
	} elseif(isset($order->adparams['popup']) || isset($order->adparams['transition']) || isset($order->adparams['floating'])){
		if(isset($order->adparams['popup'])) { $desc_stuff .= JText::_('VIEWTREEADDPOPUP'); $before = true; }
		if(isset($order->adparams['transition'])) {
			if($before) { $desc_stuff .= ", "; }
			$desc_stuff .= JText::_('VIEWTREEADDTRANSITION');
			$before = true;
		}
		if(isset($order->adparams['floating'])) {
			if($before) { $desc_stuff .= ", "; }
			$desc_stuff .= JText::_('VIEWTREEADDFLOATING');
		}
	}
	$desc_stuff .= '<br /></p>';

	$add_description = '';
	$add_description = $add_description.'<div class="test">
			<div class="adg_cell span4"><div><div>
				'.$desc_stuff.'<em>'.stripslashes($order->pack_description).'</em>
			</div></div></div>
	</div>';
	 $desc_stuff = NULL;

if($configs->showpreview==1){
	$zones_preview = '<a class="modal2" href="index.php?option=com_adagency&controller=adagencyPackages&task=preview&tmpl=component&no_html=1&cid='.intval($order->zoneid).'">'.$order->z_title;
} else {
	$zones_preview=$order->z_title;
}

	$output = NULL; $output2 = NULL;

	if(isset($order->location)&&(is_array($order->location))) {
		foreach($order->location as $element){
			$element->adparams = @unserialize($element->adparams);
			if($element->rotatebanners == 1) { $element->rotatebanners = JText::_("ADAG_YES"); } else { $element->rotatebanners = JText::_("ADAG_NO"); }
			if($configs->showpreview==1) { $sz_before = "<a class=\"modal2\" href=\"".JRoute::_('index.php?option=com_adagency&controller=adagencyPackages&task=preview&tmpl=component&no_html=1&cid='.$element->id)."\">"; $sz_after = "</a>"; } else {
				$sz_before = NULL; $sz_after = NULL;
			}

			$output2 .= "<div ><div>".JText::_('NEWADZONE').": ".$sz_before.$element->title.$sz_after."<br /><br />";
			$output2 .= JText::_("ADAG_ROTATION").": ".$element->rotatebanners."<br />";
			if(isset($element->adparams['width'])&&isset($element->adparams['height'])&&($element->adparams['width'] != "")&&($element->adparams['height'] != "")) {
				$output2.= JText::_("VIEWADSIZE").": ".$element->adparams['width']." x ".$element->adparams['height']." px<br />";
			} else { $output2 .= JText::_("VIEWADSIZE").": ".JText::_('ADAG_ANYSIZE')."<br />"; }
			$output2 .= JText::_('ADAG_SLOTS').": ".$element->rows*$element->cols." (".$element->rows . " " . JText::_("ADAG_ROWS").", ".$element->cols . " " . JText::_("ADAG_COLS").")<br />";
			$output2 .= JText::_('VIEWADTYPE').": ";
			$before = false;
               	if(isset($element->adparams['standard']) || isset($element->adparams['affiliate']) || isset($element->adparams['flash'])){
				$output2 .= JText::_("VIEW_CAMPAIGN_MEDIA_BANNERS").": ";
				if(isset($element->adparams['standard'])) { $output2 .= JText::_('VIEWTREEADDSTANDARD'); $before = true; }
				if(isset($element->adparams['affiliate'])) {
					if($before) { $output2 .= ", "; }
					$output2 .= JText::_('VIEWTREEADDADCODE');
					$before = true;
				}
				if(isset($element->adparams['flash'])) {
					if($before) { $output2 .= ", "; }
					$output2 .= JText::_('VIEWTREEADDFLASH');
				}
			} elseif(isset($element->adparams['textad'])){
				$output2 .= JText::_('VIEWTREEADDTEXTLINK');
			} elseif(isset($element->adparams['popup']) || isset($element->adparams['transition']) || isset($element->adparams['floating'])){
				if(isset($element->adparams['popup'])) { $output2 .= JText::_('VIEWTREEADDPOPUP'); $before = true; }
				if(isset($element->adparams['transition'])) {
					if($before) { $output2 .= ", "; }
					$output2 .= JText::_('VIEWTREEADDTRANSITION');
					$before = true;
				}
				if(isset($element->adparams['floating'])) {
					if($before) { $output2 .= ", "; }
					$output2 .= JText::_('VIEWTREEADDFLOATING');
				}
			}
			$output2 .= "</div></div>";
		}
	}


	$output = '<p><strong>'.JText::_("VIEWPACKAGETERMS").'</strong>:';
	if ($order->type=="fr" || $order->type=="in") { if ($order->validity!="") {
			$validity = explode("|", $order->validity, 2);
			$validity[1] = ($validity[1]=="day") ? JText::_('VIEWPACKAGE_DAY') : (($validity[1]=="week") ? JText::_('VIEWPACKAGE_WEEK') : (($validity[1]=="month") ? JText::_('VIEWPACKAGE_MONTHS') : (($validity[1]=="year") ? JText::_('VIEWPACKAGE_YEARS') : ""))) ;
			$output .= $validity[0]." ".$validity[1]; }
		  } else { $output .= $order->quantity; }
			if ($order->type == 'cpm') { $output .= ' '.JText::_('AGENCYIMPRESSIONS'); }
			elseif($order->type == 'pc') { $output .= ' '.JText::_('AGENCYCLICKS'); }
	$output .= "</p>";

	$output .= '<p style="margin-top:10px;"><strong>'.JText::_('VIEWORDERSTYPE').'</strong>
	<span>'.JText::_('ADAG_PK_'.strtoupper($order->type)).'</span><span></p>';

	if ($order->type == 'cpm') {
        $output .= '<span class="adag_tip">
                        <img src="components/com_adagency/images/tooltip.png" border="0" align="top" />
                        <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPM') . '</span>
                    </span>';
	} elseif($order->type == 'pc') {
        $output .= '<span class="adag_tip">
                        <img  src="components/com_adagency/images/tooltip.png" border="0" align="top" />
                        <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPC') . '</span>
                    </span>';
	} else {
        $output .= '<span class="adag_tip">
                        <img  src="components/com_adagency/images/tooltip.png" border="0" align="top" />
                        <span>' . JText::_('VIEWPACKAGE_TOOLTIP_FR') . '</span>
                    </span>';
	}
	$output .= '</span>';

$replace_package_with = $replace_package_with.'
	<div class="adg_row">
	<div class="adg_cell span12 adg_box">
	<div>
	<div>
	<div class="adg_row ">
	     	<div class="adg_cell span4"><div><div><strong><div style="font-size:120%;">'.$order->description.'</div></strong>
			<p style="margin-top: 10px;">'.$order->pack_description.'</p>
			'.$output.'
			</div></div></div>';


if($showZoneInfo) {
	$replace_package_with .= '<div class="adg_cell span4 adg_box_sub">'.$output2.'</div>';
}

if($order->type != "in"){
	$replace_package_with .= 
				'<div class="adg_cell span4"><div><div>'.$price_in.'
					<p class="start_buy">
						<form action="'.JRoute::_($link_in).'" method="post" >
							<input type="hidden" name="pid" value="'.$order->tid.'" />
							<input class="btn btn-primary" type="submit" value="'.$button_value.'"/>
						</form>
					</p>
				</div></div></div>';
}
else{
	//$now_int = strtotime(date("Y-m-d"));
	
	$offset = JFactory::getApplication()->getCfg('offset');
	$jnow = JFactory::getDate('now', $offset);
	$now_int = $jnow->toUnix(true);
	
	$next_date = $this->getInventoryNextDate($id);
	
	if($next_date == "NO_SLOTS_AVAILABLE"){
		$replace_package_with .=
		'<div class="adg_cell span4"><div><div><span class="ijd-product-price">'.$price_in.'</span>
			<p class="start_buy">
				<span class="sold-out">'.JText::_("ADAG_SOLD_OUT").'</span>
			</p>
		</div></div></div>';
	}
	else{
		$next_date_int = strtotime($next_date);
		
		$temp = "";
		if($next_date_int > $now_int){
			$temp .= '<div><p class="pagination-centered">';
			$temp .= '<span class="sold-out">'.JText::_("ADAG_SOLD_OUT").'</span><br/>';
			$temp .= '<span class="next-date">'.JText::_("ADAG_NEXT_INVENTORY_DATE").":</span> ".$next_date;
			$temp .= '</p></div>';
		}
		
		$replace_package_with .= 
					'<div class="adg_cell span4"><div><div><span class="ijd-product-price">'.$price_in.'</span>
						<p class="start_buy">
							<form action="'.JRoute::_($link_in).'" method="post" >
								<input type="hidden" name="pid" value="'.$order->tid.'" />
								<input class="btn btn-primary" type="submit" value="'.$button_value.'"/>
								'.$temp.'
							</form>
						</p>
					</div></div></div>';
	}
}

$replace_package_with .= '</div>
</div>
</div>
</div>
</div>';

	$k = 1 - $k;

	}
// border-bottom:1px; border-bottom-color:#000000; border-bottom-style:solid;
$replace_package_with.='</div>'
?>
<div id="overview_page">
<div class="page-title">
	<?php if(isset($advertiserid)&&($advertiserid > 0)) {
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
					<div > 
						<div class="clearfix">
							<ul>
								<li><a href="<?php echo $cpn_link;?>"><i class="fa fa-home"></i><?php echo JText::_('ADG_DASH'); ?></a></li>
					            <li><a  href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=<?php echo intval($my->id) . $Itemid_adv;?>"><i class="fa fa-user"></i><?php echo JText::_('ADG_PROF'); ?></a></li>
					            <li><a href="index.php?option=com_adagency&controller=adagencyAds<?php echo $Itemid_ads; ?>"><i class="fa fa-bars"></i><?php echo JText::_('ADG_ADS'); ?></a></li>
					            <li><a href="index.php?option=com_adagency&controller=adagencyOrders<?php echo $Itemid_ord; ?>"><i class="fa fa-shopping-cart"></i><?php echo JText::_('ADG_ORDERS'); ?></a></li>
					            <li><a href="index.php?option=com_adagency&controller=adagencyReports<?php echo $Itemid_rep; ?>"><i class="fa fa-bar-chart-o"></i><?php echo JText::_('ADG_REPORTS'); ?></a></li>
					            <li><a href="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo $Itemid_cmp; ?>"><i class="fa fa-calendar-o"></i><?php echo JText::_('ADG_CAMP'); ?></a></li>
					            <li><a href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><i class="fa fa-sign-out"></i><?php echo JText::_('ADG_LOGOUT'); ?></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>	
   <?php } ?>
<h2><?php echo JText::_('VIEW_OVERVIEW_CONTENT'); ?></h2>
</div>
  
<div class="adg_row">
<div class="adg_cell span12"
	<div><div>
	<!-- <form class="form-horizontal" method="post" name="adminForm" id="adminForm"> -->
		<?php echo str_replace('{packages}', $replace_package_with, $overview); ?>
	<!--</form> -->
	</div></div>
</div>
</div>
</div>