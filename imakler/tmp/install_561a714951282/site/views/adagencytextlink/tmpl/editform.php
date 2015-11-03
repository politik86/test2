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

$configs = $this->configs;
$configs->params = @unserialize($configs->params);
$current = $this->channel;
$banners_camps = $this->these_campaigns;
$cpanel_home = NULL;
if (!$banners_camps) { $banners_camps = array(); }
$czones = $this->czones;
$czones_select = $this->czones_select;
$campaigns_zones = $this->campaigns_zones;
$ad = $this->ad;
$data = $this->data;
$camps = $this->camps;
$lists = $this->lists;
$_row = $this->ad;
$max_chars = $this->max_chars;
$imgInfo = $this->imgInfo;
if(isset($imgInfo[0])) { $img_w = $imgInfo[0]; } else { $img_w = NULL; }
$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".intval($item_id); } else { $Itemid = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".intval($item_id_cpn); } else { $Itemid_cpn = NULL; }
$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
$document = JFactory::getDocument();
$document->addScript(JURI::root()."components/com_adagency/includes/js/serialize_unserialize.js");
include_once(JPATH_BASE."/components/com_adagency/includes/js/textlink.php");
require_once('components/com_adagency/helpers/geo_helper.php');

if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
    include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
}

include_once(JPATH_BASE."/components/com_adagency/includes/js/textlink_geo.php");
if(isset($_row->parameters["alt_text"])) {$_row->parameters["alt_text"]=str_replace("\"","'",$_row->parameters["alt_text"]);}
$nullDate = 0;
$advertiser_id = $this->advertiser_id;
$post_vars = JRequest::get( 'post' );
if (!isset($type)) $type='cpm';
if (!isset($package->type)) @$package->type=$type;
$realimgs = $this->realimgs;
$lists['image_directory'] = substr($lists['image_directory'],0,-1);
$lists['image_directory'] = JURI::base().$lists['image_directory'];
if (!isset($type)) $type = 'cpm';
if (!isset($package->type)) $package->type=$type;
foreach($realimgs as $k=>$v) $realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
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
    <h2 class="ada-ads-title"><?php echo JText::_('VIEWTREEADDTEXTLINK'); ?></h2>
  </div>

  <form class="uk-form uk-form-horizontal" action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <legend><?php echo  JText::_('NEWADDETAILS');?></legend>
    <div class="uk-form-row">
      <label for="text_title" class="uk-form-label">
        <?php echo JText::_('AD_TEXTLINK_NAME');?>
        <span class="uk-text-danger">*</span>
      </label>
      <div class="uk-form-controls">
        <input type="text" id="text_title" name="title" value="<?php echo $_row->title;?>">
      </div>
    </div>

    <div class="uk-form-row">
      <label for="text_description" class="uk-form-label">
        <?php echo JText::_('NEWADDESCTIPTION');?>
      </label>
      <div class="uk-form-controls">
        <input type="text" name="description" id="text_description"  value="<?php echo $_row->description;?>" />
      </div>
    </div>

    <div class="uk-form-row">
      <label for="text_url" class="uk-form-label">
        <?php echo JText::_('NEWADTARGET');?>
        <span class="uk-text-danger">*</span>
      </label>
      <div class="uk-form-controls">
        <input type="text" name="target_url" id="text_url"  value="<?php if (!$_row->target_url) echo 'http://'; else echo $_row->target_url;?>" />
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
        <input type="text" name="keywords" id="keywords"  value="<?php if ($_row->keywords != ""){echo $_row->keywords;} else {echo "";} ?>" />
        <p class="uk-form-help-block"><?php echo JText::_("ADAG_ENTER_KEYWORDS"); ?></p>
      </div>
    </div>
    <?php
      }
    ?>

    <legend class="uk-margin-large-top"><?php echo JText::_('NEWADHTMLPROP'); ?></legend>
    <div class="uk-form-row">
      <label for="clinktitle" class="uk-form-label">
        <?php echo JText::_('NEWAD_LINKTEXT_TITLE');?>
        <span class="uk-text-danger">*</span>
      </label>
      <div class="uk-form-controls">
        <input type="text" onkeyup="changeLinkTitle();" id="clinktitle" name="linktitle" value="<?php if(isset($post_vars['linktitle'])) { echo stripslashes($post_vars['linktitle']); } else {echo stripslashes(@$_row->parameters['alt_text_t']);} ?>" >
      </div>
    </div>

    <div class="uk-form-row">
      <label  class="uk-form-label">
        <?php echo JText::_('NEWADLINKTEXT');?>
        <span class="uk-text-danger">*</span>
      </label>
      <div class="uk-form-controls">
        <input class="formField" name="parameters[alt_text]" type="hidden"/>
        <textarea name="linktext" id="linktext" wrap="physical" style="width:100%; height: 200px;" onkeyup="var lng=<?php echo $max_chars;?>; if (document.adminForm.linktext.value.length > lng) document.adminForm.linktext.value=document.adminForm.linktext.value.substring(0,lng); else document.adminForm.nume.value = lng-document.adminForm.linktext.value.length; changeBody();"><?php if(isset($post_vars['linktext'])) { echo stripslashes($post_vars['linktext']); } else { echo stripslashes(@$_row->parameters['alt_text']);}?></textarea>
        
        <script type="text/javascript" language="javascript">
          function pasteIntercept(evt) {
            setTimeout(function(){
              var lng=<?php echo $max_chars;?>;
              if(document.adminForm.linktext.value.length > lng){
                document.adminForm.linktext.value=document.adminForm.linktext.value.substring(0,lng);
              }
              else{
                document.adminForm.nume.value = lng-document.adminForm.linktext.value.length;
              }
              changeBody();
            }, 0);
          }
          document.getElementById("linktext").addEventListener("paste", pasteIntercept, false);
        </script>
        
        <input type="text" class="span2 input-mini" readonly="" style="color:#000000; font-weight:bold; background-color:transparent;border:0px solid white" value="<?php echo JText::_('ADAG_CLEFT');?>"/>
        <input type="text" class="input-mini" readonly="" style="color:#FF0000; font-weight:bold; background-color:transparent;border:0px solid white" value="<?php echo ($max_chars - strlen(stripslashes(@$_row->parameters['alt_text'])));?>" name="nume" id="nume" />
      </div>
    </div>

    <div class="uk-form-row">
      <label for="clinkaction" class="uk-form-label">
        <?php echo JText::_('NEWAD_LINKTEXT_ACTIONTEXT');?>
      </label>
      <div class="uk-form-controls">
        <input placeholder="Tell me more >>" type="text" onkeyup="changeAction();" id="clinkaction" name="linkaction" value="<?php if(isset($post_vars['linkaction'])) { echo stripslashes($post_vars['linkaction']); } else { echo stripslashes(@$_row->parameters['alt_text_a']);} ?>" >
      </div>
    </div>

    <?php
      if(isset($configs->params['showtxtimg'])&&($configs->params['showtxtimg'] == '0')){
        // do nothing
      } else {
    ?>
    <legend class="uk-margin-large-top"><?php echo JText::_('NEWADIMAGE'); ?></legend>
      <div class="uk-form-row">
        <label for="image_file" class="uk-form-label">
          <?php $label = JText::_('NEWADUPLOADIMG'); ?>
        </label>
        <div class="uk-form-controls">
          <input type="file" name="image_file" id="image-file" onchange="document.getElementById('upload-submit').click();" />
          <button id="upload-submit" class="uk-button" style="display:none !important;" onclick="return UploadImage();"><i class="icon-upload icon-white"></i><?php echo JText::_('ADAG_UPLOAD');?></button>
          <input type="hidden" name="image_url" id="text_image" value="<?php if(isset($_row->image_url)) {echo $_row->image_url;}?>" />
          <?php
            if(isset($_row->image_url)) {
              echo '<img src="'.$lists['image_directory']."/".$_row->image_url.'" name="imagelib23" />';
            }
            include(JPATH_BASE."/components/com_adagency/includes/js/textlink_upl.php");
          ?>
        </div>
      </div>

      <?php
        if(isset($imgInfo[0])&&(isset($imgInfo[1]))&&($imgInfo[0]>0)&&($imgInfo[1]>0)){
      ?>

      <div class="uk-form-row">
        <label for="clinkaction" class="uk-form-label"></label>
        <div class="uk-form-controls">
          <a href="#" id="remimg"><?php echo JText::_('ADAG_REMIMG');?></a>
        </div>
      </div>

      <?php
        }
      ?>
    <?php } ?>

    <legend class="uk-margin-large-top"><?php echo JText::_('VIEWADPREVIEW'); ?></legend>
    <div class="uk-form-row">
      <label for="standard_url" class="uk-form-label">
        <?php echo JText::_('ADSELZONE');?>
      </label>
      <div class="uk-form-controls">
        <select id="zoneId" onchange="callZoneSettings();return false;">
          <option value="0">--------</option>
          <?php echo $lists['prevzones'];?>
        </select>
      </div>
    </div>

    <div class="uk-form-row">
      <label for="standard_url" class="uk-form-label"></label>
      <div class="uk-form-controls">
        <?php
          if(!$_row->id){
        ?>
        <div id="textlink">
          <a id="tlink">
            <span id="ttitle">&nbsp;</span>
          </a>
          <br />
          <div id="imgdiv2">
            <a id="tlink2">
              <img src="<?php
              if(isset($_row->image_url)&&($_row->image_url!='')){
                echo $lists["image_directory"].'/'.$_row->image_url;
              }
              else{
                echo JURI::base()."/components/com_adagency/images/blank.png";
              }
              ?>" name="imagelib" id="rt_image" />
            </a>
          </div>
          <div id="tbody">
            <span id="ttbody">&nbsp;</span>
          </div>
          <div id="taction">
            <a id="tlink2">
              <span id="ttaction">&nbsp;</span>
            </a>
          </div>
        </div>
        <?php 
          } else {
        ?>
        <div id="textlink">

          <?php
            if(isset($_row->parameters['alt_text_t'])&&($_row->parameters['alt_text_t']!="")){
              if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
                echo "<a id='tlink' href='".$_row->target_url."' ";
                if(isset($_row->parameters['target_window'])&&($_row->parameters['target_window']!="")){
                  echo " target='".$_row->parameters['target_window']."' ";
                }
                echo ">";
              }
              echo "<span id='ttitle'>";
              echo $_row->parameters['alt_text_t']."</span><br />";
              if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
                echo "</a>";
              }
            }
          ?>

          <div id="imgdiv2">
            <?php
              if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
                $outputz="<a id='tlink2' href='".$_row->target_url."' ";
                if(isset($_row->parameters['target_window'])&&($_row->parameters['target_window']!="")){
                    $outputz.="target='".$_row->parameters['target_window']."' ";
                }
                $outputz.=">";
                echo $outputz;
              }?>
              <?php if(isset($_row->image_url)&&($_row->image_url!='')) { ?><img id="rt_image"
              src="<?php echo $lists['image_directory']."/".$_row->image_url; ?>" name="imagelib" id="rt_image" /><?php } ?>                                            <?php
              if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
                echo "</a>";
              }
            ?>
          </div>

          <div id="tbody">
            <?php
              if(isset($_row->parameters['alt_text'])&&($_row->parameters['alt_text']!="")){
                echo "<div id='ttbody'>";
                echo $_row->parameters['alt_text'];
                echo "</div>";
              }
            ?>
          </div>

          <div id="taction">
            <?php
              if(isset($_row->parameters['alt_text_a'])&&($_row->parameters['alt_text_a']!="")){
                if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
                  $outputs="<a id='tlink2' href='".$_row->target_url."' ";
                  if(isset($_row->parameters['target_window'])&&($_row->parameters['target_window']!="")){
                    $outputs.="target='".$_row->parameters['target_window']."' ";
                  }
                  $outputs.=">";
                  echo $outputs;
                }
                echo "<span id='ttaction'>";
                echo $_row->parameters['alt_text_a'];
                echo "</span></a>";
              }
            ?>
          </div>

        </div>
        <?php } ?>
      </div>
    </div>

    <?php
      output_geoform($advertiser_id);
      require_once(JPATH_BASE."/administrator/components/com_adagency/helpers/jomsocial.php");
      $helper = new JomSocialTargeting();
      $helper->render_front($_row->id);     
    ?>

    <?php
      if(isset($camps)&&(count($camps)>0)){
        $i=0;
    ?>
    <legend><?php echo  JText::_('ADD_NEWADCMPS');?></legend>
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

                  if(trim($zone_width) != "" && trim($zone_height) != "" && isset($params["textad"])){
                    if(trim($zone_width) == trim($ad_width) && trim($zone_height) == trim($ad_height)){
                      $ok = TRUE;
                      break;
                    }
                  }
                  elseif(trim($zone_width) == "" && trim($zone_height) == "" && isset($params["textad"])){
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
    <input type="hidden" name="controller" value="adagencyTextlink" />
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" id="task" value="save" />
    <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
    <input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
    <input type="hidden" name="media_type" value="TextLink" />
    <input type="hidden" name="parameters[alt_text]" value="<?php if(isset($_row->parameters['alt_text'])) echo $_row->parameters['alt_text'];?>" />
    <input type="hidden" name="parameters[alt_text_t]" value="<?php if(isset($_row->parameters['alt_text_t'])) echo $_row->parameters['alt_text_t'];?>" />
    <input type="hidden" name="parameters[alt_text_a]" value="<?php if(isset($_row->parameters['alt_text_a'])) echo $_row->parameters['alt_text_a'];?>" />
    <input type="hidden" name="parameters[img_alt]" maxlength="200" value="<?php if(isset($post_vars['parameters']['img_alt'])) { echo stripslashes($post_vars['parameters']['img_alt']); } else {echo stripslashes(@$_row->parameters['img_alt']);} ?>">
    <?php if(!isset($_row->id)||($_row->id == 0)){ ?>
      <input type="hidden" name="parameters['font_family']" value="<?php if(isset($_row->parameters['font_family'])) echo $_row->parameters['font_family'];?>" />
      <input type="hidden" name="parameters['font_size']" value="<?php if(isset($_row->parameters['font_size'])) echo $_row->parameters['font_size'];?>" />
      <input type="hidden" name="parameters['font_weight']" value="<?php if(isset($_row->parameters['font_weight'])) echo $_row->parameters['font_weight'];?>" />
      <input type="hidden" name="parameters['align']" value="<?php if(isset($_row->parameters['align'])) echo $_row->parameters['align'];?>" />
      <input type="hidden" name="parameters['border']" value="<?php if(isset($_row->parameters['border'])) echo $_row->parameters['border'];?>" />
      <input type="hidden" name="parameters['border_color']" value="<?php if(isset($_row->parameters['border_color'])) echo $_row->parameters['border_color'];?>" />
      <input type="hidden" name="parameters['bg_color']" value="<?php if(isset($_row->parameters['bg_color'])) echo $_row->parameters['bg_color'];?>" />
      <input type="hidden" name="parameters['target_window']" value="<?php if(isset($_row->parameters['target_window'])) echo $_row->parameters['target_window'];?>" />
    <?php } ?>
    <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
    <!-- hidden zones -->
    <?php echo $lists['hidden_zones']; ?>

    <div class="uk-form-row uk-margin-large-top">
		<?php
		if(isset($camps)&&(count($camps)>0)){
		}
		else{
		?>
			<label for="" class="uk-form-label"></label>
			<div class="uk-form-controls">
		<?php
		}
		?>
			<input class="uk-button" type="button" onclick="<?php
				  if(isset($_SERVER['HTTP_REFERER']) && (strpos(" ".$_SERVER['HTTP_REFERER'],"adagencyTextlink")<1)) {
					$_SESSION['textadAdReferer'] = $_SERVER['HTTP_REFERER'];
				  } elseif ( !isset($_SERVER['HTTP_REFERER']) || !isset($_SESSION['textadAdReferer']) ) {
					$_SESSION['textadAdReferer'] = '#';
				  }
				  echo "document.location = '".$_SESSION['textadAdReferer']."';";
				?>" value="<?php echo JText::_('ADAG_BACK'); ?>" />
			<input class="uk-button uk-button-primary" type="button" value="<?php echo JText::_("AD_SAVE"); ?>" onclick="Joomla.submitbutton('save');">
		<?php
		if(isset($camps)&&(count($camps)>0)){
		}
		else{
		?>
			</div>
		<?php
		}
		?>
    </div>
  </form>
</div>
