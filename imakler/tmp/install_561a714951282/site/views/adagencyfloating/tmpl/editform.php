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
$ad = $this->ad;
$configs = $this->configs;
$current = $this->channel;
$banners_camps=$this->these_campaigns;
if (!$banners_camps) { $banners_camps = array(); }
$data = $this->data;
$realimgs = '';
$camps = $this->camps;
$lists = $this->lists;
$czones = $this->czones;
$_row=$this->ad;
$advertiser_id = $this->advertiser_id;
$nullDate = 0;
$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }
$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
$cpanel_home = "";
$document = JFactory::getDocument();
JHtml::_('behavior.framework',true);
$editor1  =  JFactory::getEditor();
include_once(JPATH_BASE."/components/com_adagency/includes/js/floating.php");
require_once('components/com_adagency/helpers/geo_helper.php');
if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
    include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
}
require_once('components/com_adagency/includes/js/floating_geo.php');
if (!isset($_row->parameters['ad_code'])) $_row->parameters['ad_code']='';
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

<div class="ada-ads">
  <div class="ijadagencyfloating" id="adagency_container">
    <p id="hidden_adagency" class="uk-hidden">
      <a id="change_cb">#</a><br />
      <a id="close_cb">#</a>
    </p>
  </div>

  <nav class="uk-navbar ada-toolbar">
    <!-- Toolbar -->
    <ul class="uk-navbar-nav">
      <li><a href="<?php echo $cpn_link;?>"><i class="uk-icon-home"></i></a></li>
      <li><a href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=<?php echo intval($my->id) . $Itemid_adv;?>"><?php echo JText::_('ADG_PROF'); ?></a></li>
      <li class="uk-active"><a href="index.php?option=com_adagency&controller=adagencyAds<?php echo $Itemid_ads; ?>"><?php echo JText::_('ADG_ADS'); ?></a></li>
      <li><a href="index.php?option=com_adagency&controller=adagencyOrders<?php echo $Itemid_ord; ?>"><?php echo JText::_('ADG_ORDERS'); ?></a></li>
      <li><a href="index.php?option=com_adagency&controller=adagencyReports<?php echo $Itemid_rep; ?>"><?php echo JText::_('ADG_REPORTS'); ?></a></li>
      <li><a href="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo $Itemid_cmp; ?>"><?php echo JText::_('ADG_CAMP'); ?></a></li>
      <li><a href="index.php?option=com_adagency&controller=adagencyPackage<?php echo $Itemid_pck; ?>"><?php echo JText::_('ADG_PACKAGES'); ?></a></li>
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
    <option selected="selected" value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds'. $Itemid_ads); ?>"><?php echo JText::_('ADG_ADS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders'.$Itemid_ord); ?>"><?php echo JText::_('ADG_ORDERS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyReports'.$Itemid_rep); ?>"><?php echo JText::_('ADG_REPORTS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns'.$Itemid_cmp); ?>"><?php echo JText::_('ADG_CAMP'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyPackage'.$Itemid_pck); ?>"><?php echo JText::_('ADG_PACKAGES'); ?></option>                               
    <option value="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><?php echo JText::_('ADG_LOGOUT'); ?></option>
  </select>

  <div class="ada-ads-heading">
    <h2 class="ada-ads-title"><?php echo JText::_('VIEWTREEADDFLOATING'); ?></h2>
  </div>

  <form class="uk-form uk-form-horizontal" action="index.php" method="post" name="adminForm" id="adminForm">
    <legend><?php echo  JText::_('NEWADDETAILS');?></legend>
    <div class="uk-form-row">
      <label for="floating_title" class="uk-form-label">
        <?php echo JText::_('NEWADTITLE');?>
        <span class="uk-text-danger">*</span>
      </label>
      <div class="uk-form-controls">
        <input type="text" name="title" id="floating_title"  value="<?php if ($_row->title!="") {echo $_row->title;} else {echo "";} ?>" />
      </div>
    </div>

    <div class="uk-form-row">
      <label for="floating_description" class="uk-form-label">
        <?php echo JText::_('NEWADDESCTIPTION');?>
      </label>
      <div class="uk-form-controls">
        <input type="text" name="description" id="floating_description"  value="<?php if ($_row->description!="") {echo $_row->description;} else {echo "";} ?>" />
      </div>
    </div>

    <input class="formField"  type="hidden" name="width" size="3" value="<?php if ($_row->width!="") {echo $_row->width;} else {echo "300";} ?>" />
    <input class="formField" type="hidden" name="height" size="3" value="<?php if ($_row->height!="") {echo $_row->height;} else {echo "300";} ?>" />

    <?php 
      if($configs->allow_add_keywords == 1){
    ?>
    <div class="uk-form-row">
      <label for="keywords" class="uk-form-label">
        <?php echo JText::_('ADAG_KEYWORDS');?>
      </label>
      <div class="uk-form-controls">
        <input type="text" name="keywords" id="keywords"  value="<?php if ($_row->keywords != ""){echo $_row->keywords;} else {echo "";} ?>" />
        <p class="uk-form-help-block"><?php echo JText::_("ADAG_ENTER_KEYWORDS"); ?></p>
      </div>
    </div>
    <?php
      }
    ?>

    <legend class="uk-margin-large-top"><?php echo  JText::_('NEWADADCODEMSG');?></legend>
    <div class="uk-form-row">
      <label for="" class="uk-form-label"></label>
      <div class="uk-form-controls">
        <?php
          echo $editor1->display( 'transitioncode', ''.stripslashes($_row->parameters['ad_code']),'100%', '300px', '20', '60',false );
        ?>
      </div>
    </div>

    <?php 
      output_geoform($advertiser_id);
      require_once(JPATH_BASE."/administrator/components/com_adagency/helpers/jomsocial.php");
      $jomsocial = new JomSocialTargeting();
      $jomsocial->render_front($_row->id);      
      
      if(isset($camps)&&(count($camps)>0)){
          $i=0;
    ?>

    <legend class="uk-margin-large-top"><?php echo  JText::_('ADD_NEWADCMPS');?></legend>
    <table class="uk-table">
      <thead>
        <th></th>
        <th><?php echo JText::_("CONFIGCMP"); ?></th>
        <th><?php echo JText::_("ADAG_ZONES_SIZES"); ?></th>
        <th><?php echo JText::_("ADAG_ON_WHICH_ZONE"); ?></th>
      </thead>

      <tbody>
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
          <td>
            <input type="hidden" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>|no|" />
            <input class="formField camp<?php echo $camp->id; ?>" type="checkbox" <?php
            if(in_array($camp->id,$banners_camps)){
                echo 'checked="checked"';
            }
            ?>id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
          </td>
          <td class="uk-text-bold"><?php echo $camp->name; ?></td>
          <td>
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
          <td>
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

                  if(trim($zone_width) != "" && trim($zone_height) != "" && isset($params["floating"])){
                    if(trim($zone_width) == trim($ad_width) && trim($zone_height) == trim($ad_height)){
                      $ok = TRUE;
                      break;
                    }
                  }
                  elseif(trim($zone_width) == "" && trim($zone_height) == "" && isset($params["floating"])){
                    $ok = TRUE;
                    break;
                  }
                }
              }

              if($ok){
                echo $czones_select[$camp->id];
              }
              else{
                echo '<span class="uk-badge uk-badge-danger">'.JText::_("ADAG_SIZE_OF_AD_UPLOADED")." (".$_row->width." x ".$_row->height." px) ".JText::_("ADAG_NOT_SUPPORTED_BY_THIS_CAMPAIGN").'</span>';
              }
            ?>
          </td>
        </tr>
      <?php
        }
      ?>
      </tbody>
    </table>
    <?php
      }
    ?>

    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="media_type" value="Floating" />
    <input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
    <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
    <input type="hidden" name="parameters['border']" value="<?php echo @$_row->parameters['border'];?>" />
    <input type="hidden" name="parameters['border_color']" value="<?php echo @$_row->parameters['border_color'];?>" />
    <input type="hidden" name="parameters['bg_color']" value="<?php echo @$_row->parameters['bg_color'];?>" />
    <input type="hidden" name="parameters['ad_code']" value="<?php echo htmlspecialchars($_row->parameters['ad_code']);?>" />
    <input type="hidden" name="controller" value="adagencyFloating" />
    <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />

    <div class="uk-form-row uk-margin-top">
      <?php
        if(isset($camps)&&(count($camps)>0)){
        } else {
      ?>
      <label for="" class="uk-form-label"></label>
      <div class="uk-form-controls">
      <?php } ?>
        <input class="uk-button" type="button" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_BACK'); ?>" />
        <input class="uk-button uk-button-primary" type="button" value="<?php echo JText::_("AD_SAVE");?>" onclick="Joomla.submitbutton('save');">
      <?php
        if(isset($camps)&&(count($camps)>0)){
      ?>
      </div>
      <?php } ?>
    </div>
  </form>
</div>
