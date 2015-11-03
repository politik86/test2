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
$n = count ($this->ads);
$lists = $this->lists;
$params = $this->params;
require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
$document =JFactory::getDocument();
$document->addScript(JURI::root()."components/com_adagency/includes/js/graybox.js");
$document->addStyleSheet('components/com_adagency/css/joomla16.css');

$user = JFactory::getUser();
$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_adagency&controller=adagencyAds&task=saveOrderAjax&tmpl=component';
JHtml::_('sortablelist.sortable', 'adslist', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
$helperView = new adagencyAdminViewadagencyAds();
$helper = new adagencyAdminHelper();
$helperModel = new adagencyAdminModeladagencyAds();
$all_advertisers = $this->getAllAdvertisers();
?>

<?php require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."includes".DS."js".DS."ads.php"); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton){
		if(pressbutton == "add" || pressbutton == "copy"){
			if(eval(document.getElementById("cb0")) && eval(document.getElementById("cb1")) && eval(document.getElementById("cb2")) && eval(document.getElementById("cb3")) && eval(document.getElementById("cb4"))){
				document.getElementById("ads-light").style.display = "";
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
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWTREEADS'); ?>
				</h2>
            </div>
      </div>
	
		<div id="filter-bar" class="row-fluid">            
    			<div class="span12">                
    				<div class="filter-search btn-group pull-left">                    
    					<label for="search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>
    					  <input type="text" id="filter_search" name="search_text" value="<?php if(isset($_SESSION['search_text'])) echo $_SESSION['search_text'];?>" />                    
    					
    					</div>                                                
    					<div class="btn-group pull-left hidden-phone">                    
    						<button class="btn adag_tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>                    
    						<button class="btn adag_tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>                
    					</div> 
    					<div class="btn-group pull-right hidden-phone">	                
    						<?php echo JText::_('VIEWAD_STATUS_SEARCH');?>
				            <select name="status_select" onChange="document.adminForm.submit()">
				                 <option value="YA" <?php if ( (!isset($_SESSION['status_select'])) || (isset($_SESSION['status_select'])|| $_SESSION['status_select'] == 'YA') ) echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_SEARCH_SELECT');?></option>
				                <option value="Y" <?php if (isset($_SESSION['status_select']) && $_SESSION['status_select'] == 'Y') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_STATUS_SEARCH_APPROVED');?></option>
				                <option value="N" <?php if (isset($_SESSION['status_select']) && $_SESSION['status_select'] == 'N') echo ' selected="selected" ';?>><?php echo JText::_('VIEWAD_STATUS_SEARCH_DISSAPPROVED');?></option>
				                <option value="P" <?php if (isset($_SESSION['status_select']) && $_SESSION['status_select'] == 'P') echo ' selected="selected" ';?>><?php echo JText::_('ADAG_PENDING');?></option>
				            </select>        
    					</div>	      
		                <div class="btn-group pull-right hidden-phone">
		                     <?php echo JText::_('VIEWAD_SEARCH_TYPE');?>
					            <select name="type_select" onChange="document.adminForm.submit()">
					                <option value="all" <?php if ( (!isset($_SESSION['type_select'])) || (isset($_SESSION['type_select']) && $_SESSION['type_select'] == 'all') ) echo ' selected="selected" ';?>>all</option>
					                <option value="Standard" <?php if (isset($_SESSION['type_select']) && $_SESSION['type_select'] == 'Standard') echo ' selected="selected" ';?>><?php echo JText::_('VIEWTREEADDSTANDARD');?></option>
					                <option value="Advanced"  <?php if (isset($_SESSION['type_select']) && $_SESSION['type_select'] == 'Advanced') echo ' selected="selected" ';?>><?php echo JText::_('VIEWTREEADDADCODE');?></option>
					                <option value="Popup" <?php if (isset($_SESSION['type_select']) && $_SESSION['type_select'] == 'Popup') echo ' selected="selected" ';?>><?php echo JText::_('VIEWTREEADDPOPUP');?></option>
					                <option value="Flash" <?php if (isset($_SESSION['type_select']) && $_SESSION['type_select'] == 'Flash') echo ' selected="selected" ';?>><?php echo JText::_('VIEWTREEADDFLASH');?></option>
					                <option value="TextLink" <?php if (isset($_SESSION['type_select']) && $_SESSION['type_select'] == 'TextLink') echo ' selected="selected" ';?>><?php echo JText::_('VIEWTREEADDTEXTLINK');?></option>
					                <option value="Transition" <?php if (isset($_SESSION['type_select']) && $_SESSION['type_select'] == 'Transition') echo ' selected="selected" ';?>><?php echo JText::_('VIEWTREEADDTRANSITION');?></option>
					                <option value="Floating" <?php if (isset($_SESSION['type_select']) && $_SESSION['type_select'] == 'Floating') echo ' selected="selected" ';?>><?php echo JText::_('VIEWTREEADDFLOATING');?></option>
                                    <option value="Jomsocial" disabled="disabled" <?php if (isset($_SESSION['type_select']) && $_SESSION['type_select'] == 'Jomsocial') echo ' selected="selected" ';?>><?php echo JText::_('VIEWTREEADDJOMSOCIAL');?></option>
					            </select>
					       
		                </div>
                        <?php
                        	$advertisers_select = JRequest::getVar("advertisers_select", "0");
						?>
                        <div class="btn-group pull-right hidden-phone">
                            <select name="advertisers_select" onChange="document.adminForm.submit();">
                                <option value="0"><?php echo JText::_("AD_AGENCY_ALL_ADV"); ?></option>
                            	<?php
									if(isset($all_advertisers) && count($all_advertisers) > 0){
										foreach($all_advertisers as $key=>$value){
											$selected = "";
											if($value["aid"] == $advertisers_select){
												$selected = 'selected="selected"';
											}
											echo '<option value="'.$value["aid"].'" '.$selected.'>'.$value["name"].'</option>';
										}
									}
								?>
                            </select>
		                </div>
		                <div class="btn-group pull-right hidden-phone">
		                     <?php echo JText::_('VIEWAD_SEARCH_ZONE');?>
           					 <?php echo $lists['zone_id']; ?>
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
<table class="table table-striped table-bordered" id="adslist">		
<thead>

        <th >
        	<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
        </th>
        <th>
            <input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
            <span class="lbl"></lbl>
        </th>
        <th>
            <?php echo JText::_('ADAG_UNIV_ID');?>
        </th>
             <th>
            <?php echo JText::_('VIEWADTITLE');?>
        </th>
      
        <th>
            <?php echo JText::_('VIEWADPUBLISHED');?>
        </th>
        <th>
            <?php echo JText::_('VIEWADADVERTISER');?>
        </th>
        <th><?php echo JText::_("VIEWADTYPE");?>
        </th>
        <th>
            <?php echo JText::_('VIEWADSIZE');?>
        </th>
        <!--<th>
            <?php echo JText::_('VIEWADZONE');?>
        </th>-->
        <th>
            <?php echo JText::_('ADAG_CREATED');?>
        </th>
        <th>
            <?php echo JText::_('VIEWADPREVIEW');?>
        </th>
        <th>
            <?php echo JText::_('VIEWADCAMPAIGNS');?>
        </th>
        <th>
            <?php echo JText::_('VIEWADIMPRESSIONS');?>
        </th>
        <th>
            <?php echo JText::_('VIEWADCLICKS');?>
        </th>
        <th>
            <?php echo JText::_('VIEWADCRT');?>
        </th>
    </thead>

    <tbody>

    <?php
    for ($i = 0; $i < $n; $i++):
        $ads = $this->ads[$i];
        $id = $ads->id;
        $checked = JHTML::_('grid.id', $i, $id);
        if ($ads->zone > 0) $act=''; else $act='&act=new';
        $mediatype = $ads->media_type;
        switch ($mediatype) {
            case 'Advanced':
            $link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=".intval($id).$act);
            break;
            case 'Standard':
            $link = JRoute::_("index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]=".intval($id).$act);
            break;
            case 'Flash':
            $link = JRoute::_("index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]=".intval($id).$act);
            break;
            case 'Transition':
            $link = JRoute::_("index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid[]=".intval($id).$act);
            break;
            case 'Floating':
            $link = JRoute::_("index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid[]=".intval($id).$act);
            break;
            case 'Popup':
            $link = JRoute::_("index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid[]=".intval($id).$act);
            break;
            case 'TextLink':
            $link = JRoute::_("index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=".intval($id).$act);
            break;
			case 'Jomsocial':
            $link = JRoute::_("index.php?option=com_adagency&controller=adagencyJomsocial&task=edit&cid[]=".intval($id).$act);
            break;
        }

        $zonelink = JRoute::_("index.php?option=com_adagency&controller=adagencyZones&task=edit&cid[]=".intval($ads->zone));
        $published = $helperView->approve($ads, $i);
        if (!isset($ads->impressions)) $ads->impressions=0;
        if (!isset($ads->click)) $ads->click=0;
        if (!isset($ads->click_rate)) $ads->click_rate=0;

        $db = JFactory::getDBO();
        $sql = "SELECT `user_id` FROM `#__ad_agency_advertis` WHERE `aid`='".intval($ads->advertiser_id2)."'";
        $db->setQuery($sql);
        $adv_user_id = $db->loadResult();

        $customerlink = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=".intval($adv_user_id));
		
		$canCheckin = $user->authorise('core.manage',     'com_checkin') || $ads->checked_out == $userId || $ads->checked_out == 0;
		$canChange  = $user->authorise('core.edit.state', 'com_adagency.adagencyAds.') && $canCheckin;

?>
    <tr class="row<?php echo $k;?>">
        <td>
			<span class="sortable-handler active" style="cursor: move;">
                <i class="icon-menu"></i>
            </span>
            <input type="text" class="width-20 text-area-order " value="<?php echo $ads->ordering; ?>" size="5" name="order[]" style="display:none;">
        </td>
        <td>
            <?php echo $checked;?>
            <span class="lbl"></lbl>
        </td>
        <td>
            <?php echo $ads->id;?>
        </td>
        <td>
            <a href="<?php echo $link;?>" ><?php echo $ads->title;?></a>
        </td>
      
        <td>
            <?php echo $published;?>
        </td>
        <td>
            <a href="<?php echo $customerlink;?>" ><?php echo $ads->advertiser;?></a>
        </td>
        <td>
        <?php
            switch($ads->media_type) {
                case 'Standard':
                    echo JText::_('VIEWTREEADDSTANDARD');
                    break;
                case 'Advanced':
                    echo JText::_('VIEWTREEADDADCODE');
                    break;
                case 'Popup':
                    echo JText::_('VIEWTREEADDPOPUP');
                    break;
                case 'Flash':
                    echo JText::_('VIEWTREEADDFLASH');
                    break;
                case 'TextLink':
                    echo JText::_('VIEWTREEADDTEXTLINK');
                    break;
                case 'Transition':
                    echo JText::_('VIEWTREEADDTRANSITION');
                    break;
                case 'Floating':
                    echo JText::_('VIEWTREEADDFLOATING');
                    break;
				case 'Jomsocial':
                    echo JText::_('VIEWTREEADDJOMSOCIAL');
                    break;
            }
        ?>
        </td>
        <td><?php
            if(!$ads->width || !$ads->height) { echo "-"; } elseif($ads->media_type != 'TextLink') { echo "{$ads->width}x{$ads->height}"; } else {
                echo "-";
            }
        ?></td>
        <!--<td>
            <?php if ($ads->zone > 0) { ?> <a href="<?php echo $zonelink;?>"><?php echo $ads->zone_name;?></a> <?php } else { ?> <span style="font-weight:bold;">No Zone</span> <?php } ?>
        </td>-->
        <td>
            <?php echo $helper->formatime($ads->created, $params);?>
        </td>
        <td>
            <a class="modal2" href="<?php echo JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&adid=".intval($ads->id)."&format=raw";?>"><?php echo JText::_('VIEWADPREVIEW');?></a>
        </td>
        <td>
        	<?php
            	$nr_campaigns = $helperModel->getCampaignCount($ads->id);
				if(intval($nr_campaigns) > 0){
			?>
            		<a href="index.php?option=com_adagency&controller=adagencyCampaigns&id=<?php echo $ads->id;?>"><?php echo intval($nr_campaigns); ?></a>
            <?php
            	}
				else{
					echo "0";
				}
			?>
        </td>
        <td>
            <?php echo $ads->impressions;?>
        </td>
        <td>
            <?php echo $ads->click;?>
        </td>
        <td>
            <?php echo $ads->click_rate;?>
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

<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>

<input type="hidden" name="option" value="com_adagency" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="adagencyAds" />
<input type="hidden" name="old_limit" value="<?php echo JRequest::getVar("limitstart"); ?>" />
</form>

<?php
	$display = JRequest::getVar("display", "0");
	$format = "none";
	if($display == 1){
		$format = "block";
	}
?>

<div id="light-modal-background" style="display:none;" onclick="javascript:closePopUp('ads-light'); return false;">&nbsp;</div>

<div class="modal alert-light" id="ads-light" style="display:<?php echo $format; ?>;">
    <p style="font-size: 17px; font-weight: bold; text-align: justify;"><?php echo JText::_("COM_ADAGENCY_ONE_ADS"); ?></p>
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
    
    <a style="background: url(components/com_adagency/images/closebox.png) no-repeat scroll center center transparent; width: 30px; height: 30px; z-index: 70000; opacity: 1; position: absolute; top: -15px; right: -20px;" class="closeDOMWindow" id="close_domwin" href="#" onclick="javascript:closePopUp('ads-light'); return false;" style="font-size:14px;"></a>
    
</div>