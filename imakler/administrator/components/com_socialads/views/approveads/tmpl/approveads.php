<?php
defined( '_JEXEC' ) or die( ';)' );
JHtml::_('behavior.modal', 'a.modal');

$db=JFactory::getDBO();
$list=$this->approveads;
$model = $this->getModel('approveads');

?>
<script type="text/javascript">
<?php if(JVERSION >= '1.6.0'){ ?>
	Joomla.submitbutton = function(action){

<?php } else { ?>
	function submitbutton( action ) {
<?php } ?>

	if(action=='deleteads')
		{
			if (document.adminForm.boxchecked.value==0){
				alert('<?php echo JText::_("SA_MAKE_SEL");?>');
				return;}

			var r=confirm('<?php echo JText::_("DELETE_AD_CONFIRM");?>');
			if (r==true)
			{
				var aa;
			}
			else return;



		}
	var form = document.adminForm;
	submitform( action );
	return;

 }
</script>
<div class="techjoomla-bootstrap">
	<form action="index.php?option=com_socialads" method="post" name="adminForm" id="adminForm">
		<?php
	// @ sice version 3.0 Jhtmlsidebar for menu
		if(JVERSION>=3.0)
		{
			if (!empty( $this->sidebar))
			{ ?>
				<div id="j-sidebar-container" class="span2">
					<?php echo $this->sidebar; ?>
				</div>
				<div id="j-main-container" class="span10">
			<?php
			}
			else
			{ ?>
				<div id="j-main-container">
			<?php
			}
		}
		?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search sa-text-filter btn-group">
				<input placeholder="<?php echo JText::_("COM_SOCIALADS_PH_MANAGEADS_SEARCH");?>" type="text" name="filter_state" id="filter_state" value="<?php echo $this->lists['filter_state']; ?>" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();" class="btn hasTooltip"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" onclick="document.getElementById('filter_state').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
				if(JVERSION >= 3.0 )
				{
					?>
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
					<?php
				}
				?>
				<?php if(JVERSION < 3.0)
				{

					echo JHtml::_('select.genericlist', $this->sstatus, "search", 'class="ad-status" size="1"
								onchange="document.adminForm.submit();" name="search"',"value", "text", $this->lists['search']);
					echo JHtml::_('select.genericlist', $this->campselect, "search_camp",'class="ad-status" size="1"
						onchange="document.adminForm.submit();" name="search_zone"', "value", "text",$this->lists['search_camp']);

					echo JHtml::_('select.genericlist', $this->status_zone, "search_zone", 'class="ad-status" size="1"
							onchange="document.adminForm.submit();" name="search_zone"',"value", "text", $this->lists['search_zone']);

				} ?>
			</div>
		</div>


		<table class="adminlist table table-striped"  width="100%">
			<thead>
				<th width="2%" align="center" class="title">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('SA_ID'),
										'a.ad_id', $this->lists['order_Dir'], $this->lists['order']); ?></th>

				<th><?php echo JHtml::_( 'grid.sort', JText::_('AD_TITLE'),
										'a.ad_title', $this->lists['order_Dir'], $this->lists['order']); ?></th>

				<th><?php echo JText::_('CAMPAIGN'); ?></th>
				<!--th><?php echo JHtml::_( 'grid.sort', JText::_('CAMPAIGN'),
										'c.campaign', $this->lists['order_Dir'], $this->lists['order']); ?></th-->

				<th><?php echo JText::_('PREVIEW'); ?></th>

				<th><?php echo JHtml::_( 'grid.sort', JText::_('AD_TYPE'),
								'a.ad_payment_type', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort',  JText::_('USERNAME'),
								'a.ad_creator', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('ZONE_NAME'),
								'a.ad_zone', $this->lists['order_Dir'], $this->lists['order']); ?></th>

				<th><?php echo JHtml::_( 'grid.sort', JText::_('APPROVAL_STATUS'),
							'a.ad_approved', $this->lists['order_Dir'], $this->lists['order']); ?></th>

				<th><?php echo JText::_('CLKSNO'); ?></th>
				<th><?php echo JText::_('IMPRNO'); ?></th>
				<th><?php //echo JHtml::tooltip(JText::_('DESC_CLICK_THROUGH_RATIO'), JText::_('CLICK_THROUGH_RATIO'));
				echo JText::_('CLICK_THROUGH_RATIO');
				?></th>
				<th><?php echo JText::_('IGNORENO');?></th>
				<th><?php echo JText::_('IGNORES');?></th>

			</thead>
			<?php
					$j=0;
			if(!empty($list)){
				 foreach($list as $listads) {

				?>
			<tr>
				<td align="center">
						<?php echo JHtml::_('grid.id', $j, $listads->ad_id ); ?>
					</td>
				<td>
						<?php echo $listads->ad_id; ?>
				</td>

				<td>
					<?php if($listads->ad_title == '')
						{
							$ad_title = JText::_('IMGAD');
						}
						else
						{
						 $ad_title = $listads->ad_title;
						 }
						 $link = JRoute::_(JUri::base().'index.php?option=com_socialads&view=buildad&calleradmin=1&adid='.$listads->ad_id);
						 ?>
						<a  href="<?php echo $link; ?>"  >
							<span class="editlinktip hasTip" title="<?php echo JText::_('EDIT_AD');?>" >
							<?php echo $ad_title; //echo JText::_('Add/View Comments'); ?>
							</span>
						</a>
				</td>

				<td>
						<?php echo $listads->campaign; ?>
				</td>

					<td>
						<?php
							$link = JRoute::_('index.php?option=com_socialads&view=lightbox&tmpl=component&layout=default&id='.$listads->ad_id);
						?>
						<a rel="{handler: 'iframe', size: {x: 350, y: 350}}" href="<?php echo $link; ?>" class="modal">
							<span class="editlinktip hasTip" title="<?php echo JText::_('PREVIEW');?>" ><img src="<?php echo JUri::root().'components/com_socialads/images/ad_ preview.png'?>"></span>
						</a>
				</td>

				<td>
						<?php if($listads->ad_alternative== 1){
									echo JText::_('ALT_AD');
						}
						elseif($listads->ad_noexpiry== 1){
									echo JText::_('UNLTD_AD');
						}
						else if($listads->ad_affiliate== 1)
						{
							echo JText::_('AD_TYP_AFFI');
						}
						else{
								if($listads->ad_payment_type== 0){
										echo JText::_('IMPRS');}
								else if($listads->ad_payment_type == 1){
										echo JText::_('CLICKS');}
								else{
										echo JText::_('PERDATE');
										}
						} ?>
				</td>
				<td>
					<?php
					$table   = JUser::getTable();
					$user_id = intval( $listads->ad_creator );
					$creaternm = '';
					if($table->load( $user_id ))
					{
						$creaternm = JFactory::getUser($listads->ad_creator);
					}
	//			 print_r($listads->ad_creator);
					echo (!$creaternm)?JText::_('NO_USER'): $creaternm->username; ?>
				</td>
				<!--Zone Name -->
				<td>

					<?php
					$zone_name='';

					$zone_name=$model->adzonename($listads->ad_id,$listads->ad_zone);
					if($zone_name)
					{
						echo $zone_name;
					}
					else
					{
					$zone_list=$model->adzonelist();
						if($zone_list)
						{

							$zone_ad['0']=JHtml::_('select.option', '0', 'Select');
							$i=1;
							foreach($zone_list as $selected_zone)
							{
								$zname=$selected_zone->zone_name;
								$zid=$selected_zone->id;
								$zone_ad[$i]=JHtml::_('select.option',$zid,$zname);

								$i++;
							}

						}
						echo JHtml::_('select.genericlist', $zone_ad, 'layout_select', 'class="inputbox"
									onChange="selectzone('.$listads->ad_id.',this);"', 'value', 'text','0');

					}
					?>
				</td>

				<!--  -->
				<td>
				<?php
					$whichever = '';
					 switch($listads->ad_approved)
					 {
							case 1 :
								$whichever = JText::_('SA_APPROVE');
							break;
							case 2 :
								$whichever = JText::_('SA_REJEC') ;
							break;

					 }
					//if($listads->ad_approved == 0)
					echo JHtml::_('select.genericlist', $this->status, 'status'.$j, 'class="ad-status" size="1" onChange="selectstatus('.$listads->ad_id.',this);"',"value", "text",$listads->ad_approved);
					 ?>
				</td>
				<td><?php echo $model->getAdtype($listads->ad_id, 1);?></td>
				<td><?php echo $model->getAdtype($listads->ad_id, 0);?></td>
				<td class="managead-td">
						<?php
							$ad_clicks=$model->getAdtype($listads->ad_id, 1);
							$ad_impressions=$model->getAdtype($listads->ad_id, 0);
						if($ad_impressions!=0)
						{
							$ctr=($ad_clicks)/($ad_impressions);
							echo  number_format ($ctr, 4);
						}
						else
							echo  number_format ($ad_clicks, 4)

						?>
				</td>
				<td><?php echo $model->getIgnorecount($listads->ad_id);?></td>
				<td>
						<?php
						$link = JRoute::_('index.php?option=com_socialads&view=ignoreads&tmpl=component&adid='.$listads->ad_id);
						?>
					<a href="<?php echo $link;?>" rel="{handler: 'iframe', size: {x: 800, y: 350}}" class="modal"><?php echo JText::_('VIEW_IGNORE'); ?></a>
				</td>
			</tr>
			<?php
					$j++;
				}
			}
			else{

				echo "<tr><td colspan='14' align=center><div class='center'>".JText::_("NO_DATA_TO_DISPLAY")."</div></td></tr>";

				}?>
			<tr>
				<td colspan="14"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>

		</table>
		<input type="hidden" id='reason' name="reason" value="" />
		<input type="hidden" id='hidid' name="id" value="" />
		<input type="hidden" id='hidstat' name="status" value="" />
		<input type="hidden" id='hidzone' name="zone" value="" />
		<input type="hidden" name="option" value="com_socialads" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="view" value="approveads" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="approveads" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
	<?php
	if (!empty( $this->sidebar))
	{ ?>
		</div>
	<?php
	} ?>
	</form>
</div>
