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

$orderid = $this->orderid;
$nullDate = 0;
$configs = $this->configs;
$lists = $this->lists;	
require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
$helper = new adagencyAdminHelper();
?>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/add_order.php"); ?>
 <form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
 	 <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWORDERSLICDET'); ?>
				</h2>
            </div>
      </div>
      
      <div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWORDERSNUMBER'); ?> </label>
			<div class="controls">
				<?php echo $orderid; ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWORDERSNUMBER_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
      <div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADDORDERS_ADVERTISER'); ?> </label>
			<div class="controls">
				<?php echo $lists['advertiser_id']; ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADDORDERS_ADVERTISER_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADDORDERS_PACKAGE'); ?> </label>
			<div class="controls">
				<?php echo $lists['package_id']; ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADDORDERS_PACKAGE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('AD_PAYMENT_METHOD'); ?> </label>
			<div class="controls">
				<?php echo $lists['payment_method']; ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('AD_PAYMENT_METHOD_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
		<?php
        	$promos = $this->promoValid();
			if($promos > 0){
		?>
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('AD_PAYMENT_PROMOCODE'); ?> </label>
			<div class="controls">
				  <input id="promocode" class="inputbox" type="text" value="" maxlength="20" name="promocode" />
			</div>
	</div>
	   <?php
       	 }
		?>
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('AD_PAYMENT_AMOUNT'); ?> </label>
			<div class="controls">
				<input id="cost" class="inputbox" type="text" value="0" maxlength="20" size="8" name="cost" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('AD_PAYMENT_AMOUNT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('AD_PAYMENT_DATE'); ?> </label>
			<div class="controls">
				<?php
					$format_string = "Y-m-d";
					$format_value = $configs->params['timeformat'];
					
					switch($format_value){
						case "0" : {
							$format_string = "Y-m-d H:i:s";
							break;
						}
						case "1" : {
							$format_string = "m/d/Y H:i:s";
							break;
						}
						case "2" : {
							$format_string = "d-m-Y H:i:s";
							break;
						}
						case "3" : {
							$format_string = "Y-m-d";
							break;
						}
						case "4" : {
							$format_string = "m/d/Y";
							break;
						}
						case "5" : {
							$format_string = "d-m-Y";
							break;
						}
					}
					
					$format_string_2 = str_replace ("-", "-%", $format_string);
					$format_string_2 = str_replace ("/", "/%", $format_string_2);
					$format_string_2 = "%".$format_string_2;
					$format_string_2 = str_replace("H:i:s", "%H:%M:%S", $format_string_2);
				 
				 	$joomla_date = JFActory::getDate();
					$current_date = $joomla_date->toSql();
				 	$now_now = $helper->formatime($current_date, $configs->params['timeformat']);
					
					echo JHtml::calendar(trim($now_now), 'pay_date', 'pay_date', $format_string_2, '');
					
					echo "<input type='hidden' name='tfa' value='".$configs->params['timeformat']."' />";
					?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('AD_PAYMENT_DATE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
			<input type="hidden" name="images" value="" />                
	        <input type="hidden" name="option" value="com_adagency" />
	        <input type="hidden" name="id" value="" />
	        <input type="hidden" name="task" value="" />
			<input type="hidden" name="controller" value="adagencyOrders" />
        </form>
