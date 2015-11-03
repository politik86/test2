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

$total_a = $this->total_a;
$total_b = $this->total_b;
$total_c = $this->total_c;
$total_o = $this->total_o;
$total_r = $this->total_r;
$currencydef = trim($this->currencydef," ");
//JHtml::_('behavior.framework',true);
$document = JFactory::getDocument();

$document->addScript(JURI::root()."administrator/components/com_adagency/js/jquery.flot.js");
$document->addScript(JURI::root()."administrator/components/com_adagency/js/jquery.flot.time.js"); 
include_once(JPATH_SITE.DS."administrator".DS."components".DS."com_adagency".DS."helpers".DS."amcharts".DS."daily_chart.php");

$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/css/g_graph.css");

$revenue = $this->getRevenue();
$orders = $this->getOrders();
$pending_ads = $this->getPendingAds();
$pending_advertisers = $this->getPendingAdvertisers();
$pending_campaigns = $this->getPendingCampaigns();

$task = JRequest::getVar("task", "");
if($task == "vimeo"){
	return false;
}

?>

<div class="row-fluid">
	<div class="span12">
    	<?php
			$form_button = '<form id="prform" name="prform" target="_blank" style="margin:0px; float:left;" method="post" action="http://www.ijoomla.com/index.php?option=com_digistore&controller=digistoreCart&task=add&pid[0]=81&cid[0]=81" onsubmit="return prodformsubmit4a60cb04c1341();">
								<input name="qty" value="1" type="hidden" />
								<input name="pid" id="product_id" value="81" type="hidden" />
								<input name="Button" type="submit" class="btn btn-warning" value="Buy Pro" />
							</form>';
		?>
        <div class="">
            <div class="alert alert-error light-msg">
                <span class="pull-left" style="line-height:35px;"><?php echo JText::_("COM_ADAGENCY_DASHBOARD_MSG"); ?></span>
                <?php
                    echo $form_button;
                ?>
                &nbsp;
                <span class="pull-left" style="line-height:35px;">
                    <a class="pull-left" href="http://adagency.ijoomla.com/pricing/" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
                </span>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="row-flow">
	<div class="span8">
		<div>			
			<div id="g_basicInfo" class="g_outer_shell clearfix">			  
				<div class="g_middle_shell">				
					<div class="g_inner_shell">				  
						<div class="span2">					
							<div class="infobox infobox-blue infobox-dark">					  
								<div class="infobox-icon">						
									<i class="fa fa-money"></i>					  
								</div>					  
								<div class="infobox-data">						
									<span class="revenue"><?php
			                        	if(isset($revenue) && count($revenue) > 0){
											$sum = 0.00;
											foreach($revenue as $key=>$value){
												$sum += $value["revenue"];
											}
											echo 	JText::_("ADAG_C_".$currencydef)." ".$sum;
										}
										else{
											echo 	"0";
										}
									?><br>						
									</span>					  
								</div>					  
								<div class="infobox-footer">						
									<div class="infobox-content"><?php echo JText::_("ADAG_REVENUE"); ?></div>					  
								</div>					  					
							</div>				  
						</div>				  
						<!--//end box-1 -->				  
						<div class="span2">					
							<div class="infobox infobox-green infobox-dark">					  
								<div class="infobox-icon">						
									<i class="fa fa-shopping-cart"></i>
								</div>					  
								<div class="infobox-data">						
									<span class="total-orders">						  
										<a href="index.php?option=com_adagency&controller=adagencyOrders"><?php echo intval($orders["0"]); ?> </a>						
									</span>					  
								</div>					  
								<div class="infobox-footer">						
									<div class="infobox-content">						  
										<a href="index.php?option=com_adagency&controller=adagencyOrders"><?php echo JText::_("VIEWPACKAGEORDERS"); ?></a>						
									</div>					  
								</div>					  					
							</div>				  
						</div>				  
						<!--//end box-2 -->				  
						<div class="span2">					
							<div class="infobox infobox-orange infobox-dark">					  
								<div class="infobox-icon">						
									<i class="fa fa-bars"></i>					  
								</div>					  
								<div class="infobox-data">						
									<span class="total-orders">						  
										<a href="index.php?option=com_adagency&controller=adagencyAds&status_select=P"><?php echo intval($pending_ads["0"]); ?> </a>						
									</span>					  
								</div>					  
								<div class="infobox-footer">						
									<div class="infobox-content">						  						  
										<a href="index.php?option=com_adagency&controller=adagencyAds&status_select=P"><?php echo JText::_("ADAG_PENDING_ADS"); ?></a>						
									</div>					  
								</div>					  					
							</div>				  
						</div>				  
						<!--//end box-3 -->				  
						<div class="span2">					
							<div class="infobox infobox-red infobox-dark">					  
								<div class="infobox-icon">						
									<i class="fa fa-user"></i>					  
								</div>					  
								<div class="infobox-data">						
									<span class="total-orders">						  
										<a href="index.php?option=com_adagency&controller=adagencyAdvertisers&advertiser_status=P"><?php echo intval($pending_advertisers["0"]);?> </a>						
									</span>					  
								</div>					  
								<div class="infobox-footer">						
									<div class="infobox-content">						  						  
										<a href="index.php?option=com_adagency&controller=adagencyAdvertisers&advertiser_status=P"><?php echo JText::_("ADAG_PENDING_ADVERTISERS"); ?></a>						
									</div>					  
								</div>					  					
							</div>				  
						</div>				  
						<!--//end box-4 -->				  
						<div class="span2">					
							<div class="infobox infobox-pink infobox-dark">					  
								<div class="infobox-icon">						
									<i class="fa fa-calendar-o"></i>					  
								</div>					  
								<div class="infobox-data">						
									<span class="total-orders">						  
										<a href="index.php?option=com_adagency&controller=adagencyCampaigns&campaign_status=P"><?php echo intval($pending_campaigns["0"]); ?></a>						
									</span>					  
								</div>					  
								<div class="infobox-footer">						
									<div class="infobox-content">						  						  
										<a href="index.php?option=com_adagency&controller=adagencyCampaigns&campaign_status=P"><?php echo JText::_("ADAG_PENDING_CAMPAIGNS"); ?></a>						
									</div>					  
								</div>					  					
							</div>				  
						</div>				  
						<!--//end box-5 -->				  
						<div class="span2">					
							<div class="infobox infobox-blue infobox-dark">					  
								<div class="infobox-icon">						
									<i class="fa fa-bar-chart-o"></i>					  
								</div>					  
								<div class="infobox-data">						
									<span class="revenue"><?php echo $avg = $this->avg();?><br>						
									</span>					  
								</div>					  
								<div class="infobox-footer">						
									<div class="infobox-content"><?php echo JText::_("ADAG_AVG_CTR"); ?></div>					  
								</div>				  					
							</div>				  
						</div>				  
					<!--//end box-6 -->				
		</div>				
		<!--// end g_inner_shell-->			  
	</div>			  
<!--// end g_middle_shell-->			
</div><!--end basic info-->
		</div>
		<div class="clearfix"></div>	
		<div id="g_daily_chart" class="row-flow">
            <div class="span12">
            	<div id="content">
                    <div class="demo-container">
                        <div id="placeholder" class="demo-placeholder"></div>
                    </div>
                </div>    
            </div>
        </div>			
		
	</div><!--end dash board main container-->
	<div class="span4">
		<div class="span12">
			<?php 
				$extensions = get_loaded_extensions();
				$text = "";
				if(in_array("curl", $extensions)){
					$data = 'http://www.ijoomla.com/adagency_announcements.txt';
					$ch = curl_init($data);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_TIMEOUT, 20); 
					$text = curl_exec($ch); 
				}
				else{
					$text = file_get_contents('http://www.ijoomla.com/adagency_announcements.txt');
				}
				if($text && (trim($text) != '')){
					echo '<div class="well well-small">'.$text.'</div>' ;
				}
			?>
		</div>
		<div class="clearfix"></div>
        <div class="row-flow">
            <div class="span12">
                <div id="ijoomla_news_tabs">
                </div>
            </div>
        </div>
	</div>
</div>

<div class="clearfix"></div>
<?php
	$recent_orders = $this->getRecentOrders();
	$active_advertisers = $this->getActiveAdvertisers();
	$active_campaigns = $this->getActiveCampaigns();
	$active_ads = $this->getActiveAds();
	$active_promocodes = $this->getActivePromoCodes();
	$active_zones = $this->getActiveZones();
	$used_promo = $this->getUsedPromo();
	$ctr = $this->getCTR();
?>
<div class="row-flow">
	<div class="span12 statistic-zone">
    	<div class="span3">
        	<div class="a_header_table">
				<?php
                    echo JText::_("ADAG_RECENT_ORDERS");
                ?>
            </div>
            <?php
            	if(isset($recent_orders) && count($recent_orders) > 0){
			?>
            	<table class="table table-striped table-bordered">
            <?php
					foreach($recent_orders as $key=>$order){
						$package_name = $order["description"];
						$package_id = $order["tid"];
						$user_name = $order["name"];
						$order_date = $order["order_date"];
						$user_id = $order["user_id"];
						$currency = $order["currency"];
						$cost = $order["cost"];
						echo '<tr>
								<td>
							  		<a href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]='.intval($user_id).'">'.$user_name.'</a>
									 '.JText::_("ADAG_BOUGHT_PACKAGE").' <a href="index.php?option=com_adagency&controller=adagencyPackages&task=edit&cid[]='.intval($package_id).'">'.$package_name.'</a>
									 <br />
									 '.JText::_("ADAG_MONTH_ON").' '.$order_date.' '.JText::_("ADAG_FOR").' '.JText::_("ADAG_C_".$currencydef).' '.$cost.'
								</td>
						      </tr>';
					}
			?>
            	</table>
            <?php
				}
			?>
        </div>
        <div class="span3">
        	<div class="a_header_table">
				<?php
                    echo JText::_("ADAG_GENERAL_STATS");
                ?>
            </div>
            <table class="table table-striped table-bordered">
            	<tr>
                	<td>
                    	<?php echo JText::_("ADAG_ORDERS_TO_DATE"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
                        	if(intval($orders) > 0){
								echo '<a href="index.php?option=com_adagency&controller=adagencyOrders&order_status=0">'.intval($orders["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("ADAG_REVENUE_TO_DATE"); ?>
                    </td>
                    <td class="pagination-centered">
						<?php
                        	if(isset($revenue) && count($revenue) > 0){
								$sum = 0.00;
								foreach($revenue as $key=>$value){
									$sum += $value["revenue"];
								}
								
								echo '<span class="revenue">';
								echo 	JText::_("ADAG_C_".$currencydef)." ".$sum;
								echo '</span>';
							}
							else{
								echo '<span class="revenue">';
								echo 	"0<br/>";
								echo '</span>';
								echo JText::_("ADAG_REVENUE");
							}
						?>
					</td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("ADAG_ACTIVE_ADVERTISERS"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($active_advertisers["0"]) > 0){
                        		echo '<a href="index.php?option=com_adagency&controller=adagencyAdvertisers&advertiser_status=Y">'.intval($active_advertisers["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("ADAG_ACTIVE_CAMPAIGNS"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($active_campaigns["0"]) > 0){
                        		echo '<a href="index.php?option=com_adagency&controller=adagencyCampaigns&campaign_status=Y">'.intval($active_campaigns["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("ADAG_ACTIVE_ADS"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($active_ads["0"]) > 0){
                        		echo '<a href="index.php?option=com_adagency&controller=adagencyAds&status_select=Y">'.intval($active_ads["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("ADAG_ACTIVE_PROMOCODES"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($active_promocodes["0"]) > 0){
                        		echo '<a href="index.php?option=com_adagency&controller=adagencyPromocodes&active_promocodes=1">'.intval($active_promocodes["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("ADAG_ACTIVE_ZONES"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($active_zones["0"]) > 0){
                        		echo '<a href="index.php?option=com_adagency&controller=adagencyZones&active_zones=1">'.intval($active_zones["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="span3">
        	<div class="a_header_table">
				<?php
                    echo JText::_("ADAG_BEST_PERFORMING_ADS");
                ?>
            </div>
            <?php
                if(isset($ctr) && count($ctr) > 0){
			?>
            		<table class="table table-striped table-bordered">
			<?php
                    foreach($ctr as $key=>$value){
                    	$bannerType="adagencyStandard";
						if($value["media_type"] == "Flash" ) {$bannerType = "adagencyFlash";}
						else if($value["media_type"] == "Advanced" ) {$bannerType = "adagencyAdcode";}
						else if($value["media_type"] == "TextLink" ) {$bannerType = "adagencyTextlink";}
						else if($value["media_type"] == "Floating" ) {$bannerType = "adagencyFloating";}
						else if($value["media_type"] == "Transition" ) {$bannerType = "adagencyTransition";}
						else if($value["media_type"] == "Standard" ) {$bannerType = "adagencyStandard";}
						else if($value["media_type"] == "Popup" ) {$bannerType = "adagencyPopup";}
			?>
            			<tr>
                            <td>
                                 <a href="index.php?option=com_adagency&controller=<?php echo $bannerType;?>&task=edit&cid[]=<?php echo $value["banner_id"]; ?>"><?php echo $value["title"]." ".$value["width"]."*".$value["height"]; ?></a> <?php echo JText::_("ADAG_BY"); ?>
                                <a href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=<?php echo intval($value["user_id"]); ?>"><?php echo $value["name"]; ?></a>
                            </td>
                            <td class="pagination-centered" nowrap="nowrap">
                                <?php
                                    echo $value["click_rate"];
                                ?>
                            </td>
                        </tr>
			<?php
					}
			?>
                    </table>
            <?php
				}
			?>
        </div>
        <div class="span3">
        	<div class="a_header_table">
				<?php
                    echo JText::_("ADAG_MOST_USED_PROMO");
                ?>
            </div>
            <?php
                if(isset($used_promo) && count($used_promo) > 0){
			?>
            		<table class="table table-striped table-bordered">
			<?php
                    foreach($used_promo as $key=>$promo){
			?>
            			<tr>
                            <td>
                                <a href="index.php?option=com_adagency&controller=adagencyPromocodes&task=edit&cid[]=<?php echo $promo["id"]; ?>"><?php echo $promo["title"]; ?></a>
                            </td>
                            <td class="pagination-centered">
                                <?php
                                    echo intval($promo["total"]);
                                ?>
                            </td>
                        </tr>
			<?php
					}
			?>
                    </table>
            <?php
				}
			?>
        </div>
    </div>
</div>

<div class="clearfix"></div>
<?php if($this->upgrade_set != NULL) { echo $this->upgrade_set."<br />";}?>
<div class="clearfix"></div>
<?php if($this->geo_not_set != NULL) { echo $this->geo_not_set."<br />";}?>
<div class="clearfix"></div>