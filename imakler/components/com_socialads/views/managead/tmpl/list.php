<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal', 'a.modal');
$document = JFactory::getDocument();
//$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css');
$document->addScript(JUri::base().'components/com_socialads/js/managead.js');
$document->setTitle('Manage Ads');
$user = JFactory::getUser();
$img_Delete=JUri::base()."components/com_socialads/images/delete.png";
$img_New=JUri::base()."components/com_socialads/images/add.png";
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

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

$ssession = JFactory::getSession();
global $mainframe;
$mainframe = JFactory::getApplication();

$js=	"jQuery(document).ready(function(){
			jQuery('.ad_type_tootip').popover();
		});
";
$document->addScriptDeclaration ( $js );

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
	$ssession->set('socialadsbackurl', $_SERVER["REQUEST_URI"]);
	$mainframe->redirect(JRoute::_('index.php?option=com_socialads&view=registration&Itemid='.$this->itemid));

	}
	return false;
}
$model = $this->getModel('managead');
if(JVERSION > 3.0)
$pending_icon = ' icon-clock ';
else
$pending_icon = ' icon-time ';
?>
<script type="text/javascript">
var root_url	=	"<?php echo JUri::base();?>";
techjoomla.jQuery(document).ready(function() {
	var width = techjoomla.jQuery(window).width();
	var height = techjoomla.jQuery(window).height();

	techjoomla.jQuery('a#modal_info').attr('rel','{handler: "iframe", size: {x: '+(width-(width*0.10))+', y: '+(height-(height*0.10))+'}}');
	techjoomla.jQuery('a#modal_ignore').attr('rel','{handler: "iframe", size: {x: '+(width-(width*0.40))+', y: '+(height-(height*0.30))+'}}');
	techjoomla.jQuery('a#modal_adpreview').attr('rel',"{handler: 'iframe', size: {x: "+(width-(width*0.50))+", y: "+(height-(height*0.45))+"}}");
});
function tableOrdering( order, dir, task )
{
	var form = document.adminForm;

	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;
	document.adminForm.submit( task );
}


	function submitbutton( action ) {
		if(action=='multipledeleteads')
		{
			if (document.adminForm.boxchecked.value==0){
				alert("<?php echo JText::_("SA_MAKE_SEL");?>");
				return;}

			var r=confirm("<?php echo JText::_('DELETE_AD');?>");
			if (r.toString()=="true")
			{
			var form = document.adminForm;
			submitform( action );
			return;
			}

		}
		else if(action=='add')
		{
			var form = document.adminForm;
			submitform( action );
			return;

		}
 }

 	function submitform(pressbutton){
		 if (pressbutton) {
		 	document.adminForm.task.value = pressbutton;
		 }
		 if (typeof document.adminForm.onsubmit == 'function') {
		 	document.adminForm.onsubmit();
		 }
		 	document.adminForm.submit();
	} //submitform() ends

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
			</div>	<!-- end of proimport-wrap div -->
				<?php
		}
		//eoc for JS toolbar inclusion

?>
<form id="adminForm" action="" method="post" name="adminForm">

 <div class="componentheading page-header"><h2><?php echo JText::_('MANAGEAD_TITLE');?></h2></div>
 <?php


	$statsforbar=$this->statsforbar;

	$imprs=0;
	$clicks=0;
	$max_invite=100;
	$cmax_invite=100;
	$yscale="";
	$titlebar="";
	$daystring="";
	$finalstats_date=array();
	$finalstats_clicks=array();
	$finalstats_imprs=array();
	$day_str_final='';
	$total_no_clicks=0;
	$total_no_impressions=0;
	$click_through_ratio=0;
	$finalstats_date1=array();
	$finalstats_date2=array();
	$finalstats_str_date1=array();//for date string
	$finalstats_str_date2=array();//for date string

	$barchart='';
	$fromDate=date('Y-m-d', strtotime(date('Y-m-d').' - 30 days'));;
	$toDate=date('Y-m-d H:i:s');

	$dateMonthYearArr = array();
	$fromDateSTR = strtotime($fromDate);
	$toDateSTR = strtotime($toDate);
	$empty_line_chart=1;

	if(empty($statsforbar[0]) && empty($statsforbar[1]))
	{
	  	$barchart=JText::_('NO_STATS');
	}
   else
   	{
			if(!empty($statsforbar[1]))
			{
			$cnt=0;

			for ($currentDateSTR = $fromDateSTR; $currentDateSTR <= $toDateSTR; $currentDateSTR += (60 * 60 * 24))
			{
				// use date() and $currentDateSTR to format the dates in between
				$currentDateStr = date("Y-m-d",$currentDateSTR);
				$day='';
				$month= '';
				$year='';
				//echo $currentDateStr;
				 $day= date("d",strtotime($currentDateStr));
				 $finalstats_date1[] = $day;
				 $month= date("m",strtotime($currentDateStr));
				 $year=date("Y",strtotime($currentDateStr));
				$finalstats_str_date1[] = $year."-".$month."-".$day; //for date string
				$finalstats_clicks[$cnt]= 0;
				//$sts = array_reverse($statsforbar[1]);
				foreach($statsforbar[1] as $cur_statsforbar)
				{
				$cur_month=$cur_statsforbar->month;
				$cur_day=$cur_statsforbar->day;
				//print_r($cur_statsforbar);die;
				if(($cur_statsforbar->month)<10)
				$cur_month="0".$cur_month;

				if(($cur_statsforbar->day)<10)
				$cur_day="0".$cur_day;
						if(($day==$cur_day) and ($month == $cur_month )and ($year == $cur_statsforbar->year))
						{
							$finalstats_clicks[$cnt]=0+$cur_statsforbar->value;
							$total_no_clicks+=$cur_statsforbar->value;
					 	}
				}
				$cnt++;
			}

			}//if ststsforbar is not empty ends
			else
			{
				$barchart=JText::_('NO_CLICKS');
			}

	      	if(!empty($statsforbar[0]))
	     	{

	     	$cnt=0;
			for ($currentDateSTR = $fromDateSTR; $currentDateSTR <= $toDateSTR; $currentDateSTR += (60 * 60 * 24))
			{
				// use date() and $currentDateSTR to format the dates in between
				$currentDateStr = date("Y-m-d",$currentDateSTR);
				$dateMonthYearArr[] = $currentDateStr;
				$day='';
				$month= '';
				$year='';
				//echo $currentDateStr;
				 $day= date("d",strtotime($currentDateStr));
				 $finalstats_date2[] = $day;
				 $month= date("m",strtotime($currentDateStr));
				 $year=date("Y",strtotime($currentDateStr));
				$finalstats_str_date2[] = $year."-".$month."-".$day; //for date string
				$finalstats_imprs[$cnt]= 0;
				//$sts = array_reverse($statsforbar[1]);
				foreach($statsforbar[0] as $cur_statsforbar)
				{
				$cur_month=$cur_statsforbar->month;
				$cur_day=$cur_statsforbar->day;
				//print_r($cur_statsforbar);die;
				if(($cur_statsforbar->month)<10)
				$cur_month="0".$cur_month;

				if(($cur_statsforbar->day)<10)
				$cur_day="0".$cur_day;
						if(($day==$cur_day) and ($month == $cur_month )and ($year == $cur_statsforbar->year))
						{
							$finalstats_imprs[$cnt]=0+$cur_statsforbar->value;
							$total_no_impressions+=$cur_statsforbar->value;
					 	}
				}
				$cnt++;
			}
			}
			else
			{
				$imprs=0;
				$barchart=JText::_('NO_IMPRS');
			}

			if(count($finalstats_date1)>count($finalstats_date2))
				$finalstats_date=$finalstats_str_date1; //for date string
			else
				$finalstats_date=$finalstats_str_date2; //for date string
			//for date string
			$day_str="'".implode("','",$finalstats_date)."'";

			$day_str_final=$day_str;
			/*print_r($finalstats_date);
			print_r($finalstats_imprs);
			print_r($finalstats_clicks);die;*/
			//echo "\n-----------";
			$emptylinechart=0;
			if($finalstats_imprs)
			{
			$imprs=implode(",",$finalstats_imprs);
			$emptylinechart=0;
			}
			else
			$emptylinechart=1;
			//echo "\n-----------";
			if($finalstats_clicks)
			{
			$clicks=implode(",",$finalstats_clicks);
			$emptylinechart=0;
			}
			else
			$emptylinechart=1;

   	}


	?>


<div class="bs-docs-grid">
<div class="row-fluid">
	<div class="show-grid span12">
		<div id="line_chart_div" class="chart_div"></div>
		<div id="manageadstats">
			<div class="manageadtable row-fluid">

				<div class="manageadtableheader span4" ><p class="muted"><?php echo $total_no_clicks;?>
					<span id="manageadspan"><?php echo JText::_('CLICKS');?></span></p>
				</div>

				<div class="manageadtableheader span4"><p class="muted"><?php echo $total_no_impressions;?>
					<span id="manageadspan"><?php echo JText::_('IMPRS');?></span></p>
				</div>

				<div class="manageadtableheader span4">
					<?php
						if($total_no_impressions!=0)
						{
						echo $ctr=number_format (($total_no_clicks)/($total_no_impressions), 4);
						//echo  number_format ($ctr, 2);

						}
						else
						echo  "0";
					?>
					<span id="manageadspan"><?php echo  JText::_('CLICK_THROUGH_RATIO');?></span>
				</div>
				<div style="clear:both"></div>
			</div>
		</div>

		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
			var linechart_imprs=[<?php echo $imprs;?>]
			var	 linechart_clicks=[<?php echo $clicks;?>]
			var	 linechart_day_str=[<?php echo $day_str_final; ?>]
			google.load("visualization", "1", {packages:["corechart"]});
		 //var visualization = new google.visualization.LineChart(container);
			google.setOnLoadCallback(draw_lineChart);
			function draw_lineChart() {
				<?php if(!$imprs and !$clicks)
				{ ?>
				document.getElementById("line_chart_div").style.display="none";
				document.getElementById("manageadstats").style.display="none";

				//document.getElementById("urlgraph").style.display="none";
				return;
				<?php } ?>

				var data = new google.visualization.DataTable();
				data.addColumn('string', '<?php echo JText::_("LINE_YEAR");?>');
				data.addColumn('number', '<?php echo JText::_("IMPRS");?>');
				data.addColumn('number', '<?php echo JText::_("CLICKS");?>');

				var impr=linechart_imprs;
				var clicks=linechart_clicks;
				// alert(clicks);
				var assign_date=linechart_day_str;
				data.addRows(assign_date.length+1);
				/* console.log("impr--------");
				console.log(impr);
				console.log("clicks--------");
				console.log(clicks);
				console.log("assign_date--------");
				console.log(assign_date); 	*/
				for(var i=0;i<assign_date.length;i++)
				{
					data.setValue(i, 0, assign_date[i].toString());
					data.setValue(i, 1, impr[i]);
					data.setValue(i, 2, clicks[i]);
				}
				var chart = new google.visualization.LineChart(document.getElementById('line_chart_div'));

				chart.draw(data, {
					colors: ['#67c2ef','#78CD51'],
					pointSize: 3,
					vAxis:{textStyle:{fontSize:'11'},title:'<?php echo JText::_("LINE_CHART_VAXIS");?>',titleTextStyle:{fontSize:'11'}},
					hAxis:{textStyle:{fontSize:'9'},title:'<?php echo JText::_("LINE_CHART_HAXIS");?>',titleTextStyle:{fontSize:'9'}},
					title: '<?php echo JText::_("MANAGEAD_GRAPH_TITLE");?>'});
			}
			</script>

	</div>
	<div style="clear:both;" > </div>

		<div class="row-fluid">
		<div class="show-grid span12">
				<div class="managead-toolbar ">
					<div class="row-fluid managead-toolbar_maindiv">
						<div class="span3" style="text-align:left">
							<span class="editlinktip " title="<?php echo JText::_('DEL_AD');?>">
									<a class="toolbar btn btn-danger" onclick="javascript: submitbutton('multipledeleteads');" >
										<i class="icon-trash icon-white"></i><!--img class="sa_toolbar_img" src="<?php echo $img_Delete;?>"  alt="Tooltip" style="width: 32px;"-->
									</a>
							</span>

							<span  style="padding-left:2px;">
								<span class="editlinktip " title="<?php echo JText::_('NEW_AD');?>" >
									<a class="btn btn-success" onclick="javascript: submitbutton('add');">
									<!--img class="sa_toolbar_img" src="<?php echo $img_New;?>" style="width: 32px;"-->
									<i class="icon-plus icon-white"></i>
									</a>
								</span>
							</span>
						</div>
						<div class="span9" style="text-align:right">
								<?php
									if($socialads_config['select_campaign'])
									{
								 echo JHtml::_('select.genericlist', $this->camp_dd, "search_camp",'class="ad-status inputbox input-medium" size="1"
										onchange="document.adminForm.submit();" name="search_zone"', "value", "text",$this->lists['search_camp']);
									} ?>
								<span style="padding-left:10px;">
								<?php

								echo JHtml::_('select.genericlist', $this->status_zone, "search_zone", 'class="ad-status inputbox input-medium" size="1"
										onchange="document.adminForm.submit();" name="search_zone"',"value", "text", $this->lists['search_zone']);
								?>
								 </span>
									<?php
								if(JVERSION >= 3.0 )
								{
									?>
										<?php echo $this->pagination->getLimitBox(); ?>
									<?php
								}
								?>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="managead table table-hover" style="overflow:scroll;">
						<thead>
							<tr class="managead-tr">
								<th width="1%" align="center" class="title">
								<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
								</th>
								<th width="25%"><?php echo JHtml::_( 'grid.sort', JText::_('MYAD'), 'ad.ad_title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
								<?php if($socialads_config['select_campaign']==1)
								{  ?>
									<th class="managead-td"><?php echo JHtml::_( 'grid.sort', JText::_('CAMPAIGN'), 'c.campaign', $this->lists['order_Dir'], $this->lists['order']);?></th>
								<?php } ?>
								<th class="managead-td"><?php echo JHtml::_( 'grid.sort',  JText::_('ADZONE'), 	'ad.ad_zone', $this->lists['order_Dir'], $this->lists['order']); ?></th>
								<th class="managead-td"><?php echo JHtml::_( 'grid.sort', JText::_('TYPE'),'ad.ad_payment_type', $this->lists['order_Dir'], $this->lists['order']); ?></th>

								<th><?php echo JHtml::_( 'grid.sort', JText::_('IMPRS'),'az.per_imp', $this->lists['order_Dir'], $this->lists['order']); ?></th>
								<th><?php echo JHtml::_( 'grid.sort', JText::_('CLICKS'),'az.per_click', $this->lists['order_Dir'], $this->lists['order']); ?></th>
								<th class="hidden-tablet hidden-phone"><?php echo JHtml::tooltip(JText::_('DESC_CLICK_THROUGH_RATIO'), JText::_('CLICK_THROUGH_RATIO'), '', JText::_('CLICK_THROUGH_RATIO'));?></th>

								<?php if($socialads_config['select_campaign']==0)
								{  ?>
									<th align="center"><?php echo JHtml::tooltip(JText::_('DESC_PAYMENT_STATUS'), JText::_('PAYMENT_STATUS'), '', JText::_('PAYMENT_STATUS'));?></th>
								<?php } ?>

								<th class="hidden-tablet hidden-phone"><?php echo JHtml::tooltip(JText::_('DESC_IGNORENO'), JText::_('IGNORENO'), '', JText::_('IGNORENO')); ?></th>
								<th><?php echo JHtml::tooltip(JText::_('DESC_ACTIONS'), JText::_('ACTIONS'), '', JText::_('ACTIONS'));?></th>
							</tr>
						</thead>
				<?php
					$k = $j = 0;
					if(count($this->myads))
					{
						foreach($this->myads as $ads)
						{

							if($ads->ad_approved=='1')
								$tr_class="class=''";
							else if($ads->ad_approved=='0')
								$tr_class="class='warning'";
							else if($ads->ad_approved=='2')
								$tr_class="class='error'";
							?>

							<tr <?php echo $tr_class;?>>
								<td  class="managead-center">
									<?php echo JHtml::_('grid.id', $j, $ads->ad_id);?>
								</td>
								<td>
									<span class="ad-type-img" >
										<?php if($ads->ad_guest == 1){?>
											<img src="<?php echo JUri::base().'components/com_socialads/images/guest.png'?>" />
											<?php }
										else if(($ads->ad_guest == 0) && ($ads->ad_alternative == 0)){ ?>
										  <img src="<?php echo JUri::base().'components/com_socialads/images/group.png'?>" />
										<?php } ?>
									</span>
									<?php $link = JRoute::_(JUri::base().'index.php?option=com_socialads&tmpl=component&view=adsummary&Itemid='.$this->itemid.'&adid='.$ads->ad_id);?>
									<a  rel="{handler: 'iframe', size: {x: 700, y: 800}}" href="<?php echo $link; ?>" class="modal" id="modal_info">
											<?php if($ads->ad_title == '')
											{
												echo JText::_('IMGAD');
											}
											else
											{
											 echo $ads->ad_title;
											} ?>
									</a>
								</td>
							<?php
							if($socialads_config['select_campaign']==1)
							{?>
								<td>
									<?php
										if ($ads->campaign ){
											echo $ads->campaign;
										}
										else{
											echo "--";
										}
									?>
								</td>
								<?php } ?>
								<td>
									<?php if($ads->zone_name == '')
									{
										echo JText::_('NOZONE');
									}
									else
									{
									 echo $ads->zone_name;
									 } ?>
								</td>
								<td>
								<?php
									$ad_impression=$ad_click=0;
									if($ads->ad_alternative== 1)
									{
										echo JText::_('ALT_AD');
									}
									else if($ads->ad_noexpiry== 1)
									{
										echo JText::_('UNLTD_AD');
									}
									else if($ads->ad_affiliate== 1)
									{
										echo JText::_('AD_TYP_AFFI');
									}
									else
									{
										if($ads->ad_payment_type== 0){
											$ad_impression	=1;
											echo JText::_('IMPRS');
										}
										else if($ads->ad_payment_type == 1)
										{
											$ad_click	=1;
											echo JText::_('CLICKS');
										}
										else if($ads->ad_payment_type == 3){
											echo JText::_('SELL_THROUGH');
										}
										else { ?>
											<img src="<?php echo JUri::Base().'/components/com_socialads/images/start_date.png' ?>">
											<?php echo $ads->ad_startdate; ?>
											<br/>
											<?php if(($ads->ad_enddate!='0000-00-00') )			//if not 0 then	only show end date
											{
												if(!$ads->subscription_id) //if not recurring payment then only show end date
												{
												?>
													<img src="<?php echo JUri::Base().'/components/com_socialads/images/end_date.png' ?>">
													<?php echo $ads->ad_enddate;
												}
											}
											?>
											<?php }
									}
								?>
								</td>
								<?php
								$out_of	='';
								if($socialads_config['select_campaign']==0)
									{

										if($ads->camp_id!=0 && !$ads->bid_value)			//if camp ad is there den they dont have credits..
										{

										}
										else if($ads->bid_value)
										{
												$out_of	 =	$ads->bid_value;

										}
										else if($ads->ad_alternative== 1 || $ads->ad_noexpiry== 1 || $ads->ad_affiliate == 1){
											$out_of	=	JText::_('CREDIT_UNLIMITED');
										}
										else if($ads->ad_payment_type == 2){

										}
										else {
											$out_of = $ads->ad_credits_balance;
										}
									if( $out_of){
										$text_to_show	=	JText::_('CREDITS')." : " .$out_of.'<br />';
										if($ad_impression	== 1)
											$text_to_show	.=	 JText::_('IMPRS')." : " .$ads->ad_impressions;
										if($ad_click	== 1 )
											$text_to_show	.=	  JText::_('CLICKS')." : " .$ads->ad_clicks;

										$out_of_anchor	='<a class="ad_type_tootip" data-content="'. $text_to_show.'" data-placement="top" data-html="html"  data-trigger="hover" rel="popover" >';

										$out_of_anchor =  ' / ' .$out_of_anchor . $out_of . '</a>';
									}
								 } ?>


								<td class="managead-center">
									<?php
										if($ads->ad_impressions)
										echo $ads->ad_impressions;
										else
										echo "0";

										if($ad_impression	== 1 && $out_of)
											echo $out_of_anchor;
									?>
								</td>
								<td class="managead-center">
										<?php
										if($ads->ad_clicks)
										echo $ads->ad_clicks;
										else
										echo "0";

										if($ad_click	== 1 && $out_of)
											echo $out_of_anchor;

										?>
								</td>
								<td class="managead-center hidden-tablet hidden-phone">
										<?php
										if($ads->ad_impressions!=0)
										{
											$ctr=($ads->ad_clicks)/($ads->ad_impressions);
											echo  number_format ($ctr, 4);
										}
										else
										{
											echo "0.0000";
										}
										?>
								</td>
							<?php
							if($socialads_config['select_campaign']==0)
							{?>
								<td class="managead-center">
									<?php
										$payment_status_img = '';
										if($ads->ad_alternative== 1 || $ads->ad_noexpiry== 1 || $ads->ad_affiliate ==1 )
										{ ?>
											<i class="icon-ok"></i>
									<?php	}
										else
										{
											 switch($ads->status)
											 {
													case 'P' :?>
														<i class="<?php echo $pending_icon; ?>"></i>
											<?php			break;
													case 'C' :?>
														<i class="icon-ok"></i>
											<?php			break;
													case 'RF' : ?>
														<i class="icon-remove"></i>
											<?php 		break;
													default :?>
														<i class="icon-minus"></i>
											<?php
													break;
											 }
										}
									?>
								</td>
						<?php } ?>
								<td class="managead-center hidden-tablet hidden-phone" >
									<?php
										$ignore_cnt=0;
										$ignore_cnt=$model->getIgnorecount($ads->ad_id);
										if($ignore_cnt==0)
											echo $ignore_cnt;
										else
										{
											$link = JRoute::_('index.php?option=com_socialads&view=ignoreads&tmpl=component&adid='.$ads->ad_id);
										?>
										<a href="<?php echo $link;?>" rel="{handler: 'iframe', size: {x: 700, y: 350}}" class="modal" id="modal_ignore">
											<?php
											echo $ignore_cnt;
											?>
										</a>
											<?php
										 }
									?>
								</td>

								<td class="managead-right" >
									<?php
											$link = JRoute::_('index.php?option=com_socialads&view=lightbox&tmpl=component&layout=default&id='.$ads->ad_id);
										?>
										<div>
											 <?php if($ads->ad_published == 1 && $ads->ad_approved == 1 ){ ?>
												<input type="checkbox" name="mychkeckbox" id="chkbox_<?php echo $ads->ad_id; ?>" value="" onClick="publishcheck(this)" checked="true" title="<?php echo JText::_('COM_SOCIALADS_DISABLE_DIS_AD');?>"/> <?php }
												else if($ads->ad_approved == 0 || $ads->ad_approved == 2){
													//echo JText::_('INACTIVE'); ?>
													<span>&nbsp;</span>
											<?php }
											else{   ?>
												<input type="checkbox" name="mychkeckbox" id="chkbox_<?php echo $ads->ad_id; ?>" value="" onClick="publishcheck(this)" title="<?php echo JText::_('COM_SOCIALADS_ENABLE_DIS_AD');?>"/>
											<?php }	?>
											<a rel="{handler: 'iframe', size: {x: 350, y: 350}}" href="<?php echo $link; ?>" class="modal" id="modal_adpreview">
												<span class="editlinktip ad-preview-img" title="<?php echo JText::_('PREVIEW');?>" ><img src="<?php echo JUri::base().'components/com_socialads/images/ad_ preview.png'?>"></span>
											</a>
										</div>
								</td>
							</tr>
								<?php $k = 1 - $k;
								$j++;
						}
					}else
					{?>
						<tr>
							<td colspan="10">
							<div class="alert alert-warning">
								<?php echo JText::_('COM_SA_NO_DATA'); ?>
							</div>
						</tr>
					<?php
					}
					 ?>
						<tr>
							<?php
							if(JVERSION<3.0)
							   $class_pagination='pager';
							else
							   $class_pagination='pagination';
							?>
							<td colspan="<?php if ($socialads_config['select_campaign']=='1'){ echo '13'; }else{ echo '15';}?>" align="center">
							<div class="<?php echo $class_pagination; ?>">
							<?php
							if(JVERSION<3.0)
								echo $this->pagination->getListFooter();
							else
								echo $this->pagination->getPagesLinks();
							?>
							 </div>
						   </td>
						</tr>
						<tfoot>
							<tr class="info">
								<td colspan="<?php if ($socialads_config['select_campaign']=='1'){ echo '13'; }else{ echo '15';}?>" align="center">
									<span><img src="<?php echo JUri::Base().'/components/com_socialads/images/guest.png' ?>"> = <?php echo JText::_('GUEST_ADS');?>, </span>
									<span style="padding-left:10px;"><img src="<?php echo JUri::Base().'components/com_socialads/images/group.png' ?>"> = <?php echo JText::_('TARGET_ADS');?>, </span>
									<span style="padding-left:5px;"><i class="icon-minus"></i> = <?php echo JText::_('COM_SOCIALADS_NO_ADORDER');?>, </span>
									<span ><i class="<?php echo $pending_icon; ?>" ></i><!--img src="<?php echo JUri::Base().'components/com_socialads/images/pending.png' ?>"--> = <?php echo JText::_('SA_PENDIN');?>, </span>
									<span style="padding-left:5px;"><i class="icon-ok"></i><!--img src="<?php echo JUri::Base().'components/com_socialads/images/confirm.png' ?>"--> = <?php echo JText::_('SA_CONFIRM')."/".JText::_('SA_APPROVE');?>, </span>
									<span style="padding-left:5px;"><i class="icon-remove"></i><!--img src="<?php echo JUri::Base().'components/com_socialads/images/refund.png' ?>"--> = <?php echo JText::_('SA_REFUND')."/".JText::_('SA_REJEC');?> </span>
								</td>

							</tr>
						</tfoot>
					</table>

			</div>
			<input type="hidden" name="option" value="com_socialads" />
			<input type="hidden" name="view" value="managead" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="controller" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
					<!--input type="hidden" name="defaltevent" value="<?php echo $this->lists['camp_name'];?>" /-->
		</div><!--row-fluid end-->
		</div>

</div><!--grid docs-->
</div>
</form>
</div>
