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
	
	include_once("components/com_adagency/models/adagencypackage.php");
	$helper = new adagencyModeladagencyPackage();
	$showpreview = $configs->showpreview;
	
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
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js");
	$document->addScript(JURI::root()."components/com_adagency/includes/js/graybox.js");
	$document->addScriptDeclaration('
		ADAG(function(){
			ADAG(\'.cpanelimg\').click(function(){
				document.location = "'.JURI::root()."index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn . '";
			});
        });
    ');
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
			$pck = $this->getModel("adagencyConfig")->getItemid('adagencypackage');
			if($pck != 0) { $Itemid_pck = "&Itemid=" . intval($pck); } else { $Itemid_pck = NULL; }
		    if($ads != 0) { $Itemid_ads = "&Itemid=" . intval($ads); } else { $Itemid_ads = NULL; }
		    if($adv != 0) { $Itemid_adv = "&Itemid=" . intval($adv); } else { $Itemid_adv = NULL; }
		    if($cmp != 0) { $Itemid_cmp = "&Itemid=" . intval($cmp); } else { $Itemid_cmp = NULL; }
		    if($ord != 0) { $Itemid_ord = "&Itemid=" . intval($ord); } else { $Itemid_ord = NULL; }
		    if($rep != 0) { $Itemid_rep = "&Itemid=" . intval($rep); } else { $Itemid_rep = NULL; }
	 ?>
		  <nav class="uk-navbar ada-toolbar">
          <!-- Toolbar -->
          <ul class="uk-navbar-nav">
            <li><a href="<?php echo $cpn_link;?>"><i class="uk-icon-home"></i></a></li>
            <li><a href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=<?php echo intval($my->id) . $Itemid_adv;?>"><?php echo JText::_('ADG_PROF'); ?></a></li>
            <li><a href="index.php?option=com_adagency&controller=adagencyAds<?php echo $Itemid_ads; ?>"><?php echo JText::_('ADG_ADS'); ?></a></li>
            <li><a href="index.php?option=com_adagency&controller=adagencyOrders<?php echo $Itemid_ord; ?>"><?php echo JText::_('ADG_ORDERS'); ?></a></li>
            <li><a href="index.php?option=com_adagency&controller=adagencyReports<?php echo $Itemid_rep; ?>"><?php echo JText::_('ADG_REPORTS'); ?></a></li>
            <li><a href="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo $Itemid_cmp; ?>"><?php echo JText::_('ADG_CAMP'); ?></a></li>
            <li class="uk-active"><a href="index.php?option=com_adagency&controller=adagencyPackage<?php echo $Itemid_pck; ?>"><?php echo JText::_('ADG_PACKAGES'); ?></a></li>
          </ul>
          <div class="uk-navbar-flip">
            <ul class="uk-navbar-nav">
              <li><a href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><i class="uk-icon-sign-out"></i></a></li>
            </ul>
          </div>
          </nav>
				
			<select name="ada-toolbar-mobile" class="ada-toolbar-mobile" id="ada-toolbar-mobile" onchange="window.open(this.value, '_self');" >
                <option value="<?php echo $cpn_link;?>"><i class="fa fa-home"></i><?php echo JText::_('ADG_DASH'); ?></a></li>
                <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid='.intval($my->id).''. $Itemid_adv);?>"><?php echo JText::_('ADG_PROF'); ?></option>
                <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds'. $Itemid_ads); ?>"><?php echo JText::_('ADG_ADS'); ?></option>
                <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders'.$Itemid_ord); ?>"><?php echo JText::_('ADG_ORDERS'); ?></option>
                <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyReports'.$Itemid_rep); ?>"><?php echo JText::_('ADG_REPORTS'); ?></option>
                <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns'.$Itemid_cmp); ?>"><?php echo JText::_('ADG_CAMP'); ?></option>
                <option selected="selected" value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyPackage'.$Itemid_pck); ?>"><?php echo JText::_('ADG_PACKAGES'); ?></option>                               
                <option value="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><?php echo JText::_('ADG_LOGOUT'); ?></option>
            </select>
		
   <?php } ?>
<h2 class="ada-packages-title"><?php echo JText::_('VIEW_OVERVIEW_CONTENT'); ?></h2>
</div>

	<?php
    	//echo str_replace('{packages}', $replace_package_with, $overview);
	
		if(strpos(" ".$overview, '{packages}') != 0){
			$overview = str_replace('{packages}', "", $overview);
			echo $overview;
	?>
    <div class="ada-packages-name">
    <ul class="uk-grid uk-grid-collapse uk-grid-width-medium-1-3 uk-visible-large">
      <li><h4><?php echo JText::_('VIEWPACKAGEDESC');?></h4></li>
      <li><h4><?php echo JText::_('ADAG_ZONE_INFO');?></h4></li>
      <li><h4><?php echo JText::_('VIEWPACKAGEPRICE');?></h4></li>
    </ul>
  </div>

  <?php
    for($i = 0; $i < $n; $i++){
    $order = $this->packages[$i];
    $order->adparams = @unserialize($order->adparams);
    $id = $order->tid;
    $order->zones = str_replace("All Zones",JText::_('ADAG_ALL_ZONES'), $order->zones);
    if($helper->getFreePermission($advertiserid,$id) == false) {continue;}
    $checked = JHTML::_('grid.id', $i, $id);
    $link = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0".$Itemid_cmp);    
  ?>

  <div class="ada-package">
    <div class="uk-grid uk-grid-collapse">
      <?php if($showZoneInfo){ ?>
      <div class="uk-width-medium-1-3">
      <?php } else { ?>
      <div class="uk-width-medium-2-3">
      <?php } ?>
        
        <div class="ada-package-desc">
          <?php
              $tmpl = JRequest::getVar("tmpl", "");
              if($tmpl != "component"){
          ?>
          <h4 class="ada-package-title"><?php echo $order->description; ?></h4>
          <?php } else { ?>
          <h4 class="ada-package-title"><a href="#" onclick="document.getElementById('otid').value = '<?php echo $id; ?>'; document.getElementById('close-modal-btn').click();"><?php echo $order->description;?></a></h4>
          <?php } ?>

          <?php if($order->pack_description != ''){ ?>
          <i class="uk-text-success"><?php echo stripslashes($order->pack_description);?></i>
          <?php } ?>

          <ul class="ada-package-list uk-list uk-list-line">
            <li>
              <span><?php echo JText::_("VIEWPACKAGETERMS"); ?>:</span>
              <?php
                if($order->type=="fr" || $order->type=="in"){
                  if($order->validity!=""){
                    $validity = explode("|", $order->validity, 2);
                    $validity[1] = ($validity[1]=="day") ? JText::_('VIEWPACKAGE_DAY') : (($validity[1]=="week") ? JText::_('VIEWPACKAGE_WEEK') : (($validity[1]=="month") ? JText::_('VIEWPACKAGE_MONTHS') : (($validity[1]=="year") ? JText::_('VIEWPACKAGE_YEARS') : ""))) ;
                    echo $validity[0]." ".$validity[1];
                  }
                }
                else{
                  echo $order->quantity;
                }
                
                if($order->type == 'cpm'){
                  echo ' '.JText::_('AGENCYIMPRESSIONS');
                }
                elseif($order->type == 'pc'){
                  echo ' '.JText::_('AGENCYCLICKS');
                }
              ?>
            </li>
            <li>
              <span><?php echo JText::_('VIEWORDERSTYPE'); ?>:</span>
              <?php echo JText::_('ADAG_PK_'.strtoupper($order->type)); ?>

              <?php
                if($order->type == 'cpm'){
                    echo '
                        <span class="adag_tip uk-visible-large">
                            <i class="uk-icon-info-circle"></i>
                            <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPM') . '</span>
                        </span>';
                }
                elseif($order->type == 'pc'){
                    echo '
                        <span class="adag_tip uk-visible-large">
                            <i class="uk-icon-info-circle"></i>
                            <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPC') . '</span>
                        </span>';
                }
                else{
                    echo '
                        <span class="adag_tip uk-visible-large">
                            <i class="uk-icon-info-circle"></i>
                            <span>' . JText::_('VIEWPACKAGE_TOOLTIP_FR') . '</span>
                        </span>';
                }
              ?>
            </li>
          </ul>
        </div>

      </div>

      <?php if($showZoneInfo){ ?>
      <div class="uk-width-medium-1-3">
        
        <div class="ada-package-zone">
          <?php
            if(isset($order->location)&&(is_array($order->location))){
              foreach($order->location as $element){
                $element->adparams = @unserialize($element->adparams);
                if($element->rotatebanners == 1){
                    $element->rotatebanners = JText::_("ADAG_YES");
                }
                else{
                    $element->rotatebanners = JText::_("ADAG_NO");
                }
                
                if($showpreview==1){
                    //$sz_before = "<a class=\"modal2\" href=\"".JRoute::_('index.php?option=com_adagency&controller=adagencyPackages&task=preview&tmpl=component&no_html=1&cid='.$element->id.$Itemid)."\">";
                //$sz_after = "</a>";
                $sz_before = "<a class=\"modal2\" href=\"".JURI::root().'index.php?option=com_adagency&controller=adagencyPackages&task=preview&tmpl=component&no_html=1&cid='.$element->id.$Itemid."\">";
                $sz_after = "</a>";
                }
                else{
                    $sz_before = NULL;
                    $sz_after = NULL;
                }
          ?>
          <h4 class="ada-package-title uk-h4">
            <!-- Zone info -->
            <?php echo JText::_('NEWADZONE').": ".$sz_before.$element->title.$sz_after; ?>
          </h4>
          <ul class="ada-package-list uk-list uk-list-line">
            <li>
              <!-- Rotation info -->
              <span><?php echo JText::_("ADAG_ROTATION")."</span>: ".$element->rotatebanners; ?>
            </li>
            <li>
              <!-- Size info -->
              <?php
                if(isset($element->adparams['width'])&&isset($element->adparams['height'])&&($element->adparams['width'] != "")&&($element->adparams['height'] != "")){
                echo "<span>".JText::_("VIEWADSIZE")."</span>: ".$element->adparams['width']." x ".$element->adparams['height']." px"; 
                }
                else{
                echo "<span>".JText::_("VIEWADSIZE")."</span>: ".JText::_('ADAG_ANYSIZE');
                }
              ?>
            </li>
            <li>
              <!-- Slots info -->
              <?php echo "<span>".JText::_('ADAG_SLOTS')."</span>: ".$element->rows*$element->cols." (".$element->rows. " " . JText::_("ADAG_ROWS").", ".$element->cols . " " . JText::_("ADAG_COLS").")"; ?>
            </li>
            <li>
              <!-- Type info -->
              <?php
                echo "<span>".JText::_('VIEWADTYPE')."</span>: ";
                $before = false;
                if(isset($element->adparams['standard']) || isset($element->adparams['affiliate']) || isset($element->adparams['flash'])){
                echo "<em>".JText::_("VIEW_CAMPAIGN_MEDIA_BANNERS").":</em> ";
                if(isset($element->adparams['standard'])){
                echo JText::_('VIEWTREEADDSTANDARD');
                $before = true;
                }
                if(isset($element->adparams['affiliate'])) {
                if($before){
                echo ", ";
                }
                echo JText::_('VIEWTREEADDADCODE');
                $before = true;
                }
                if(isset($element->adparams['flash'])) {
                if($before){
                echo ", ";
                }
                echo JText::_('VIEWTREEADDFLASH');
                }
                }
                elseif(isset($element->adparams['textad'])){
                echo JText::_('VIEWTREEADDTEXTLINK');
                }
                elseif(isset($element->adparams['popup']) || isset($element->adparams['transition']) || isset($element->adparams['floating'])){
                if(isset($element->adparams['popup'])){
                echo JText::_('VIEWTREEADDPOPUP');
                $before = true;
                }

                if(isset($element->adparams['transition'])) {
                if($before){
                echo ", ";
                }
                echo JText::_('VIEWTREEADDTRANSITION');
                $before = true;
                }

                if(isset($element->adparams['floating'])) {
                if($before){
                echo ", ";
                }
                echo JText::_('VIEWTREEADDFLOATING');
                }
                }
                }
              ?>
            </li>
          </ul>
        </div>
        <?php } ?>

      </div>
      <?php } ?>

      <div class="uk-width-medium-1-3">
        <?php
          if($order->type == "in"){
            $offset = JFactory::getApplication()->getCfg('offset');
            $jnow = JFactory::getDate('now', $offset);
            $now_int = $jnow->toUnix(true);
            
            $next_date = $this->getInventoryNextDate($id);
            
            if($next_date != "NO_SLOTS_AVAILABLE"){
        ?>
        <div class="ada-package-cost">
          <h2 class="ada-package-title">
            <?php
            $configs = $this->configs;
            $params_conf = unserialize($configs->params);
            $currency_price = 0;
            if(isset($params_conf['currency_price'])){
                $currency_price = $params_conf['currency_price'];
            }
            
            $free_not_free = JText::_('VIEWPACKAGE_BUY');
            if($order->cost > 0) {
                if($currency_price == 0){
                    echo JText::_("ADAG_C_".$currencydef).$order->cost;
                }
                else{
                    echo $order->cost.JText::_("ADAG_C_".$currencydef);
                }
            }
            else{
                echo JText::_('VIEWPACKAGEFREE');
                $free_not_free = JText::_("ADAG_START");
            }
            ?>
          </h2>
          <form class="ada-package-form" action="<?php
              if($my->id && (isset($advertiserid) && $advertiserid > 0)){
                  echo $link;
              }
              elseif($my->id &&  (!isset($advertiserid) || (isset($advertiserid) && $advertiserid == 0) )){
                  echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&user=reg&cid=0'.$Itemid);
              }
              elseif(!$my->id){
                  echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=register&returnpage=buy'.$Itemid);
              }
              ?>" method="post" >
              <input type="hidden" name="pid" value="<?php echo $order->tid; ?>" />
              <input class="uk-button uk-button-danger uk-button-large" type="submit" value="<?php echo $free_not_free; ?>"/>
          </form>
        </div>
        <?php } } else { ?>
        <div class="ada-package-cost">
          <h2 class="ada-package-title">
            <?php
              $configs = $this->configs;
              $params_conf = unserialize($configs->params);
              $currency_price = 0;
              if(isset($params_conf['currency_price'])){
                  $currency_price = $params_conf['currency_price'];
              }
              
              $free_not_free = JText::_('VIEWPACKAGE_BUY');
              if($order->cost > 0) {
                  if($currency_price == 0){
                      echo JText::_("ADAG_C_".$currencydef).$order->cost;
                  }
                  else{
                      echo $order->cost.JText::_("ADAG_C_".$currencydef);
                  }
              }
              else{
                  echo JText::_('VIEWPACKAGEFREE');
                  $free_not_free = JText::_("ADAG_START");
              }
            ?>
          </h2>
          <form class="ada-package-form" action="<?php
              if($my->id && (isset($advertiserid) && $advertiserid > 0)){
                  echo $link;
              }
              elseif($my->id &&  (!isset($advertiserid) || (isset($advertiserid) && $advertiserid == 0) )){
                  echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&user=reg&cid=0'.$Itemid);
              }
              elseif(!$my->id){
                  echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=register&returnpage=buy'.$Itemid);
              }
              ?>" method="post" >
              <input type="hidden" name="pid" value="<?php echo $order->tid; ?>" />
              <input class="uk-button uk-button-danger uk-button-large" type="submit" value="<?php echo $free_not_free; ?>"/>
          </form>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>

  <?php
    $k = 1 - $k;
    }
	} // if packages
	else{
		echo $overview;
	}
  ?>
</div>