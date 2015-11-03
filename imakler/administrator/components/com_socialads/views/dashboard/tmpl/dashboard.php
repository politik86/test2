<?php
defined( '_JEXEC' ) or die( ';)' );
jimport( 'joomla.form.formvalidator' );
jimport('joomla.html.pane');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
jimport( 'joomla.html.parameter' );
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
$db=JFactory::getDBO();
$result=$this->allincome;
$document = JFactory::getDocument();
$document->addScript(JURI::base().'components/com_socialads/js/adminsocialads.js');
$document->addStyleSheet(JURI::base().'components/com_socialads/css/sadashboard.css');

if(JVERSION < 3.0)
{
$document->addScript( JURI::root().'components/com_socialads/js/jquery-1.7.1.min.js' );
}
$model=$this->getModel('dashboard');
$mntnm_cnt=1;
$i=0;
////////////////////
$session = JFactory::getSession();
$session->set('socialads_from_date','');
$session->set('socialads_end_date', '');
$session->set('statsforbar', '');
$session->set('statsforpie', '');
$session->set('ignorecnt', '');
$session->set('statsfor_line_day_str_final', '');
$session->set('statsfor_line_imprs', '');
$session->set('statsfor_line_clicks', '');
$session->set('periodicorderscount', '');
$ignorecnt=array();
$ignorecnt=$this->ignoreCount123;
$backdate = date('Y-m-d', strtotime(date('Y-m-d').' - 30 days'));

$kk=0;


///////////////////////////////////

$curdate='';
foreach($this->AllMonthName as $AllMonthName)
{
	$AllMonthName_final[$i]=$AllMonthName['month'];
	$curr_MON=$AllMonthName['month'];
	$month_amt_val[$curr_MON]=0;
		$i++;

}

$emptybarchart=1;
foreach($this->MonthIncome as $MonthIncome)
{
$month_year='';
 $month_year=$MonthIncome->YEARNM;
$month_name=$MonthIncome->MONTHSNAME;

$month_int = (int)$month_name;
$timestamp = mktime(0, 0, 0, $month_int);
$curr_month=date("F", $timestamp);

foreach($this->AllMonthName as $AllMonthName)
{
//print_r($AllMonthName);die;
if(($curr_month==$AllMonthName['month']) and ($MonthIncome->ad_amount) and ($month_year==$AllMonthName['year']))
$month_amt_val[$curr_month]=str_replace(",",'',$MonthIncome->ad_amount);

if($MonthIncome->ad_amount)
$emptybarchart=0;
else
$emptybarchart=1;


}

}
//echo $emptybarchart;die;
 $month_amt_str=implode(",",$month_amt_val);
 $month_name_str=implode("','",$AllMonthName_final);
 $month_name_str="'".$month_name_str."'";
 $month_array_name=array();
 /////////////////////

  $js = "

  var linechart_imprs;
	var linechart_clicks;
	var linechart_day_str=new Array();

  function refreshViews()
	{

	jQuery.noConflict();
		fromDate = document.getElementById('from').value;
		toDate = document.getElementById('to').value;
		fromDate1 = new Date(fromDate.toString());
		toDate1 = new Date(toDate.toString());
		difference = toDate1 - fromDate1;
		days = Math.round(difference/(1000*60*60*24));
		if(parseInt(days)<=0)
		{
			alert('".JText::_('DATELESS')."');
			return;

		}
		//Set Session Variables
		jQuery(document).ready(function(){
		var info = {};
		jQuery.ajax({
		    type: 'GET',
		    url: '?option=com_socialads&controller=dashboard&task=SetsessionForGraph&fromDate='+fromDate+'&toDate='+toDate,
		    dataType: 'json',
		    async:false,
		    success: function(data) {


		    }
		});
		//Make Chart and Get Data
		jQuery.ajax({
		    type: 'GET',
		    url: '?option=com_socialads&controller=dashboard&task=makechart',
		    async:false,
		    dataType: 'json',
		    success: function(data) {
				document.getElementById('pending_orders').value=data.pending_orders;
				document.getElementById('confirmed_orders').value=data.confirmed_orders;
				document.getElementById('refund_orders').value=data.refund_orders;
				document.getElementById('periodic_orders').innerHTML = data.periodicorderscount;

			google.setOnLoadCallback(drawPieChart);
			drawPieChart();

		    }
		});

		});
	}
	";
  $document->addScriptDeclaration($js);


?>




<style type="text/css">
 .pagination a{
   text-decoration:none;
 }
</style>
<form action="index.php" name="adminForm" id="adminForm"  method="post">
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



<?php
$xml = JFactory::getXML(JPATH_SITE.'/administrator/components/com_socialads/socialads.xml');
$currentversion=(string)$xml->version;
//Load the xml file
//$xml->loadFile(JPATH_SITE.'/administrator/components/com_socialads/socialads.xml');
if($xml->document)
foreach($xml->document->_children as $var)
{
	if($var->_name=='version')
		$currentversion = $var->_data;
}


$logo_path='<img src="'.JURI::base().'components/com_socialads/images/techjoomla.png" alt="TechJoomla" style="vertical-align:text-top;"/>';
?>

<script type="text/javascript">
	function vercheck()
	{
		callXML('<?php echo $currentversion; ?>');
		if(document.getElementById('NewVersion').innerHTML.length<220)
		{
			document.getElementById('NewVersion').style.display='inline';
		}
	}
	function callXML(currversion)
	{
		if (window.XMLHttpRequest)
		{
			xhttp=new XMLHttpRequest();
		}
		else // Internet Explorer 5/6
		{
			xhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}

		xhttp.open("GET","<?php echo JURI::base(); ?>index.php?option=com_socialads&task=getVersion",false);
		xhttp.send("");
		latestver=xhttp.responseText;

		if(latestver!=null)
		{
			if(currversion == latestver)
			{
				document.getElementById('NewVersion').innerHTML='<span style="display:inline; color:#339F1D;;">&nbsp;<?php echo JText::_("LAT_VERSION");?> <b>'+latestver+"</b></span>";
			}
			else
			{
				document.getElementById('NewVersion').innerHTML='<span style="display:inline; color:#FF0000;">&nbsp;<?php echo JText::_("LAT_VERSION");?> <b>'+latestver+"</b></span>";
			}
		}
	}
</script>
</form>

<div class="techjoomla-bootstrap"><!--START techjoomla-bootstrap-->
	<div class="row-fluid">
		<div class="span8">
			<div class="row-fluid">
				<div class="well">

					<div id="container_left1 ">
						<?php
						if(!$this->allincome)
						$this->allincome=0;
						echo $title = "<div class='sa-docs-alltime-income'>
							<span id=\"totalOrdersLifeTitle\" style=\"float: left;\">" . JText::_('') . "</span><div style=\"\">" .'' ."</div>";
						echo $text = "<div id=\"ordersTotalLife\" style=\"text-align: center; font-size: 16px; font-weight: bold;\"> ".$this->allincome."<span style=\"\">".$socialads_config['currency']."</span></div></div>";

						//echo $html = $model->getbox($title,$text); ?>
					</div>


					<div id="container_left2">

					<?php
					echo $title = "<div class='sa-docs-monthly-orders-income'>
						<span id=\"totalOrdersLifeTitle\" style=\"float: left;\">" . JText::_('') . "</span>
							<div style=\"\"></div>";
					echo $text = "<div id=\"monthin\" style=\"text-align: center; font-size: 16px; font-weight: bold;\"> </div></div>";

					//echo $html = $model->getbox($title,$text); ?>
					</div>
				</div>

				<div class="well">
					<div class="row-fluid">
						<div class="pull-right" >
							<div class="inline">&nbsp;<?php echo JText::_('FROM_DATE'); ?>&nbsp;</div>

							<div class="inline"><?php echo JHTML::_('calendar', $backdate, 'from', 'from', '%Y-%m-%d', array('class'=>'inputbox input-medium')); ?> </div>
							<div class="inline"> <?php echo  "&nbsp;".JText::_('TO_DATE')."&nbsp;"; ?> </div>
							<div class="inline"> <?php echo JHTML::_('calendar', date('Y-m-d'), 'to', 'to', '%Y-%m-%d', array('class'=>'inputbox input-medium')); ?> </div>


								&nbsp;&nbsp;<input id="btnRefresh" type="button" class="btn btn-success" value="<?php echo JText::_('COM_SA_GO'); ?>" style="font-weight: bold;" onclick="refreshViews(); document.getElementById('from').style.backgroundColor = 'white'; document.getElementById('to').style.backgroundColor = 'white'"/>

						</div>
					</div>

				<div id="container_right1 ">
					<style type="text/css">
						div.inline { float:left; }
						.clearBoth { clear:both; }
					</style>


					<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">

					// Load the Visualization API and the piechart package.
					google.load('visualization', '1', {'packages':['corechart']});

					// Set a callback to run when the Google Visualization API is loaded.
					google.setOnLoadCallback(drawChart);
					// Create and populate the data table.
					function drawChart() {

					<?php if(!$this->allincome) {?>

					document.getElementById("monthin").innerHTML='<?php  echo '<h5>'.JText::_("NO_STATS").'<h5>'; ?>';
					return;
					<?php } ?>
					var data = new google.visualization.DataTable();
					//alert(data);

					var raw_dt1=[<?php echo $month_amt_str;?>];
					//console.log(raw_dt1);
					var raw_data = [raw_dt1];
				 var Months = [<?php echo $month_name_str;?>];

					data.addColumn("string", "<?php echo JText::_('BAR_CHART_HAXIS_TITLE');?>");

					data.addColumn("number","<?php echo JText::_('BAR_CHART_VAXIS_TITLE').' ('.$socialads_config['currency'].')';?>");

					data.addRows(Months.length);

					for (var j = 0; j < Months.length; ++j) {
					  data.setValue(j, 0, Months[j].toString());
					}
					for (var i = 0; i  < raw_data.length; ++i) {
					  for (var j = 1; j  <=(raw_data[i].length); ++j) {
						data.setValue(j-1, i+1, raw_data[i][j-1]);

					  }
					}

					// Create and draw the visualization.
					new google.visualization.ColumnChart(document.getElementById("monthin")).
						draw(data,
							 {title:'<?php echo JText::_("");?>',
							  width:'48%', height:300,
							  fontSize:'13px',
							  colors: ['#78cd51','#67c2ef','#0174DF'],
							  backgroundColor:'transparent',
							  hAxis: {title: "<?php echo JText::_('BAR_CHART_HAXIS_TITLE');?>"},
							  vAxis: {title: "<?php echo JText::_('BAR_CHART_VAXIS_TITLE').' ('.$socialads_config['currency'].')';?>"}

							  }
						);
					}
					</script>

			<?php
		if(!$this->tot_periodicorderscount)
		$this->tot_periodicorderscount=0;
			echo $title = "<div class='sa-docs-periodic-income'>
						<span id=\"totalOrdersLifeTitle\" >" . JText::_('') . "
						</span>
					";
			echo $text = "<div style=\"text-align: center; font-size: 16px; font-weight: bold;\"><span id=\"periodic_orders\">".$this->tot_periodicorderscount."</span><span style=\"margin-left: 5px;\">".$socialads_config['currency']."</span></div></div>";

			//echo $html = $model->getbox($title,$text); ?>
			</div>


			<div class="clearfix"></div>
					<div id="container_right2" class="">

					<?php
					echo $title = "<div class='sa-docs-periodic-orders' >
						<span id=\"totalOrdersLifeTitle\" style=\"float: left;\">" . JText::_('') . "</span><div style=\"\">" . ' ' . "</div>";
					echo $text = "<div id=\"chart_div-backend\" style=\"text-align: center; font-size: 16px; font-weight: bold;\"></div> </div>";

					//echo $html = $model->getbox($title,$text); ?>

							 <?php

								$statsforpie = $this->statsforpie;
								$currentmonth='';
								$pending_orders=$confirmed_orders=$refund_orders=0;

							if(empty($statsforpie[0]) && empty($statsforpie[1]) && empty($statsforpie[2]))
							{
								$barchart=JText::_('NO_STATS');
								$emptylinechart=1;
							}
							else
							{
								if(!empty($statsforpie[0]))
								{
										 $pending_orders= $statsforpie[0];
								}
							 // echo "clk=";echo $clicks;
								if(!empty($statsforpie[1]))
								{
										 $confirmed_orders = $statsforpie[1];
								}
								if(!empty($statsforpie[1]))
								{
										 $refund_orders = $statsforpie[2];
								}
							}

							$emptypiechart=0;
							if(!$pending_orders and !$confirmed_orders and !$refund_orders)
							$emptypiechart=1;
							 ?>

						   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
							<script type="text/javascript">

							// Load the Visualization API and the piechart package.
							//google.load('visualization', '1', {'packages':['piechart']});

							// Set a callback to run when the Google Visualization API is loaded.
							google.setOnLoadCallback(drawPieChart);

							// Callback that creates and populates a data table,
							// instantiates the pie chart, passes in the data and
							// draws it.
							function drawPieChart() {
								var pending_orders=0;
								var confirmed_orders=0;
								var refund_orders=0;
								pending_orders=parseInt(document.getElementById("pending_orders").value);
								confirmed_orders=parseInt(document.getElementById("confirmed_orders").value);
								refund_orders=parseInt(document.getElementById("refund_orders").value);
								if(pending_orders==0 && confirmed_orders==0 && refund_orders==0){
									document.getElementById("chart_div-backend").innerHTML='<?php  echo '<h5>'.JText::_("NO_STATS").'<h5>'; ?>';
									return;
								}

							// Create our data table.
								var data = new google.visualization.DataTable();
								data.addColumn('string', 'Event');
								data.addColumn('number', 'Amount');
								data.addRows([

									['<?php echo JText::_("PENDING_ORDS");?>',pending_orders],
									['<?php echo JText::_("CONFIRM_ORDS");?>',confirmed_orders],
									['<?php echo JText::_("REFUND_ORDS");?>',refund_orders]
								]);

								var chart = new google.visualization.PieChart(document.getElementById('chart_div-backend'));
								chart.draw(data, { width: '100%', height: 200,is3D:true,fontSize:'10px',
									colors: ['#67c2ef','#78CD51','#FABB3D'],
									backgroundColor:'transparent',
									title: '<?php echo JText::_("").' '.$currentmonth;?> '});
							}
							</script>

					</div><!--End container_right2-->
				</div>

			</div>
		</div>

		<div class="span4">
				<!---->
					<?php

			$versionHTML = '<span class="label label-info pull-right">' .
								JText::_('COM_SA_HAVE_INSTALLED_VER') . ': ' . $this->version .
							'</span>';
			if ($this->latestVersion)
			{
				if ($this->latestVersion->version > $this->version)
				{
					$versionHTML = '<div class="alert alert-error">' .
										'<i class="icon-puzzle install"></i>' .
										JText::_('COM_SA_HAVE_INSTALLED_VER') . ': ' . $this->version .
										'<br/>' .
										'<i class="icon icon-info"></i>' .
										JText::_("COM_SA_NEW_VER_AVAIL") . ': ' .
										'<span class="socialads_latest_version_number">' .
											$this->latestVersion->version .
										'</span>
										<br/>' .
										'<i class="icon icon-warning"></i>' .
										'<span class="small">' .
											JText::_("COM_SA_LIVE_UPDATE_BACKUP_WARNING") . '
										</span>' . '
									</div>
									<div>
										<a href="index.php?option=com_installer&view=update" class="socialads-btn-wrapper btn btn-small btn-primary">' .
											JText::sprintf('COM_SA_LIVE_UPDATE_TEXT', $this->latestVersion->version) . '
										</a>
										<a href="' . $this->latestVersion->infourl . '/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=socialads&utm_content=updatedetailslink&utm_campaign=socialads_ci' . '" target="_blank" class="socialads-btn-wrapper btn btn-small btn-info">' .
											JText::_('COM_SA_LIVE_UPDATE_KNOW_MORE') . '
										</a>
									</div>';
				}
			}
			?>
<!---->
				<?php if (!$this->downloadid): ?>
 					<div class="">
 						<div class="">
 							<div class="alert alert-warning">
 								<?php echo JText::sprintf('COM_SA_LIVE_UPDATE_DOWNLOAD_ID_MSG', '<a href="https://techjoomla.com/my-account/add-on-download-ids" target="_blank">' . JText::_('COM_SA_LIVE_UPDATE_DOWNLOAD_ID_MSG2') . '</a>'); ?>
 							</div>
 						</div>
 					</div>
 				<?php endif; ?>
 				<div class="">
					 <div class="">
 						<?php echo $versionHTML; ?>
 					</div>
 					<br><br>
<!---->

			<div class="well well-small">
				<div class="module-title nav-header">
					<strong><?php echo JText::_('COM_sA'); ?></strong>
				</div>
				<hr class="hr-condensed"/>

				<div class="row-fluid">
					<div class="span12 alert alert-success"><?php echo JText::_('ABOUT1'); ?></div>
				</div>

				<div class="row-fluid">
					<div class="span12">
						<p class="pull-right"><span class="label label-info"><?php echo JText::_('COM_SA_LINKS'); ?></span></p>
					</div>
				</div>

				<div class="row-striped">
					<div class="row-fluid">
						<div class="span12">
							<a href="http://techjoomla.com/table/documentation-for-socialads/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=socialads&utm_content=textlink&utm_campaign=socialads_ci" target="_blank"><i class="icon-file"></i> <?php echo JText::_('COM_SA_DOCS');?></a>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<a href="http://techjoomla.com/documentation-for-socialads/faqs-for-socialads.html/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=socialads&utm_content=textlink&utm_campaign=socialads_ci" target="_blank">
								<?php
								if(JVERSION >= '3.0')
									echo '<i class="icon-help"></i>';
								else
									echo '<i class="icon-question-sign"></i>';
								?>
								<?php echo JText::_('COM_SA_FAQS');?>
							</a>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<a href="http://feeds.feedburner.com/techjoomla/blogfeed" target="_blank">
								<?php
								if(JVERSION >= '3.0')
									echo '<i class="icon-feed"></i>';
								else
									echo '<i class="icon-bell"></i>';
								?> <?php echo JText::_('COM_SA_RSS');?></a>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span12">
							<a href="https://techjoomla.com/support-tickets/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=socialads&utm_content=textlink&utm_campaign=socialads_ci" target="_blank">
								<?php
								if(JVERSION >= '3.0')
									echo '<i class="icon-support"></i>';
								else
									echo '<i class="icon-user"></i>';
								?> <?php echo JText::_('COM_SA_TECHJOOMLA_SUPPORT_CENTER'); ?></a>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<a href="http://extensions.joomla.org/extensions/extension/ads-a-affiliates/banner-management/socialads-for-joomla" target="_blank">
								<?php
								if(JVERSION >= '3.0')
									echo '<i class="icon-quote"></i>';
								else
									echo '<i class="icon-bullhorn"></i>';
								?> <?php echo JText::_('COM_SA_LEAVE_JED_FEEDBACK'); ?></a>
						</div>
					</div>
				</div>

				<br/>
<!--
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6"><?php echo JText::_('COM_SA_HAVE_INSTALLED_VER'); ?></div>
						<div class="span6"><?php echo $currentversion; ?></div>
					</div>

					<div class="row-fluid">
						<div class="span6">
							<button class="btn btn-small" type="button" onclick="vercheck();"><?php echo JText::_('COM_SA_CHECK_LATEST_VERSION');?></button>
						</div>
						<div class="span6" id='NewVersion'></div>
					</div>
				</div>
-->
				<div class="row-fluid">
					<div class="span12">
						<p class="pull-right">
							<span class="label label-info"><?php echo JText::_('COM_SA_STAY_TUNNED'); ?></span>
						</p>
					</div>
				</div>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span4"><?php echo JText::_('COM_SA_FACEBOOK'); ?></div>
						<div class="span8">
							<!-- facebook button code -->
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));</script>
							<div class="fb-like" data-href="https://www.facebook.com/techjoomla" data-send="true" data-layout="button_count" data-width="250" data-show-faces="false" data-font="verdana"></div>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span4"><?php echo JText::_('COM_SA_TWITTER'); ?></div>
						<div class="span8">
							<!-- twitter button code -->
							<a href="https://twitter.com/techjoomla" class="twitter-follow-button" data-show-count="false">Follow @techjoomla</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span4"><?php echo JText::_('COM_SA_GPLUS'); ?></div>
						<div class="span8">
							<!-- Place this tag where you want the  1 button to render. -->
							<div class="g-plusone" data-annotation="inline" data-width="300" data-href="https://plus.google.com/102908017252609853905"></div>
							<!-- Place this tag after the last  1 button tag. -->
							<script type="text/javascript">
							(function() {
							var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
							po.src = 'https://apis.google.com/js/plusone.js';
							var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
							})();
							</script>
						</div>
					</div>
				</div>

				<br/>
				<div class="row-fluid">
					<div class="span12 center">
						<?php
						$logo_path='<img src="'.JURI::base().'components/com_socialads/images/techjoomla.png" alt="TechJoomla" class="jbolo_vertical_align_top"/>';
						?>
						<a href='http://techjoomla.com/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=socialads&utm_content=logolink&utm_campaign=socialads_ci' target='_blank'>
							<?php echo $logo_path;?>
						</a>
						<p><?php echo JText::_('COM_SA_COPYRIGHT'); ?></p>
					</div>
				</div>
			</div>
		</div><!--END span4 -->

	</div>
</div>

<!-- Extra code for zone -->
<input type="hidden" name="pending_orders" id="pending_orders" value="<?php if($pending_orders) echo $pending_orders; else echo '0'; ?>">
<input type="hidden" name="confirmed_orders" id="confirmed_orders" value="<?php if($confirmed_orders) echo $confirmed_orders; else echo '0';  ?>">
<input type="hidden" name="refund_orders" id="refund_orders" value="<?php if($refund_orders) echo $refund_orders; else echo '0'; ?>">
<!-- Extra code for zone -->

<script type="text/javascript">
			jQuery(document).ready(function(){
				document.getElementById("pending_orders").value=<?php if($pending_orders) echo $pending_orders; else echo '0'; ?>;
				document.getElementById("confirmed_orders").value=<?php if($confirmed_orders) echo $confirmed_orders; else echo '0'; ?>;
				document.getElementById("refund_orders").value=<?php  if($refund_orders) echo $refund_orders; else echo '0'; ?>;

			});
</script>

