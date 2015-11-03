<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
		JHTML::_('behavior.tooltip');
		JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();

		JToolBarHelper::deleteList('', 'deletecoupon');
		JToolBarHelper::addNew($task = 'add', $alt = JText::_('SA_NEW'));

		$input=JFactory::getApplication()->input;
		$cid		= $input->get( 'cid','','ARRAY');

?>
<script type="text/javascript">
<?php if(JVERSION >= '1.6.0'){ ?>

		Joomla.submitbutton = function(action)
		{
			if(action == "deletecoupon")
				if(!confirm('<?php echo JText::_("COM_SA_CONFIRM_DELETE_COUPON"); ?>'))
				{
					return false;
				}
		<?php
		}
		else
		{ ?>
		function submitbutton( action )
		{
<?php 	} ?>

	var form = document.adminForm;
	submitform( action );
	return;

 }
</script>
<div class="techjoomla-bootstrap">
	<form action="" method="post" name="adminForm" id="adminForm">
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
			<div class="filter-search sa-text-filter btn-group ">
				<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" placeholder="<?php echo JText::_("COM_SOCIALADS_PH_COUPON_SEARCH");?>" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();" class="btn hasTooltip"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" onclick="document.getElementById('search').value='';this.form.getElementById('filter_type').value='0';this.form.getElementById('filter_logged').value='0';this.form.submit();"><i class="icon-remove"></i></button>
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

				<th width="2%" align="center" class="title">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th width="2%" class="title" nowrap="nowrap" align="center">
					<?php echo JHTML::_('grid.sort',  JText::_( 'C_ID'), 'id',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th class="title" align="left" width="10%" align="center">
					<?php echo JHTML::_('grid.sort',   JText::_( 'C_NAM'), 'name',   $this->lists['order_Dir'],   $this->lists['order'] );

					 ?>
				</th>
				<th width="10%" class="title" align="center">
					<?php echo JHTML::_('grid.sort',   JText::_( 'C_PUB'), 'published',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="left">
					<?php echo JHTML::_('grid.sort',   JText::_( 'C_COD'), 'code',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="center">
					<?php echo JHTML::_('grid.sort',   JText::_( 'C_VAL'), 'value',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="left">
					<?php echo JHTML::_('grid.sort',   JText::_( 'C_TYP'), 'val_type',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>



				<th width="10%" class="title" align="center">
					<?php echo JHTML::_('grid.sort',   JText::_( 'M_USE'), 'max_use',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="left">
					<?php echo JHTML::_('grid.sort',   JText::_( 'DET'), 'description',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="center">
					<?php echo JHTML::_('grid.sort',   JText::_( 'C_VALF'), 'from_date',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title" align="center">
					<?php echo JHTML::_('grid.sort',   JText::_( 'C_EXP'), 'exp_date',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>

			</tr>
		</thead>

		<tbody>
		<?php if(empty($this->coupons))
		{?>
		<tr class="<?php echo 'row$k'; ?>">
			<td colspan="11">
				<div class="well" >
					<div class="alert alert-error">
						<span ><?php echo JText::_('SA_COUPON_NOT_FPUND'); ?> </span>
					</div>
				</div>
			</td>
		</tr>
		<?php
		}
		else
		{
			$k = 0;

			for ($i=0, $n=count( $this->coupons ); $i < $n; $i++)
			{
			$zone_type='';

				$row 	= $this->coupons[$i];
				$published 	= JHTML::_('jgrid.published', $row->published, $i );

				$link 	= 'index.php?option=com_socialads&amp;view=managecoupon&amp;task=edit&amp;layout=form&amp;cid[]='. $row->id. '';


			?>
			<tr class="<?php echo 'row$k'; ?>">

				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td align="center">
					<?php echo $row->id; ?>
				</td>
				<td align="left">
				<a href="<?php echo $link; ?>">
						<?php echo $row->name; ?></a>
				</td>
				<td align="center">
					<?php echo $published ?>
				</td>
			<td align="left">
					<?php echo stripslashes($row->code); ?>
				</td>
				<td align="center">
					<?php echo $row->value ?>
				</td>
				<td align="left">
					<?php  if($row->val_type==0){echo JText::_( "C_FLAT");}else{echo JText::_( "C_PER");} ?>
				</td>


				<td align="center">
					<?php echo $row->max_use ?>
				</td>
				<td align="left">
					<?php echo $row->description ?>
				</td>
				<td align="center">
					<?php
					if($row->from_date!='0000-00-00 00:00:00')
					{
					$from_date=date("Y-m-d",strtotime($row->from_date));
					 echo $from_date;
					 }
					  else
					 echo "-";
					 ?>
				</td>
				<td align="center">
					<?php
					if($row->exp_date!='0000-00-00 00:00:00')
					{
						$exp_date=date("Y-m-d",strtotime($row->exp_date));
					 	echo $exp_date ;

					 }
					 else
					 echo "-";

					 ?>
				</td>




			</tr>
			<?php
				$k = 1 - $k;
				}

		}	?>

		</tbody>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
				</tfoot>
	</table>

	<input type="hidden" name="option" value="com_socialads" />
	<input type="hidden" name="view" value="managecoupon" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
