<?php
defined( '_JEXEC' ) or die( ';)' );
JHtml::_('behavior.tooltip');
$db=JFactory::getDBO();
$result=$this->adorders;
$totalamount=0;
?>
<div class="techjoomla-bootstrap">
	<form action="" name="adminForm" id="adminForm" class="form-validate" method="post">
		<?php
		// @ sice version 3.0 Jhtmlsidebar for menu
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
				<input placeholder="<?php echo JText::_("COM_SOCIALADS_PH_ORDERS_SEARCH");?>" type="text" name="search_list" id="search_list" value="<?php echo htmlspecialchars($this->lists['search_list']);?>" class="hasTooltip" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();" class="btn hasTooltip" title="" ><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" onclick="document.getElementById('search_list').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>

			<div class="btn-group pull-right">
				<?php
				if(JVERSION >= 3.0 )
				{
					?>
						<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
						<?php echo $this->pagination->getLimitBox(); ?>
					<?php
				}
				if(JVERSION < 3.0)
				{
					echo JHtml::_('select.genericlist', $this->pay, "search_pay", 'class="ad-status" size="1" onchange="document.adminForm.submit();" name="search_pay"',"value", "text", $this->lists['search_pay']);

					echo JHtml::_('select.genericlist', $this->sstatus, "search_select", 'class="ad-status" size="1" onchange="document.adminForm.submit();" name="search_select"',"value", "text",JString::strtoupper($this->lists['search_select']));

					echo JHtml::_('select.genericlist', $this->sstatus_gateway, "search_gateway", 'class="ad-status" size="1" onchange="document.adminForm.submit();" name="search_gateway"',"value", "text", $this->lists['search_gateway']);
				}
				?>
			</div>
		</div>
	<table class="adminlist table table-striped">
			<thead>
				<th width="5%"><?php echo JHtml::_( 'grid.sort', JText::_('ORDER_ID'),
							'id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th width="5%"><?php echo JHtml::_( 'grid.sort', JText::_('AD_ID'),
							'ad_id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('AD_TITLE'),
							'ad_title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('CREDITS'),
							'ad_credits_qty', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('AD_TYPE'),
							'ad_payment_type', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('GETWAY'),
							'processor', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('USERNAME'),
							'ad_creator', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('ORDER_DATE'),
							'cdate', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('AMOUNT'),
							'ad_amount', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', JText::_('PAYMENT_STATUS'),
					'status', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			</thead>


			<?php
			$k=0;
			if(!empty($result)){
			foreach($result as $adorders) { ?>
			<tr>
				<td>
					<?php echo $adorders->id; ?>
				</td>
				<td>
					<?php if($adorders->ad_id=='')
					{
						echo "--";
					}
					else
					{
							echo $adorders->ad_id;
					}
					?>
				</td>
				<td>
				<?php if($adorders->ad_title == '')
						{
							echo "--";
						}
						else
						{
						 echo $adorders->ad_title;
						 }
				?>

				</td>
				<td>
					<?php
						if($adorders->ad_payment_type == 2){ ?>
							<img src="<?php echo JUri::root().'/components/com_socialads/images/start_date.png' ?>">
							<?php echo $adorders->ad_startdate; ?>
							<br/><img src="<?php echo JUri::root().'/components/com_socialads/images/end_date.png' ?>">
							<?php echo $adorders->ad_enddate;
						}
						else
							echo $adorders->ad_credits_qty; ?>
				</td>
				<td>
					<?php

						if($adorders->ad_payment_type=='') {
							echo "--";
						}
						else if($adorders->ad_payment_type==0) {
							echo JText::_('IMPRS');
						}

						else if($adorders->ad_payment_type== 1) {
							echo JText::_('CLICKS');
						}
						else {
								echo JText::_('PERDATE');
						}
					 ?>
				</td>
				<td>
					<?php

				if($adorders->processor)
				{
					echo $adorders->processor;
				}
				elseif($adorders->ad_amount == 0 && !empty($adorders->ad_coupon) )
				{
					echo JText::_('COUPON');
				}?>
				</td>

				<td>
					<?php
					$table   = JUser::getTable();
					$user_id = intval( $adorders->payee_id );
					$creaternm = '';
					if($table->load( $user_id ))
					{
						$creaternm = JFactory::getUser($adorders->payee_id);
					}
	//			 print_r($adorders->ad_creator);
					echo (!$creaternm)?JText::_('NO_USER'): $creaternm->username; ?>
				</td>
				<td>
					<?php echo $adorders->cdate; ?>
				</td>
				<td>
					<?php echo $adorders->ad_amount;

					$totalamount=$totalamount+$adorders->ad_amount;
					?>
				</td>
				<td>
					 <?php
					 $whichever = '';

					 switch($adorders->status)
					 {
							case 'C' :
								$whichever =  JText::_('SA_CONFR');
							break;
							case 'RF' :
								$whichever = JText::_('SA_REFUN') ;
							break;
							case 'E' :
								$whichever = JText::_('SA_ERR') ;
							break;
					 }

					 if($adorders->status == 'P')
					 echo JHtml::_('select.genericlist',$this->pstatus,'pstatus'.$k,'class="pad_status"  onChange="selectstatusorder('.$adorders->id.',this);"',"value","text",$adorders->status);
					 else
					 echo $whichever ;
					 ?>
				</td>
			</tr>
			<?php
			$k++;
			}

			}else{
				echo "<tr><td colspan='14' align=center><div class='center'>".JText::_("NO_DATA_TO_DISPLAY")."</div></td></tr>";
				}
				?>
		<tr>
				<td colspan="7"></td>
				<td><b><?php echo JText::_('Total'); ?></b></td>
				<td><b><?php echo $totalamount;?></b></td>
				<td></td>
			</tr>
			<tr>
			<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
		</table>

		<input type="hidden" name="option" value="com_socialads" />
		<input type="hidden" id='hidid' name="id" value="" />
		<input type="hidden" id='hidstat' name="status" value="" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="view" value="adorders" />
		<input type="hidden" name="controller" value="adorders" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		</table>

	</form>
</div>
