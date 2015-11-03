<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2015 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('behavior.modal', 'a.modal2');

$db = JFactory::getDbo();
$sql = "select count(*) from #__ad_agency_advertis";
$db->setQuery($sql);
$db->query();
$count = $db->loadColumn();
$count = @$count["0"];

$advertiser = $this->advertiser;
if(!isset($advertiser->aid) || ($advertiser->aid <= 0)){
	if(intval($count) >= 2){
		echo '<div class="uk-alert">'.JText::_("COM_ADAGENCY_ONE_ADVERTISER").'</div>';
		return true;
	}
}

global $mainframe;
$my =  JFactory::getUser();
$document =  JFactory::getDocument();
$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
$charset = 'abcdefghijklmnopqrstuvxyz';
$code = '';
$code_length = 5;

for($i=0; $i < $code_length; $i++){
    $code = $code.substr($charset, mt_rand(0, strlen($charset) - 1), 1);
}
$item_id = $this->itemid;
if($item_id != 0){ 
  $Itemid = "&Itemid=".$item_id; 
} 
else{ 
  $Itemid = NULL; 
}
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0){ 
  $Itemid_cpn = "&Itemid=".intval($item_id_cpn); 
} 
else{ 
  $Itemid_cpn = NULL; 
}
$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
$lists = $this->lists;
$user = $this->user;
$configs = $this->conf;


$cid = JRequest::getInt('cid');
if($cid == NULL){
  $cid = 0;
}
$xx = rand(1,5); 
$yy = rand(1,5);
$configs->xx = $xx; 
$configs->yy = $yy;
$_SESSION['ADAG_CALC'] = NULL;
if($cid != $my->id && $my->id){
    $getUser= JRequest::getVar('user', NULL, 'get');
}
$getTask = JRequest::getVar('task', NULL, 'get');
$getStatus = JRequest::getVar('status', NULL, 'get');

if(isset($getUser)){
  $reg = $getUser;
}
else{
  $reg = "";
}
if(isset($getTask)){
  $editpf = $getTask;
}
else{
  $editpf = "";
}
if(isset($getStatus)){
  $statusadv = $getStatus;
}
else{
  $statusadv = "";
}
if($statusadv == "pending"){ 
  echo _JAS_LOGIN_FAILED_MSG ;
}
if($advertiser->aid == 0){
    if(isset($_SESSION['ad_company'])) $advertiser->company = $_SESSION['ad_company'];
    if(isset($_SESSION['ad_description'])) $advertiser->description = $_SESSION['ad_description'];
    if(isset($_SESSION['ad_approved'])) $advertiser->approved = $_SESSION['ad_approved'];
    if(isset($_SESSION['ad_enabled'])) $user->block = $_SESSION['ad_enabled'];
    if(isset($_SESSION['ad_username'])) $user->username = $_SESSION['ad_username'];
    if(isset($_SESSION['ad_email'])) $user->email = $_SESSION['ad_email'];
    if(isset($_SESSION['ad_name'])) $user->name = $_SESSION['ad_name'];
    if(isset($_SESSION['ad_website'])) $advertiser->website = $_SESSION['ad_website'];
    if(isset($_SESSION['ad_address'])) $advertiser->address = $_SESSION['ad_address'];
    if(isset($_SESSION['ad_city'])) $advertiser->city = $_SESSION['ad_city'];
    if(isset($_SESSION['ad_zip'])) $advertiser->zip = $_SESSION['ad_zip'];
    if(isset($_SESSION['ad_telephone'])) $advertiser->telephone = $_SESSION['ad_telephone'];
    if(isset($_SESSION['toagreecond'])) $advertiser->agreecond = 1;

    if($my->id>0){
        $user->email = $my->email;
        $user->username = $my->username;
        $user->name = $my->name;
    }
}
?>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/advertisers.php"); ?>

<!-- Register Container -->
<div class="ada-register">
  <?php
      if(isset($advertiser->aid) && ($advertiser->aid > 0)){
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
      <li class="uk-active"><a href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=<?php echo intval($my->id) . $Itemid_adv;?>"><?php echo JText::_('ADG_PROF'); ?></a></li>
      <li><a href="index.php?option=com_adagency&controller=adagencyAds<?php echo $Itemid_ads; ?>"><?php echo JText::_('ADG_ADS'); ?></a></li>
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
    <option selected="selected" value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid='.intval($my->id).''. $Itemid_adv);?>"><?php echo JText::_('ADG_PROF'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds'. $Itemid_ads); ?>"><?php echo JText::_('ADG_ADS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders'.$Itemid_ord); ?>"><?php echo JText::_('ADG_ORDERS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyReports'.$Itemid_rep); ?>"><?php echo JText::_('ADG_REPORTS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns'.$Itemid_cmp); ?>"><?php echo JText::_('ADG_CAMP'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyPackage'.$Itemid_pck); ?>"><?php echo JText::_('ADG_PACKAGES'); ?></option>                               
    <option value="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><?php echo JText::_('ADG_LOGOUT'); ?></option>
  </select>

  <?php
  }
  ?>

  <div class="ada-register-heading">
    <!-- Register title -->
    <h2 class="ada-register-title">
      <?php
        if($my->id > 0){ echo JText::_('VIEWADVERTISER_MY_PROFILE'); } 
        else{ echo JText::_('VIEWADVERTISER_ADVERTISE'); }
      ?>
    </h2>
    <h4 class="ada-register-subtitle">
      <?php 
        if($advertiser->aid == 0){ echo JText::_('VIEWADVERTISER_INTRO_TEXT'); }  
      ?>
    </h4>
  </div>
  <!-- Register form -->
  <form class="uk-form uk-form-horizontal" action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid='.intval($advertiser->user_id).$Itemid)?>" method="post" name="adminForm" id="adminForm">
    <fieldset>
      <!-- Basic info -->
      <legend><?php echo  JText::_('ADAG_BASIC_INFO');?></legend>
      <div class="uk-form-row">
        <label for="name" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERCONTACT');?>:
          <span class="uk-text-danger">*</span>
        </label>
        <div class="uk-form-controls">
          <input type="text" id="name" name="name" maxlength="50" value="<?php echo $user->name; ?>" />
        </div>
      </div>

      <div class="uk-form-row">
        <label for="email" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISEREMAIL');?>:
          <span class="uk-text-danger">*</span>
        </label>
        <div class="uk-form-controls">
          <input type="text" id="email" name="email" maxlength="100" <?php if (($advertiser->aid > 0)||($my->id>0)) echo ' readonly disabled ';?> value="<?php echo $user->email; ?>" />
        </div>
      </div>

      <?php if(isset($configs->show) && (in_array('phone', $configs->show))){ ?>
      <div class="uk-form-row">
        <label for="telephone" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERPHONE');?>:

          <?php if(isset($configs->mandatory) && (in_array('phone',$configs->mandatory))){ ?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <input type="text" id="telephone" name="telephone" maxlength="20" value="<?php echo $advertiser->telephone; ?>" />
        </div>
      </div>
      <?php } ?>

      <?php if(isset($configs->show) && (in_array('url', $configs->show))){ ?>
      <div class="uk-form-row">
        <label for="website" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERURL');?>:

          <?php if(isset($configs->mandatory) && (in_array('url',$configs->mandatory))){ ?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <input type="text" id="website" name="website" maxlength="255" value="<?php echo $advertiser->website?$advertiser->website:'http://'; ?>" />
        </div>
      </div>
      <?php } ?>
      
      <!-- Username -->
      <legend class="uk-margin-large-top"><?php echo  JText::_('ADAD_LOGIN_INFO');?></legend>
      <div class="uk-form-row">
        <label for="username" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERLOGIN').": ";?>

          <?php if($my->id<1) {?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <?php if((isset($advertiser->aid)&&($advertiser->aid>0))||(isset($my->id)&&($my->id>0))){ ?>
          <input type="text" placeholder="<?php echo $my->username; ?>" readonly disabled />
          <?php } else { ?>
          <input type="text" id="username" name="username" maxlength="25" value="<?php echo $user->username; ?>" />
          <?php } ?>
        </div>
      </div>

      <?php if(!isset($my->id)||($my->id <= 0)){ ?>
      <!-- New Password -->
      <div class="uk-form-row">
        <label for="newpswd" class="uk-form-label">
          <?php echo JText::_('ADAG_NEWPASS');?>:
          <span class="uk-text-danger">*</span>
        </label>
        <div class="uk-form-controls">
          <input id="newpswd" type="password" name="password" maxlength="100" value="" />
        </div>
      </div>

      <div class="uk-form-row">
        <label for="newpswd2" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERPASS2');?>:
          <span class="uk-text-danger">*</span>
        </label>
        <div class="uk-form-controls">
          <input id="newpswd2" type="password" name="password2" maxlength="100" value="" />
        </div>
      </div>
      <?php } ?>

      <?php if(isset($configs->show) && (in_array('company', $configs->show))){ ?>
      <!-- Advertiser Info -->
      <legend class="uk-margin-large-top"><?php echo  JText::_('VIEWADVERTISERINFO');?></legend>
      <div class="uk-form-row">
        <label for="company" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERCOMPNAME');?>:

          <?php if(isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) { ?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <input type="text" id="company" name="company" maxlength="100" value="<?php echo $advertiser->company; ?>" />
        </div>
      </div>

      <div class="uk-form-row">
        <label for="description" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERDESC');?>:

          <?php if(isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) { ?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <textarea NAME="description" ROWS="3" COLS="50"><?php echo $advertiser->description;?></textarea>
        </div>
      </div>
      <?php } ?>

      <?php if(isset($configs->show) && (in_array('address', $configs->show))){ ?>
      <!-- Advertiser Info -->
      <legend class="uk-margin-large-top"><?php echo  JText::_('VIEWADVERTISERINFO3');?></legend>

      <?php include(JPATH_BASE."/components/com_adagency/includes/js/advertisers_country.php"); ?>

      <div class="uk-form-row">
        <label for="country" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERCOUNTRY');?>:

          <?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <?php echo $this->lists['country_option']; ?>
        </div>
      </div>

      <div class="uk-form-row">
        <label for="customerlocation" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERPROV');?>:

          <?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <?php echo $this->lists['customerlocation'];?>
        </div>
      </div>

      <div class="uk-form-row">
        <label for="city" class="uk-form-label">
          <?php echo JText::_('ADAG_CITY');?>:

          <?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <input type="text" id="city" name="city" maxlength="100" value="<?php echo $advertiser->city; ?>" />
        </div>
      </div>

      <div class="uk-form-row">
        <label for="address" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERADDRESS');?>:

          <?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <input type="text" id="address" name="address" maxlength="100" value="<?php echo $advertiser->address; ?>" />
        </div>
      </div>

      <div class="uk-form-row">
        <label for="zip" class="uk-form-label">
          <?php echo JText::_('VIEWADVERTISERZIP');?>:

          <?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>
          <span class="uk-text-danger">*</span>
          <?php } ?>
        </label>
        <div class="uk-form-controls">
          <input type="text" id="zip" name="zip" maxlength="12" value="<?php echo $advertiser->zip; ?>" />
        </div>
      </div>
      <?php } ?>

      <?php if(isset($configs->show) && (in_array('email', $configs->show))){ ?>
      <!-- Email Reports -->
      <legend class="uk-margin-large-top"><?php echo  JText::_('VIEWADVERTISEREMAILOPT');?></legend>
      <div class="uk-form-row">
        <label for="" class="uk-form-label"></label>
        <div class="uk-form-controls">
          <label><input type="checkbox" <?php if ($advertiser->email_daily_report=='Y') echo 'checked' ?> NAME="email_daily_report" value="Y"> <?php echo JText::_('VIEWADVERTISERDAY'); ?></label>
        </div>
        <div class="uk-form-controls">
          <label><input type="checkbox" <?php if ($advertiser->email_weekly_report=='Y') echo 'checked' ?> NAME="email_weekly_report" value="Y"> <?php echo JText::_('VIEWADVERTISERWEEK'); ?></label>
        </div>
        <div class="uk-form-controls">
          <label><input type="checkbox" <?php if ($advertiser->email_month_report=='Y') echo 'checked' ?> NAME="email_month_report" value="Y"> <?php echo JText::_('VIEWADVERTISERMONTH'); ?></label>
        </div>
        <div class="uk-form-controls">
          <label><input type="checkbox" <?php if ($advertiser->email_campaign_expiration=='Y') echo 'checked' ?> NAME="email_campaign_expiration" value="Y"> <?php echo JText::_('VIEWADVERTISEREXP'); ?></label>
        </div>
      </div>
      <?php } ?>

	<?php
		if ($advertiser->aid == 0){
	?>
		<?php
        	if(isset($configs->show)&&(in_array('captcha',$configs->show))){
				$plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
				$params = new JRegistry($plugin->params);
				$public_key = $params->get('public_key','');
		?>
				<div class="uk-form-row">
					<label for="" class="uk-form-label"></label>
        			<div class="uk-form-controls">
						<script src='https://www.google.com/recaptcha/api.js'></script>
						<div class="g-recaptcha" data-sitekey="<?php echo $public_key; ?>"></div>
					</div>
				</div>
	<?php
			}
		}
	?>

      <?php if (($configs->askterms == '1') && (!isset($advertiser->aid) || ($advertiser->aid < 1))) { ?>
      <div class="uk-form-row">
        <label for="" class="uk-form-label"></label>
        <div class="uk-form-controls">
          <label>
            <input type="checkbox" name="agreeterms" <?php if(isset($advertiser->agreecond)) { echo " checked='checked' ";}?>/> 
            <input type="hidden" name="checkagreeterms" value="1" />
            <a class="modal2" href="index.php?option=com_content&view=article&id=<?php echo intval($configs->termsid);?>&tmpl=component"><?php echo JText::_("VIEWADVERTISERAGREETERMS");?></a>
          </label>
        </div>
      </div>
      <?php } else { ?>
      <div class="uk-form-row">
        <div class="uk-form-controls">
          <input type="hidden" name="checkagreeterms" value="0" />
        </div>
      </div>
      <?php } ?>

      <div class="uk-form-row uk-margin-large-top">
        <label for="" class="uk-form-label"></label>
        <div class="uk-form-controls">
          <input type="button" class="uk-button" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_CANCEL'); ?>" />
          <input class="uk-button uk-button-primary" type="button" onclick="Joomla.submitbutton('save')" value="<?php if ($advertiser->aid==0) { echo JText::_("ADAG_CREATE_ACCOUNT"); } else { echo JText::_("AD_SAVE"); } echo " >"; ?>">
        </div>
      </div>
    </fieldset>

    <?php if($my->id == 0) { echo JHTML::_( 'form.token' ); } ?>
    <input name="is_already_registered" id="is_already_registered" value="<?php echo $my->id;?>" type="hidden" />
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="aid" value="<?php echo $advertiser->aid; ?>" />
    <input type="hidden" name="block" value="<?php echo $my->block; ?>" />
    <input type="hidden" name="user_id" value="<?php echo $advertiser->user_id; ?>" />
    <input type="hidden" name="lastreport" value="<?php if (isset($advertiser->lastreport)) echo $advertiser->lastreport; else echo time(); ?>" />
    <input type="hidden" name="weekreport" value="<?php if (isset($advertiser->weekreport)) echo $advertiser->weekreport; else echo time(); ?>" />
    <input type="hidden" name="monthreport" value="<?php if (isset($advertiser->monthreport)) echo $advertiser->monthreport; else echo time(); ?>" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="controller" value="adagencyAdvertisers" />
  </form>
</div>
