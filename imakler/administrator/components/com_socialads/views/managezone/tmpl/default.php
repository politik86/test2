<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
JHTML::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
if(version_compare(JVERSION, '3.0', 'gte')) {
	JHtml::_('formbehavior.chosen', 'select');
}
JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::editList();

JToolBarHelper::deleteList('', 'deletezone');
JToolBarHelper::addNew($task = 'add', $alt = JText::_('SA_NEW'));

$input=JFactory::getApplication()->input;
$cid		= $input->get( 'cid','','ARRAY');
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");


?>
<script type="text/javascript">
<?php if(JVERSION >= '1.6.0'){ ?>
	Joomla.submitbutton = function(action){
<?php } else { ?>
	function submitbutton( action ) {
<?php } ?>
	var form = document.adminForm;
	var cblength=<?php echo count( $this->zones ); ?>;
	var cnt=0;
	var i=0;
	if( action == 'deletezone')
	{
		for(i=0;i<cblength;i++)
		{
			if (document.getElementById("cb"+i).checked==true)
			{
				var checked_cid=document.getElementById("cb"+i).value;
				cnt++;

			}
		}
		if(parseInt(cnt)>1)
		{
			//alert(action);return;
			var con=confirm("<?php echo JText::_('COM_SOCIALADS_ZONE_DEL_ORPHAN_SURE_MSG'); ?>");
			if(con==true)
			<?php if(JVERSION >= '1.6.0') { ?>
				Joomla.submitform(action);
			<?php } else {?>
				submitform( action );
			<?php } ?>
			return;
		}
		else
		{
			for(i=0;i<cblength;i++)
			{
				if (document.getElementById("cb"+i).checked==true)
				{
				var checked_cid=document.getElementById("cb"+i).value;

				window.addEvent('domready', function(){
				var url = "index.php?option=com_socialads&task=getZonead&controller=managezone&selzoneid="+checked_cid;
					<?php if(JVERSION >= '1.6.0') { ?>
					var a = new Request({url:url,
					<?php } else {?>
							new Ajax(url, {
					<?php } ?>

					method: 'get',
					onComplete: function(response) {
						if(parseInt(response))
						{
							var con=confirm("<?php echo JText::_('COM_SOCIALADS_ZONE_DEL_ORPHAN_SURE_MSG'); ?>");
							if(con==true)
							<?php if(JVERSION >= '1.6.0') { ?>
							Joomla.submitform(action);
							<?php } else {?>
							submitform( action );
							<?php } ?>
							return;
						}
						else
						{
							var con=confirm("<?php echo JText::_('COM_SOCIALADS_ZONE_DEL_SURE_MSG'); ?>");
							if(con==true)
						<?php if(JVERSION >= '1.6.0') { ?>
							Joomla.submitform(action);
						<?php } else {?>
							submitform( action );
						<?php } ?>
							return;
						}
					}
					<?php if(JVERSION >= '1.6.0') { ?>
							}).send();
					<?php } else {?>
							}).request();
					<?php } ?>
				});
				}
			}
		}
	}
	else if(action == 'unpublish')
	{
		cnt=0;
		for(i=0;i<cblength;i++)
		{
			if (document.getElementById("cb"+i).checked==true)
			{
				var checked_cid=document.getElementById("cb"+i).value;
				var no_ad=document.getElementById("no_of_ads"+i).value;

				if(parseInt(no_ad))
				{
					var con=confirm("After unpublishing some Ads Uder this zone will become uncompatible.Are you sure you want to Unpublish Zone?");
					if(con==true)
						<?php if(JVERSION >= '1.6.0') { ?>
							Joomla.submitform(action);
						<?php } else {?>
							submitform( action );
						<?php } ?>
					return;
				}
				else
				{
					var con=confirm("Are you sure you want to Unpublish Zone?");
					if(con==true)
					<?php if(JVERSION >= '1.6.0') { ?>
						Joomla.submitform(action);
					<?php } else {?>
						submitform( action );
					<?php } ?>
					return;
				}
				cnt++;
			}
		}
	}
	else
	{

		<?php if(JVERSION >= '1.6.0') { ?>
			Joomla.submitform(action);
		<?php } else {?>
			submitform( action );
		<?php } ?>
		return;
	}
 }
</script>
<div class="techjoomla-bootstrap">
<form action="index.php?option=com_socialads" method="post" name="adminForm" id="adminForm">
	<?php
	if(JVERSION>=3.0):
		 if (!empty( $this->sidebar)) : ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
		<?php else : ?>
			<div id="j-main-container">
		<?php endif;
	endif;
	?>
		<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search sa-text-filter btn-group">
					<input placeholder="<?php echo JText::_("COM_SOCIALADS_PH_ZONE_SEARCH");?>" type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="hasTooltip" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();" class="btn hasTooltip" title="" ><i class="icon-search"></i></button>
					<button type="button" class="btn hasTooltip" title="" onclick="document.getElementById('search').value='';this.form.submit();" ><i class="icon-remove"></i></button>
				</div>

			<?php
			if(JVERSION >= 3.0 )
			{
				?>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<?php
			}
			?>
			</div>

	<table class="adminlist table table-striped" cellpadding="1">
		<thead>
			<tr>
			<!--extra code -->
				<th width="2%" class="title">

				</th>
				<th width="2%" align="center" class="title">

				</th>
				<th class="title" align="left" width="8%">

				</th>
				<th width="5%" class="title" >

				</th>
				<th width="6%" class="title">

				</th>
				<th width="6%" class="title" >

				</th>

				<th width="6%" class="title">

				</th>
				<th width="6%" class="title">

				</th>
				<th width="8%" colspan="2" >
					<a href="" onclick="return false" style="cursor:auto"><?php echo JText::_( 'Z_MAX_CHAR'); ?></a>
				</th>
				<th width="8%" colspan="2" class="title">
					<a href="" onclick="return false" style="cursor:auto"><?php echo JText::_( 'Z_IMG_DIM'); ?></a>
				</th>
				<?php if($socialads_config['zone_pricing']){ ?>
				<th width="9%" colspan="3" align="center">
					<a href="" onclick="return false" style="cursor:auto"><?php echo JText::_( 'Z_PRI'); ?></a>
				</th>
				<?php } ?>
				<!--th width="10%" class="title">
					<?php echo JHTML::_('grid.sort',   'Number Of Ads', 'num_ads',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th-->

				<th width="2%" class="title" nowrap="nowrap">

				</th>
			</tr>
			<tr>
			<!-- -->
				<th width="2%" class="title">
					<?php echo JText::_( 'AD_NUM' ); ?>
				</th>
				<th width="2%" align="center" class="title">
					<?php
					if(version_compare(JVERSION, '3.0', 'gte')) {
						echo JHtml::_('grid.checkall');
					}
					else{
					?>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					<?php } ?>
				</th>
				<th class="title" align="left" width="8%">
					<?php echo JHTML::_('grid.sort',    JText::_( 'ZON_NAM' ), 'zone_name', $this->lists['order_Dir'], $this->lists['order'] );
					 ?>
				</th>
				<th width="5%" class="title" >
					<?php echo JHTML::_('grid.sort',   JText::_( 'ZON_PUB' ) , 'published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				<th width="6%" class="title">
					<?php echo JHTML::_('grid.sort',   JText::_( 'AD_TYP' ), 'ad_type',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<th width="6%" class="title" >
					<?php echo JHTML::_('grid.sort',   JText::_( 'ZON_ORI' ), 'zone_type',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<th width="6%" class="title">
					<?php echo JHTML::_('grid.sort',    JText::_( 'ZON_LAY' ), 'layout',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<th width="4%" class="title">
					<a href="" onclick="return false" style="cursor:auto"><?php echo  JText::_( 'AD_NOS' ); ?></a>
				</th>
				<th width="4%" class="title">
					<?php echo JHTML::_('grid.sort',    JText::_( 'Z_TITLE' ), 'max_title',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<th width="4%" class="title">
					<?php echo JHTML::_('grid.sort',    JText::_( 'Z_DESC' ), 'max_des',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<th width="4%" class="title">
					<?php echo JHTML::_('grid.sort',    JText::_( 'Z_WID' ), 'img_width',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<th width="4%" class="title">
					<?php echo JHTML::_('grid.sort',    JText::_( 'Z_HT' ), 'img_height',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<?php if($socialads_config['zone_pricing']){ ?>
				<th width="3%" class="title">
					<?php echo JHTML::_('grid.sort',    JText::_( 'Z_PPC' ), 'per_click',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<th width="3%" class="title">
					<?php echo JHTML::_('grid.sort',    JText::_( 'Z_PPI' ), 'per_imp',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<th width="3%" class="title">
					<?php echo JHTML::_('grid.sort',    JText::_( 'Z_PPD' ), 'per_day',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
				<?php } ?>
				<th width="2%" class="title" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',    JText::_( 'Z_ID' ), 'id',  $this->lists['order_Dir'],  $this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>

		<tbody>
		<?php
			$k = 0;

			for ($i=0, $n=count( $this->zones ); $i < $n; $i++)
			{
			$zone_type='';

				$row 	= $this->zones[$i];
				$published 	= JHTML::_('jgrid.published', $row->published, $i );
				$model=$this->getModel('managezone');
				$adcount=0;
				$adcount=$model->getZoneaddatacount($row->id);
				$link 	= 'index.php?option=com_socialads&amp;view=managezone&amp;task=edit&amp;layout=form&amp;cid[]='. $row->id. '&amp;adcnt='.$adcount;

			?>
			<tr class="<?php echo 'row$k'; ?>" >
				<td >
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<?php echo JHtml::_('grid.id', $i, $row->id ); ?>
				</td>
				<td >
				<a href="<?php echo $link; ?>">
				<?php echo $row->zone_name; ?></a>
				<?php if(!in_array($row->id,$this->modules)) {?>
						<a href="index.php?option=com_modules&filter_module=mod_socialads" target="_blank"><span class="" ><img alt="Missing" title="<?php echo JText::_('MOD_MIS'); ?>" src="<?php echo JURI::base()?>components/com_socialads/images/missing.png"></span></a>
					<?php } ?>
				</td>
			<td align="center">
				<?php echo $published;?>
			</td>
			<td>
					<?php

					$row->ad_type = str_replace('||',',',$row->ad_type);
					$row->ad_type = str_replace('|','',$row->ad_type);
					$ad_type= explode(',',$row->ad_type);
					foreach($ad_type as $key=>$value)
					{
						switch($value)
						{
						case 'text_img'	: $ad_type[$key]=Jtext::_('AD_TYP_TXT_IMG');
										break;
						case 'img'		: $ad_type[$key]=Jtext::_('AD_TYP_IMG');
										break;
						case 'text'		: $ad_type[$key]=Jtext::_('AD_TYP_TXT');
										break;
						case 'affiliate': $ad_type[$key]=Jtext::_('AD_TYP_AFFI');
										break;
						}
					}
					$ad_type=implode(',',$ad_type);
					echo $ad_type;

					?>
				</td>
				<td>
					<?php

					if($row->zone_type=="1")
					$zone_type=JText::_("Z_HORI");
					else if($row->zone_type=="2")
					$zone_type=JText::_("Z_VERTI");
					echo $zone_type;
					?>
				</td>
				<td>
					<?php echo str_replace('|',',',$row->layout); ?>
				</td>
				<td align="center">
					<?php

						echo $adcount;
						$adnm="no_of_ads".$i;
					?>
					<input type="hidden" name="<?php echo $adnm; ?>" id="<?php echo $adnm; ?>" value="<?php echo $adcount; ?>">
				</td>
				<td align="center">
					<?php echo $row->max_title; ?>
				</td>
				<td align="center">
					<?php echo $row->max_des; ?>
				</td>
				<td align="center">
					<?php echo $row->img_width; ?>
				</td>
				<td align="center">
					<?php echo $row->img_height; ?>
				</td>
				<?php if($socialads_config['zone_pricing']){ ?>
				<td align="center">
					<?php echo $row->per_click; ?>
				</td>
				<td align="center">
					<?php echo $row->per_imp; ?>
				</td>
				<td align="center">
					<?php echo $row->per_day; ?>
				</td>
				<?php } ?>
				<!--td>
					<?php echo $row->num_ads; ?>
				</td-->


				<td align="right">
					<?php echo $row->id; ?>
				</td>

			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="16">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="option" value="com_socialads" />
	<input type="hidden" name="view" value="managezone" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
