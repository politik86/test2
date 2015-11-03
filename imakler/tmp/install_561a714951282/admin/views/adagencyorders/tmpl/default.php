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
	$n = count ($this->orders);
	$configs = $this->configs;
	$lists = $this->lists;	
	$plugs = $this->plugs;	
	require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");	
	
$user = JFactory::getUser();
$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_adagency&controller=adagencyOrders&task=saveOrderAjax&tmpl=component';
JHtml::_('sortablelist.sortable', 'orderlist', 'adminForm', strtolower($listDirn), $saveOrderingUrl);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('behavior.modal');
$helper = new adagencyAdminHelper();
?>
<form action="index.php?option=com_adagency&controller=adagencyOrders" method="post"name="topform1">
	
	<div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWTREEORDERS'); ?>
				</h2>
            </div>
            <a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674037">
			    <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
			    <?php echo JText::_("COM_ADAGENCY_VIDEO_ORDERS_SETTINGS"); ?>   
			</a>

       </div>
	
		<div id="filter-bar" class="row-fluid">            
    			<div class="span12">                
    				<div class="filter-search btn-group pull-left">                    
    					<label for="search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>                    
    					<input type="text" id="filter_search" name="search_order" value="<?php if(isset($_SESSION['search_order'])) echo $_SESSION['search_order'];?>" />   
    					</div>                                                
    					<div class="btn-group pull-left hidden-phone">                    
    						<button class="btn adag_tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>                    
    						<button class="btn adag_tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>                
    					</div> 
    					<div class="btn-group pull-right hidden-phone">	                
    						<?php echo JText::_('VIEWAD_SEARCH_ADVERTISER');?>
							<?php echo $lists['advertiser_id'];	?>	    
    					</div>	      
		                <div class="btn-group pull-right hidden-phone">
		                   <?php echo JText::_('VIEWAD_SEARCH_PACKAGE');?>
						   <?php echo $lists['package_id'];?>	
		                </div>
		                <div class="btn-group pull-right hidden-phone">	                
    						<?php echo JText::_('VIEWAD_SEARCH_PAYMENT_METHOD');?>
							<?php echo $lists['payment_method'];	?>	
    					</div>	      
		                <div class="btn-group pull-right hidden-phone">
		                    <?php echo JText::_('VIEWAD_STATUS_SEARCH');?>
							<select name="order_status" onChange="document.adminForm.submit()">
								<option value="-1" <?php if (!isset($_SESSION['order_status']) || $_SESSION['order_status'] == '-1') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_SEARCH_SELECT');?></option>
								<option value="0" <?php if (isset($_SESSION['order_status']) && $_SESSION['order_status'] == '0') echo ' selected="selected" ';?>><?php echo JText::_('VIEWORDERSPAID');?></option>
								<option value="1" <?php if (isset($_SESSION['order_status']) && $_SESSION['order_status'] == '1') echo ' selected="selected" ';?>><?php echo JText::_('VIEWORDERSPENDING');?></option>
							</select>
		                </div>
		                
		                <div class="btn-group pull-right hidden-phone">
		                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
		                    <?php echo $this->pagination->getLimitBox(); ?>
		                </div>	       	
    			</div>    
    	</div>    
</form>

<form action="index.php" name="adminForm" id="adminForm" method="post">

<div class="row-fluid">
<div class="span12">
<div id="editcell">
<table class="table table-striped table-bordered" id="orderlist">
<thead>

    	<!--
		<th>
        	<?php //echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
        </th>
        -->
        <th width="5">
			<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
			 <span class="lbl"></lbl>
		</th>
	        <th width="20">
			<?php echo JText::_('VIEWORDERSID');?>
		</th>
		<th>
			<?php echo JText::_('VIEWORDERSADVERT');?>
		</th>
			<th>
			<?php echo JText::_('VIEWORDERSORDERDATE');?>
		</th>
		<th>
			<?php echo JText::_('VIEWORDERSPRICE');?>
		</th>
		<th>
			<?php echo JText::_('VIEWORDERSPACKAGE');?>
		</th>
		<th>
			<?php echo JText::_('VIEWORDERSMETHOD');?>
		</th>
		<th>
			<?php echo JText::_('VIEWORDERSSTATUS');?>
		</th>
		<th>
			<?php echo JText::_('VIEWORDERSACTIONS');?>
		</th>
</thead>

<tbody>

<?php 
	for ($i = 0; $i < $n; $i++):
		$order = $this->orders[$i];
		
		$id = $order->oid;
		$checked = JHTML::_('grid.id', $i, $id);
		$link = JRoute::_("index.php?option=com_adagency&controller=adagencyOrders&task=edit&cid[]=".intval($id));
		$customerlink = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=".intval($order->user_id));
		$packagelink = JRoute::_("index.php?option=com_adagency&controller=adagencyPackages&task=edit&cid[]=".intval($order->tid));
		
?>
	<tr class="row<?php echo $k;?>"> 
    	<!--
        <td>
			<span class="sortable-handler active" style="cursor: move;">
                <i class="icon-menu"></i>
            </span>
            <input type="text" class="width-20 text-area-order " value="<?php //echo $order->ordering; ?>" size="5" name="order[]" style="display:none;">
        </td>
        -->
	    <td>
	    	<?php echo $checked;?>
	    	 <span class="lbl"></lbl>
		</td>		

	    <td>
	    	<a href="index.php?option=com_adagency&controller=adagencyOrders&task=edit&cid[]=<?php echo intval($order->oid);?>"><?php echo $id;?></a>
		</td>		
	     	<td>
	     	    	<a href="<?php echo $customerlink; ?>" ><?php echo $order->company;?></a>
		</td>		
	     	<td>
	     	    	<?php  echo $helper->formatime($order->order_date, $configs->params['timeformat']);?>
		</td>		

	     	<td>
	     	    	<?php echo $order->cost.'&nbsp;';
						if(isset($order->currency)&&($order->currency != NULL)) {
							echo $order->currency;
						} else {
							echo $configs->currencydef;
						}
					?>
		</td>		


	     	<td>
	     	    	<a href="<?php echo $packagelink;?>" ><?php echo $order->notes;?></a>
		</td>		
<td>
	     	    	<?php
					if ($order->alias == "2CO") $order->alias="twocheckout";
					$ok=0;
						foreach($plugs as $a_plug) {
							if($a_plug[0] == $order->alias.".php") { echo $a_plug[1]; $ok=1; }
							//break;
						}
						 
					if ($ok==0) echo $order->alias;
					$ok=0;
					?>
		</td>
<td>
	     	    	<?php if ($order->status=='paid') echo JText::_('VIEWORDERSPAID'); else echo JText::_('VIEWORDERSPENDING'); ?>
		</td>
<td>
	     	    	<?php if ($order->status=='paid') echo '<a onclick="return confirm(\'Are you sure you want to delete this order?\');" href="index.php?option=com_adagency&controller=adagencyOrders&task=remove&cid[]='.intval($order->oid).'">'.JText::_('VIEWORDERSDELETE').'</a>'; else echo '<a style="color:green" href="index.php?option=com_adagency&controller=adagencyOrders&task=confirm&cid[]='.$order->oid.'">'.JText::_('VIEWORDERSCONFIRM').'</a>'.'&nbsp;|&nbsp;'.'<a onclick="return confirm(\'Are you sure you want to delete this order?\');" href="index.php?option=com_adagency&controller=adagencyOrders&task=remove&cid[]='.intval($order->oid).'">'.JText::_('VIEWORDERSDELETE').'</a>'; ?>
		</td>
	</tr>


<?php 
		$k = 1 - $k;
	endfor;
?>
</tbody>
</table>
<?php echo $this->pagination->getListFooter(); ?>
</div>
</div>
</div>

<input type="hidden" name="option" value="com_adagency" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="adagencyOrders" />
<input type="hidden" name="old_limit" value="<?php echo JRequest::getVar("limitstart"); ?>" />
</form>