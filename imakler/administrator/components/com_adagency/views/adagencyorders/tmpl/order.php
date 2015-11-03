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

$order = $this->order;
$configs = $this->configs;
$paywith = $this->paywith;
$lists = $this->lists;
$allplug = $this->allplug;

if($order->type=="fr" || $order->type=="in"){
	if(strpos($order->validity,"|")>0){
		$temp = explode("|",$order->validity);
		$temp[1] = JText::_("ADAG_".strtoupper($temp[1]));
		$order->details = implode("|",$temp);
	}
	else{
		$order->details = $order->validity;
	}
}
else{
	$order->details = $order->quantity.' '.JText::_("ADAG_".strtoupper($order->type));
}

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_adagency/includes/css/adagency_template.css');
$document->addStyleSheet(JURI::root() . 'components/com_adagency/includes/css/ad_agency.css');

$cost = $order->cost;
$total = $order->cost;
$discount = "";
if(isset($_SESSION["new_cost"]) && trim($_SESSION["new_cost"]) != ""){
	$total = trim($_SESSION["new_cost"]);
	if($configs->showpromocode == 1){
		$cost = trim($_SESSION["new_cost"]);
	}
}

if(isset($_SESSION["discount"]) && trim($_SESSION["discount"]) != ""){
	$discount = trim($_SESSION["discount"]);
	$discount = round($discount, 2);
}

?>
<div id="order_info">
<div class="page-title">
<h2><?php echo JText::_("AD_BUY_PACK");?></h2>
</div>
<div class="adg_row">
	<div class="adg_cell span12">
	<div><div>
	<form class="form-horizontal" action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders&task=checkout');?>" method="post" name="adminForm">
		<div class="control-group">
			<label for="name" class="control-label"><b><?php echo JText::_('BUY_PACKNAME');?>:</b></label>
			<div class="controls">
				<?php echo $order->description;?>
			</div>
		</div>
        
        <div class="control-group">
			<label for="name" class="control-label"><b><?php echo JText::_('BUY_PACKTYPE');?>:</b></label>
			<div class="controls">
				<?php if ($order->type=="cpm") { echo JText::_('BUY_PACKCPM'); } elseif ($order->type=="pc") { echo JText::_('BUY_PACKPC'); } elseif ($order->type=="fr") { echo JText::_('BUY_PACKFR'); } elseif ($order->type=="in"){ echo JText::_("BUY_PACKIM"); } ?>
			</div>
		</div>
        
        <div class="control-group">
			<label for="name" class="control-label"><b><?php echo JText::_('BUY_PACKDETAILS');?>:</b></label>
			<div class="controls">
				<?php
                    	$details = $order->details;
						$details = str_replace("|", " ", $details);
						$details = ucwords($details);
						$number = intval($details);
						if($number > 1){
							$details .= "s";
						}
						echo $details;
					?>
			</div>
		</div>
        
        <div class="control-group">
			<label for="name" class="control-label"><b><?php echo JText::_('BUY_PACKPRICE');?>:</b></label>
			<div class="controls">
				<?php
                	$params = unserialize($configs->params);
					$currency_price = 0;
					if(isset($params['currency_price'])){
						$currency_price = $params['currency_price'];
					}
					
					if($currency_price == 0){
						echo JText::_("ADAG_C_".$configs->currencydef).$cost;
					}
					else{
						echo $cost.JText::_("ADAG_C_".$configs->currencydef);
					}
				?>
			</div>
		</div>
    
    	<?php
    		if($configs->showpromocode == 0 && trim(@$_SESSION["new_cost"]) != ""){
		?>
        		<div class="control-group">
                    <label for="name" class="control-label"><b><?php echo JText::_('ADAG_DISCOUNT');?>:</b></label>
                    <div class="controls">
                        <?php
                        	if($currency_price == 0){
								echo JText::_("ADAG_C_".$configs->currencydef).$discount;
							}
							else{
								echo $discount.JText::_("ADAG_C_".$configs->currencydef);
							}
						?>
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="name" class="control-label"><b><?php echo JText::_('ADAG_TOTAL_PRICE');?>:</b></label>
                    <div class="controls">
                        <?php
							if($currency_price == 0){
								echo JText::_("ADAG_C_".$configs->currencydef).$total;
							}
							else{
								echo $total.JText::_("ADAG_C_".$configs->currencydef);
							}
						?>
                    </div>
                </div>
        <?php
        	}
		?>
    
    	<div class="control-group">
            <label for="name" class="control-label"><b><?php echo JText::_('BUY_PACKPAYMENT');?>:</b></label>
            <div class="controls">
                <?php
					if(!$paywith){
						echo $lists['payment_type'];
                		if(isset($lists['payment_type'])){
							$valid=1;
						}
                	}
					else{
                        echo '<strong>'.strtoupper($this->paywith_display_name).'</strong>';
                        $valid=1;
                        echo '<input type="hidden" name="payment_type" value="'.$paywith.'">';
                	}
                ?>
            </div>
		</div>

	<?php if ($allplug==0) $valid=0;?>
	<div class="adg_cell span3"></div>
		<div class="a_row span7 adg_padding_left">
			<INPUT id="buy" class="btn btn-warning" TYPE="submit" value="<?php echo JText::_("AD_BUY_PACK2");?> >>" <?php if(!$valid) echo 'disabled="disabled"'; ?>>
		</div>				
	<input type="hidden" name="task" value="checkout" />
	<input type="hidden" name="tid" value="<?php echo $order->tid; ?>" />
	<?php
		if(JRequest::getInt('Itemid','0','get') != '0') {
			echo "<input type='hidden' name='Itemid' value='".JRequest::getInt('Itemid','0','get')."' />";
		}
	?>
	<input type="hidden" name="aurorenew" value="<?php echo intval($_SESSION["aurorenew"]); ?>" />
    <input type="hidden" name="orderid" value="<?php echo intval(JRequest::getVar("orderid", "0")); ?>" />
	</form>
	</div></div>
</div>
</div>
</div>