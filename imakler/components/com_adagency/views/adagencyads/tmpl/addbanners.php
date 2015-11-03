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
  $iconsaddr = JURI::base()."components/com_adagency/images/";
  JHtml::_('behavior.framework',true);
  
  $document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js" );
  $document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
  require_once(JPATH_BASE . "/components/com_adagency/includes/js/ads.php");
  $type = $this->type;
?>

<!-- Add Ads Container -->
<div class="ada-add__ads">
  <?php
    if(isset($this->wiz)) {
        echo "<div class='uk-alert'>".JText::_('ADAG_PENDING_ADS2')."</div>";
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
      <h2 class="ada-ads-title"><?php echo JText::_('ADAG_ADDNEWAD'); ?></h2>
      <h4 class="ada-ads-subtitle">
        <?php echo JText::_('ADAG_SELADTYPE'); ?>
      </h4>
      <div class="ada-ads-options">
        <?php
          if($type == 'banner'){
            echo "<a class='uk-button uk-button-primary uk-margin-top' href='index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid."'> << ".JText::_('ADAG_BKTOADS')."</a>";
          }
        ?>
      </div>
  </div>

  <form class="form-horizontal clearfix adg_row" method="post" name="adminForm" id="adminForm">
    <ul class="uk-grid uk-grid-width-medium-1-2 uk-grid-fix">
    
	<?php
      if($type == 'banner'){
    ?>

    <?php if($configs->allowstand) {
          $link = JRoute::_('index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]=0'.$Itemid);
          //
          // STANDARD BANNER
    ?>

      <li>
        <div class="ada-ads-box">
          <div class="ada-ads-box-image">
            <a href="<?php echo $link; ?>"><span class="fa fa-picture-o"></span></a>
          </div>
          <div class="ada-ads-box-body">
            <h3 class="ada-ads-box-title"><a href="<?php echo $link; ?>"><?php echo JText::_('JAS_STANDART'); ?></a></h3>
            <div class="ada-ads-box-desc"><?php echo JText::_('ADAG_DSC_STANDARD'); ?></div>
          </div>
        </div>
      </li>

    <?php
        } // end standard
    ?>

    <?php if($configs->allowswf) {
          $link = JRoute::_('index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]=0'.$Itemid);
          //
          // FLASH BANNER
    ?>

      <li>
        <div class="ada-ads-box">
          <div class="ada-ads-box-image">
            <a href="<?php echo $link; ?>"><span class="fa fa-flash"></span></a>
          </div>
          <div class="ada-ads-box-body">
            <h3 class="ada-ads-box-title"><a href="<?php echo $link; ?>"><?php echo JText::_('JAS_FLASH'); ?></a></h3>
            <div class="ada-ads-box-desc"><?php echo JText::_('ADAG_DSC_FLASH'); ?></div>
          </div>
        </div>
      </li>

    <?php
        } // end flash
    ?>

    <?php if($configs->allowadcode) {
          $link = JRoute::_('index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=0'.$Itemid);
          //
          // AFFILIATE BANNER
    ?>

      <li>
        <div class="ada-ads-box">
          <div class="ada-ads-box-image">
            <a href="<?php echo $link; ?>"><span class="fa fa-building-o"></span></a>
          </div>
          <div class="ada-ads-box-body">
            <h3 class="ada-ads-box-title"><a href="<?php echo $link; ?>"><?php echo JText::_('JAS_AFFILIATE'); ?></a></h3>
            <div class="ada-ads-box-desc"><?php echo JText::_('ADAG_DSC_ADV'); ?></div>
          </div>
        </div>
      </li>

    <?php
        } // end affiliate

      } else {
    ?>
	
    <?php
    	include_once(JPATH_SITE.DS."components".DS."com_adagency".DS."models".DS."adagencyconfig.php");
		$ad_configs = new adagencyModeladagencyConfig();
		$is_js_installed = $ad_configs->isJomSocialStreamAd();

		if($is_js_installed && $configs->allowsocialstream) {
          $link = JRoute::_('index.php?option=com_adagency&controller=adagencyJomsocial&task=edit&cid[]=0'.$Itemid);
          //
          // JS STREAM
    ?>

      <li>
        <div class="ada-ads-box">
          <div class="ada-ads-box-image">
            <a href="#" onclick="alert('<?php echo JText::_("COM_ADAGENCY_JS_STREAM_MSG"); ?>');"><span class="fa fa-bullhorn"></span></a>
          </div>
          <div class="ada-ads-box-body">
            <h3 class="ada-ads-box-title"><a href="#" onclick="alert('<?php echo JText::_("COM_ADAGENCY_JS_STREAM_MSG"); ?>');"><?php echo JText::_('JAS_JOMSOCIAL'); ?></a></h3>
            <div class="ada-ads-box-desc"><?php echo JText::_('ADAG_DSC_STREAM_JS'); ?></div>
          </div>
        </div>
      </li>

    <?php
        } // end js stream
    ?>
    
    <?php if(($configs->allowstand)||($configs->allowswf)||($configs->allowadcode)) {
          $link = JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=addbanners&type=banner'.$Itemid);
          //
          // BANNER
    ?>

      <li>
        <div class="ada-ads-box">
          <div class="ada-ads-box-image">
            <a href="<?php echo $link; ?>"><span class="fa fa-desktop"></span></a>
          </div>
          <div class="ada-ads-box-body">
            <h3 class="ada-ads-box-title"><a href="<?php echo $link; ?>"><?php echo JText::_('ADAG_BANNERSFA'); ?></a></h3>
            <div class="ada-ads-box-desc"><?php echo JText::_('ADAG_DSC_BANNER'); ?></div>
          </div>
        </div>
      </li>

    <?php
        } // end banner
    ?>

    <?php if($configs->allowpopup) {
          $link = JRoute::_('index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid[]=0'.$Itemid);
          //
          // POPUP
    ?>

      <li>
        <div class="ada-ads-box">
          <div class="ada-ads-box-image">
            <a href="<?php echo $link; ?>"><span class="fa fa-square"></span></a>
          </div>
          <div class="ada-ads-box-body">
            <h3 class="ada-ads-box-title"><a href="<?php echo $link; ?>"><?php echo JText::_('JAS_POPUP'); ?></a></h3>
            <div class="ada-ads-box-desc"><?php echo JText::_('ADAG_DSC_POPUP'); ?></div>
          </div>
        </div>
      </li>

    <?php
        } // end popup
    ?>

    <?php if($configs->allowtxtlink) {
          $link = JRoute::_('index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=0'.$Itemid);
          //
          // TEXTLINK
    ?>

      <li>
        <div class="ada-ads-box">
          <div class="ada-ads-box-image">
            <a href="<?php echo $link; ?>"><span class="fa fa-font"></span></a>
          </div>
          <div class="ada-ads-box-body">
            <h3 class="ada-ads-box-title"><a href="<?php echo $link; ?>"><?php echo JText::_('JAS_TEXT_LINK'); ?></a></h3>
            <div class="ada-ads-box-desc"><?php echo JText::_('ADAG_DSC_TEXTAD'); ?></div>
          </div>
        </div>
      </li>

    <?php
        } // end textlink
    ?>

    <?php if($configs->allowfloat) {
          $link = JRoute::_('index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid[]=0'.$Itemid);
          //
          // FLOAT
    ?>

      <li>
        <div class="ada-ads-box">
          <div class="ada-ads-box-image">
            <a href="<?php echo $link; ?>"><span class="fa fa-chain-broken"></span></a>
          </div>
          <div class="ada-ads-box-body">
            <h3 class="ada-ads-box-title"><a href="<?php echo $link; ?>"><?php echo JText::_('JAS_FLOATING'); ?></a></h3>
            <div class="ada-ads-box-desc"><?php echo JText::_('ADAG_DSC_FLOATING'); ?></div>
          </div>
        </div>
      </li>

    <?php
        } // end float
    ?>

    <?php if($configs->allowtrans) {
          $link = JRoute::_('index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid[]=0'.$Itemid);
          //
          // TRANSITION
    ?>

      <li>
        <div class="ada-ads-box">
          <div class="ada-ads-box-image">
            <a href="<?php echo $link; ?>"><span class="fa fa-arrows-h"></span></a>
          </div>
          <div class="ada-ads-box-body">
            <h3 class="ada-ads-box-title"><a href="<?php echo $link; ?>"><?php echo JText::_('JAS_TRANSITION'); ?></a></h3>
            <div class="ada-ads-box-desc"><?php echo JText::_('ADAG_DSC_TRANSITION'); ?></div>
          </div>
        </div>
      </li>

    <?php
        } // end transition
    ?>

    <?php
      }
    ?>
    </ul>

    <div class="uk-form-row">
      <input type="button" class="uk-button" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_BACK'); ?>" />
    </div>
  </form>
</div>
