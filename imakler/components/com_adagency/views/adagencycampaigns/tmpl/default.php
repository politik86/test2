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

JHtml::_('behavior.multiselect');

  $k = 0;
  $n = count ($this->camps);
  $task = $this->task2;
  $nrads = $this->nrads;
  $camps = $this->camps;

  $rezultat = $this->rezultat;
  $params = $this->params;
  $configs = $this->configs;
  $helper = new adagencyAdminHelper();
  $advertiser = $this->advertiser;

  $item_id = $this->itemid;
    $item_id2 = $this->itemid_ads;
    $item_id3 = $this->itemid_pkg;
  if($item_id != 0) { $Itemid = "&Itemid=" . intval($item_id); } else { $Itemid = NULL; }
    if($item_id2 != 0) { $Itemid_ads = "&Itemid=" . intval($item_id2); } else { $Itemid_ads = NULL; }
    if($item_id3 != 0) { $Itemid_pkg = "&Itemid=" . intval($item_id3); } else { $Itemid_pkg = NULL; }
    $item_id_cpn = $this->itemid_cpn;
    if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".intval($item_id_cpn); } else { $Itemid_cpn = NULL; }

  
  require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
  $document = JFactory::getDocument();
  
  JHtml::_('behavior.framework',true);
  
  $document->addScript(JURI::root()."components/com_adagency/includes/js/uikit.min.js");
  $document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
    $cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
  $document->addScriptDeclaration('
    ADAG(function(){
      ADAG(\'.cpanelimg\').click(function(){
        document.location = "' . $cpn_link . '";
      });
    });');

?>

<!-- Campaigns Container -->
<div class="ada-campaigns">
  <?php   if(isset($advertiser->aid)&&($advertiser->aid > 0)){ 
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
    <option value="<?php echo $cpn_link;?>"><i class="fa fa-home"></i><?php echo JText::_('ADG_DASH'); ?></a></li>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid='.intval($my->id).''. $Itemid_adv);?>"><?php echo JText::_('ADG_PROF'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds'. $Itemid_ads); ?>"><?php echo JText::_('ADG_ADS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders'.$Itemid_ord); ?>"><?php echo JText::_('ADG_ORDERS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyReports'.$Itemid_rep); ?>"><?php echo JText::_('ADG_REPORTS'); ?></option>
    <option selected="selected" value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns'.$Itemid_cmp); ?>"><?php echo JText::_('ADG_CAMP'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyPackage'.$Itemid_pck); ?>"><?php echo JText::_('ADG_PACKAGES'); ?></option>                               
    <option value="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><?php echo JText::_('ADG_LOGOUT'); ?></option>
  </select>

  <?php } ?>

  <!-- Alert -->
  <?php if(isset($_SESSION['cmp_pending_to_approved'])&&($_SESSION['cmp_pending_to_approved']=='N')) {
    echo "<div class='uk-alert'>".JText::_('ADAG_ORPEN')."</div>";
    unset($_SESSION['cmp_pending_to_approved']);
  }  ?>

  <div class="ada-campaigns-heading">
    <h2 class="ada-campaigns-title"><?php echo JText::_('VIEW_CAMPAIGN_CMP'); ?></h2>
    <h4 class="ada-campaigns-subtitle"><?php echo JText::_('ADAG_CAMP_INTROTEXT'). ' ';?></h4>

    <?php if ($task=="complete") { ?>
    <?php echo "<strong>".JText::_('AD_PAYSUCCESSFUL')."</strong>";?><br /><br />
    <?php } /*else if ($task=="complete" && !$nrads){ ?>
    <?php echo "<strong>".JText::_('AD_PAYSUCCESSFUL_ADD')."</strong>"; ?><br /><br /><?php }*/ ?>
    <?php  if ($task=="failed") { ?>
    <?php echo JText::_('AD_PAY_ERROR');?><br /><br />
    <?php } ?>
  </div>

	<script type="text/javascript" language="javascript">
		function checkNrCampaigns(){
			if(eval(document.getElementById("cb0"))){
				alert("<?php echo addslashes(JText::_("COM_ADAGENCY_ONE_CAMPAIGN")); ?>");
				return false;
			}
			return true;
		}
	</script>

  <div class="ada-campaigns-actions">
    <form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds' . $Itemid_ads)?>" method="POST">
        <input class="uk-button uk-button-success" type="submit" value="<?php echo JText::_('VIEW_CAMPAIGN_ADD_ADS');?>"/>
    </form>
    <form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0' . $Itemid)?>" method="POST" onsubmit="return checkNrCampaigns()">
        <input class="uk-button uk-button-primary" type="submit" value="<?php echo JText::_('VIEW_CAMPAIGN_CREATE_CMP');?>"/>
    </form>
  </div>

  <form class="uk-form" method="post" name="adminForm" id="adminForm" class="uk-form">
    <?php
        $status_filter = JRequest::getVar("status_filter", "-1");
        $payment_filter = JRequest::getVar("payment_filter", "-1");
        $approval_filter = JRequest::getVar("approval_filter", "-1");
    ?>

    <script language="javascript" type="application/javascript">
        function refreshPageForFilters(value){
            status_filter = document.getElementById("status_filter").value;
            payment_filter = document.getElementById("payment_filter").value;
            approval_filter = document.getElementById("approval_filter").value;
            window.location = '<?php echo JURI::root(); ?>index.php?option=com_adagency&view=adagencycampaigns&Itemid=<?php echo $item_id; ?>&status_filter='+status_filter+"&payment_filter="+payment_filter+"&approval_filter="+approval_filter;
        }
        
        function deleteCampaigns(action){
            if(confirm("<?php echo JText::_("ADAG_SURE_DELETE_CAMPAIGNS"); ?>")){
                if(action == 'remove'){
                    document.adminForm.remove_action.value='remove';
                }
                document.adminForm.task.value='remove';
                document.adminForm.submit();
            }
        }
        
        function renewCampaign(id, name, orderid, campaign_id){
            document.adminForm.task.value = 'edit';//'checkout';
            document.adminForm.otid.value = id;
            document.adminForm.tid.value = id;
            document.adminForm.name.value = name;
            //document.adminForm.controller.value = 'adagencyOrders';
            document.adminForm.remove_action.value='renew';
            document.adminForm.orderid.value=orderid;
            document.adminForm.campaign_id.value=campaign_id;
            
            //document.adminForm.action ="index.php?option=com_adagency&controller=adagencyOrders&task=order&tid="+id;
            document.adminForm.action = "index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0&Itemid=<?php echo $item_id; ?>"
            document.adminForm.submit();
        }
    </script>

    <div class="ada-campaigns-filters">
      <ul class="uk-grid uk-grid-small uk-grid-fix uk-grid-width-medium-1-3">
        <li>
          <select class="uk-width-1-1" name="status_filter" id="status_filter" onchange="refreshPageForFilters();">
            <option value="-1" <?php if($status_filter == -1){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEWADSTATUS"); ?></option>
            <option value="1" <?php if($status_filter == 1){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_ACTIVE"); ?></option>
            <option value="0" <?php if($status_filter == 0){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_INACTIVE"); ?></option>
            <option value="2" <?php if($status_filter == 2){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_EXPIRED"); ?></option>
          </select>
        </li>
        <li>
          <select class="uk-width-1-1" name="payment_filter" id="payment_filter" onchange="refreshPageForFilters();">
            <option value="-1" <?php if($payment_filter == -1){ echo 'selected="selected"'; } ?>><?php echo JText::_("BUY_PACKPAYMENT"); ?></option>
            <option value="0" <?php if($payment_filter == 0){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEWORDERSPAID"); ?></option>
            <option value="1" <?php if($payment_filter == 1){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEWORDERSUNPAID"); ?></option>
          </select>
        </li>
        <li>
          <select class="uk-width-1-1" name="approval_filter" id="approval_filter" onchange="refreshPageForFilters();">
            <option value="-1" <?php if($approval_filter == -1){ echo 'selected="selected"'; } ?>><?php echo JText::_("ADAG_APPROVAL"); ?></option>
            <option value="Y" <?php if($approval_filter == "Y"){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_APPROVED"); ?></option>
            <option value="P" <?php if($approval_filter == "P"){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_PENDING"); ?></option>
            <option value="N" <?php if($approval_filter == "N"){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_REJECTED"); ?></option>
          </select>
        </li>
      </ul>
    </div>

    <!-- Table -->
    <table class="uk-table uk-table-striped ada-campaigns-table" id="adg_campaing_list_table">
      <script language="javascript" type="text/javascript">
          function campaignDetails(campaign_id){
              ADAG.ajax({
                  type: "GET",
                  url: "<?php echo JURI::root(); ?>index.php?option=com_adagency&controller=adagencyCampaigns&task=details&cid="+campaign_id+"&format=raw&tmpl=component",
                  data: "",
                  success: function(response){
                      document.getElementById("modal-body").innerHTML = response;
                  }
              });
          }
      </script>

      <thead>
        <tr>
          <th><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" /></th>
          <th><?php echo JText::_('ID'); ?></th>
          <th><?php echo JText::_('AD_CMP_CMPNAME'); ?></th>
          <th class="uk-visible-large"><?php echo JText::_('AD_INFO'); ?></th>
          <th class="uk-visible-large"><?php echo JText::_('AD_CMPACTIVE')."?"; ?></th>
          <th class="uk-visible-large"><?php echo JText::_('AD_STATUS'); ?></th>
          <th class="uk-visible-large"><?php echo JText::_('VIEWTREEADS'); ?></th>
          <th><?php echo JText::_('AD_MANAGE_ADS'); ?></th>
          <th><?php echo JText::_('VIEWADACTION'); ?></th>
        </tr>
      </thead>

      <tbody>

        <?php
          $j = 0;
          for($i = 0; $i < $n; $i++){
            $camp = $this->camps[$i];
            $id = $camp->id;
            $checked = JHTML::_('grid.id', $i, $id);
            $link = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=".intval($id).$Itemid);
            
            $expired=0;
            if(($camp->type=="cpm"  || $camp->type=="pc") && $camp->quantity < 1){
              $expired=1;
            }
            
            if($camp->type=="fr" || $camp->type=="in"){
              $datan = date("Y-m-d H:i:s");
              if($datan > $camp->camp_validity && $camp->camp_validity != "0000-00-00 00:00:00"){
                $expired=1;
              }
            }
        ?>

        <tr>
          <td><?php echo $checked;?></td>
          <td><?php echo $id;?></td>
          <td><a href="<?php echo $link; ?>" ><?php echo $camp->name; ?></a></td>
          <td class="uk-visible-large">
            <a href="#campaign-details" onclick="javascript:campaignDetails(<?php echo intval($camp->id); ?>);" data-uk-modal>
              <i class="fa fa-info-circle"></i>
            </a>
          </td>
          <td class="uk-visible-large">
            <?php
              $offset = JFactory::getApplication()->getCfg('offset');
              $today = JFactory::getDate('now', $offset);
              $current_time = $today->toSql(true);
              $current_time = strtotime($current_time);
                
              if($expired == 1 ){
                  echo '<span class="uk-badge uk-badge-danger campaign-status">
                      '.JText::_("VIEW_CAMPAIGN_EXPIRED").'
                      </span>';
              }
              elseif(strtotime($camp->start_date) > $current_time){
                echo '<span class="uk-badge campaign-status">
                    '.JText::_("ADAG_START").": ".$helper->formatime($camp->start_date, $configs->params['timeformat']).'
                    </span>';
              }
              elseif($camp->status == 1){
                echo '<span class="uk-badge uk-badge-success campaign-status">
                    <a href="index.php?option=com_adagency&controller=adagencyCampaigns&task=pause&cid='.intval($camp->id).'">'.JText::_("AD_CMPACTIVE").'</a>
                    </span>';
              }
              elseif($camp->status == 0){
                echo '<span class="uk-badge uk-badge-warning campaign-status">
                    <a href="index.php?option=com_adagency&controller=adagencyCampaigns&task=unpause&cid='.intval($camp->id).'">'.JText::_("AD_CMPPAUSED").'</a>
                    </span>';
              }
            ?>
          </td>
          <td class="uk-visible-large">
            <?php
              jimport('joomla.html.html.bootstrap');
              
              if($camp->approved == "Y"){
                echo '<span class="adag_tip">
                    <i class="fa fa-check"></i>
                    <span>'.JText::_("AD_APPROVED").'</span>
                  </span>';
              }
              elseif($camp->approved == "N"){
                echo '<span class="adag_tip">
                    <i class="fa fa-ban"></i>
                    <span>'.JText::_("ADAG_DECLINED").'</span>
                  </span>';
              }
              elseif($camp->approved == "P"){
                echo '<span class="adag_tip">
                    <i class="fa fa-clock-o"></i>
                    <span>'.JText::_("ADAG_PENDING").'</span>
                  </span>';
              }
            ?>
          </td>
          <td class="uk-visible-large">
            <?php
              if($camp->cnt){
                echo '<span class="campaign-ads">'.$camp->cnt.'</span>';
              }
              else{
                echo '<span class="uk-text-danger campaign-ads">'.$camp->cnt.'</span>';
              }
            ?>
          </td>
          <td>
            <div class="uk-button-dropdown">
              <?php
                $btn_class = "";
                if($camp->cnt == 0){
                  $btn_class = "uk-button-primary";
                }
              ?>
              <button class="uk-button uk-button-small <?php echo $btn_class; ?> dropdown-toggle" data-toggle="dropdown">
                <?php echo JText::_("AD_MANAGE_ADS"); ?>
                <span class="uk-icon-caret-down"></span>
              </button>
              <ul class="dropdown-menu">
                <li><?php echo '<a href="'.JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=addbanners' . $Itemid_ads).'">'.JText::_("ADAG_ADD_NEW_ADS").'</a>'; ?></li>
                <li><?php echo '<a href="'.$link.'">'.JText::_("ASSIGN_EXISTING_ADS").'</a>'; ?></li>
              </ul>
            </div>
          </td>
          <td>
            <?php
              $package_id = $camp->otid;
              $order_details = $this->getOrderDetails($camp->id, $package_id);
            ?>
          
            <div class="uk-button-dropdown">
              <button class="uk-button uk-button-small dropdown-toggle" data-toggle="dropdown">
                <?php echo JText::_("VIEWADACTION"); ?>
                <span class="uk-icon-caret-down"></span>
              </button>
              <ul class="dropdown-menu">
                
              <?php 
              $hide_after = 0;
              $helpvar2 = new adagencyModeladagencyCampaigns();
              $rows = $helpvar2->getlistPackages();
              
              foreach($rows as $key=>$element){
                if($element->tid == $package_id){
                  $hide_after = $element->hide_after;             
                  break;
                }
                
              }
              if($expired == 0){ ?>
                <li><?php echo '<a href="'.$link.'">'.JText::_("AD_EDIT").'</a>'; ?></li>
                <?php
                  $offset = JFactory::getApplication()->getCfg('offset');
                  $jnow = JFactory::getDate('now', $offset);
                  $current_date = $jnow->toSql(true);
                
                  if($camp->status == 1 && strtotime($camp->start_date) < strtotime($current_date)){
                ?>
                    <li><?php echo '<a href="index.php?option=com_adagency&controller=adagencyCampaigns&task=pause&cid='.intval($camp->id).$Itemid.'">'.JText::_("AD_CMPPAUSE").'</a>'; ?></li>
                <?php
                  }
                  
                  if(isset($order_details["0"]) && $order_details["0"]["payment_type"] == "Free"){
                    // do nothing
                  }
                  elseif(isset($order_details) && isset($order_details["0"]) && $order_details["0"]["status"] == "paid"){
                    // campaign is paid
                  }
                  else{
                    $itemid = JRequest::getVar("Itemid", "0");
                    if(intval($itemid) != "0"){
                      $Itemid = "&Itemid=".intval($itemid);
                    }
                    $_SESSION["LCC"] = intval($camp->id);
                    $_SESSION["LCC2"] = intval($package_id);
                    echo '<li><a href="index.php?option=com_adagency&controller=adagencyOrders&task=order&tid='.intval($package_id).$Itemid.'">'.JText::_("ADAG_PAY").'</a></li>';
                  }
                }
                else {
                  
                  if($hide_after == 1) {?><li><?php echo JText::_("NOT_AVAILABLE");?></li><?php }
                  else {?>
                  <li><?php echo '<a onclick="javascript:renewCampaign('.$camp->otid.', \''.trim(addslashes($camp->name)).'\', '.intval(@$order_details["0"]["oid"]).', '.intval($id).');">'.JText::_("AD_RENEW_CAMP").'</a>'; ?></li>
                  <?php } 
                }?>
              </ul>
          </td>
        </tr>

        <?php $k = 1 - $k; ?>
        <?php } ?>

      </tbody>
    </table>

    <fieldset>
      <input class="uk-button" type="button" name="delete_selected" id="delete_selected" onclick="javascript:deleteCampaigns('');" value="<?php echo JText::_("ADAG_DELETE_SELECTED"); ?>" />
      <input class="uk-button uk-visible-large" type="button" name="delete_expired" id="delete_expired" onclick="javascript:deleteCampaigns('remove');" value="<?php echo JText::_("ADAG_DELETE_EXPIRED"); ?>" />
    </fieldset>

    <input type="hidden" name="boxchecked" value="0" /> 
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="controller" value="adagencyCampaigns" />
    <input type="hidden" name="remove_action" value="" />
    <input type="hidden" name="otid" value="" />
    <input type="hidden" name="tid" value="" />
    <input type="hidden" name="name" value="" />
    <input type="hidden" value="0" name="aurorenew" />
    <input type="hidden" value="0" name="orderid" />
    <input type="hidden" value="0" name="campaign_id" />
    <?php echo JHtml::_('form.token'); ?>
  </form>

  <div id="campaign-details" class="uk-modal">
    <div class="uk-modal-dialog">
      <a class="uk-modal-close uk-close"></a>
      <div class="modal-header">
          <h2>
              <?php echo JText::_("ADAG_ABOUT_CAMPAIGN"); ?>
          </h2>
      </div>
      
      <div id="modal-body" class="modal-body">
      </div>
    </div>
  </div>
</div>
