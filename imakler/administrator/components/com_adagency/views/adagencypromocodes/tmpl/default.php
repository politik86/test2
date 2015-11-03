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
	$n = count ($this->promos);
	
	$document = JFactory::getDocument();
	$document->addStyleSheet("components/com_adagency/assets/css/digistore.css");

$user = JFactory::getUser();
$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_adagency&controller=adagencyPromocodes&task=saveOrderAjax&tmpl=component';
JHtml::_('sortablelist.sortable', 'promocodeList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

?>
<form action="index.php" name="adminForm" id="adminForm" method="post">
	<div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWTREEPROMOCODES'); ?>
				</h2>
            </div>
            
       </div>
	
		<div id="filter-bar" class="row-fluid">            
    			<div class="span12">                
    				<div class="filter-search btn-group pull-left">                    
    					<label for="search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>   
    					<?php
							$promosearch = JRequest::getVar("promosearch", "");
						?>
						<input type="text" id="filter_search" name="promosearch" value="<?php echo trim($promosearch);?>" />                 
    					
    					</div>                                                
    					<div class="btn-group pull-left hidden-phone">                    
    						<button class="btn adag_tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>                    
    						<button class="btn adag_tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>                
    					</div> 
    					
		                <div class="btn-group pull-right hidden-phone">
		                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
		                    <?php echo $this->pagination->getLimitBox(); ?>
		                </div>	       	
    			</div>    
    	</div>    
	
<div class="row-fluid">
<div class="span12">	
<div id="editcell">
<table class="table table-striped table-bordered" id="promocodeList">
	
<thead>
		<th>
        	<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
        </th>
        <th width="5">
			<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
			<span class="lbl"></span>
		</th>
	        <th width="20">
			<?php echo JText::_('VIEWPROMOID');?>
		</th>
		<th>
			<?php echo JText::_('VIEWPROMOTITLE');?>
		</th>

		<th>
			<?php echo JText::_('VIEWPROMOCODE');?>
		</th>
		<th><?php echo JText::_("VIEWPROMOPUBLISHED");?>	
		</th>
		<th>Status</th>
		<th>
			<?php echo JText::_('VIEWPROMOTIMEUSED');?>
		</th>

		<th>
			<?php echo JText::_('VIEWPROMOUSAGESLIST');?>
		</th>
</thead>

<tbody>

<?php 
	JHTML::_("behavior.tooltip");
	for ($i = 0; $i < $n; $i++):
		$promo =& $this->promos[$i];
		$id = $promo->id;

		$checked = JHTML::_('grid.id', $i, $id);
		$link = JRoute::_("index.php?option=com_adagency&controller=adagencyPromocodes&task=edit&cid[]=".$id);
		
		$published = JHTML::_('grid.published', $promo->published, $i);
		$status = $this->publishAndExpiryHelper($img, $alt, $times, $status, $promo->codestart, $promo->codeend, $promo->published, $promo->codelimit, $promo->used);
		
		$canCheckin = $user->authorise('core.manage',     'com_checkin') || $promo->checked_out == $userId || $promo->checked_out == 0;
		$canChange  = $user->authorise('core.edit.state', 'com_adagency.adagencyAds.') && $canCheckin;

?>
	<tr class="row<?php echo $k;?>"> 
		<td>
			<span class="sortable-handler active" style="cursor: move;">
                <i class="icon-menu"></i>
            </span>
            <input type="text" class="width-20 text-area-order " value="<?php echo $promo->ordering; ?>" size="5" name="order[]" style="display:none;">
        </td>
        <td>
	    	<?php echo $checked;?>
	    	<span class="lbl"></span>
		</td>		

	     	<td>
	     	    	<?php echo $id;?>
		</td>		
	     	<td>
	     	    	<a href="<?php echo $link;?>" ><?php echo $promo->title;?></a>
		</td>		

	     	<td>
	     	    	<a href="<?php echo $link;?>" ><?php echo $promo->code;?></a>
		</td>		
		<td>
      		<?php echo $published; ?>
		</td>		
		<td>
                    <?php 
		
		echo $status;
?>
		</td>
	     	<td>
	     	    	<?php echo ($promo->used);?>
		</td>		

	     	<td>
	     	    	<?php echo $promo->codelimit>0?($promo->codelimit - $promo->used):JText::_("DS_UNLIMITED");?>
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
<input type="hidden" name="controller" value="adagencyPromocodes" />
<input type="hidden" name="old_limit" value="<?php echo JRequest::getVar("limitstart"); ?>" />
</form>
