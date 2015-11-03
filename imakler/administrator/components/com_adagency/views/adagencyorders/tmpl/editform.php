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
$nullDate = 0;
$configs = $this->configs;
require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
$helper = new adagencyAdminHelper();
?>
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
				<?php echo $order->oid; ?>
			</div>
	</div>
	
	 <div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWORDERSDATE'); ?> </label>
			<div class="controls">
				<?php echo $helper->formatime($order->order_date, $configs->params['timeformat']); ?>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWORDERSADV'); ?> </label>
			<div class="controls">
				<?php echo $order->aid; ?>
			</div>
	</div>	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWORDERSLICDET'); ?> </label>
			<div class="controls">
				<?php echo $order->type; ?>
			</div>
	</div>	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWORDERSQNTY'); ?> </label>
			<div class="controls">
				<?php echo $order->quantity; ?>
			</div>
	</div>	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWORDERSCOST'); ?> </label>
			<div class="controls">
				<?php echo $order->cost.'&nbsp;'.$configs->currencydef; ?>
			</div>
	</div>	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWORDERSPMT'); ?> </label>
			<div class="controls">
				<?php echo $order->payment_type; ?>
			</div>
	</div>	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWORDERSNOTES'); ?> </label>
			<div class="controls">
				<?php echo $order->notes; ?>
			</div>
	</div>	

			<input type="hidden" name="images" value="" />                
	        <input type="hidden" name="option" value="com_adagency" />
	        <input type="hidden" name="id" value="<?php echo $order->oid; ?>" />
	        <input type="hidden" name="task" value="" />
			<input type="hidden" name="controller" value="adagencyOrders" />
        </form>
