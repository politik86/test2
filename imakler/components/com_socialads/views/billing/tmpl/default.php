<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
//require(JPATH_SITE.DS."components".DS."com_socialads".DS."views".DS."showad".DS."tmpl".DS."default.php");
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

$user =JFactory::getUser();
$ssession = JFactory::getSession();
$mainframe = JFactory::getApplication();
$input=JFactory::getApplication()->input;
?>
<div class="techjoomla-bootstrap">
<?php
if (!$user->id)
{
	?>
	<div class="alert alert-block">
	<?php echo JText::_('BUILD_LOGIN'); ?>
	</div>
	</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	//.$_SERVER["REQUEST_URI"];
	if($socialads_config['sa_reg_show'])
	{
				$itemid = $input->get('Itemid',0,'INT');
	$ssession->set('socialadsbackurl', $_SERVER["REQUEST_URI"]);
	$mainframe->redirect(JRoute::_('index.php?option=com_socialads&view=registration&Itemid='.$itemid,false));

	}
return false;
}

require_once(JPATH_COMPONENT . DS . 'helper.php');
	$socialadshelper = new socialadshelper();
	$init_balance = $socialadshelper->getbalance();
	if($init_balance!=NULL && $init_balance !=1.00)    // HARDCODED FOR NOW.......
	{
		$itemid	= $socialadshelper->getSocialadsItemid('payment');
		$not_msg	= JText::_('MIM_BALANCE');
		$not_msg	= str_replace('{clk_pay_link}','<a href="'.JRoute::_('index.php?option=com_socialads&view=payment&Itemid='.$itemid).'">'.JText::_('SA_CLKHERE').'</a>', $not_msg);
		JError::raiseNotice( 100, $not_msg );
	}

if ($socialads_config['select_campaign']=='0')
{
	?>
	<div class="alert alert-block">
	<?php echo JText::_('AD_NO_AUTH_SEE'); ?>
	</div>
	</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
return false;
}

$stat_info = $this->billing[0];

//$pay_info = $this->billing[0];
$camp_name = $this->billing[1];
$coupon_code = $this->billing[2]; // get coupon code array
$ad_title = $this->billing[3];

/* month filter try */


	## An array of $key=>$value pairs ##
	$months = array(0=>'Month', 1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

	## Initialize array to store dropdown options ##
	$month = array();

	foreach($months as $key=>$value) :
		## Create $value ##
		$month[] = JHtml::_('select.option', $key, $value);
	endforeach;
	// year filter
	$year = array();
		$year = range(2015, 2000, 1);
		//print_r($year); die();
		foreach($year as $key=>$value)
		{
				unset($year[$key]);
				$year[$value]= $value;
		}
		foreach($year as $key=>$value) :
		## Create $value ##
		$year1[] = JHtml::_('select.option', $key, $value);
	endforeach;

?>

 <script type="text/javascript" >

function add_pay()
{

		window.location='index.php?option=com_socialads&view=payment';
}

function change_table(id){

	var a = document.getElementById("pay_table");
	var b = document.getElementById("spent_table");
	if(id=='pay_tab_change')
	{
		a.style.display="block";
		b.style.display="none";
	}
	else
	{
		a.style.display="none";
		b.style.display="block";
	}

}


function applycoupon(){




	var coupon_code = document.getElementById('coupon_code').value;


	if(jQuery('#coupon_code').val() =='')
	{
		alert("Enter Coupen Code");
	}
	else
	{

		jQuery.ajax({
			url: '?option=com_socialads&task=getcoupon&coupon_code='+coupon_code,
			type: 'GET',
			dataType: 'json',
			success: function(data) {
			if(data != 0)
			{

				if(data[0].val_type == 1){
						alert("Sorry.! the coupon is percentile coupon.");
						return false;
					}
				else
						{
							var value = data[0].value;
							add_payment(coupon_code,value);

						}
			}
			else
				alert(document.getElementById('coupon_code').value +" Coupon does not Exists");


			}
		});
	}

}


function add_payment(coupon_code,value)
{

		jQuery.ajax({
		url: '?option=com_socialads&controller=payment&task=add_payment&coupon_code='+coupon_code+'&value='+value,
		type: 'GET',
		dataType: 'json',
		success: function(data) {
			if(data != 0)
			{
				alert("<?php echo JText::_('COUPON_ADDED_SUCCESS'); ?>");
				window.location = "?option=com_socialads&view=billing";
			}
		}
	});
}




</script>
<?php
   //newly added for JS toolbar inclusion
   if(file_exists(JPATH_SITE . DS .'components'. DS .'com_community') and $socialads_config['show_js_toolbar']==1)
   {
	require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'toolbar.php');
	$toolbar    = CFactory::getToolbar();
	$tool = CToolbarLibrary::getInstance();

	?>
<style>
   <!--
      div#proimport-wrap #community-wrap { margin: 0;padding: 0; }
      div#proimport-wrap #community-wrap { min-height: 45px !important; }
      -->
</style>
<script src="<?php echo JUri::root().'components/com_community/assets/bootstrap/bootstrap.min.js'; ?>" type="text/javascript"></script>
<div id="proimport-wrap">
   <div id="community-wrap">
      <?php	echo $tool->getHTML();	?>
   </div>
</div>
<!-- end of proimport-wrap div -->
<?php
   }
   //eoc for JS toolbar inclusion
?>


	<form action="" method="post" name="adminForm3" id="adminForm3">
		<div class="page-header">
			<h2><?php echo JText::_('BILLING');?></h2>
		</div>
		<div class="sa-campaign-toolbar">
			<div id="month_filter" >
				<?php
				 echo JHtml::_('select.genericlist', $month,'month', 'name="filter_order" ', "value", "text",$this->lists['month']);
				 echo JHtml::_('select.genericlist', $year,'year', 'name="filter_order" ', "value", "text",$this->lists['year']);
				 ?>
				 <button type="button" name="go" title="<?php echo JText::_('GO'); ?>" class="btn btn-success" id="go" onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>

			</div>
		</div>

		<ul class="nav nav-tabs" id="AdWalletTab">
			<li class="active" ><a href="#spent_table" data-toggle="tab"><?php echo JText::_('ACC_HIS'); ?></a></li>
			<li><a href="#pay_table" data-toggle="tab"><?php echo JText::_('PAY_CREDTIS_ONLY'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane table-responsive" id="pay_table">

				<table class="table table-condensed ">
					<thead>

						<tr>
								<!--th><?php echo JText::_('DATE'); ?></th-->
								<th><?php

					  echo JHtml::tooltip(JText::_('DATE_WISE_RECORD'), '','', JText::_('DATE'));
					 ?></th>
								<!--th><?php echo JText::_('DESCRIPTION'); ?></th-->
								<th><?php

					  echo JHtml::tooltip(JText::_('PAY_DONE'),'', '', JText::_('DESCRIPTION'));
					 ?></th>
						<th><?php
					  echo JHtml::tooltip(JText::_('PAYMENT_AMOUNT'), '', '', JText::sprintf('PAYMENT',$socialads_config['currency'] ) );
					 ?></th>
						</tr>
					</thead>
				<?php


				foreach($stat_info as $key)
				{
					$comment = explode('|',$key->comment);
					if(!empty($key->credits) && $key->credits!=0.00)
					{
					?>

						<tr>

							<td>
								<?php echo $key->time; ?>
							</td>
							<td >
								<?php	if($comment[0]=='ADS_PAYMENT')
									{
										echo JText::_('ADS_PAYMENT');
									}
									elseif($comment[0]=='VIA_MIGRATTION')
									{
										foreach($ad_title as $index=>$value)
										{
											if(isset($comment[1]) && $index==$comment[1])
											{
											echo JText::sprintf('VIA_MIGRATTION',$value);
											}
										}
									}
									elseif($comment[0]=='COUPON_ADDED')
									{

										foreach($coupon_code as $index=>$value)
										{
											if($index==$key->type_id)
											{
											$coupon_msg = JText::sprintf('COUPON_ADDED',$value);
											echo $coupon_msg;
											}
										}
									}	?>
							</td>
							<td>
							<?php echo $key->credits; ?>
							</td>
						</tr>
				<?php }
				 }

				?>
				</table>

			</div>

			<div class="tab-pane  active table-responsive" id="spent_table">

					<table class="table table-condensed ">
						<thead>
						<tr>

							<th><?php

					  echo JHtml::tooltip(JText::_('DATE_WISE_RECORD'), '','', JText::_('DATE'));
					 ?></th>
							<th><?php

					  echo JHtml::tooltip(JText::_('PAY_DONE'),'', '', JText::_('DESCRIPTION'));
					 ?></th>
							<th><?php

					  echo JHtml::tooltip(JText::_('PAYMENT_AMOUNT'), '', '',JText::sprintf('PAYMENT',$socialads_config['currency'] ));
					 ?></th>
							<!--th><?php echo JText::_('TOTAL_SPENT'); ?></th-->
							<th><?php

					  echo JHtml::tooltip(JText::_('TOTAL_SPENT'),'', '', JText::sprintf('TOTAL_SPENT',$socialads_config['currency'] ));
					 ?></th>
							<!--th><?php echo JText::_('AMOUNT_DUE'); ?></th-->
							<th><?php

					  echo JHtml::tooltip(JText::_('AMOUNT_DUE_REMAINING'), '', '', JText::sprintf('AMOUNT_DUE',$socialads_config['currency'] ));
					 ?></th>

						</tr>
					</thead>
					<?php





					$balance = 0;



					foreach($stat_info as $key)
					{
						$comment = explode('|',$key->comment);
								?>

							<tr>

								<td style="width:15%">
									<?php echo $key->time; ?>
								</td>

								<td>
									<?php
									if($comment[0]=='SPENT_DONE_FROM_MIGRATION')
									{

										foreach($ad_title as $index=>$value)
										{
											if(isset($comment[1]) && $index==$comment[1])
											{
											echo JText::sprintf('SPENT_DONE_FROM_MIGRATION',$value);
											}
										}
									}
									elseif($comment[0]=='ADS_PAYMENT')
									{
										echo JText::_('ADS_PAYMENT');
									}
									elseif($comment[0]=='VIA_MIGRATTION')
									{
										foreach($ad_title as $index=>$value)
										{
											if(isset($comment[1]) && $index==$comment[1])
											{
											echo JText::sprintf('VIA_MIGRATTION',$value);
											}
										}
									}
									elseif($comment[0]=='COUPON_ADDED')
									{

										foreach($coupon_code as $index=>$value)
										{
											if($index==$key->type_id)
											{
											$coupon_msg = JText::sprintf('COUPON_ADDED',$value);
											echo $coupon_msg;
											}
										}
									}
									elseif('DAILY_CLICK_IMP')
									{
										foreach($camp_name as $index=>$value)
										{
											if($index==$key->type_id)
											{
											$spent_msg = JText::sprintf('DAILY_CLICK_IMP',$value);
											echo $spent_msg;
											}
										}

									//echo $txt;
									}	?>
								</td>

								<td style="width:10%">
										<?php echo $key->credits; ?>
								</td>

								<td style="width:10%">
									<?php echo $key->spent; ?>
								</td>
								<td style="width:10%">
										<?php echo $key->balance; ?>
								</td>


							</tr>



					<?php
					}

					?>
					</table>

			</div>
		</div>

	<div class="form-actions" >
		<div class="form-inline">
			<input type="button" class="btn btn-success" value="<?php echo JText::_('ADD_PAYMENT'); ?>" onclick="add_pay(); " />
			<label title="<?php echo JText::_('COM_SA_REDEEM_COUPON_TITLE'); ?>"><?php echo JText::_('REDEEM_COUPON'); ?></label>
			<input id="coupon_code" type="text" class="input-mini" name="coupon" placeholder="code" />
			<input type="button" class="btn btn-primary" id="add_coupon" value="<?php echo JText::_('SUBMIT'); ?>" onclick="applycoupon(); "/>
		</div>
	</div>
	<!--input type="hidden" name="defaltevent" value="<?php echo $this->lists['filter_order'];?>" /-->
	<input type="hidden" name="option" value="com_socialads" />
	</form>
</div>
