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
  $banners_camps = $this->these_campaigns;
    if (!$banners_camps) { $banners_camps = array(); }
  $data = $this->data;
  $realimgs = $this->realimgs;
  $advertiser_id = $this->advertiser_id;
  $camps = $this->camps;
  $ad = $this->ad;
  
  if (isset($this->cb_count)) {
    $cb_count = $this->cb_count;
  } else {
    $cb_count = 0;
  }
    //echo "<pre>";print_r($camps);die();
  $lists = $this->lists;
  $_row = $this->ad;
  
  if(isset($_SESSION["title"]) && trim($_SESSION["title"]) != ""){
    $_row->title = trim($_SESSION["title"]);
  }
  if(isset($_SESSION["description"]) && trim($_SESSION["description"]) != ""){
    $_row->description = trim($_SESSION["description"]);
  }
  if(isset($_SESSION["target_url"]) && trim($_SESSION["target_url"]) != ""){
    $_row->target_url = trim($_SESSION["target_url"]);
  }
  if(isset($_SESSION["keywords"]) && trim($_SESSION["keywords"]) != ""){
    $_row->keywords = trim($_SESSION["keywords"]);
  }
  unset($_SESSION["title"]);
  unset($_SESSION["description"]);
  unset($_SESSION["target_url"]);
  unset($_SESSION["keywords"]);
  
  $czones = $this->czones;
  $czones_select = $this->czones_select;
  $campaigns_zones = $this->campaigns_zones;
  
  $configs = $this->configs;
  $siz_sel = $this->size_selected;
  //if(isset($_row->width)&&isset($_row->height)&&(($_row->width==0)||($_row->height==0))) {
    if(isset($siz_sel[0])&&isset($siz_sel[1])&&($siz_sel[0]>0)&&($siz_sel[1]>0)){
      $_row->width=$siz_sel[0];$_row->height=$siz_sel[1];
    }
  //}
  $nullDate = 0;
  $item_id = $this->itemid;
  if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
  $item_id_cpn = $this->itemid_cpn;
    if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }
  $cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
  $document =  JFactory::getDocument();
  $document->addScript(JURI::root()."/components/com_adagency/includes/js/standard_upl.js");
  include_once(JPATH_BASE."/components/com_adagency/includes/js/standard.php");
  require_once('components/com_adagency/helpers/geo_helper.php');
  $current = $this->channel;
    if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
        include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
    }
  include_once(JPATH_BASE."/components/com_adagency/includes/js/standard_geo.php");
  $lists['image_directory']=substr($lists['image_directory'],0,-1);
  $lists['image_directory']= JURI::base().$lists['image_directory'];
    //echo "<pre>";var_dump($camps);die();
  if (!isset($type)) $type='cpm';
  if (!isset($package->type)) @$package->type=$type;
    foreach($realimgs as $k=>$v)
      $realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
      $realimgs = implode(",\n", $realimgs);
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
  <div class="ijadagencytextlink" id="adagency_container">
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
    <h2 class="ada-ads-title"><?php echo JText::_('VIEWTREEADDSTANDARD'); ?></h2>
  </div>

  <form class="uk-form uk-form-horizontal" action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <legend><?php echo  JText::_('NEWADDETAILS');?></legend>
    <div class="uk-form-row">
      <label for="standard_title" class="uk-form-label">
        <?php echo JText::_('NEWADTITLE');?>
        <span class="uk-text-danger">*</span>
      </label>
      <div class="uk-form-controls">
        <input type="text" id="standard_title" name="title" value="<?php if ($_row->title!="") {echo $_row->title;} else {echo "";} ?>">
      </div>
    </div>

    <div class="uk-form-row">
      <label for="standard_description" class="uk-form-label">
        <?php echo JText::_('NEWADDESCTIPTION');?>
      </label>
      <div class="uk-form-controls">
        <input type="text" name="description" id="standard_description"  value="<?php if ($_row->description!="") {echo $_row->description;} else {echo "";} ?>" />
      </div>
    </div>

    <div class="uk-form-row">
      <label for="standard_url" class="uk-form-label">
        <?php echo JText::_('NEWADTARGET');?>
        <span class="uk-text-danger">*</span>
      </label>
      <div class="uk-form-controls">
        <input type="text" name="target_url" id="standard_url"  value="<?php if ($_row->target_url!="") {echo $_row->target_url;} else {echo "http://";} ?>" />
      </div>
    </div>

    <?php 
      if($configs->allow_add_keywords == 1){
    ?>
    <div class="uk-form-row">
      <label for="keywords" class="uk-form-label">
        <?php echo JText::_('ADAG_KEYWORDS');?>
      </label>
      <div class="uk-form-controls">
        <input type="text" id="keywords" name="keywords" value="<?php if ($_row->keywords != ""){echo $_row->keywords;} else {echo "";} ?>" >
        <p class="uk-form-help-block"><?php echo JText::_("ADAG_ENTER_KEYWORDS"); ?></p>
      </div>
    </div>
    <?php
      }

      $label = JText::_('JAS_UPLOADIMAGE');
    ?>

    <legend class="uk-margin-large-top"><?php echo JText::_('NEWADIMAGE'); ?></legend>
    <div class="uk-form-row">
      <label for="image_file" class="uk-form-label">
        <?php echo $label; ?>
      </label>
      <div class="uk-form-controls">
        <input type="file" multiple="" name="image_file" id="image-file" onchange="document.getElementById('upload-submit').click();" />
        <button id="upload-submit" class="uk-button" style="display:none !important;" onclick="return UploadImage();"><i class="icon-upload icon-white"></i><?php echo JText::_('ADAG_UPLOAD');?></button>
        <input type="hidden" name="image_url" id="image_url" value="<?php echo $_row->image_url;?>" />
      </div>
    </div>

    <?php
      if (isset($_row->image_url) && ($_row->image_url != NULL)){ 
    ?>
    <div class="uk-form-row">
      <label for="standard_url" class="uk-form-label">
        <?php echo JText::_('NEWADPREVIEW');?>
      </label>
      <div class="uk-form-controls">
        
        <?php 
          if(!$_row->id){
        ?>
        <div id="imgdiv" class="imgOutline" style="display: block;">
        <?php
          if(!$_row->image_url){
            echo '<img id="imagelib" src="images/blank.png" name="imagelib" />';
          }
          else{
            echo '<img id="imagelib" src="'.$lists["image_directory"]."/".$_row->image_url.'" name="imagelib" />';
          }
        ?>
        </div>
        <?php
          }
          else{
        ?>
        <div id="imgdiv" class="imgOutline" style="display: block;">
          <img id="imagelib" src="<?php echo $lists['image_directory']."/".$_row->image_url; ?>" name="imagelib" />
        </div>
        <?php
          }
        ?>
        <div id="imgwait" style="display:none;">
          <img src="<?php echo JURI::root()."components/com_adagency/images/pleasewait.gif"; ?>" />
        </div>
      </div>
    </div>

    <div class="uk-form-row">
      <label for="standard_width" class="uk-form-label"></label>
      <div class="uk-form-controls">
        <?php
          if($_row->width>0){
            echo $_row->width;
            echo "<input type='hidden' id='standard_width' name='width' value='".$_row->width."' />";
          }
          else{
            echo "";
          } 
        ?> x 
        <?php 
          if($_row->height>0){
            echo $_row->height; 
            echo "<input type='hidden' id='standard_height' name='height' value='".$_row->height."' />";
          }
          else{
            echo "";
          }
        ?>
        <span class="uk-text-muted"><?php echo JText::_('NEWADSIZE'); ?></span>
      </div>
    </div>
    <?php } else { ?>
    <div class="uk-form-row">
      <label for="popup_pageurl" class="uk-form-label"></label>
      <div class="uk-form-controls">
        <div id="imgwait" style="display:none;">
            <img src="<?php echo JURI::root()."components/com_adagency/images/pleasewait.gif"; ?>" />
        </div>
      </div>
    </div>
    <?php 
      }

      output_geoform($advertiser_id);
      require_once(JPATH_BASE."/administrator/components/com_adagency/helpers/jomsocial.php");
      $helper = new JomSocialTargeting();
      $helper->render_front($_row->id);

      if(isset($camps)&&(count($camps)>0)){
        $i=0;
    ?>
    <legend class="uk-margin-large-top"><?php echo  JText::_('ADD_NEWADCMPS');?></legend>
    <table class="uk-table uk-table-middle">
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
          <th>
            <input type="hidden" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>|no|" />
            <input class="formField camp<?php echo $camp->id; ?>" type="checkbox" <?php
            if(in_array($camp->id,$banners_camps)){
                echo 'checked="checked"';
            }
            ?>id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
          </th>
          <th class="uk-text-bold"><?php echo $camp->name; ?></th>
          <th>
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
          </th>
          <th>
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

                  if(trim($zone_width) != "" && trim($zone_height) != "" && isset($params["standard"])){
                    if(trim($zone_width) == trim($ad_width) && trim($zone_height) == trim($ad_height)){
                      $ok = TRUE;
                      break;
                    }
                  }
                  elseif(trim($zone_width) == "" && trim($zone_height) == "" && isset($params["standard"])){
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
          </th>
        </tr>
      <?php
        }
      ?>
      </tbody>
    </table>
    <?php
      if(isset($_row->image_url)) {
        $url_to_camps = '<a target="_blank" href="' . JURI::root() . '/index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&Itemid=' . $item_id . '">' . JText::_('ADAG_HERE') . '</a>';
      }

      if(isset($displayed) && (count($displayed) == 0) && (isset($_row->image_url))) {
        $url_to_camps = '<a target="_blank" href="' . JURI::root() . '/index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&Itemid=' . $item_id . '">' . JText::_('ADAG_HERE') . '</a>';
      }

      }
    ?>

    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" id="task" value="save" />
    <input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
    <input type="hidden" name="media_type" value="Standard" />
    <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
    <input type="hidden" name="controller" value="adagencyStandard" />
    <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />

    <div class="uk-form-row uk-margin-large-top">
      <?php
        if(isset($camps)&&(count($camps)>0)){
        } else {
      ?>
      <label for="" class="uk-form-label"></label>
      <div class="uk-form-controls">
      <?php } ?>
        <input class="uk-button" type="button" onclick="<?php
          if(isset($_SERVER['HTTP_REFERER']) && (strpos(" ".$_SERVER['HTTP_REFERER'],"adagencyStandard")<1)) {
            $_SESSION['standardAdReferer'] = $_SERVER['HTTP_REFERER'];
          } elseif ( !isset($_SERVER['HTTP_REFERER']) || !isset($_SESSION['standardAdReferer']) ) {
            $_SESSION['standardAdReferer'] = '#';
          }
      
          echo "document.location = '".$_SESSION['standardAdReferer']."';";
        ?>" value="<?php echo JText::_('ADAG_BACK'); ?>" />
        <input class="uk-button uk-button-primary" type="button" value="<?php echo JText::_("AD_SAVE");?>" onclick="Joomla.submitbutton('save');">
      <?php
        if(isset($camps)&&(count($camps)>0)){
		}
		else{
      ?>
      </div>
      <?php } ?>
    </div>
  </form>
</div>
