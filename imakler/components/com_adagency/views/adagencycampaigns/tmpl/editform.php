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

JHtml::_('behavior.framework',true);
JHtml::_('behavior.calendar');
JHtml::_('behavior.modal', "a.modal2");

$helper = new adagencyAdminHelper();
$camp_row = $this->camp;
$stats = $this->stats;
$package_row = $this->package_row;
$ban_row = $this->ban_row;
$camps_ads = $this->camps_ads;
$pack_id = NULL;
$format = NULL;
$cbrw = NULL;$bnrs = NULL; $awh = NULL;
$pstatus = $this->pstatus;
if(isset($ban_row)&&(is_array($ban_row))){
    foreach($ban_row as $el){
        $cbrw[] = $el->id;
    }
    $cbrw = @implode(",",$cbrw);
}
  
$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

$configs = $this->configs;

if ( isset($camp_row->params['adslim']) ) {
    $adslim = (int)$camp_row->params['adslim'];
} elseif ( (!isset($camp_row->id) || ($camp_row->id <= 0)) && isset($configs->params['adslim']) ) {
    $adslim = $configs->params['adslim'];
} else {
    $adslim = 999;
}

$lists = $this->lists;
$task = $this->task;
$text = $this->text;
$post_data = JRequest::get('post');
$get_data = JRequest::get('get');

require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
$document = JFactory::getDocument();
$count_total_banners = intval($this->count_total_banners);
$count_available_banners = count($ban_row);

$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
$document->addStyleSheet(JURI::root().'components/com_adagency/includes/css/featherlight.min.css');
$document->addScript( JURI::root()."components/com_adagency/includes/js/featherlight.min.js" );

$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".intval($item_id_cpn); } else { $Itemid_cpn = NULL; }
$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);


$cid = JRequest::getVar("cid", "0");

$can_buy = true;

if(intval($cid) == 0){
  $can_buy = $this->checkInventoryPackage();
}

  $remove_action = JRequest::getVar("remove_action", "");
  if($remove_action == ""){
    include(JPATH_BASE."/components/com_adagency/includes/js/campaigns.php");
  }
?>

<!-- Add Campaign Container -->
<div class="ada-add__campaign">

  <?php
    $remove_action = JRequest::getVar("remove_action", "");
    $label = "";
    if($remove_action == ""){
      $renew = 0;
      $label = ucfirst($text). ' ' . JText::_("AD_NEW_CAMP"); }
    else{
      $renew = 1;
       $label = JText::_("AD_RENEW_CAMP")." ".$camp_row->name;
    }
      $my =  JFactory::getUser();
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
      <li class="uk-active"><a href="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo $Itemid_cmp; ?>"><?php echo JText::_('ADG_CAMP'); ?></a></li>
      <li><a href="index.php?option=com_adagency&controller=adagencyPackage<?php echo $Itemid_pck; ?>"><?php echo JText::_('ADG_PACKAGES'); ?></a></li>
    </ul>
    <div class="uk-navbar-flip">
      <ul class="uk-navbar-nav">
        <li><a href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><i class="uk-icon-sign-out"></i></a></li>
      </ul>
    </div>
  </nav>

  <select name="ada-toolbar-mobile" class="ada-toolbar-mobile" id="ada-toolbar-mobile" onchange="window.open(this.value, '_self');" >
    <option value="<?php echo $cpn_link;?>"><?php echo JText::_('ADG_DASH'); ?></li>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid='.intval($my->id).''. $Itemid_adv);?>"><?php echo JText::_('ADG_PROF'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds'. $Itemid_ads); ?>"><?php echo JText::_('ADG_ADS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders'.$Itemid_ord); ?>"><?php echo JText::_('ADG_ORDERS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyReports'.$Itemid_rep); ?>"><?php echo JText::_('ADG_REPORTS'); ?></option>
    <option selected="selected" value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns'.$Itemid_cmp); ?>"><?php echo JText::_('ADG_CAMP'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyPackage'.$Itemid_pck); ?>"><?php echo JText::_('ADG_PACKAGES'); ?></option>                               
    <option value="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><?php echo JText::_('ADG_LOGOUT'); ?></option>
  </select>

  <script language="javascript" type="text/javascript">
    function adagencysubmit(){
      submitbutton('refresh');
    }
    
    Joomla.submitbutton = function(pressbutton){
      var form = document.adminForm;
      if(pressbutton == 'save'){
        name = document.adminForm.name.value;
        if(name == ""){
          alert("<?php echo JText::_("JS_INSERT_CMPNAME"); ?>");
          return false;
        }
      }
      submitform(pressbutton);
    }
  </script>

  <div class="ada-campaigns-heading">
    <h2 class="ada-campaigns-title"><?php echo $label; ?></h2>
  </div>

  <form class="uk-form uk-form-horizontal" action="index.php" method="post" name="adminForm" id="adminForm">

  <?php
    if(intval($cid) == "0"){
  ?>

    <fieldset>
      <legend><?php echo  JText::_('ADAG_BASIC_INFO');?></legend>
      <!-- Campaign name -->
      <div class="uk-form-row">
        <label for="name" class="uk-form-label">
          <?php echo JText::_("AD_NEW_CAMP_NAME");?>:
          <?php  if ($task=='new') { ?><span class="uk-text-danger">*</span><?php  } ?>
        </label>

        <div class="uk-form-controls">
          <?php
            $remove_action = JRequest::getVar("remove_action", "");
            if($task=='new' && $remove_action == ""){ 
          ?>

          <input type="text" name="name" maxlength="255" value="<?php if(isset($post_data['name'])) { echo $post_data['name']; } ?>" />

          <?php } else { ?>

          <input class="agency_label"  type="hidden" name="name" size="25" maxlength="255" value="<?php echo $camp_row->name; ?> " />

          <?php echo "<p class='uk-form-help-block'>".$camp_row->name."</p>"; } ?>
        </div>
      </div>
      
      <?php
        if (!$camp_row->id) {
      ?> 

      <!-- Package select -->
      <div class="uk-form-row">
        <label for="name" class="uk-form-label">
          <?php echo JText::_("AD_NEW_CAMP_PACKAGE"); ?>:
          <span class="uk-text-danger">*</span>
        </label>

        <div class="uk-form-controls">
          <?php echo $lists["package"]; ?>
          <?php
            $remove_action = JRequest::getVar("remove_action", "");
            if($remove_action == ""){
          ?>

          <p class="uk-form-help-block"><a rel="{handler: 'iframe', size: {x: 500, y: 500}}" class="modal2 uk-visible-large" href="<?php echo JURI::root();?>index.php?option=com_adagency&amp;controller=adagencyPackages&amp;tmpl=component<?php echo $Itemid; ?>" ><?php echo JText::_('ADAG_VIEW_PACKS');?></a></p>
          <!-- <a data-toggle="modal" href="#list-packages"> <?php //echo JText::_('ADAG_VIEW_PACKS'); ?> </a> -->
          <?php } ?>
        </div>
      </div>

      <?php
        if($can_buy){
      ?>

      <!-- Date select -->
      <div class="uk-form-row ada-reports-range-data">
        <label for="name" class="uk-form-label">
          <?php echo JText::_("AD_NEW_CAMP_START_DATE"); ?>:
          <span class="uk-text-danger">*</span>
        </label>

        <div class="uk-form-controls">
          <?php
          $ymd = '%Y-%m-%d';

          if($configs->params['timeformat'] == 0){
          $ymd = "%Y-%m-%d %H:%M:%S";
          }
          elseif($configs->params['timeformat'] == 1){
          $ymd = "%m/%d/%Y %H:%M:%S";
          }
          elseif($configs->params['timeformat'] == 2){
          $ymd = "%d-%m-%Y %H:%M:%S";
          }
          elseif($configs->params['timeformat'] == 3){
          $ymd = "%Y-%m-%d";
          }
          elseif($configs->params['timeformat'] == 4){
          $ymd = "%m/%d/%Y";
          }
          elseif($configs->params['timeformat'] == 5){
          $ymd = "%d-%m-%Y";
          }

          $format = str_replace("%", "", $ymd);
          $format = str_replace("H:M:S", "H:i:s", $format);

          $offset = JFactory::getApplication()->getCfg('offset');
          $jnow = JFactory::getDate('now', $offset);
          $jnow = $jnow->toSql(true);

          $camp_row->start_date = date($format, strtotime($jnow));

          if(trim($camp_row->start_date) == ""){
          $camp_row->start_date = date($format);
          }
          echo JHtml::calendar($camp_row->start_date, 'start_date', 'start_date', $ymd, '');
          echo "<input type='hidden' name='tfa' value='".$configs->params['timeformat']."' />";
          ?>
        </div>
      </div>

      <?php } ?>

      <?php
        @$most_recent_available_date = $_SESSION["most_recent_available_date"];
        if(!$can_buy && $most_recent_available_date != "NO_SLOTS_AVAILABLE"){
      ?>

      <!-- Recent date -->
      <div class="uk-form-row">
        <label for="name" class="uk-form-label">
          <?php echo JText::_("ADAG_NEXT_AVAILABLE_DATE"); ?>:
          <span class="uk-text-danger">*</span>
        </label>

        <div class="uk-form-controls">
          <?php
            $today = date("Y-m-d", time());
            if($today == $most_recent_available_date){
          ?>
          <input type="text" value="<?php echo JText::_("ADAG_TODAY")." (".$most_recent_available_date.")"; ?>" name="most_recent_available_date_2" disabled="disabled" />

          <?php } else{ ?>
          <input type="text" value="<?php echo $most_recent_available_date; ?>" name="most_recent_available_date_2" disabled="disabled" />
          <?php } ?>

          <input type="hidden" value="<?php echo $most_recent_available_date; ?>" name="most_recent_available_date" />
        </div>
      </div>

      <?php } elseif($most_recent_available_date == "NO_SLOTS_AVAILABLE"){ ?>

      <!-- Sold out -->
      <div class="uk-form-row">
        <label for="name" class="uk-form-label"></label>
        <div class="uk-form-controls">
          <span class="uk-text-primary"><?php echo JText::_("ADAG_SOLD_OUT"); ?></span>
        </div>
      </div>

      <?php } ?>

      <?php
        $db = JFactory::getDBO();
        $query = "SHOW columns FROM #__ad_agency_campaign WHERE field='renewcmp'";
        $db->setQuery($query);
        $result = $db->loadRow();
        $renewcmp = $result[4];
        if($renewcmp == 2){
        $db = JFactory::getDBO();
        $pack_id = intval($camp_row->otid) == 0 ? JRequest::getVar("pid", "0") : intval($camp_row->otid);
        $sql = "select `type`, `cost` from #__ad_agency_order_type where tid=".$pack_id;
        $db->setQuery($sql);
        $db->query();
        $result = $db->loadAssocList();
        $type = @$result["0"]["type"];
        $cost = @$result["0"]["cost"];

        if(isset($result) && (trim($type) == "fr" || trim($type) == "in") && $cost != "0.00"){
      ?>

      <!-- Renew -->
      <div class="uk-form-row">
        <label for="name" class="uk-form-label"></label>
        <div class="uk-form-controls">
          <label><input type="checkbox" name="autorenewcmp" value="1" id="autorenewcmp"/>&nbsp;<?php echo JText::_("ADAG_AA_RENEW_CMP"); ?></label>
        </div>
      </div>

      <?php } } ?>

      <?php } ?>

      <?php
        $db = JFactory::getDBO();

        $sql = "select `cost` from #__ad_agency_order_type where tid=".intval($pack_id);
        $db->setQuery($sql);
        $db->query();
        $cost = $db->loadColumn();
        @$cost = $cost["0"];
        $promos = $this->promoValid();
        if($promos > 0 && ($cost != "0" && $cost != "0.0" && $cost != "0.00") ){
      ?>

      <div class="uk-form-row">
        <label for="name" class="uk-form-label"><?php echo JText::_("AD_PAYMENT_PROMOCODE"); ?></label>
        <div class="uk-form-controls">
          <input type="text" value="" name="promocode" />
        </div>
      </div>

      <?php } ?>
    </fieldset>

    <input type="hidden" name="default" <?php if ($camp_row->default=='Y') echo 'checked'; ?> value ="Y">
    <input class="inputbox" type="hidden" name="notes" size="40" maxlength="255" value="" />
    <?php } ?>

    <?php
      if($cid != 0){
    ?>

    <div class="ada-campaigns-box uk-panel uk-panel-box uk-panel-box-secondary uk-panel-header">
      <h3 class="uk-panel-title"><i class="uk-icon-bullhorn"></i> <?php echo $camp_row->name; ?></h3>
      <ul class="uk-list uk-list-line">
        <li><b><?php echo JText::_("AD_NEW_CAMP_PK_NAME"); ?></b>: <?php echo $package_row->description; ?></li>
        <li><b><?php echo JText::_("AD_NEW_CAMP_PK_TYPE"); ?></b>: <?php if ($camp_row->type=="cpm") { echo JText::_('ADAG_CPM_TEXT'); } elseif ($camp_row->type=="pc") { echo JText::_('ADAG_PC_TEXT'); } elseif ($camp_row->type=="fr") { echo JText::_('ADAG_FR_TEXT'); } elseif ($camp_row->type=="in") { echo JText::_('ADAG_IN_TEXT'); } ?></li>
        <li><b><?php echo JText::_("AD_NEW_CAMP_START_DATE"); ?></b>: <?php echo $helper->formatime($camp_row->start_date, $configs->params['timeformat']); ?></li>
      </ul>
    </div>

    <div class="ada-campaigns-box uk-panel uk-panel-box uk-panel-box-secondary uk-panel-header">
      <h3 class="uk-panel-title"><i class="uk-icon-info-circle"></i> <?php echo JText::_("AD_NEW_CAMP_STS"); ?></h3>
      <?php 
        if ($camp_row->type == "cpm") {
        if($camp_row->quantity > 0){
        $package_row->quantity = intval($package_row->quantity) - intval($camp_row->quantity);
      ?>
      <span class="uk-text-success uk-text-bold"><?php echo $package_row->quantity; ?></span>
      <?php echo JText::_("AD_CAMP_IMP");?>,
      <span class="uk-text-success uk-text-bold"><?php echo $camp_row->quantity; ?></span> 
      <?php echo JText::_("AD_CAMP_IMP_LEFT");?>

      <?php } else { ?>
      <span class="uk-text-danger">
        <?php echo JText::_("AD_CAMP_EXPIRED");?>
      </span>
      <?php } } ?>

      <?php
        if($camp_row->type == "pc"){
        if($camp_row->quantity > 0){
        $package_row->quantity = intval($package_row->quantity) - intval($camp_row->quantity);
      ?>

      <span class="uk-text-success uk-text-bold"><?php echo $package_row->quantity; ?></span>
      <?php echo JText::_("AD_CAMP_CLK");?>,
      <span class="uk-text-success uk-text-bold"><?php echo $camp_row->quantity; ?></span>
      <?php echo JText::_("AD_CAMP_CLK_LEFT");?>

      <?php } else { ?>

      <span class="uk-text-danger"><?php echo JText::_("AD_CAMP_EXPIRED");?></span>

      <?php } } ?>

      <?php
        if($camp_row->type == "fr" || $camp_row->type == "in"){
        if($camp_row->expired){
      ?>

      <span class="uk-text-danger">
        <?php echo JText::_("AD_CAMP_EXPIRED");?>
      </span>

      <?php } else { ?>
            
      <span>
        <?php echo $camp_row->time_left['days']; ?>
      </span>
      <?php echo JText::_("AD_CAMP_DAYS");?>
      <span>
        <?php echo $camp_row->time_left['hours']; ?>
      </span>
      <?php echo JText::_("AD_CAMP_HOURS");?>
      <span>
        <?php echo $camp_row->time_left['mins']; ?>
      </span> 
      <?php echo JText::_("AD_CAMP_MINS");?> 
      <?php echo JText::_("AD_CAMP_IMP_LEFT");?>
            
      <?php } } ?>
    </div>

    <div class="ada-campaigns-box uk-panel uk-panel-box uk-panel-box-secondary uk-panel-header">
      <h3 class="uk-panel-title"><i class="uk-icon-bar-chart"></i> <?php echo JText::_("ADAG_STATS"); ?></h3>
      <ul class="uk-list uk-list-line">
        <li><b><?php echo JText::_("AD_CAMP_DURATION"); ?></b>: <?php echo $stats['days'] . "&nbsp;days&nbsp;" . $stats['hours'] . "&nbsp;".JText::_("AD_CAMP_HOURS")."&nbsp;" . $stats['mins'] . "&nbsp;".JText::_("AD_CAMP_MINS")."&nbsp;"; ?></li>
        <li><b><?php echo JText::_("AD_CAMP_CLICKS"); ?></b>: <?php if (@$stats['click']) { echo @$stats['click']; } else echo '0';?></li>
        <li><b><?php echo JText::_("AD_CAMP_IMPS"); ?></b>: <?php if (@$stats['impressions']) { echo @$stats['impressions']; } else echo '0'; ?></li>
        <li><b><?php echo JText::_("AD_CLICK_RATE"); ?></b>: <?php if (@$stats['click_rate']) { echo @$stats['click_rate']; } else echo '0.00';  ?>%</li> 
      </ul>
    </div>

    <?php } ?>

    <!-- Tables, edit ads for campaign -->
    <?php
      if(($count_total_banners == 0)&& ($camp_row->id)&&(!isset($package_row->tid)||($package_row->tid == 0))) {
        $the_text = JText::_('ADAG_NO_ADS_AVAILABLE').JText::_('ADAG_NO_ADS_AVAILABLE3');
        echo '<div class="uk-visible-large uk-alert uk-alert-danger">'.str_replace('[L]','</a>',str_replace('[LINK]','<a href="index.php?option=com_adagency&controller=adagencyAds&task=addbanners'.$Itemid.'">',$the_text)).'</div>';
      } elseif(isset($package_row->tid)&& ($camp_row->id)) {
        if($count_available_banners == 0) {
                  //echo "<pre>";var_dump($package_row);die();
                  if (is_array($package_row->allzones))
                  foreach($package_row->allzones as $element) {
                      $package_row->adparams = $element->adparams;
                      if(is_array($package_row->adparams)) {
                          $temp2 = array();
                          $bnrs = array();
                          $awh = NULL;
                          foreach($package_row->adparams as $key=>$value) {
                              $temp2[] = $key;
                          }

                          if((in_array('width',$temp2))&& (in_array('height',$temp2)) && ($package_row->adparams['width']) && ($package_row->adparams['height']) ) {
                              $awh = "- ".JText::_('ADAG_MUST_BE').strtolower(JText::_('VIEWADSIZE'))." ".$package_row->adparams['width']." x ".$package_row->adparams['height']."<br />";
                          }
                          if(in_array('popup',$temp2)) { $bnrs[] = JText::_('JAS_POPUP'); $awh = ""; }
                          if(in_array('transition',$temp2)) { $bnrs[] = JText::_('JAS_TRANSITION'); $awh = ""; }
                          if(in_array('floating',$temp2)) { $bnrs[] = JText::_('JAS_FLOATING'); $awh = ""; }
                          if(in_array('textad',$temp2)) { $bnrs[] = JText::_('JAS_TEXT_LINK'); }
                          if(in_array('standard',$temp2)) { $bnrs[] = JText::_('JAS_STANDART'); }
                          if(in_array('affiliate',$temp2)) { $bnrs[] = JText::_('JAS_BANNER_CODE'); }
                          if(in_array('flash',$temp2)) { $bnrs[] = JText::_('JAS_FLASH'); }
                          $bnrs = implode(", ", $bnrs);
                          $bnrs = "- " . JText::_('ADAG_MUST_BE') . " " . $bnrs . "<br />";
                          $the_text[] = '<p>' . JText::_('NEWADZONE') . ' \'' . $element->z_title . '\' '
                                              . JText::_('ADAG_REQUIREMENTS') . ': <br />' . $bnrs . $awh
                                              . '</p>';
                      }
                  }
                  // JText::_('ADAG_NO_ADS_AVAILABLE2') . ."<br />" . JText::_('ADAG_NO_ADS_AVAILABLE3')
                  // echo "<pre>";var_dump($the_text);die();
                  //echo implode('<hr />', $the_text);die();

          $the_text = '<p class="uk-visible-large">' . JText::_('ADAG_NO_ADS_AVAILABLE2') . '</p>' . implode('', $the_text)
          . '<p class="uk-visible-large">' . JText::_('ADAG_NO_ADS_AVAILABLE3') . '</p>';

          echo '<div class="uk-alert uk-alert-danger">'.str_replace('[L]','</a>',str_replace('[LINK]','<a href="index.php?option=com_adagency&controller=adagencyAds&task=addbanners'.$Itemid.'">',$the_text)).'</div>';
          } else {
          echo "<div class='uk-visible-large'><h2>".JText::_("ADAG_CMP_SEL_ADS")."</h2></div>";

          if ( isset($adslim) && ($adslim != 999) ) {
              echo "<p class='uk-visible-large'><span>" . JText::_('ADAG_CMP_ADS_LIM') . ": " . $adslim . "</span></p>";
          }

          echo "<p class='uk-visible-large uk-text-primary'>".JText::_("ADAG_CMP_REQ_NOTE")."<p />";
    ?>

    <table class="uk-table uk-table-striped uk-table-hover ada-campaigns-table">
      <thead>
        <tr>
          <th class="uk-visible-large"><?php echo JText::_("AD_NEW_CAMP_BAN_ID"); ?></th>
          <th><?php echo JText::_("AD_NEW_CAMP_BAN_NAME"); ?></th>
          <th><?php echo JText::_("NEWADZONE"); ?></th>
          <th><?php echo JText::_("AD_NEW_CAMP_BAN_APR"); ?></th>
          <th><?php echo JText::_("ADAG_INCLUDE");?></th>
          <th class="uk-visible-large"><?php echo JText::_("VIEWADPREVIEW");?></th>
        </tr>
      </thead>

      <tbody>
        <?php
          $k=0;
          for ($i=0, $n=count( $ban_row ); $i < $n; $i++){
          	$row = $ban_row[$i];
			
			$link = "";
			$id = $row->id;
			$mediatype = $row->media_type;
			switch ($mediatype) {
				case 'advanced':
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid=".intval($id)."&Itemid=".intval($item_id));
					break;
				case 'standard':
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid=".intval($id)."&Itemid=".intval($item_id));
					break;
				case 'flash':
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid=".intval($id)."&Itemid=".intval($item_id));
					break;
				case 'transition':
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid=".intval($id)."&Itemid=".intval($item_id));
					break;
				case 'floating':
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid=".intval($id)."&Itemid=".intval($item_id));
					break;
				case 'popup':
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid=".intval($id)."&Itemid=".intval($item_id));
					break;
				case 'textLink':
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid=".intval($id)."&Itemid=".intval($item_id));
					break;
				case 'jomsocial':
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyJomsocial&task=edit&cid=".intval($id)."&Itemid=".intval($item_id));
					break;
			}
        ?>
        <tr>
          <td class="uk-visible-large"><?php echo $row->id; ?></td>
          <td><a href="<?php echo $link; ?>"><?php echo $row->title; ?></a></td>
          <td><?php echo $row->zones; ?></td>
          <td>
            <?php
              if($row->approved == 'Y') { echo "<span class='uk-text-success'>".JText::_('NEWADAPPROVED'); }
              elseif($row->approved == 'N') { echo "<span class='uk-text-danger'>".JText::_('ADAG_REJECTED'); }
              elseif($row->approved == 'P') { echo "<span class='uk-text-warning'>".JText::_('ADAG_PENDING'); }
              echo "</span>";
            ?>
          </td>
          <td>
            <input type="checkbox" id="bid[<?php echo ($i+1);?>]" name="banner[<?php echo $row->id;?>][add]" value="1" <?php if (in_array($row->id, $camps_ads)){echo 'checked="checked"';} ?> />
            <span class="lbl"></span>
          </td>
          <td class="uk-visible-large">
			<?php
				if($row->media_type == "popup" && $row->parameters['popup_type']=="webpage"){
              ?>
                  <a href='<?php echo $row->parameters['page_url']; ?>' data-featherlight="ajax"><i class="uk-icon-eye"></i></a>
              <?php
                }
                else{
                  if(($row->media_type == "transition") || $row->media_type == "floating" || $row->media_type == "textLink" || $row->media_type == "standard" || $row->media_type == "advanced" || $row->media_type == "flash" || $row->media_type == "popup" || $row->media_type == "jomsocial") {
              ?>
                    <a href='<?php echo "index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&adid=".$row->id; ?>' data-featherlight="ajax"><i class="uk-icon-eye"></i></a>
              <?php
                  }
                }
              ?>
			
			
			
			<?php
             /* $width = 300;
              $height = 300;
              if(isset($row->width)){
              $width = $row->width;
              }

              if(isset($row->height)){
              $height = $row->height;
              }*/
            ?>
           <!--  <a rel="{handler: 'iframe', size: {x: <?php echo $width+50; ?>, y: <?php echo $height+50; ?>}}" class="modal2" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&format=raw&adid='.$row->id.$Itemid);?>"><?php echo JText::_("VIEWADPREVIEW");?></a> -->
          </td>
        </tr>

        <input TYPE="hidden" NAME="banner[<?php echo $row->id;?>][rw]"  size="5" maxlength="6" value="<?php echo $row->relative_weighting>0?$row->relative_weighting: '100';?>">
        <?php $k = 1 - $k; } ?>

        <?php if(!isset($get_data['cid'][0])||($get_data['cid'][0]==0)) {
          echo "<input type='hidden' id='countbids' value='".$i."' />";
          }
        ?>
      </tbody>
    </table>

    <?php } } ?>

    <?php
      $cid = JRequest::getVar("cid", "0");
      if(intval($cid) != "0"){
    ?>
    <div class="ada-campaigns-heading ada-campaigns-heading--history">
      <h2 class="ada-campaigns-title"><?php echo JText::_("ADAG_HISTORY"); ?>:</h2>
    </div>

    <table class="uk-table uk-table-striped">
      <thead>
        <tr>
          <th><?php echo JText::_("ADAG_DATE"); ?></th>
          <th><?php echo JText::_("VIEWADACTION"); ?></th>
          <th><?php echo JText::_("ADAG_BY"); ?></th>
        </tr>
      </thead>

      <tbody>
        <?php
          $activities = $camp_row->activities;
          $users = array();
          $db = JFactory::getDBO();
          if(trim($activities) != ""){
              $activities_array = explode(";", $activities);
              if(is_array($activities_array) && count($activities_array) > 0){
                  foreach($activities_array as $key=>$activity){
                      $activity_array = explode(" - ", $activity);
                      if(is_array($activity_array) && count($activity_array) > 0 && trim($activity_array["0"]) != ""){
                          $row  = '<tr>';
                          $row .=   '<td>';
                          $row .=     $helper->formatime($activity_array["1"], $configs->params['timeformat']);
                //date("m/d/Y", strtotime(trim($activity_array["1"])));
                          $row .=   '</td>';
                          $row .=   '<td>';
                          if(trim($activity_array["0"]) == 'Purchased(new)'){

                          $row .=   JText::_('HISTORY_PURCHASED_NEW');
                          }
                          elseif(trim($activity_array["0"]) == 'Purchased(renewal)'){
                            $row .=   JText::_('HISTORY_PURCHASED_RENEW');
                          }
                          elseif(trim($activity_array["0"]) == 'Expired'){
                            $row .=   JText::_('HISTORY_EXPIRED');
                          }
                          elseif(trim($activity_array["0"]) == 'Deleted'){
                            $row .=   JText::_('HISTORY_DELETED');
                          }
                          elseif(trim($activity_array["0"]) == 'Paused'){
                              $row .=   JText::_('HISTORY_PAUSED');
                          }
                          elseif(trim($activity_array["0"]) == 'Re-Started'){
                            $row .=   JText::_('HISTORY_RE_STARTED');
                          }
                          elseif(trim($activity_array["0"]) == 'Active'){
                            $row .=   JText::_('Active');
                          }
                          elseif(trim($activity_array["0"]) == 'Approved'){
                            $row .=   JText::_('Approved');
                          }
                          $row .=   '</td>';
                          if(isset($activity_array["2"])){
                              $usertype = "";
                              $name = "";
                                        
                    $sql = "select u.id, u.name, ug.title, a.aid, GROUP_CONCAT(DISTINCT CAST(ugm.group_id as CHAR)) groups 
        from #__user_usergroup_map ugm, #__usergroups ug, #__users u left outer join #__ad_agency_advertis a on a.user_id = u.id
        where u.id=ugm.user_id and ugm.group_id=ug.id and u.id=".intval($activity_array["2"])." group by u.id and u.id=a.user_id";
                    $db->setQuery($sql);
                    $db->query();
                    $result = $db->loadAssocList();
                    
                    $user_history_name = "Advertiser";
                    $groups = $result["0"]["groups"];
                    $groups_array = explode(",", $groups);
        
                    if(count($groups_array) > 1){
                      $user_history_name = "Admin";
                    }
                    
                                        if(isset($users[trim($activity_array["2"])])){
                                            $usertype = $user_history_name; //trim($users[$activity_array["2"]]["aid"]) != "" ? "Advertiser" : $users[$activity_array["2"]]["title"];
                                            $name = $users[$activity_array["2"]]["name"]; 
                                        }
                                        else{
                                            if(isset($result) && count($result) > 0){
                                                $users[$activity_array["2"]] = $result["0"];
                                                $usertype = $user_history_name; //trim($users[$activity_array["2"]]["aid"]) != "" ? "Advertiser" : $users[$activity_array["2"]]["title"];
                                                $name = $users[$activity_array["2"]]["name"];
                                            }
                                        }
                                        
                                        $row .=   '<td>';
                                     
                                        $row .=     trim($usertype)." (".trim($name).")";
                    
                                        $row .=   '</td>';
                                    }
                                    $row .= '</tr>';
                                    echo $row;
                                }
                            }
                        }
                    }
            else{

            $row  = '<tr>';
            $row .=   '<td>';
            $row .=     $helper->formatime($camp_row->start_date, $configs->params['timeformat']);
            $row .=   '</td>';
            $row .=   '<td>';
            $row .=     'Purchased(new)';
            $row .=   '</td>';
            
            $usertype = "";
            $name = "";
            
            $sql = "select u.id, u.name, ug.title, a.aid,  GROUP_CONCAT(DISTINCT CAST(ugm.group_id as CHAR)) groups  
    from #__user_usergroup_map ugm, #__usergroups ug, #__users u left outer join #__ad_agency_advertis a on a.user_id = u.id
    where u.id=ugm.user_id and ugm.group_id=ug.id and u.id=".intval($camp_row->aid)." group by u.id and u.id=a.user_id";
            $db->setQuery($sql);
            $db->query();
            $result = $db->loadAssocList();
            
            $user_history_name = "Advertiser";
            @$groups = $result["0"]["groups"];
            $groups_array = explode(",", $groups);

            if(count($groups_array) > 1){
              $user_history_name = "Admin";
            }
      
            if(isset($result) && count($result) > 0){
              @$users[$activity_array["2"]] = $result["0"];
              @$usertype = $user_history_name; //trim($users[$activity_array["2"]]["aid"]) != "" ? "Advertiser" : $users[$activity_array["2"]]["title"];
              @$name = $users[$activity_array["2"]]["name"];
            }
            
            $row .=   '<td>';
            $row .=     $user_history_name." (".trim($name).")";
            $row .=   '</td>';
            $row .= '</tr>';
                // show row
                echo $row;
              }
            }
            ?>
    
        <?php
          $remove_action = JRequest::getVar("remove_action", "");
          $campaign_id = $camp_row->id;
          if($remove_action != ""){
            $campaign_id = JRequest::getVar("campaign_id", "0");
            $orderid = JRequest::getVar("orderid", "0");
            $otid = JRequest::getVar("otid", "0");
            echo '<input type="hidden" name="orderid" value="'.intval($orderid).'" />';
            echo '<input type="hidden" name="otid" value="'.intval($otid).'" />';
            echo '<input type="hidden" name="remove_action" value="renew" />';
          }
        ?>
        
        <input type="hidden" name="id" value="<?php echo $campaign_id; ?>" />
        <input type="hidden" name="task" value="save" />
        <input type="hidden" name="option" value="com_adagency" />
        <input type="hidden" name="controller" value="adagencyCampaigns" />
        <input type="hidden" name="aid" value="<?php echo $camp_row->aid; ?>" />
        <?php
          $ad = JRequest::getVar("ad", "0");
          if(intval($ad) != 0){
            echo '<input type="hidden" name="ad" value="'.intval($ad).'" />';
          }
        ?>

        <?php if ($camp_row->id>0) { ?>
        <input type="hidden" name="cbrw" value="<?php echo $cbrw; ?>" />
        <input type="hidden" name="otid" value="<?php echo $camp_row->otid; ?>" />
        <input type="hidden" name="approved" value="<?php echo $camp_row->approved; ?>" />
        <input type="hidden" name="type" value="<?php echo $camp_row->type; ?>" />
        <input type="hidden" name="quantity" value="<?php echo $camp_row->quantity; ?>" />
        <input type="hidden" name="validity" value="<?php echo $camp_row->validity; ?>" />
        
        <input type="hidden" name="start_date" value="<?php echo $camp_row->start_date; ?>" />
        
        <input type="hidden" name="cost" value="<?php echo $camp_row->cost; ?>" />
        <?php } else {
          if(!isset($configs->params['timformat'])) {
            $configs->params['timformat'] = NULL;
          }
        ?>
        <input type="hidden" name="now_datetime" value="<?php echo $helper->formatime(date("Y-m-d H:i:s"),$configs->params['timformat']); ?>" />
        <?php } ?>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
      </tbody>
    </table>

    <div class="uk-form-row uk-margin-large-top">
      <label for="" class="uk-form-label"></label>
      <div class="uk-form-controls">
        <input type="button" class="uk-button" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_BACK'); ?>" />
          <?php
            if(@$most_recent_available_date != "NO_SLOTS_AVAILABLE"){
            if($cid == 0){
          ?>

          <input class="uk-button uk-button-primary" TYPE="button" onclick="Joomla.submitbutton('save')" value="<?php
              if($camp_row->id>0) { echo JText::_("AD_SAVE"); }
              else { echo JText::_('AD_CONTINUE');} ?>" />

          <?php } else { ?>
          <input class="uk-button uk-button-primary" TYPE="button" onclick="Joomla.submitbutton('save')" value="<?php
            if($camp_row->id>0) { echo JText::_("AD_SAVE"); }
            else { echo JText::_('AD_CONTINUE');}
          ?>" />
            
          <?php
            }
          }
          ?>
      </div>
    </div>
  </form>
</div>

<div id="ajax-response" style="display:none;">&nbsp;</div>
