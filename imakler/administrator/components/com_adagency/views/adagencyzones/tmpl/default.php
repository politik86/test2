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
	$n = count ($this->zones);
	$configs = $this->configs;
	$lists = $this->lists;	
	
$user = JFactory::getUser();
$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_adagency&controller=adagencyAds&task=saveOrderAjax&tmpl=component';
JHtml::_('sortablelist.sortable', 'zonelist', 'adminForm', strtolower($listDirn), $saveOrderingUrl);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('behavior.modal');

?>

<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton){
		if(pressbutton == "add" || pressbutton == "duplicate"){
			if(eval(document.getElementById("cb0")) && eval(document.getElementById("cb1"))){
				document.getElementById("zone-light").style.display = "";
				document.getElementById("light-modal-background").style.display = "";
				return false;
			}
		}
		submitform(pressbutton);
	}
	
	function closePopUp(modal_id){
		document.getElementById(modal_id).style.display = "none";
		document.getElementById("light-modal-background").style.display = "none";
	}
</script>

<form action="index.php" name="adminForm" id="adminForm" method="post">
	<div class="row-fluid">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWTREEZONES'); ?>
				</h2>
            <a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674048">
				    <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
				    <?php echo JText::_("COM_ADAGENCY_VIDEO_ZONES_SETTINGS"); ?>   
			</a>
	</div>
		<div id="filter-bar" class="row-fluid">            
    			<div class="span12">                
    				<div class="filter-search btn-group pull-left">                    
    					<label for="search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>                    
    					<input type="text" id="filter_search" name="search_zone" value="<?php if(isset($_SESSION['search_zone'])) echo $_SESSION['search_zone'];?>" />   
    				</div>                                                
    					<div class="btn-group pull-left hidden-phone">                    
    						<button class="btn adag_tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>                    
    						<button class="btn adag_tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>                
    					</div> 
		                <div class="btn-group pull-right hidden-phone">
		                    <?php echo JText::_("ZONEPOSITION"); ?>	
							<?php echo $lists['module_position']; ?>	
		                </div>  
		                <div class="btn-group pull-right hidden-phone">
		                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
		                    <?php echo $this->pagination->getLimitBox(); ?>
		                </div>	
		           </div>       	
    		</div>    
 <div class="row-fluid">
  <div class="span12">	
	<div id="editcell" >
<table class="table table-striped table-bordered" id="zonelist">
<thead>
    	<th>
        	<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
        </th>
        
		<th width="5">
			<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
			<span class="lbl"></lbl>
		</th>
				<th width="20">
			<?php echo JText::_('ZONEID');?>
		</th>
	        <th>
			<?php echo JText::_('ZONEMODNAME');?>
		</th>

		<th>
			<?php echo JText::_('ZONEPUB');?>
		</th>
		<th><?php echo JText::_("ZONEPOSITION");?>	
		</th>
		<th>
			<?php echo JText::_('ZONEPAGES');?>
		</th>

		<th>
			<?php echo JText::_('ZONEADSZONE');?>
		</th>
        
        <th>
			<?php echo JText::_('ADAG_SUPPORTED_AD_TYPES');?>
		</th>
        
        <th>
			<?php echo JText::_('ADAG_ADSIZE');?>
		</th>
		
        <th>
        	<?php echo JText::_("VIEWDSADMINADS"); ?>
        </th>
</thead>

<tbody>
<?php 
	JHtml::_('bootstrap.tooltip');
	$zones_ads = $this->getZonesAds();
	
	for ($i = 0; $i < $n; $i++):
		$zone = $this->zones[$i];
		
		$adparams = $zone->adparams;
		$adparams = unserialize($adparams);
		$supported_ad = array();
		if(isset($adparams) && is_array($adparams) && count($adparams) > 0){
			foreach($adparams as $key=>$value){
				if($key != "width" && $key != "height"){
					$supported_ad[] = ucfirst($key);
				}
			}
		}
		
		$size = JText::_("ADAG_ANYSIZE");
		if(isset($adparams["width"]) && trim($adparams["width"]) != "" && !in_array("Textad", $supported_ad)){
			$size = $adparams["width"]." x ".$adparams["height"];
		}
		
		$id = $zone->id;
		///////var_dump($zone);die();
		$checked = JHTML::_('grid.id', $i, $id);
		$link = JRoute::_("index.php?option=com_adagency&controller=adagencyZones&task=edit&cid[]=".$id);		
		$published = JHTML::_('grid.published', $zone, $i );

		$canCheckin = $user->authorise('core.manage',     'com_checkin') || $zone->checked_out == $userId || $zone->checked_out == 0;
		$canChange  = $user->authorise('core.edit.state', 'com_adagency.adagencyZones.') && $canCheckin;
		
		$nr_banners = 0;
		if(isset($zones_ads[$id]["total"])){
			$nr_banners = $zones_ads[$id]["total"];
		}
		
		$template_positions = $this->getThePositions();
?>
	<tr class="row<?php echo $k;?>"> 
		<td>
        	<span class="sortable-handler active" style="cursor: move;">
                <i class="icon-menu"></i>
            </span>
            <input type="text" class="width-20 text-area-order " value="<?php echo $zone->ordering; ?>" size="5" name="order[]" style="display:none;">
        </td>
        
        <td>
			<?php echo $checked;?>
			<span class="lbl"></lbl>
		</td>		
		
        <td>
			<?php echo $id;?>
		</td>		
	    
        <td>
	      	<a href="<?php echo $link;?>" ><?php echo $zone->title;?></a>
		</td>		

	    <td>
	       	<a href="<?php echo $link;?>" ><?php echo $published;?></a>
		</td>		
	     	
	    <td>
	       	<?php
				if($this->isJomsocialInstalled() && strpos($zone->position, "js_") !== FALSE){
					echo '<span class="label label-success">'.$zone->position.'</span>';
				}
            	elseif(in_array($zone->position, $template_positions)){
					echo '<span class="label label-success">'.$zone->position.'</span>';
				}
				else{
					echo '<span class="label label-important">'.$zone->position.'</span>';
				}
			?>
		</td>		
		
        <td>
		<?php  
			if (is_null( $zone->pages )) {
				echo JText::_('ZONEPAGENONE');
			} else if ($zone->pages > 0) {
				echo JText::_('ZONEPAGEVARIES');
			} else {
				echo JText::_('ZONEPAGEALL');
			}
		?>
		</td>
	    <td>
			<?php echo $zone->banners." rows x ".$zone->banners_cols." columns";?>
		</td>
        <td>
        	<?php echo implode(", ", $supported_ad); ?>
        </td>
        <td>
        	<?php echo $size; ?>
        </td>
        <td>
        	<?php
            	if(intval($nr_banners) != 0){
			?>
        			<a href="index.php?option=com_adagency&controller=adagencyAds&zone_id=<?php echo $id; ?>"><?php echo $nr_banners; ?></a>
            <?php
            	}
				else{
					echo "0";
				}
			?>
        </td>
	</tr>
<?php 
		$k = 1 - $k;
	endfor;
?>
</tbody>
<?php echo $this->pagination->getListFooter(); ?>
</table>
</div>
</div>
</div>

    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="adagencyZones" />
    <input type="hidden" name="old_limit" value="<?php echo JRequest::getVar("limitstart"); ?>" />
</form>

<div id="light-modal-background" style="display:none;" onclick="javascript:closePopUp('zone-light'); return false;">&nbsp;</div>

<div class="modal alert-light" id="zone-light" style="display:none;">
    <p style="font-size: 17px; font-weight: bold; text-align: justify;"><?php echo JText::_("COM_ADAGENCY_ONE_ZONE"); ?></p>
    <br />
    <br />
    <p class="pagination-centered">
        <form id="prform" name="prform" target="_blank" style="margin:0px; text-align: center;" method="post" action="http://www.ijoomla.com/index.php?option=com_digistore&controller=digistoreCart&task=add&pid[0]=81&cid[0]=81" onsubmit="return prodformsubmit4a60cb04c1341();">
            <input name="qty" value="1" type="hidden" />
            <input name="pid" id="product_id" value="81" type="hidden" />
            <input name="Button" type="submit" class="btn btn-warning" value="Buy Pro" />
        </form>
    </p>
    <br />
    <br />
    <p class="pagination-centered">
        <a href="http://adagency.ijoomla.com/pricing/" style="font-size:18px; text-decoration:underline;" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
    </p>
    
    <a style="background: url(components/com_adagency/images/closebox.png) no-repeat scroll center center transparent; width: 30px; height: 30px; z-index: 70000; opacity: 1; position: absolute; top: -15px; right: -20px;" class="closeDOMWindow" id="close_domwin" href="#" onclick="javascript:closePopUp('zone-light'); return false;" style="font-size:14px;"></a>
</div>