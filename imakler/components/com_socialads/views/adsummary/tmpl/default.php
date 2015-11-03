<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
//jimport('joomla.html.pane');
JHtml::_('behavior.tooltip');
//jimport('joomla.plugin.plugin');
//jimport('joomla.event.plugin');
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
$buildadsession = JFactory::getSession();

	require_once(JPATH_COMPONENT . DS . 'helper.php');
	$socialadshelper = new socialadshelper();
	$init_balance = $socialadshelper->getbalance();
	if($init_balance!=NULL && $init_balance !=1.00)    // HARDCODED FOR NOW.......
	{
		$itemid	= $socialadshelper->getSocialadsItemid('payment');
		$not_msg	= JText::_('MIM_BALANCE');
		$not_msg	= str_replace('{clk_pay_link}','<a onclick="window.parent.document.location.href=\''.JRoute::_('index.php?option=com_socialads&view=payment&Itemid='.$itemid).'\'">'.JText::_('SA_CLKHERE').'</a>', $not_msg);
		JError::raiseNotice( 100, $not_msg );
	}

$curdate='';
global $mainframe;
$mainframe = JFactory::getApplication();
$input=JFactory::getApplication()->input;
          //$post=$input->post;

$sitename = $mainframe->getCfg('sitename');
$user = JFactory::getUser();
$ignorecnt=$this->ignoreCount123;
$backdate = date('Y-m-d', strtotime(date('Y-m-d').' - 30 days'));

$kk=0;
$chm=array();
foreach($ignorecnt as $ignorecnt1)
{
//$datedifference=(strtotime($ignorecnt1->idate)-strtotime($backdate))/(60 * 60 * 24);
$start_ts = strtotime($backdate);
$end_ts = strtotime($ignorecnt1->idate);
$diff = $end_ts - $start_ts;

$ignorecnt_day_diff	=$diff / 86400;

$ignorecnt=$ignorecnt1->ignorecount;

$ignorecnt_day_diff=$ignorecnt_day_diff-1;

$chm[]='A'.$ignorecnt.',FF9900,0,'.$ignorecnt_day_diff.',15';
//chm=A2,FF9900,0,10,15
$kk++;
}

$chm_str=implode('|',$chm);

//'A1,666666,0,30,15|A3,666666,0,28,15'

//print_r($chm_str);
?>
<div class="techjoomla-bootstrap">
<?php
if (!$user->id)
{
	?>
	<div class="alert alert-success">
	<?php echo JText::_('BUILD_LOGIN'); ?>
	</div>
	</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	return false;
}

if(!$this->adcheck) {
	echo JText::_('NOT_AUTH');
	return false;
}

require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
require_once(JPATH_COMPONENT . DS . 'helper.php');
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css');
$document->addScript(JUri::base().'components/com_socialads/js/flowplayer-3.2.9.min.js');//added by manoj stable 2.7.5
//load bootstrap file manually
$laod_boostrap=$socialads_config['load_bootstrap'];
	if(!empty($laod_boostrap))
	{
		$document->addStyleSheet(JUri::base().'media/techjoomla_strapper/css/bootstrap.min.css');
	}
//include_once JPATH_ROOT.'/media/techjoomla_strapper/strapper.php';
//AkeebaStrapper::bootstrap();
$document->addScript(JUri::root().'media/techjoomla_strapper/js/bootstrap.min.js' );

?>

<?php
$month_array_name = array(JText::_('SA_JAN'),JText::_('SA_FEB'),JText::_('SA_MAR'),JText::_('SA_APR'),JText::_('SA_MAY'),JText::_('SA_JUN'),JText::_('SA_JUL'),JText::_('SA_AUG'),JText::_('SA_SEP'),JText::_('SA_OCT'),JText::_('SA_NOV'),JText::_('SA_DEC')) ;
$charge_op = $this->chargeoption;
$msg1=JText::_('REVIEW_VALID1');
$msg2=JText::_('REVIEW_VALID2');
$msg3=JText::_('REVIEW_VALID3');
$msg4=JText::_('NO_POINTS');
$msg5=JText::_('NO_STATS');
$charge = $socialads_config['charge'];
$article_id=$socialads_config['article'];

 $js = 'var linechart_imprs;
		var linechart_clicks;
		var linechart_day_str=new Array();
	function show_cop(){

		if(techjoomla.jQuery("#coupon_chk").is(":checked"))
		{
			if(techjoomla.jQuery("#totaldisplay").val() == "" && techjoomla.jQuery("#totaldays").val() == "")
			{
				techjoomla.jQuery("#coupon_chk").attr("checked", false);
			}
			if(dochk())
				techjoomla.jQuery("#cop_tr").show();
		}
		else
		{
			techjoomla.jQuery("#cop_tr").hide();
			techjoomla.jQuery("#dis_amt").hide();
			techjoomla.jQuery("#coupon_code").val("");
		}
	}

function applycoupon(){
if(techjoomla.jQuery("#coupon_chk").is(":checked"))
{
	if(techjoomla.jQuery("#coupon_code").val() =="")
		alert("'.JText::_('ENTER_COP_COD').'");
	else
	{
	techjoomla.jQuery.ajax({
		url: "index.php?option=com_socialads&task=getcoupon&coupon_code="+document.getElementById("coupon_code").value,
		type: "GET",
		dataType: "json",
		success: function(data) {
		amt=0;
		val=0;
		if(data != 0)
		{
			if(data[0].val_type == 1)
				val = (data[0].value/100)*techjoomla.jQuery("#totalamount").val();
			else
				val = data[0].value;

			amt = round(techjoomla.jQuery("#totalamount").val()- val);
			if(amt <= 0)
				amt=0;

			techjoomla.jQuery("#dis_amt").html("<td class=\"ad-price-lable\" >'.JText::_("SHOWAD_NET_AMT_PAY").' </td><td>"+ amt+"&nbsp;"+techjoomla.jQuery("#currency").html()+"</td>");
			techjoomla.jQuery("#dis_amt").show();
		}
		else
			alert(document.getElementById("coupon_code").value + "'.JText::_("COP_EXISTS").'");

		}
	});
	}
}
}

	function submitbutton() {
		if(('.$article_id.') == 1){
			if(document.getElementById("chk_tnc").checked == true){
				getcall();
			}
			else{
				alert("'.JText::_("AGREE").'");
				return false;
			}
		}
		else if(('.$article_id.') == 0){
			getcall();
		}

	}//submitbutton() ends

	function dochk(){

		jQuery("#cop_dis_opn_hide").val(0);
				if(jQuery("#cop_tr").is(":visible") ) {

						var cop_text = document.getElementById("coupon_code").value;
						if(jQuery("#dis_amt").is(":hidden") && cop_text)
						{
							jQuery("#cop_dis_opn_hide").val(1);
						}

				}

		var form = document.adminForm;
		var totaldisplay = document.adminForm.totaldisplay.value;
		var totalamount = form.totalamount.value;
		var unlimited_ad=false;
if (document.getElementById("unlimited_ad"))

		unlimited_ad = document.getElementById("unlimited_ad").checked;
		var chargeoption = form.chargeoption.value;
				var daterangefrom = form.datefrom.value;
		var charge_points = 0;


		if((parseInt(chargeoption) >= 2) &&  (unlimited_ad != true) )
		{


		    if((daterangefrom == "") && (parseInt(chargeoption)== 2))
		    {
		      alert(datemsg);
					return false;
		    }
				var now=new Date();
				var year = now.getFullYear();
				var month = now.getMonth()+1;
				var date = now.getDate();
				if(date >=1 && date <=9)
				{
					var newdate = "0"+date;
				}
				else
				{
					var newdate = date;
				}
				if(month >=1 && month <=9)
				{
					var newmonth = "0"+month;
				}
				else
				{
					var newmonth = month;
				}

				today = year+"-"+newmonth+"-"+newdate;

				if(parseInt(chargeoption)== 2)
				{
					if((daterangefrom) < (today))
					{
						alert(wrongdates);
						return false;
					}

				}
				else
				{
					if((daterangefrom) < (today))
					{
						alert(wrongdates);
						return false;
					}

				}
			}


	if((parseInt(chargeoption)!=2))
	{
		if(((totaldisplay == "") || isNaN(totaldisplay)) || (totaldisplay <= 0))
		{
			alert("'.$msg1.'");
			document.getElementById("totaldisplay").focus();
			return false;
		}
	}
		if(document.getElementById("gateway").value=="jomsocialpoints")
		{
			charge_points = '.$charge.'*jconver;
			if(Number(jpoints) < Number(totalamount)){
				alert("'.$msg4.'"+"Currently you have "+jpoints+ "points.");
				return false;
			}
		}
		if(document.getElementById("gateway").value=="alphauserpoints")
		{
			charge_points = '.$charge.'*jconver;
			if(Number(jpoints) < Number(totalamount)){
				alert("'.$msg4.'"+"Currently you have "+jpoints+ "points.");
				return false;
			}
		}

		if(document.getElementById("gateway").value==null)
		{
			alert("'.$msg3.'");
			return false;
		}
		if(!(charge_points==0) & (totalamount < charge_points)){
			alert("'.$msg2.'"+charge_points);
			document.getElementById("totaldisplay").focus();
			return false;
		}
		if(totalamount < '.$charge.')
		{
			alert("'.$msg2.'"+'.$charge.');
			document.getElementById("totaldisplay").focus();
			return false;
		}
		return true;
	}

	function getcall(){
		if(dochk())
			//Joomla.submitbutton();
		document.adminForm.submit();
	}//function getcall ends

	function closepop(el)
	{
   		techjoomla.jQuery("#payment_tab_spacer").hide();
   		techjoomla.jQuery("#bottomdiv").hide();
			techjoomla.jQuery("#payment_tab_spacer_lower_bottom").hide();
	}


	function refreshViews()
	{

		fromDate = document.getElementById("from").value;
		toDate = document.getElementById("to").value;
		fromDate1 = new Date(fromDate.toString());
		toDate1 = new Date(toDate.toString());
		difference = toDate1 - fromDate1;
		days = Math.round(difference/(1000*60*60*24));
		if(parseInt(days)<=0)
		{
			alert("'.JText::_("DATELESS").'");
			return;

		}
		//Set Session Variables
		techjoomla.jQuery(document).ready(function(){
		var info = {};
		techjoomla.jQuery.ajax({
		    type: "GET",
		    url: "?option=com_socialads&controller=adsummary&task=SetsessionForGraph&fromDate="+fromDate+"&toDate="+toDate,
		    dataType: "json",
		    async:false,
		    success: function(data) {


		    }
		});
		//Make Chart and Get Data
		techjoomla.jQuery.ajax({
		    type: "GET",
		    url: "?option=com_socialads&controller=adsummary&task=makechart",
		    async:false,
		    dataType: "json",
		    success: function(data) {
			techjoomla.jQuery("#bar_chart_graph").html(""+data.barchart);

				var clicks_pie=0;
				var imprs_pie=0;
			document.getElementById("clicks_pie").value=data.clicks_pie;
			document.getElementById("imprs_pie").value=data.imprs_pie;

			var barchart="";
			barchart=data.barchart;
			var emptylinechart=0;
			emptylinechart=data.emptylinechart;

			linechart_imprs=data.linechart_imprs;
			linechart_clicks=data.linechart_clicks;

			if(parseInt(emptylinechart)==1)
			{
				techjoomla.jQuery("#line_chart_div").html(""+barchart);

			}
			else
			{
				google.load("visualization", "1", {packages:["corechart"]});
				linechart_day_str=data.linechart_day_str;
				linechart_day_str= linechart_day_str.split(",");
				google.setOnLoadCallback(draw_lineChart);
				draw_lineChart();

			}

			//google.load("visualization", "1", {"packages":["piechart"]});

			// Set a callback to run when the Google Visualization API is loaded.
			//google.setOnLoadCallback(drawChart);
			drawChart();

		    }
		});

		});
	}
	';
	$ad_type = $this->adtype;
	$document->addScriptDeclaration($js);

?>
<?php $link = JRoute::_('index.php?option=com_content&tmpl=component&view=article&id='.$socialads_config['tnc']);?>
<script type="text/javascript">

techjoomla.jQuery(document).ready(function() {
	techjoomla.jQuery("#payment_tab_spacer").hide();

      /* techjoomla.jQuery("#buynow").live('click', function(event) {

         		techjoomla.jQuery("#payment_tab_spacer").show();
         		techjoomla.jQuery("#bottomdiv").show();
         		techjoomla.jQuery("#chargeoption").attr("disabled",true);
         		techjoomla.jQuery("#payment_tab_spacer_lower_bottom").show();
						calpoints();
            return false;
        });*/
		techjoomla.jQuery("#del").click(function() {
			var r=confirm("<?php echo JText::_('DELETE_AD');?>");
			if (r==true){
				window.open("<?php echo JUri::base().'index.php?option=com_socialads&task=deletead&adid='.$input->get('adid',0,'INT').'&Itemid='. $input->get('Itemid',0,'INT');?>",'_parent');
			  }
		 });

    });

    techjoomla.jQuery.fn.slideFadeToggle = function(easing, callback) {
        return this.animate({ opacity: 'toggle', height: 'toggle' }, "fast", easing, callback);
    };
</script>

<?php

/*
$tabspane =& JPane::getInstance('Tabs');
$pane =& JPane::getInstance('sliders', array('allowAllClose' => true));
*/
?>


<div class="adsummary_main">
<!--form strts here for showing preview of an ad-->

<div class="componentheading page-header"><h2><?php echo JText::_('SUMMARY')?></h2></div>

		<!--TABS STARTS HERE-->

		<div class="tabbable"> <!-- Only required for left/right tabs -->
		   <ul class="nav nav-tabs">
				<li class="active"><a href="#stats_tab" data-toggle="tab"><?php echo JText::_('PREVIEW'); ?></a></li>
				<li><a href="#statistic_tab" data-toggle="tab"><?php echo JText::_('STATS'); ?></a></li>

			<?php	if(!($ad_type[0]->ad_noexpiry == 1 || $ad_type[0]->ad_alternative == 1) && ($socialads_config['select_campaign']==0)){  ?>
				<li><a href="#payment_tab" data-toggle="tab"><?php echo JText::_('HISTORY'); ?></a></li>
			<?php	}	?>
  		   </ul>

			<div class="tab-content ad_summary_div">

				<div class="tab-pane active" id="stats_tab">
					<div class="row-fuild">
						<div class = "ad_view span4">
							<?php echo $this->preview; ?>
						</div>
						<!--	<br /> <br /> -->
						<div class="preview-buttons span8">
							<?php
									$date2 = $ad_type[0]->ad_enddate;

									$todays_date = date("Y-m-d");
									$today = strtotime($todays_date);

									$expiration_date2 = strtotime($date2);
									$expiration_date_day=date("Y-m-d",$expiration_date2);
									$expiration_date_daytimestmp=strtotime($expiration_date_day);
									$difference=$expiration_date_daytimestmp-$today;
									?>

								<?php
								if($socialads_config['select_campaign']==0)   // hide buy button if campaign is selected in backend
								{

									if($ad_type[0]->ad_noexpiry == 0 && $ad_type[0]->ad_alternative == 0 && ($expiration_date2<$today) && ($ad_type[0]->ad_credits == 0) && ($ad_type[0]->camp_id == 0))
									{?>
									<input type="button" id="buynow" name="buynow" class="button btn btn-success" value="<?php  echo JText::_('BUYNOW'); ?>" onclick="javascript:parent.window.location='<?php echo JRoute::_(JUri::base().'index.php?option=com_socialads&view=buildad&sa_addCredit=1&adid='.$input->get('adid',0,'INT').'&Itemid='. $input->get('Itemid',0,'INT'));?>'" /><br /><br />
									<?php  }
									else if(!empty($ad_type[0]->ad_credits) ){ ?>
									<input type="button" id="buynow" name="buynow" class="button btn btn-success" value="<?php echo JText::_('BUYMORE'); ?>" onclick="javascript:parent.window.location='<?php echo JRoute::_(JUri::base().'index.php?option=com_socialads&view=buildad&sa_addCredit=1&adid='.$input->get('adid',0,'INT').'&Itemid='. $input->get('Itemid',0,'INT'));?>'" /><br /><br />

									<?php }
								}
									?>

								<input type="button" id="edit" class="button btn btn-warning" value="<?php echo JText::_('EDIT')?>" onclick="javascript:parent.window.location='<?php echo JRoute::_(JUri::base().'index.php?option=com_socialads&view=buildad&adid='.$input->get('adid',0,'INT').'&Itemid='. $input->get('Itemid',0,'INT'));?>'"/><br /><br />
								<!-- a class="editbutton" target="_parent" id="edit" name="edit" href="<?php echo JRoute::_(JUri::base().'index.php?option=com_socialads&view=managead&adid='.$input->get('adid',0,'INT').'&Itemid='. $input->get('Itemid',0,'INT'));?>"><?php echo JText::_('EDIT')?></a><br /><br /> -->
								<input type="button" id="del" class="button btn btn-danger" value="<?php echo JText::_('DELETE')?>"/><br /><br />
						</div><!--preview-buttons ENDS-->
					</div>
				</div>

					<!-- TAB2 START HERE-->
				<div class="tab-pane" id="statistic_tab">
					<div class="row-fluid ad_summary_toolbar"><!--1st div start for bar graph-->
						<div style="display:inline-block">
							<div style="display:inline-block" class=""><?php echo JText::_('COM_SOCIALADS_FROM_DATE'); ?> &nbsp;</div>

							<div style="display:inline-block" class=""><?php echo JHtml::_('calendar', $backdate, 'from', 'from', '%Y-%m-%d', array('class'=>' input-small')); ?> &nbsp;</div>

							<div style="display:inline-block" class=""> <?php echo  JText::_("COM_SOCIALADS_TO"); ?> &nbsp;</div>

							<div style="display:inline-block" class=""> <?php echo JHtml::_('calendar', date('Y-m-d'), 'to', 'to', '%Y-%m-%d', array('class'=>' input-small')); ?> &nbsp;</div>
						</div>
						<input id="btnRefresh" type="button" class="btn btn-success" value="<?php echo  JText::_("COM_SOCIALADS_GO"); ?>" style="font-weight: bold;" onclick="refreshViews(); document.getElementById('from').style.backgroundColor = 'white'; document.getElementById('to').style.backgroundColor = 'white'"/>
						<div class="clerfix"></div>
					</div>
					<div class="row-fluid">
							<div class="span6">
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
									$toDate=date('Y-m-d');

									$dateMonthYearArr = array();
									$fromDateSTR = strtotime($fromDate);
									$toDateSTR = strtotime($toDate);

									if(empty($statsforbar[0]) && empty($statsforbar[1]))
									{
									  ?>
									  <div class="alert alert-success">
									 <?php echo 	$barchart=JText::_('NO_STATS');  ?>
									 </div>
									 <?php
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
												$barchart=JText::_("NO_CLICKS");
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
												 if(!$finalstats_date1)
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

											//echo "\n-----------";
											if($finalstats_imprs)
											$imprs=implode(",",$finalstats_imprs);
											//echo "\n-----------";
											if($finalstats_clicks)
											$clicks=implode(",",$finalstats_clicks);

											echo '<div id="line_chart_div" class="chart_div"></div>';
									}
									?>
								</div><!--1st div end for bar graph SPAN6-->

								<div  class="span4"><!--pie-chart starts here-->
									 <?php

										$statsforpie = $this->statsforpie;
									  //	print_r($statsforpie);die;
										$clicks_pie = 0;
										$imprs_pie = 0;
										$currentmonth='';
											//print_r($statsforbar);
										//print_r($statsforbar[1]);

									if(empty($statsforbar[0]) && empty($statsforbar[1]))
									{
										echo "";
									}
									else
									{
										if(!empty($statsforpie[1]))
										{
											for($z = 0 ; $z < count($statsforpie[1]); $z++)
											{
												$clicks_pie= $statsforpie[1][$z]->value;
												$currentmonth=$statsforpie[1][$z]->month;
											}
										}

									 // echo "clk=";echo $clicks;
										if(!empty($statsforpie[0]))
										{
											for($z = 0 ; $z < count($statsforpie[0]); $z++)
											{
												$imprs_pie = $statsforpie[0][$z]->value;
												$currentmonth=$statsforpie[0][$z]->month;
											}
										}

										// $currentmonth = $month_array_name[$currentmonth];
									}

									 ?>

								   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
									<script type="text/javascript">


									<?php
									$session = JFactory::getSession();
									if(!$session->get('socialads_from_date','') and  !$session->get('socialads_end_date',''))
									{
									if($imprs or $clicks)
									{
									?>
									 linechart_imprs=[<?php echo $imprs;?>]
									 linechart_clicks=[<?php echo $clicks;?>]
									 linechart_day_str=[<?php echo $day_str_final; ?>]
									google.load("visualization", "1", {packages:["corechart"]});
									 //var visualization = new google.visualization.LineChart(container);
									  google.setOnLoadCallback(draw_lineChart);
									<?php
									}
									}
										 ?>



									////////////////////////////


									  function draw_lineChart() {
										//	alert("in");
										var data = new google.visualization.DataTable();
										data.addColumn('string', '<?php echo JText::_("LINE_YEAR");?>');
										data.addColumn('number', '<?php echo JText::_("IMPRS");?>');
										data.addColumn('number', '<?php echo JText::_("CLICKS");?>');


										var impr=linechart_imprs;
										var clicks=linechart_clicks;
										var assign_date=linechart_day_str;
										data.addRows(assign_date.length+1);
											//alert(impr)
									/*console.log("impr--------");
									   console.log(impr);
									   console.log("clicks--------");
										console.log(clicks);
									   console.log("assign_date--------");
										console.log(assign_date);*/
										for(var i=0;i<assign_date.length;i++)
										{
											data.setValue(i, 0, assign_date[i].toString());
											//console.log("----------");
											//console.log(assign_date[i].toString());
											// console.log(i);
											// console.log("----------");
											// if(parseInt(impr[i])!=0)
											data.setValue(i, 1, impr[i]);
											// if(parseInt(clicks[i])!=0)
											data.setValue(i, 2, clicks[i]);
										}

										var chart = new google.visualization.LineChart(document.getElementById('line_chart_div'));
										chart.draw(data, {
										colors: ['#67c2ef','#78CD51'],
										vAxis:{textStyle:{fontSize:'10'},title:'<?php echo JText::_("LINE_CHART_VAXIS");?>',titleTextStyle:{fontSize:'11'}},
										hAxis:{slantedText:true,textStyle:{fontSize:'10'},title:'',titleTextStyle:{fontSize:'11'}},
										title: '<?php echo JText::_("LINE_CHART_TITLE");?>'});
									  }




									///////////////////////////////////////////////////
									// Load the Visualization API and the piechart package.
						//google.load('visualization', '1', {'packages':['piechart']});

									// Set a callback to run when the Google Visualization API is loaded.
									google.setOnLoadCallback(drawChart);

									// Callback that creates and populates a data table,
									// instantiates the pie chart, passes in the data and
									// draws it.
									function drawChart() {
										var clicks_pie=0;
										var imprs_pie=0;

										imprs_pie=parseInt(document.getElementById("imprs_pie").value);
										clicks_pie=parseInt(document.getElementById("clicks_pie").value);
										if(parseInt(clicks_pie)==0 && parseInt(imprs_pie)==0)
										{
										document.getElementById('chart_div').innerHTML="";
										return;

										}
									// Create our data table.
										var data = new google.visualization.DataTable();
										data.addColumn('string', 'Event');
										data.addColumn('number', 'Amount');
										data.addRows([


											['<?php echo JText::_("IMPRS");?>',imprs_pie],
											['<?php echo JText::_("CLICKS");?>',clicks_pie]
										]);

										var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
										chart.draw(data, { is3D:true, colors: ['#67c2ef','#78CD51'],title: '<?php echo JText::_("CLKIMPRATIOMONT")?>'});
									}
									</script>

										<div id="chart_div" class="chart_div"></div>
								</div><!--div for stats pie SPAN4-->

						<div class="clearfix"></div>
				</div> <!--row fluid ends-->
				</div><!--tab 2 satatistic ends here-->
				<div class="tab-pane" id="payment_tab">
						<form method="POST" action="">
							<div class="table-responsive">
								<table class="table table-striped" >
									<thead>
												<tr class="summery-tr" >
													<th width="9%"><?php echo JText::_("DATE");?></th>

												<th	width="9%">
														<?php echo JText::_("SUBSCRIPTION_ID");?>
													</th>

													<th><?php echo JText::_("TRANS");?></th>
													<th><?php echo JText::_("CREDITS_BOUGHT");?></th>

													<th><?php echo JText::_("SA_AMT");?></th>
												  <th><?php echo JText::_("STATUS");?></th>
												  <th ><?php echo JText::_("PAYMENT_MODE")?></th>
												  <th><?php echo JText::_("CURRENCY")?></th>
													<th></th>

													<!--th>&nbsp;</th-->
													<!--<th><?php echo JText::_("STATUS_PENDING")?></th>-->
													</tr>
												</thead>
												<?php
												$k = 0;
												foreach($this->payinfo as $info)
												{ ?>
												<tr  class="<?php echo 'sectiontableentry'.($k+1); ?>">
													<td class="summery-td">
														<?php echo $info->cdate;?>
													</td>
												<?php
												if($info->subscription_id)
												{
												?>
												<td class="summery-td">
														<?php echo $info->subscription_id;?>
													</td>
												<?php
												}
												else
												{
												?>
												<td class="summery-td">
													</td>
													<?php
													}
													?>
													<td class="summery-td">
														<?php echo $info->transaction_id;?>
													</td>

													<td class="summery-td">
														<?php echo $info->ad_credits_qty;?>
													</td>


													<td class="summery-td">
														<?php echo $info->ad_amount;?>
													</td>

													<td class="summery-td">
														<?php
														 if($info->status =='P')
															{echo JText::_("SA_PENDIN");}
														 else if($info->status == 'C')
															{echo JText::_("SA_APPROVE");}
														 else if($info->status == 'F' or $info->status == 'X')
															{echo JText::_("SA_REJEC");}
														?>
													</td>
													<td class="summery-td">
														<?php
														$pay_method='';
														$pluginParams='';

														$plugin =JPluginHelper::getPlugin('socialads',$info->processor);

														if($plugin)
														$pluginParams = json_decode( $plugin->params );

														if($pluginParams)
														 $pay_method = $pluginParams->plugin_name;

														if($pay_method)
															echo $pay_method;
														else
														{
															if($info->processor)
															echo $info->processor;
															else
															{
															$pay_method1=array();
															$pay_method1=explode("_",$info->processor);
															if(isset($pay_method1[1]))
																echo $pay_method1[1];
															}
														}
													?>
													</td>
													<td><?php echo $charge = $socialads_config['currency'];?></td>

												<td class="summery-td">
														<?php
														$link = JUri::base().'index.php?option=com_socialads&controller=showad&task=cancelsubscription&subscriptionid='.$info->subscription_id.'&id='.$info->id.'&processor='.$info->processor.'&ad_id='.$info->ad_id;
														$link =	JRoute::_($link);
														?>
													<a  href="<?php echo $link ?>" id="modal_info" class="modal_info">
													<?php
													if($info->subscription_id)
												{
														echo JText::_('CANCELSUBSCRIPTION');
													}
												 ?>
													</a>
													</td>

												</tr>
												<?php $k = 1 - $k;
											  } ?>


											</table>
							</div>
						</form>

				</div>


			</div><!--tab-content ENDS-->
		</div> <!--TABABLE ENDS-->


			 <!-- Extra code for zone -->
	 				 <input type="hidden" name="imprs_pie" id="imprs_pie" value="<?php echo $imprs_pie; ?>">
	 				 <input type="hidden" name="clicks_pie" id="clicks_pie" value="<?php echo $clicks_pie; ?>">
					<input type="hidden" name="pric_click" id="pric_click" value="<?php echo $this->zoneprice[0]->per_click; ?>">
					<input type="hidden" name="pric_day" id="pric_day" value="<?php echo $this->zoneprice[0]->per_day; ?>">
					<input type="hidden" name="pric_imp" id="pric_imp" value="<?php echo $this->zoneprice[0]->per_imp; ?>">

					<!-- Extra code for zone -->

					<!-- added in 2.7.5 beta3 -->
						<input type="hidden" name="editview" id="editview" value="0">
<script type="text/javascript">
techjoomla.jQuery(document).ready(function(){
			document.getElementById("clicks_pie").value=<?php echo $clicks_pie; ?>;
			document.getElementById("imprs_pie").value=<?php echo $imprs_pie; ?>;
			});
</script>

</div>	<!--adsummary_main ENDS-->
</div><!--techjoomla-bootstrap ENDS-->

