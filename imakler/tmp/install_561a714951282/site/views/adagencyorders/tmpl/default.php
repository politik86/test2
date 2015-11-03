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

  $k = 0;
  $get = JRequest::get('get');
  $n = count ($this->orders);
  $currencydef = trim($this->currencydef," ");
  $plugs = $this->plugs;
  $params = $this->params;
  $helper = new adagencyAdminHelper();
  $itemid = $this->itemid;
    $itemid_ads = $this->itemid_ads;
    $itemid_adv = $this->itemid_adv;
    $itemid_cmp = $this->itemid_cmp;
    $itemid_pkg = $this->itemid_pkg;
    $item_id_cpn = $this->itemid_cpn;
  if($itemid != 0) { $Itemid = "&Itemid=" . intval($itemid); } else { $Itemid = NULL; }
    if($itemid_ads != 0) { $Itemid_ads = "&Itemid=" . intval($itemid_ads); } else { $Itemid_ads = NULL; }
    if($itemid_adv != 0) { $Itemid_adv = "&Itemid=" . intval($itemid_adv); } else { $Itemid_adv = NULL; }
    if($itemid_cmp != 0) { $Itemid_cmp = "&Itemid=" . intval($itemid_cmp); } else { $Itemid_cmp = NULL; }
    if($itemid_pkg != 0) { $Itemid_pkg = "&Itemid=" . intval($itemid_pkg); } else { $Itemid_pkg = NULL; }
    if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".intval($item_id_cpn); } else { $Itemid_cpn = NULL; }


  require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
  $document = JFactory::getDocument();
  JHtml::_('behavior.framework',true);
  $document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
    $cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid);
  $document->addScriptDeclaration('
    ADAG(function(){
      ADAG(\'.cpanelimg\').click(function(){
        document.location = "' . $cpn_link . '";
      });
    });');
?>

<!-- Orders Container -->
<div class="ada-orders">
  <?php if(isset($get['p'])&&(intval($get['p'])==1)){ ?>
    <div class="uk-alert">
      <?php echo JText::_('ADAG_ORPEN'); ?>
    </div>
  <?php } ?>

  <?php if(isset($this->aid)&&($this->aid > 0)) {
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
    <li class="uk-active"><a href="index.php?option=com_adagency&controller=adagencyOrders<?php echo $Itemid_ord; ?>"><?php echo JText::_('ADG_ORDERS'); ?></a></li>
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
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds'. $Itemid_ads); ?>"><?php echo JText::_('ADG_ADS'); ?></option>
    <option selected="selected" value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders'.$Itemid_ord); ?>"><?php echo JText::_('ADG_ORDERS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyReports'.$Itemid_rep); ?>"><?php echo JText::_('ADG_REPORTS'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns'.$Itemid_cmp); ?>"><?php echo JText::_('ADG_CAMP'); ?></option>
    <option value="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyPackage'.$Itemid_pck); ?>"><?php echo JText::_('ADG_PACKAGES'); ?></option>                               
    <option value="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&'.JSession::getFormToken().'=1&return='.$return_url); ?>"><?php echo JText::_('ADG_LOGOUT'); ?></option>
  </select>

  <?php } ?>

  <div class="ada-orders-heading">
    <h2 class="ada-orders-title"><?php echo JText::_('AD_CP_ORDERS'); ?></h2>
  </div>

  <div class="ada-orders-actions">
    <form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds' . $Itemid_ads); ?>" method="POST">
      <input class="uk-button uk-button-success" name="" type="submit" value="<?php echo JText::_('ADAG_ADDBANNER2');?>"/>
    </form>
    <form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0' . $Itemid_cmp); ?>" method="POST">
      <input class="uk-button uk-button-primary" name="" type="submit" value="<?php echo JText::_('ADAG_ACMP');?>"/>
    </form>
  </div>

  <!-- Table -->
  <form class="form-horizontal" method="post" name="adminForm" id="adminForm">
    <table class="uk-table uk-table-striped">
      <thead>
        <tr>
          <th class="uk-visible-large"><?php echo JText::_('VIEWORDERSID');?></th>
          <th class="uk-visible-large"><?php echo JText::_('VIEWORDERSORDERDATE');?></th>
          <th><?php echo JText::_('VIEWORDERSORDERDESC');?></th>
          <th><?php echo JText::_('VIEWORDERSTYPE');?></th>
          <th class="uk-visible-large"><?php echo JText::_('VIEWORDERSPRICE');?></th>
          <th class="uk-visible-large"><?php echo JText::_('VIEWORDERSMETHOD');?></th>
          <th><?php echo JText::_('VIEWORDERSSTATUS');?></th>
        </tr>
      </thead>

      <tbody>
        <?php
          $j=0;
          for($i = 0; $i < $n; $i++){

          $order =& $this->orders[$i];
          $id = $order->oid;
          $checked = JHTML::_('grid.id', $i, $id);
          $link = JRoute::_("index.php?option=com_adagency&controller=adagencyOrders&task=edit&cid[]=" . intval($id) . $Itemid);
          $customerlink = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=" . intval($order->aid) . $Itemid_adv);
          $packagelink = JRoute::_("index.php?option=com_adagency&controller=adagencyPackages&task=edit&cid[]=" . intval($order->tid) . $Itemid_pkg);
      
          $payment_method = "";
          $name = $order->notes;
          $ok=0;
          foreach($plugs as $a_plug) {
            if($a_plug[0] == $order->payment_type.".php"){
              if(strtoupper($a_plug["1"]) == "AUTHORIZENET"){
                $name = JText::_("VIEWORDERSPACKAGE");
              }
              $payment_method = JText::_("ADAG_".strtoupper($a_plug["1"])."_PAYMENT");
              $ok=1;
            }
          }
          
          if($ok==0){
            if(trim(strtolower($order->payment_type)) == 'free'){
              $payment_method = JText::_("VIEWPACKAGEFREE");
            }
            else{
              $payment_method = JText::_("ADAG_".strtoupper($order->payment_type)."_PAYMENT");
            }
          }
          $ok=0;
        ?>
        <tr>
          <td class="uk-visible-large"><?php $j++; echo $j; ?></td>
          <td class="uk-visible-large"><?php echo $helper->formatime($order->order_date, @$params['timeformat']); ?></td>
          <td><?php echo $name; ?></td>
          <td><?php echo JText::_("ADAG_".strtoupper($order->type)."_TEXT"); ?></td>
          <td class="uk-visible-large">
            <?php
              $currency_price = $this->currency_price;
              
              if(isset($order->currency)&&($order->currency != NULL)) {
                if($currency_price == 0){
                  echo JText::_("ADAG_C_".$order->currency);
                  echo $order->cost;
                }
                else{
                  echo $order->cost;
                  echo JText::_("ADAG_C_".$order->currency);
                }
              }
              else{
                if(isset($order->currencydef)){
                  if($currency_price == 0){
                    echo JText::_("ADAG_C_".$order->currencydef);
                    echo $order->cost;
                  }
                  else{
                    echo $order->cost;
                    echo JText::_("ADAG_C_".$order->currencydef);
                  }
                }
              }
            ?>
          </td>
          <td class="uk-visible-large"><strong><?php echo $payment_method; ?></strong></td>
          <td>
            <?php 
              if($order->status=='paid'){
                echo '<span class="uk-text-success">'.JText::_("VIEWORDERSPAID").'</span>';
              }
              else{
                echo '<span class="uk-text-danger">'.JText::_("VIEWORDERSPENDING").'</span>';
              }
            ?>
          </td>
        </tr>
        <?php $k = 1 - $k; ?>
        <?php } // close ?>
      </tbody>
    </table>
  </form>
</div>
