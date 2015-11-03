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

$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=" . intval($item_id); } else { $Itemid = NULL; }
if($this->itemid_camp != 0) { $Itemid2 = "&Itemid=" . intval($this->itemid_camp); } else { $Itemid2 = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".intval($item_id_cpn); } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

$k = 0;
$n = count ($this->ads);
$configs = $this->configs;
$imgfolder = $this->imgfolder;
$advertiser_id = $this->advertiser_id;
JHTML::_('behavior.combobox');
// JHTML::_('behavior.mootools');
$root = JURI::root();
JHtml::_('behavior.multiselect');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_adagency/includes/css/featherlight.min.css');
$document->addScript( JURI::root()."components/com_adagency/includes/js/featherlight.min.js" );
$helper = new adagencyModeladagencyAds();
?>


<!-- Ads Container -->
<div class="ada-ads">

<!-- Alert Messages -->
<?php if(isset($_GET['p'])&&(intval($_GET['p'])==1)) { ?>
  <div class="uk-alert"><?php echo JText::_('ADAG_PENDING_ADS');?></div>
<?php } elseif(isset($_GET['w'])&&(intval($_GET['w'])==1)) {
  echo "<div class='uk-alert'>".JText::_('ADAG_PENDING_ADS2')."</div>";
} ?>

  <?php if(isset($advertiser_id)&&($advertiser_id > 0)) { 
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

  <?php } ?>

  <div class="ada-ads-heading">
    <h2 class="ada-ads-title"><?php echo JText::_('VIEWAD_ADS'); ?></h2>
    <h4 class="ada-ads-subtitle"><?php echo JText::_('VIEWAD_INTRO_TEXT2'); ?></h4>
  </div>

	<script type="text/javascript">
		function redirect(){
			if(eval(document.getElementById("cb0")) && eval(document.getElementById("cb1")) && eval(document.getElementById("cb2")) && eval(document.getElementById("cb3")) && eval(document.getElementById("cb4"))){
				alert("<?php echo JText::_("COM_ADAGENCY_ONE_ADS"); ?>");
				return false;
			}
			document.location.href="<?php echo JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid;?>";
		}
	</script>

  <form class="uk-form" action="<?php JRoute::_('index.php?option=com_adagency&controller=adagencyAds'.$Itemid);?>" name="adminForm" id="adminForm" method="post">
    <fieldset>
      <div class="uk-grid">

        <div class="uk-width-medium-1-2">
          <input type="button" class="uk-button uk-button-success" onClick="javascript:redirect()" value="<?php echo JText::_('ADAG_ADDBANNER');?>" />
          <input type="button" class="uk-button uk-button-primary" onClick="document.location.href='<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0' . $Itemid2); ?>'" value="<?php echo JText::_('AD_CR_CAMP');?>" />
        </div>

        <div class="uk-width-medium-1-2 uk-text-right">
          <?php $search_text = JRequest::getVar("search_text", ""); ?>

          <input type="text" value="<?php echo $search_text; ?>" name="search_text" id="filter_search" class="uk-margin-remove">
          <button title="Search" type="submit" class="uk-button"><i class="icon-search"></i></button>
          <button title="Clear" onclick="document.id('filter_search').value='';this.form.submit();" type="button" class="uk-button"><i class="icon-remove"></i></button>  
        </div>

        <div class="uk-width-1-1 uk-margin">
          <?php $type_select = JRequest::getVar("type_select", "all"); ?>

          <select name="type_select" class="uk-width-1-1" onChange="document.adminForm.submit()">
            <option value="all" <?php if($type_select == "all"){ echo 'selected="selected"';}?>><?php echo JText::_("ZONEPAGEALL"); ?></option>
            <option value="Standard" <?php if($type_select == "Standard"){ echo 'selected="selected"';}?>><?php echo JText::_('ADAG_STANDARD');?></option>
            <option value="Advanced" <?php if($type_select == "Advanced"){ echo 'selected="selected"';}?>><?php echo JText::_('JAS_ADVANCED');?></option>
            <option value="Popup" <?php if($type_select == "Popup"){ echo 'selected="selected"';}?>><?php echo JText::_('ADAG_POP_UP');?></option>
            <option value="Flash" <?php if($type_select == "Flash"){ echo 'selected="selected"';}?>><?php echo JText::_('ADAG_FLASH');?></option>
            <option value="TextLink" <?php if($type_select == "TextLink"){ echo 'selected="selected"';}?>><?php echo JText::_('ADAG_TEXTAD');?></option>
            <option value="Transition" <?php if($type_select == "Transition"){ echo 'selected="selected"';}?>><?php echo JText::_('ADAG_TRANSITION');?></option>
            <option value="Floating" <?php if($type_select == "Floating"){ echo 'selected="selected"';}?>><?php echo JText::_('ADAG_FLOATING');?></option>
          </select>
        </div>

      </div>

      <!-- Ads table -->
      <?php if ($n > 0) {
      include(JPATH_BASE.DS."components".DS."com_adagency".DS."includes".DS."js".DS."ads_del_ad.php"); ?>

      <table class="uk-table uk-table-striped">
        <thead>
          <tr>
            <th><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" /></th>
            <th class="uk-visible-large"><?php echo JText::_('ID'); ?></th>
            <th><?php echo JText::_('VIEWADTITLE');?></th>
            <th class="uk-visible-large"><?php echo JText::_("VIEWADTYPE");?></th>
            <th class="uk-visible-large"><?php echo JText::_('VIEWADSIZE');?></th>
            <th class="uk-visible-large"><?php echo JText::_('VIEWADCAMPAIGNS');?></th>
            <th><?php echo JText::_('VIEWADSTATUS');?></th>
            <th><?php echo JText::_('VIEWADPREVIEW');?></th>
          </tr>
        </thead>

        <tbody>
          <?php
            for($i = 0; $i < $n; $i++){
              $ads = $this->ads[$i];
              $ads->parameters = unserialize($ads->parameters);
              $approved = $ads->approved;
              if($approved == 'Y') { $color = "green"; $alt = JText::_("NEWADAPPROVED"); } elseif ($approved == 'P') { $color = "orange"; $alt = JText::_("ADAG_PENDING"); } else { $color  = 'red'; $alt = JText::_("ADAG_REJECTED");}
              if (!isset($ads->parameters['align'])) $ads->parameters['align']='';
              if (!isset($ads->parameters['valign'])) $ads->parameters['valign']='';
              if (!isset($ads->parameters['padding'])) $ads->parameters['padding']='';
              if (!isset($ads->parameters['border'])) $ads->parameters['border']='';
              if (!isset($ads->parameters['bg_color'])) $ads->parameters['bg_color']='';
              if (!isset($ads->parameters['border_color'])) $ads->parameters['border_color']='';
              if (!isset($ads->parameters['font_family'])) $ads->parameters['font_family']='';
              if (!isset($ads->parameters['font_size'])) $ads->parameters['font_size']='';
              if (!isset($ads->parameters['font_weight'])) $ads->parameters['font_weight']='';
              $id = $ads->id;
              $checked = JHTML::_('grid.id', $i, $id);
              $mediatype = $ads->media_type;
              switch ($mediatype) {
                case 'Advanced':
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid=".intval($id).$Itemid);
                break;
                case 'Standard':
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid=".intval($id).$Itemid);
                break;
                case 'Flash':
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid=".intval($id).$Itemid);
                break;
                case 'Transition':
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid=".intval($id).$Itemid);
                break;
                case 'Floating':
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid=".intval($id).$Itemid);
                break;
                case 'Popup':
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid=".intval($id).$Itemid);
                break;
                case 'TextLink':
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid=".intval($id).$Itemid);
                break;
                case 'Jomsocial':
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyJomsocial&task=edit&cid=".intval($id).$Itemid);
                break;
              }
          
              $zonelink = JRoute::_("index.php?option=com_adagency&controller=adagencyZones&task=edit&cid=".intval($ads->zone.$Itemid));
              $customerlink = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=".intval($ads->advertiser_id2.$Itemid));
          
              if (!isset($ads->impressions)) $ads->impressions=0;
              if (!isset($ads->click)) $ads->click=0;
              if (!isset($ads->click_rate)) $ads->click_rate=0;
          ?>
          <tr>
            <td><?php echo JHtml::_('grid.id', $i, $ads->id); ?></td>
            <td class="uk-visible-large"><?php echo $id; ?></td>
            <td><a href="<?php echo $link;?>" ><?php echo $ads->title;?></a></td>
            <td class="uk-visible-large">
              <?php 
                $adtype = strtoupper($ads->media_type);
                if($adtype == 'STANDARD'){
                  echo JText::_("ADAG_STANDARD");
                }
                elseif($adtype == 'TEXTLINK'){
                  echo JText::_("ADAG_TEXTAD");
                }
                else{
                  echo JText::_('JAS_'.strtoupper($ads->media_type)); 
                }
              ?>
            </td>
            <td class="uk-visible-large">
              <?php
                if(!$ads->width || !$ads->height){
                  echo "-";
                }
                elseif($ads->media_type != "TextLink"){
                  echo "{$ads->width}x{$ads->height}";
                }
                else{
                  echo "-";
                }
              ?>
            </td>
            <td class="uk-visible-large">
              <?php
                $cmp_count = $helper->getCampaignCount($ads->id);
                if($cmp_count == 0){
                  echo "<span class='uk-text-danger'>".$cmp_count."</span>";
                }
                else{
                  echo "<span class='uk-text-primary'>".$cmp_count."</span>";
                }
              ?>
            </td>
            <td><span class="uk-text-success"><?php echo $alt; ?></span></td>
            <td>
              <?php
                if($ads->media_type == "Popup" && $ads->parameters['popup_type']=="webpage"){
              ?>
                  <a href='<?php echo $ads->parameters['page_url']; ?>' data-featherlight="ajax"><i class="uk-icon-eye"></i></a>
              <?php
                }
                else{
                  if(($ads->media_type == "Transition") || $ads->media_type == "Floating" || $ads->media_type == "TextLink" || $ads->media_type == "Standard" || $ads->media_type == "Advanced" || $ads->media_type == "Flash" || $ads->media_type == "Popup" || $ads->media_type == "Jomsocial") {
              ?>
                    <a href='<?php echo "index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&adid=".$ads->id.$Itemid; ?>' data-featherlight="ajax"><i class="uk-icon-eye"></i></a>
              <?php
                  }
                }
              ?>
            </td>
          </tr>
          <?php $k = 1 - $k; } ?>
          <?php } // close ?>
        </tbody>
      </table>

      <div class="uk-form-row">
        <input type="button" onclick="javascript:Change(0);" class="uk-button" value="<?php echo JText::_("ADAG_DELETE"); ?>" />
      </div>
    </fieldset>

    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="sid" value="" />
    <input type="hidden" name="controller" value="adagencyAds" />
    <input type="hidden" name="boxchecked" id="boxchecked" value="0" />
  </form>
</div>
