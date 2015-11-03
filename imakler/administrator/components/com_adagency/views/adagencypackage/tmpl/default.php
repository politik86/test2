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

JHtml::_('behavior.framework',true);

$document = JFactory::getDocument();
$k = 0;
$n = count ($this->packages);
$configs = $this->configs;
$ordersnr = $this->ordersnr;
$lists=$this->lists;
if(!isset($_SESSION['package_status'])) {$_SESSION['package_status']=NULL;}


$user = JFactory::getUser();
$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_adagency&controller=adagencyPackages&task=saveOrderAjax&tmpl=component';
JHtml::_('sortablelist.sortable', 'packageList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('behavior.modal');

?>
 
<?php if (!$this->nz) { ?>
    <SCRIPT>
    function saveorder(n, task) {
        Joomla.submitform("saveorder");
    }
    </SCRIPT>
  	<form action="index.php" name="adminForm" id="adminForm" method="post">
    	<div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWTREEPACKAGES'); ?>
				</h2>
            </div>
            <a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674044">
			    <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
			    <?php echo JText::_("COM_ADAGENCY_VIDEO_PACKAGE_SETTINGS"); ?>   
			</a>
       </div>
	
		<div id="filter-bar" class="row-fluid">            
    			<div class="span12">                
    				<div class="filter-search btn-group pull-left">                    
    					<label for="search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>                    
    					<input type="text" id="filter_search" name="search_package" value="<?php if(isset($_SESSION['search_package'])) echo $_SESSION['search_package'];?>" />   
    					</div>                                                
    					<div class="btn-group pull-left hidden-phone">                    
    						<button class="btn adag_tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>                    
    						<button class="btn adag_tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>                
    					</div> 
    					<div class="btn-group pull-right hidden-phone">	                
    						 <?php echo JText::_('VIEWAD_SEARCH_TYPE');?>
                    		 <?php echo $lists['type_package'];	?>
    					</div>	      
		                <div class="btn-group pull-right hidden-phone">
		                    <?php echo JText::_('VIEWAD_SEARCH_ZONE');?>
                    		<?php echo $lists['package_zone'];	?>
		                </div>
		                <div class="btn-group pull-right hidden-phone">
		                    <?php echo JText::_('VIEWADSTATUS');?>
		                    <select name="package_status" onChange="document.topform1.submit()">
		                        <option value="-1" <?php if (!isset($_SESSION['order_status']) || $_SESSION['package_status'] == '-1') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_SEARCH_SELECT'); ?></option>
		                        <option value="1" <?php if (isset($_SESSION['package_status']) && $_SESSION['package_status'] == '1') echo ' selected="selected" ';?>><?php echo JText::_('VIEWPACKAGEPUBLISHED');?></option>
		                        <option value="0" <?php if (isset($_SESSION['package_status']) && $_SESSION['package_status'] == '0') echo ' selected="selected" ';?>><?php echo JText::_('VIEWPACKAGEUNPUBLISHED');?></option>
		                    </select>
		                </div>
		                <div class="btn-group pull-right hidden-phone">
		                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
		                    <?php echo $this->pagination->getLimitBox(); ?>
		                </div>	       	
    			</div>    
    	</div>    

<?php
    }
	else{
?>
<script type="text/javascript">
    function nextToZonePackage(){
		count_checked = 0;
		var chk_arr =  document.getElementsByName("cid[]");
		var chklength = chk_arr.length;             			
		for(k=0; k<chklength; k++){
			if(chk_arr[k].checked){
				count_checked ++;
			}
		}
		
		if (!count_checked) {
			alert('<?php echo JText::_('ADAG_NZ_AT_LEAST'); ?>');
			return false;
		}
		else{
			submitbutton('zonePacks');
		}
		return false;
	}
</script>
<div class="row-fluid">
	<div class="span12">
		<div id="system-message-container">
		    <div class="alert alert-notice">		       
		        	<div class="adag_bold"><?php echo JText::_('ADAG_NZ_ADDED'); ?></div>
				    <div class="adag_normal"><?php echo JText::_('ADAG_NZ_MSG1'); ?></div>
				    <div class="adag_note"><?php echo JText::_('ADAG_NZ_NOTE'); ?></div>		       
		    </div>
		</div>
	</div>
</div>
	<button class="btn btn-success pull-right" style="margin-top:10px;" id="adag-next" onclick="javascript:nextToZonePackage();"><?php echo JText::_('ADAG_NEXT'); ?> >> </button>
<form action="index.php" name="adminForm" id="adminForm" method="post">
<?php
    }
?>

<div class="row-fluid">
<div class="span12">
<div id="editcell">
<table class="table table-striped table-bordered" id="packageList">	
<thead>
		<th>
        	<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
        </th>
        <th width="5">
			<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
			 <span class="lbl"></lbl>
		</th>
		<th width="5">
			<?php echo JText::_('ADAG_UNIV_ID');?>
		</th>
	        <th>
			<?php echo JText::_('VIEWPACKAGEDESC');?>
		</th>
		<th>
			<?php echo JText::_('VIEWPACKAGETYPE');?>
		</th>
		<th>
			<?php echo JText::_('VIEWDSADMINZONES');?>
		</th>

		<th>
			<?php echo JText::_('VIEWPACKAGEPUBLISHED');?>
		</th>
		<!--
        <th width="65">
			<?php echo JText::_('ORDER');?><a class="saveorder" title="Save Order" href="javascript:saveorder(<?php echo $n;?>, 'saveorder')">
</a>
		</th>
        -->
		<th>
			<?php echo JText::_('VIEWPACKAGETERMS');?>
		</th>
		<th>
			<?php echo JText::_('VIEWPACKAGEPERIOD');?>
		</th>
		<th>
			<?php echo JText::_('VIEWPACKAGEPRICE');?>
		</th>
		<th>
			<?php echo JText::_('VIEWPACKAGEORDERS');?>
		</th>
</thead>

<tbody>

<?php
	for ($i = 0; $i < $n; $i++):
		$order =& $this->packages[$i];
		$id = $order->tid;
		$checked = JHTML::_('grid.id', $i, $id);
		$link = JRoute::_("index.php?option=com_adagency&controller=adagencyPackages&task=edit&cid[]=".$id);
		$published = JHTML::_('grid.published', $order, $i );
		
		$canCheckin = $user->authorise('core.manage',     'com_checkin') || $order->checked_out == $userId || $order->checked_out == 0;
		$canChange  = $user->authorise('core.edit.state', 'com_adagency.adagencyPackages.') && $canCheckin;
?>
	<tr class="row<?php echo $k;?>">
	     	<td>
                <span class="sortable-handler active" style="cursor: move;">
                    <i class="icon-menu"></i>
                </span>
                <input type="text" class="width-20 text-area-order " value="<?php echo $order->ordering; ?>" size="5" name="order[]" style="display:none;">
            </td>
            <td>
            	<?php echo $checked;?> <span class="lbl"></lbl>
            </td>
			<td width="5" align="center"><?php echo $id;?></td>
	     	<td><a href="<?php echo $link;?>" ><?php echo $order->description;?></a></td>
	     	<td><?php echo JText::_('ADAG_'.strtoupper($order->type));?></td>
	     	<td><?php
					if(isset($order->location)&&(is_array($order->location))) {
						$cnt = 0;
						foreach($order->location as $element){
							if($cnt >0) { echo ", "; }
							echo "<a href='index.php?option=com_adagency&controller=adagencyZones&task=edit&cid[]=".$element->zone_id."'>".$element->title."</a>";
							$cnt++;
						}
					}
				?>
			</td>
	     	<td><?php echo $published;?></td>
			<!--
            <td align="center"><input type="text" maxlength="4" size="5" name="pack[<?php echo $id;?>]" value="<?php echo $order->ordering;?>"/></td>
            -->
	     	<td><?php if ($order->type!="fr" && $order->type!="in") { echo $order->quantity."&nbsp;"; if ($order->type=="cpm") { echo "Impressions"; } else { echo "Clicks"; } } else { echo "-"; } ?></td>
	     	<td><?php if ($order->type=="fr" || $order->type=="in") { if ($order->validity!="") {
					$validity = explode("|", $order->validity, 2);
					$validity[1] = ($validity[1]=="day") ? "Day(s)" : (($validity[1]=="week") ? "Week(s)" : (($validity[1]=="month") ? "Month(s)" : (($validity[1]=="year") ? "Year(s)" : ""))) ;
					echo $validity[0]." ".$validity[1]; } } else { echo "-"; } ?></td>
	     	<td><?php echo $order->cost."&nbsp;".$configs->currencydef;?></td>
			<td>
            	<?php
                	if($ordersnr[$order->tid] > 0){
				?>
            			<a href="index.php?option=com_adagency&controller=adagencyOrders&package_id=<?php echo $id; ?>"><?php echo $ordersnr[$order->tid];?></a></td>
               	<?php
                	}
					else{
						echo "0";
					}
				?>
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
<input type="hidden" name="controller" value="adagencyPackages" />
<input type="hidden" name="old_limit" value="<?php echo JRequest::getVar("limitstart"); ?>" />
<input type="hidden" name="nz" value="<?php echo $this->nz; ?>" />
</form>
