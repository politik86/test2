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
if(!$banners_camps){
  $banners_camps = array();
}

$data = $this->data;
$realimgs = $this->realimgs;
$advertiser_id = $this->advertiser_id;
$camps = $this->camps;
$ad = $this->ad;

if(isset($this->cb_count)) {
  $cb_count = $this->cb_count;
}
else{
  $cb_count = 0;
}

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

$configs = $this->configs;
$siz_sel = $this->size_selected;

if(isset($siz_sel[0])&&isset($siz_sel[1])&&($siz_sel[0]>0)&&($siz_sel[1]>0)){
  $_row->width=$siz_sel[0];$_row->height=$siz_sel[1];
}

$nullDate = 0;
$item_id = $this->itemid;

if($item_id != 0){
  $Itemid = "&Itemid=".$item_id;
}
else{
  $Itemid = NULL;
}

$item_id_cpn = $this->itemid_cpn;

if($item_id_cpn != 0){
  $Itemid_cpn = "&Itemid=".$item_id_cpn;
}
else{
  $Itemid_cpn = NULL;
}

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

include_once(JPATH_BASE."/components/com_adagency/includes/js/standard.php");
require_once('components/com_adagency/helpers/geo_helper.php');

$current = $this->channel;

if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
  include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
}
include_once(JPATH_BASE."/components/com_adagency/includes/js/standard_geo.php");

$lists['image_directory']=substr($lists['image_directory'],0,-1);
$lists['image_directory']= JURI::base().$lists['image_directory'];

if(!isset($type)){
  $type='cpm';
}

if(!isset($package->type)){
  @$package->type=$type;
}

foreach($realimgs as $k=>$v){
  $realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
}

$realimgs = implode(",\n", $realimgs);
$my =  JFactory::getUser();
$return_url = base64_encode("index.php?option=com_adagency".$Itemid);
$ads = $this->getModel("adagencyConfig")->getItemid('adagencyads');
$adv = $this->getModel("adagencyConfig")->getItemid('adagencyadvertisers');
$cmp = $this->getModel("adagencyConfig")->getItemid('adagencycampaigns');
$rep = $this->getModel("adagencyConfig")->getItemid('adagencyreports');
$ord = $this->getModel("adagencyConfig")->getItemid('adagencyorders');
$pck = $this->getModel("adagencyConfig")->getItemid('adagencypackage');

if($pck != 0){
  $Itemid_pck = "&Itemid=".intval($pck);
}
else{
  $Itemid_pck = NULL;
}

if($ads != 0){
  $Itemid_ads = "&Itemid=".intval($ads);
}
else{
  $Itemid_ads = NULL;
}

if($adv != 0){
  $Itemid_adv = "&Itemid=".intval($adv);
}
else{
  $Itemid_adv = NULL;
}

if($cmp != 0){
  $Itemid_cmp = "&Itemid=".intval($cmp);
}
else{
  $Itemid_cmp = NULL;
}

if($ord != 0){
  $Itemid_ord = "&Itemid=".intval($ord);
}
else{
  $Itemid_ord = NULL;
}

if($rep != 0){
  $Itemid_rep = "&Itemid=".intval($rep);
}
else{
  $Itemid_rep = NULL;
}

$headline_limit = "10";
$content_limit = "100";
$show_sponsored_stream_info = "1";
$show_create_ad_link = "1";
$js_settings = $this->js_settings;

if(isset($js_settings["headline_limit"])){
  $headline_limit = intval($js_settings["headline_limit"]);
}
if(isset($js_settings["content_limit"])){
  $content_limit = intval($js_settings["content_limit"]);
}

if(isset($js_settings["show_sponsored_stream_info"])){
  $show_sponsored_stream_info = intval($js_settings["show_sponsored_stream_info"]);
}
if(isset($js_settings["show_create_ad_link"])){
  $show_create_ad_link = intval($js_settings["show_create_ad_link"]);
}

$document->addScript("components/com_adagency/includes/js/ajaxupload.js");

?>

<script type="text/javascript" language="javascript">
  function changePromoteURL2(){
    target_url = document.getElementById("target_url").value;
    
    //------------------------------------------------------
    var message;
    var myRegExp =/^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;
    var urlToValidate = target_url;
    if(!myRegExp.test(urlToValidate)){
      alert("<?php echo JText::_("ADAG_NOT_A_VALID_URL"); ?>");
      return false;
    }
    //------------------------------------------------------
    
    if(!target_url.contains("http")){
      target_url = 'http://'+target_url;
    }
    
    var hostname = new URL(target_url).hostname;
    
    start = "http://";
    if(target_url.contains("https")){
      start = "https://";
    }
    document.getElementById("preview_promote_url").innerHTML = start + hostname;
  }
  
  function changePromoteURL(){
    timeoutID = window.setTimeout(changePromoteURL2, 500);
  }
  
  function cutText(element_id, max_value){
    if(element_id == "ad_headline"){
      length = document.getElementById("ad_headline").value.length;
      if(parseInt(max_value) - length >= 0){
        document.getElementById("head_left").innerHTML = max_value - length;
      }
      else{
        document.getElementById("ad_headline").value = document.getElementById("ad_headline").value.substring(0, max_value);
        document.getElementById("head_left").innerHTML = "0";
      }
      document.getElementById("preview_ad_headline").innerHTML = document.getElementById("ad_headline").value;
    }
    else if(element_id == "ad_text"){
      length = document.getElementById("ad_text").value.length;
      if(parseInt(max_value) - length >= 0){
        document.getElementById("content_left").innerHTML = max_value - length;
      }
      else{
        document.getElementById("ad_text").value = document.getElementById("ad_text").value.substring(0, max_value);
        document.getElementById("content_left").innerHTML = "0";
      }
      document.getElementById("preview_ad_text").innerHTML = document.getElementById("ad_text").value;
    }
  }
  
  function changeTarget(){
    values = document.getElementsByName("hidden_ids");
    url = "";
    audience = 0;
    total = 0;
    
    for(i=0; i<values.length; i++){
      element_id = values[i].value;
      element = document.getElementById("jomsocial_"+element_id);
      
      value = "";
      id = element_id;
      
      if(element.tagName.toLowerCase() == "input"){
        if(element.checked == true){
          value = element.value;
          url += "&target_"+id+"="+value;
        }
      }
      else if(element.tagName.toLowerCase() == "select"){
        value = element.value;
        url += "&target_"+id+"="+value;
      }
    }
    
    var url = "<?php echo JURI::root(); ?>index.php?option=com_adagency&controller=adagencyJomsocial&task=target&format=raw&tmpl=component"+url;
    var req = new Request.HTML({
      method: 'get',
      url: url,
      async:false,
      data: { 'do' : '1' },
      onComplete: function(response){
        document.getElementById("ajax-result").empty().adopt(response);
        return_result = document.getElementById("ajax-result").innerHTML;
        temp = return_result.split("-");
        
        audience = temp[0];
        total = temp[1];
      }
    }).send();
    
    var myData = new Array(['<?php echo JText::_("ADAG_AUDIENCE"); ?>', parseInt(audience)], ['', parseInt(total)]);
    var colors = ['#FB9900', '#FACC00'];
    var myChart = new JSChart('basicpiechart', 'pie', null);
    myChart.setDataArray(myData);
    myChart.colorizePie(colors);
    myChart.setTitleColor('#857D7D');
    myChart.setPieUnitsColor('#9B9B9B');
    myChart.setPieValuesColor('#6A0000');
    myChart.setTitle('');
    myChart.setSize(250, 250);
    myChart.draw();
  }
  
  function timeToStamp(string_date){
    var form = document.adminForm;
    var time_format = form["time_format"].value;
    myDate = string_date.split(" ");
    myDate = myDate[0].split("-");
    
    if(myDate instanceof Array){
    }
    else{
      myDate = myDate[0].split("/");
    }
    var newDate = '';
    
    switch (time_format){
      case "0" :
        newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];
        break;
      case "1" :
        newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
        break;
      case "2" :
        newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
        break;
      case "3" :
        newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];
        break;
      case "4" :
        newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
        break;
      case "5" :
        newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
        break;
    }
    
    return newDate;
  }
  
  window.addEvent("domready", function(){
    jQuery("#target_url").focusout(function(){
        changePromoteURL();
      });
  });
  
  window.addEvent("domready", function(){
    new AjaxUpload("ajaxuploadavatar", {
      action: "<?php echo JURI::root(); ?>index.php?option=com_adagency&controller=adagencyJomsocial&task=upload&tmpl=component&format=row&no_html=1",
      name: "image_file",
      multiple: false,
      onSubmit: function(id, fileName) {
        jQuery('#onAjaxavatar').css('display', 'inline');
      },
      onComplete: function(file, response){
        if(eval(document.getElementById("image-image-url"))){
          document.getElementById("image-image-url").src = "<?php echo JURI::root(); ?>"+response;
        }
                
        if(eval(document.getElementById("preview-image-image-url"))){
          document.getElementById("preview-image-image-url").src = "<?php echo JURI::root(); ?>"+response;
        }
        
        if(eval(document.getElementById("image_url"))){
          var filename = response.split('/').pop();
          document.getElementById("image_url").value = filename;
        }
        
        if(eval(document.getElementById("onAjaxavatar"))){
          jQuery('#onAjaxavatar').hide();
        }
        
        if(eval(document.getElementById("delete-image-url"))){
          document.getElementById("delete-image-url").style.display = "inline-block";
        }
      }
    });
  });
  
  window.addEvent("domready", function(){
    new AjaxUpload("ajaxuploadcontent", {
      action: "<?php echo JURI::root(); ?>index.php?option=com_adagency&controller=adagencyJomsocial&task=uploadImageContent&tmpl=component&format=row&no_html=1",
      name: "image_content_file",
      multiple: false,
      onSubmit: function(id, fileName) {
        jQuery('#onAjaxcontent').css('display', 'inline');
      },
      onComplete: function(file, response){
      if(response == ""){
      if(eval(document.getElementById("onAjaxavatar"))){
        jQuery('#onAjaxcontent').hide();
          }
      return true;
    }
    
    if(eval(document.getElementById("image-image-content"))){
          document.getElementById("image-image-content").src = "<?php echo JURI::root(); ?>"+response;
        }
                
        if(eval(document.getElementById("preview-image-image-content"))){
          document.getElementById("preview-image-image-content").src = "<?php echo JURI::root(); ?>"+response;
        }
        
        if(eval(document.getElementById("image_content"))){
          var filename = response.split('/').pop();
          document.getElementById("image_content").value = filename;
        }
        
        if(eval(document.getElementById("onAjaxavatar"))){
          jQuery('#onAjaxcontent').hide();
        }
        
        if(eval(document.getElementById("delete-image-content"))){
          document.getElementById("delete-image-content").style.display = "inline-block";
        }
      }
    });
  });
  
  Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    if((pressbutton=='save')||(pressbutton=='apply')||(pressbutton=='save_and_new_camp')){
      if(form['title'].value == ""){
        alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
        return false;
      }
      else if(form['target_url'].value == ""){
        alert( "<?php echo JText::_("JS_INSERT_PROMOTE_URL");?>" );
        return false;
      }
      
      if(pressbutton == 'save'){
        nr_camps_available = document.getElementById("nr_camps_available").value;
        checked = false;
        for(i=1; i<=nr_camps_available; i++){
          if(document.getElementsByName("adv_cmp["+i+"]")[1].checked){
            checked = true;
          }
        }
        
        if(!checked){
          alert("<?php echo JText::_("ADAG_SELECT_CAMPAIGNS"); ?>");
          return false;
        }
      }
      
      submitform(pressbutton);
    }
    else{
      submitform(pressbutton);
    }
    return true;
  }
</script>

<script type="text/javascript" language="javascript" src="<?php echo JURI::root(); ?>components/com_adagency/includes/js/adagency_modal.js"></script>

<!-- JomSocial Stream Ads -->
<div class="ada-joms">
  <div class="ijadagencyjomsocial" id="adagency_container">
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
    
  <div class="ada-joms-heading">
    <h2 class="ada-joms-title"><?php echo JText::_('VIEWTREEADDJOMSOCIAL'); ?></h2>
  </div>

  <form class="uk-form uk-form-horizontal" action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <!-- Promote URL -->
    <div class="ada-joms-promote">
      <h3 class="ada-joms-promote-title"><?php echo JText::_('ADAG_URL_TO_PROMOTE'); ?> <span class="uk-text-danger">*</span></h3>
      <input type="text" id="target_url" name="target_url" id="standard_url"  value="<?php if ($_row->target_url!="") {echo $_row->target_url;} else {echo "http://";} ?>" onpaste="javascript:changePromoteURL();" />
    </div>
  
    <!-- Details -->
    <div class="ada-joms-box">
      <h4 class="ada-joms-box-title"><?php echo JText::_('NEWADDETAILS'); ?></h4>

      <div class="uk-form-row">
        <label for="standard_title" class="uk-form-label">
          <?php echo JText::_('NEWJSADTITLE');?>
          <span class="uk-text-danger">*</span>
        </label>
        <div class="uk-form-controls">
          <input type="text" id="standard_title" name="title" value="<?php if($_row->title!=""){ echo $_row->title; }else{echo "";} ?>" />
        </div>
      </div>
    </div>

    <!-- Image -->
    <div class="ada-joms-box ada-joms-box-reset">
      <h4 class="ada-joms-box-title ada-joms-box-title-reset"><?php echo JText::_('NEWADIMAGE'); ?></h4>

      <ul class="uk-grid uk-grid-width-medium-1-2 uk-grid-small">
        <!-- Avatar -->
        <li>
          <div class="ada-joms-imagebox">
            <em><?php echo JText::_("ADAG_AD_AVATAR_IMAGE"); ?></em>
            <?php
              $img_path = "";

              if(!isset($_row->image_url) || trim($_row->image_url) == ""){
                $img_path = "components/com_adagency/images/ad_avatar.png";
                } else {
                $img_path = $lists['image_path'].$_row->image_url;
              }
            ?>
            
            <div class="ada-joms-image">
              <img src="<?php echo $img_path; ?>" id="image-image-url" alt="ad avatar image" title="ad avatar image">
              <div id="onAjaxavatar" style="display: none;font-weight: bold;margin-left: 20px;width: 300px;">
                <img src="<?php echo JURI::root(); ?>administrator/components/com_adagency/images/ajax-loader.gif" />
                <?php echo JText::_("ADAG_UPLOADING"); ?>
              </div>
            </div>

            <?php
              $display = "none";

              if(isset($_row->image_url) && trim($_row->image_url) != ""){
                $display = "inline-block";
              }
            ?>

            <div class="ada-joms-box-opt">
              <a href="#" class="uk-button uk-button-danger" style="display:<?php echo $display; ?>;" id="delete-image-url" onclick="javascript:deleteImage('div-image-url', 'image-image-url'); return false;">
                <?php echo JText::_("ADAG_DELETE"); ?>
              </a>

              <input class="uk-button uk-button-primary" type="button" name="ajaxuploadavatar" value="<?php echo JText::_("ADAG_UPLOAD"); ?>" id="ajaxuploadavatar"/>
              <input type="hidden" name="image_url" id="image_url" value="<?php if(isset($_row->image_url)){ echo $_row->image_url; }?>" />
            </div>

            <div class="ada-joms-box-info uk-text-muted uk-visible-large">
              <?php echo JText::_("ADAG_SUGGESTED_SIZE_IMAGE_AVATAR"); ?>
              <i class="uk-icon-check-circle uk-text-success uk-visible-large"></i>
            </div>
          </div>
        </li>
        <!-- Content Image -->
        <li>
          <div class="ada-joms-imagebox">
            <em><?php echo JText::_("ADAG_AD_CONTENT_IMAGE"); ?></em>
            <?php
              $img_path = "";

              if(!isset($_row->image_content) || trim($_row->image_content) == ""){
                $img_path = "components/com_adagency/images/ad_content_image.png";
                } else {
                $img_path = $lists['image_path'].$_row->image_content;
              }
            ?>
                                        
            <div class="ada-joms-image">
              <img src="<?php echo $img_path; ?>" id="image-image-content" alt="ad content image" title="ad content image">
              <div id="onAjaxcontent" style="display: none;font-weight: bold;margin-left: 20px;width: 300px;">
                <img src="<?php echo JURI::root(); ?>administrator/components/com_adagency/images/ajax-loader.gif" />
                <?php echo JText::_("ADAG_UPLOADING"); ?>
              </div>
            </div>
                                        
            <?php
              $display = "none";

              if(isset($_row->image_content) && trim($_row->image_content) != ""){
                $display = "inline-block";
              }
            ?>

            <div class="ada-joms-box-opt">
              <a href="#" class="uk-button uk-button-danger" id="delete-image-content" style="display:<?php echo $display; ?>;" onclick="javascript:deleteImage('div-image-content', 'image-image-content'); return false;">
                <?php echo JText::_("ADAG_DELETE"); ?>
              </a>

              <input class="uk-button uk-button-primary" type="button" name="ajaxuploadcontent" value="<?php echo JText::_("ADAG_UPLOAD"); ?>" id="ajaxuploadcontent"/>
              <input type="hidden" name="image_content" id="image_content" value="<?php if(isset($_row->image_content)){ echo $_row->image_content; }?>" />
            </div>

            <div class="ada-joms-box-info uk-text-muted uk-visible-large">
              <?php echo JText::_("ADAG_SUGGESTED_SIZE_IMAGE_CONTENT"); ?>
              <i class="uk-icon-check-circle uk-text-success uk-visible-large"></i>
            </div>
          </div>
        </li>
        
        <!-- SCRIPTS -->
	<script language="javascript" type="text/javascript">
		function deleteImage(div_id, image_id){
			if(image_id == "image-image-url"){
				document.getElementById("image-image-url").src = "<?php echo JURI::root(); ?>components/com_adagency/images/ad_avatar.png";
				document.getElementById("preview-image-image-url").src = "<?php echo JURI::root(); ?>components/com_adagency/images/ad_avatar.png";
				document.getElementById("delete-image-url").style.display = "none";
				document.adminForm.image_url.value = "";
			}
			else if(image_id == "image-image-content"){
				document.getElementById("image-image-content").src = "<?php echo JURI::root(); ?>components/com_adagency/images/ad_content_image.png";
				document.getElementById("preview-image-image-content").src = "<?php echo JURI::root(); ?>components/com_adagency/images/ad_content_image.png";
				document.getElementById("delete-image-content").style.display = "none";
				document.adminForm.image_content.value = "";
			}
		}

          function getSelectedValue2(frmName, srcListName){
          var form = eval('document.' + frmName);
          var srcList = form[srcListName];

          i = srcList.selectedIndex;
          if(i != null && i > -1){
          return srcList.options[i].value;
          }
            else{
              return null;
            }
          }

          function UploadImage(){
          var fileControl = document.adminForm.image_file;
          var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
          if(thisext != ".jpeg" && thisext != ".jpg" && thisext != ".gif" && thisext != ".png" && thisext != ".JPEG" && thisext != ".JPG" && thisext != ".GIF" && thisext != ".PNG"){
            alert('<?php echo JText::_('JS_INVALIDIMG');?>');
            return false;
          }

          if(fileControl.value){
            document.adminForm.task.value = 'upload';
            return true;
          }
            return false;
          }

          function UploadImageContent(){
          var fileControl = document.adminForm.image_content_file;
          var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));

          if(thisext != ".jpeg" && thisext != ".jpg" && thisext != ".gif" && thisext != ".png" && thisext != ".JPEG" && thisext != ".JPG" && thisext != ".GIF" && thisext != ".PNG"){
            alert('<?php echo JText::_('JS_INVALIDIMG');?>');
            return false;
          }

          if(fileControl.value){
          document.adminForm.task.value = 'uploadImageContent';
            return true;
          }
            return false;
          }
        </script>
      </ul>
    </div>

    <!-- Heading & Text -->
    <div class="ada-joms-box">
      <h4 class="ada-joms-box-title"><?php echo JText::_('ADAG_AD_HEADLINE_TEXT'); ?></h4>

      <div class="uk-form-row">
        <label for="standard_url" class="uk-form-label">
          <?php echo JText::_('ADAG_AD_HEADLINE');?>
        </label>
        <div class="uk-form-controls">
          <input type="text" name="ad_headline" id="ad_headline" value="<?php echo $_row->ad_headline; ?>" onkeyup="javascript:cutText('ad_headline', '<?php echo intval($headline_limit); ?>');" />
          <?php
            $current_length = strlen($_row->ad_headline);
            echo '<span id="head_left">'.(intval($headline_limit) - intval($current_length)).'</span>&nbsp;'.JText::_("ADAG_CHARACTERS_LEFT");
          ?>
        </div>
      </div>

      <div class="uk-form-row">
        <label for="standard_url" class="uk-form-label">
          <?php echo JText::_('ADAG_AD_TEXT');?>
        </label>
        <div class="uk-form-controls">
          <textarea name="ad_text" id="ad_text" style="width:90%; height:150px;" onkeyup="javascript:cutText('ad_text', '<?php echo intval($content_limit); ?>');"><?php echo $_row->ad_text; ?></textarea>
          <?php
            $current_length = strlen($_row->ad_text);
            echo '<span id="content_left">'.(intval($content_limit) - intval($current_length)).'</span>&nbsp;'.JText::_("ADAG_CHARACTERS_LEFT");
          ?>
        </div>
      </div>
    </div>

    <!-- Preview -->
    <div class="ada-joms-preview">
      <h4 class="ada-joms-preview-title"><?php echo JText::_('ADAG_AD_PREVIEW'); ?></h4>

      <div class="ada-joms-preview-avatar">
        <?php
          $preview_path = "";

          if(!isset($_row->image_url) || trim($_row->image_url) == ""){
            $preview_path = "components/com_adagency/images/ad_avatar.png";
            }else{
            $preview_path = $lists['image_path'].$_row->image_url;
          }
        ?>
        <img src="<?php echo $preview_path; ?>" id="preview-image-image-url" alt="ad avatar image" title="ad avatar image">
      </div>

      <div class="ada-joms-preview-content">
        <h3 class="uk-h3" id="preview_ad_headline"><?php echo $_row->ad_headline; ?></h3>
        <span id="preview_promote_url" class="ada-joms-preview-link">
          <?php
            $temp = parse_url($_row->target_url);
            if(isset($temp["host"])){
              echo $temp["scheme"]."://".$temp["host"];
            }
          ?>
        </span>
  
        <p id="preview_ad_text"><?php echo $_row->ad_text; ?></p>

        <div id="preview_content_image">
          <?php
            $img_src = "";
            if(!isset($_row->image_content) || trim($_row->image_content) == ""){
              $img_src = "components/com_adagency/images/ad_content_image.png";
              }else{
              $img_src = $lists['image_path'].$_row->image_content;
            }
          ?>
          <img src="<?php echo $img_src; ?>" id="preview-image-image-content" alt="ad content image" title="ad content image">
          <div class="uk-float-left">
            <?php
                if($show_sponsored_stream_info == "1"){
                    echo JText::_("ADAG_SPONSORED_STREAM");
                }
            ?>
          </div>
          <div class="uk-float-right">
            <?php
                if($show_create_ad_link == "1"){
                    echo JText::_("ADAG_CREATE_AN_AD");
                }
            ?>
          </div>
        </div>
      </div>
    </div>

    <?php
      output_geoform($advertiser_id);
    ?>

    <div class="ada-joms-box ada-joms-target">
      <h4 class="ada-joms-box-title"><?php echo JText::_('ADAG_TARGET_AUDIENCE'); ?></h4>

      <div class="uk-grid uk-grid-fix">
        <?php
          if(isset($js_settings["target_audience_preview"]) && $js_settings["target_audience_preview"] == 1){
            $width = "uk-width-medium-2-3"; // if targetting is on
          } else {
            $width = ""; // off
          }
        ?>
        <div class="uk-width-1-1 <?php echo $width; ?>">

          <?php
            require_once(JPATH_BASE."/components/com_adagency/helpers/jomsocial_js_ad.php");
            $helper = new JomSocialTargetingJSAd();
            $helper->renderJsAd($_row->id, "");
            
            $audience = 0;
            $total = 0;
          ?>

        </div>
        
        <?php
          if(isset($js_settings["target_audience_preview"]) && $js_settings["target_audience_preview"] == 1){
        ?>
        <div class="uk-width-1-1 uk-width-medium-1-3">
          <fieldset>
            <legend><?php echo JText::_("ADAG_TARGET_AUDIENCE_PREVIEW"); ?></legend>
            <script language="javascript" type="text/javascript" src="<?php echo JURI::root()."administrator/components/com_adagency/js/jscharts.js"; ?>"></script>
            <div id="basicpiechart" class="ada-piechart">Loading...</div>
            <script type="text/javascript">
              var myData = new Array(['<?php echo JText::_("ADAG_AUDIENCE"); ?>', <?php echo intval($audience); ?>], ['', <?php echo intval($total); ?>]);
              var colors = ['#FB9900', '#FACC00'];
              var myChart = new JSChart('basicpiechart', 'pie', null);
              myChart.setDataArray(myData);
              myChart.colorizePie(colors);
              myChart.setTitleColor('#857D7D');
              myChart.setPieUnitsColor('#9B9B9B');
              myChart.setPieValuesColor('#6A0000');
              myChart.setTitle('');
              myChart.setSize(250, 250);
              myChart.draw();
            </script>
          </fieldset>
        </div>
        <?php } ?>
      </div>
    </div>

    <?php
      if(isset($camps)&&(count($camps)>0)){
      $i=0;
    ?>
    <div class="ada-joms-box ada-joms-target">
      <h4 class="ada-joms-box-title"><?php echo  JText::_('ADD_NEWADCMPS');?></h4>
      <table class="uk-table uk-table-striped uk-table-hover uk-table-condensed uk-margin-remove">
        <thead>
          <th><input type="checkbox" name="" value="" disabled /></th>
          <th><?php echo JText::_("CONFIGCMP"); ?></th>
        </thead>

        <tbody>
          <?php
            $nr_camps_available = 0;
            $displayed = array();
            foreach($camps as $camp){
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
              if(in_array($camp->id, $banners_camps)){
              echo 'checked="checked"';
              }
              ?> id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
            </td>
            <td class="uk-width-1-1 uk-text-primary">
              <?php echo $camp->name; ?>
            </td>
          </tr>
          <?php
            $nr_camps_available ++;
            }
          ?>
          <input type="hidden" name="nr_camps_available" id="nr_camps_available" value="<?php echo $nr_camps_available; ?>" />
        </tbody>
      </table>
    </div>

    <?php
    }

    if(isset($_row->image_url)){
      $url_to_camps = '<a target="_blank" href="' . JURI::root() . '/index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&Itemid=' . $item_id . '">' . JText::_('ADAG_HERE') . '</a>';
    }

    if(isset($displayed) && (count($displayed) == 0) && (isset($_row->image_url))){
      $url_to_camps = '<a target="_blank" href="' . JURI::root() . '/index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&Itemid=' . $item_id . '">' . JText::_('ADAG_HERE') . '</a>';
    }

    ?>

    <div id="ajax-result" style="display:none;"></div>

    <?php
      if(intval($_row->id) > 0){
    ?>

    <script type="text/javascript" language="javascript">
      changeTarget();
    </script>

    <?php
      }
    ?>

    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" id="task" value="save" />
    <input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
    <input type="hidden" name="media_type" value="Jomsocial" />
    <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
    <input type="hidden" name="controller" value="adagencyJomsocial" />
    <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />

    <div class="uk-form-row uk-margin-large-top">
      <input class="uk-button" type="button" onclick="<?php
        if(isset($_SERVER['HTTP_REFERER']) && (strpos(" ".$_SERVER['HTTP_REFERER'],"adagencyStandard")<1)) {
        $_SESSION['standardAdReferer'] = $_SERVER['HTTP_REFERER'];
      }
        elseif( !isset($_SERVER['HTTP_REFERER']) || !isset($_SESSION['standardAdReferer']) ){
        $_SESSION['standardAdReferer'] = '#';
      }
      echo "document.location = '".$_SESSION['standardAdReferer']."';";

      ?>" value="<?php echo JText::_('ADAG_BACK'); ?>" />
		
        <?php
        	if(is_array($camps) && count($camps) > 0){
		?>
                <div class="btn-group">
                    <a class="uk-button uk-button-success dropdown-toggle" data-toggle="dropdown" href="#">
                        <?php echo JText::_("ADAG_CONTINUE"); ?>
                        <span class="uk-icon-caret-down"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" onclick="Joomla.submitbutton('save');">
                              <?php echo JText::_("ADAG_SAVE_AND_SELECTED_CAMP");?>
                            </a>
                        </li>
                        <li>
                            <a href="#" onclick="Joomla.submitbutton('save_and_new_camp');">
                              <?php echo JText::_("ADAG_SAVE_AND_NEW_CAMP");?>
                            </a>
                        </li>
                    </ul>
                </div>
        <?php
        	}
			else{
		?>
        		<input class="uk-button uk-button-success" type="button" onclick="Joomla.submitbutton('save_and_new_camp');" value="<?php echo JText::_("AD_SAVE"); ?>" />
        <?php
			}
		?>
    </div>
  </form>
</div>
