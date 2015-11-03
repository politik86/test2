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

$task = JRequest::getVar("task", "");
if($task == "vimeo"){
	return false;
}


function getCurrentVersionData(){
	$component = "com_adagency";
	$version = "";		
	$data = 'www.ijoomla.com/ijoomla_latest_version.txt';  
    $extensions = get_loaded_extensions();
    $text = "";
    if(in_array("curl", $extensions)){
        $ch = @curl_init($data);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_TIMEOUT, 10);                             
        
        $version = @curl_exec($ch);
        if(isset($version) && trim($version) != ""){  
                       
            $pattern = "/3.0_".$component."=(.*);/msU"; 
            preg_match($pattern, $version, $result);
            if(is_array($result) && count($result) > 0){
                $version = trim($result["1"]);
            }
            return $version;
        }
    }
    else{
             
        $text = file_get_contents('www.ijoomla.com/ijoomla_latest_version.txt');
        return  $text;
    }   
}

function getLocalVersionString(){			
	$component = "com_adagency";
	$xml_file = "adagency.xml";
	
	$version = '';
	$path = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$component.DS.$xml_file;
	if(file_exists($path)){
		$data = implode("", file($path));
		$pos1 = strpos($data,"<version>");
		$pos2 = strpos($data,"</version>");
		$version = substr($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		return $version;
	}
	else{
		return "";
	}
}

$latest_version = getCurrentVersionData();
$installed_version = getLocalVersionString();
?>

<script language="javascript" type="text/javascript">
	function alertNotification(){
		document.getElementById("pub-not-content").className = "open";
	}
	
	jQuery(document).click(function(e){
		if ($(e.target).attr('id') != 'pub-dropdown-toggle' && $(e.target).attr('id') != 'icon-bell' && $(e.target).attr('id') != 'badge-important'){
			if(eval(document.getElementById("pub-not-content"))){
				document.getElementById("pub-not-content").className = "";
			}
		}
	})
</script>

<div class="g_admin_top_wrap">
<div class="ui-app">

    <div class="navbar">
        <div class="navbar-inner">
            <div class="container-fluid">
                <div class="nav-collapse collapse">
                    <div class="pull-left">
                        <a target="_blank" href="http://adagency.ijoomla.com/"><img src="components/com_adagency/images/logo_top.png" /></a>
                        <span class="badge badge-important" id="jomsocial-version">V <?php echo $installed_version; ?></span>
                        <?php
                        	if($latest_version != $installed_version){
								echo '&nbsp;&nbsp;<span class="white-color">'.JText::_("COM_IJOOMLA_AD_AGENCY_NEW_VERSION_AVAILABLE").": V ".$latest_version.'&nbsp; (<a class="white-color" href="http://adagency.ijoomla.com/changelog/change-log-joomla-3-x/" target="_blank">'.JText::_("COM_IJOOMLA_AD_AGEMCY_CHANGE_LOG").'</a>)  (<a class="white-color" href="http://www.ijoomla.com/redirect/general/latestversion.htm" target="_blank">'.JText::_("COM_IJOOMLA_AD_AGENCY_DOWNLOAD").'</a>) </span>';
							}
						?>
                    </div>
                    <div class="pull-right">
                        <div class="ui-app">
                            <div class="navbar2">
                                <div class="g_navbar-inner">
                                    <div class="container-fluid">
                                        <div class="nav-collapse collapse">
                                            <div class="">
                                                <div id="g_rating">
                                                	<ul class="pull-right padding-top">
                                                        <li class="pull-right"><a href="http://twitter.com/ijoomla" target="_blank" />
                                                            <?php
                                                            echo '<span class="small-text">'.JText::_("ADAG_TWITTER").'</span>';
                                                            ?>
                                                            <img src="components/com_adagency/images/icons/twitter.png" />
                                                            </a></li>
                                                        <li class="pull-right"><a href="https://www.facebook.com/ijoomla" target="_blank" />
                                                            <?php
                                                            echo '<span class="small-text">'.JText::_("ADAG_FACEBOOK").'</span>';
                                                            ?>
                                                            <img src="components/com_adagency/images/icons/facebook.png" />
                                                            </a></li>
                                                     </ul>
                                                     
                                                     <?php
                                                     	$pending_advertisers = $this->getPendingAdvertisers();
														$pending_ads = $this->getPendingAds();
														$pending_campaigns = $this->getPendingCampaigns();
														$pending_payments = $this->getPendingPayments();
														
														if(intval($pending_advertisers) > 0 || intval($pending_ads) > 0 || intval($pending_campaigns) > 0 || intval($pending_payments) > 0){
															$sum = intval($pending_advertisers) + intval($pending_ads) + intval($pending_campaigns) + intval($pending_payments);
                                                     ?>                                                     
                                                         <ul class="nav ace-nav pull-right">
                                                            <li class="" id="pub-not-content">
                                                                <a href="#" class="dropdown-toggle" id="pub-dropdown-toggle" onclick="javascript:alertNotification();">
                                                                    <?php echo JText::_("COM_ADAGENCY_NOTIFICATIONS"); ?>
                                                                    <i class="js-icon-bell-alt fa fa-bell" id="icon-bell"></i>
                                                                    <div class="badge badge-important no-margin" id="badge-important"><?php echo intval($sum); ?></div>
                                                                </a>
                                                                <ul class="pull-right dropdown-navbar navbar-js dropdown-menu dropdown-caret dropdown-closer">
                                                                    <li class="nav-header">
                                                                        <i class="js-icon-warning-sign"></i>
                                                                        <?php echo intval($sum)." ".JText::_("COM_ADAGENCY_NOTIFICATIONS"); ?>
                                                                    </li>
                                                                    <?php
                                                                    	if(intval($pending_advertisers) > 0){
																	?>
                                                                            <li>
                                                                                <a href="index.php?option=com_adagency&controller=adagencyAdvertisers&advertiser_status=P">
                                                                                    <div class="clearfix">
                                                                                        <span class="pull-left">
                                                                                        	<?php echo JText::_("COM_ADAGENCY_PENDING_ADVERTISERS"); ?>
                                                                                        </span>
                                                                                        <span class="pull-right orange">
																							<?php echo intval(intval($pending_advertisers)); ?>
																						</span>
                                                                                    </div>
                                                                                </a>
                                                                            </li>
                                                                    <?php
                                                                    	}
																	?>
                                                                    <?php
                                                                    	if(intval($pending_ads) > 0){
																	?>
                                                                            <li>
                                                                                <a href="index.php?option=com_adagency&controller=adagencyAds&status_select=P">
                                                                                    <div class="clearfix">
                                                                                        <span class="pull-left">
                                                                                        	<?php echo JText::_("COM_ADAGENCY_PENDING_ADS"); ?>
                                                                                        </span>
                                                                                        <span class="pull-right orange">
																							<?php echo intval(intval($pending_ads)); ?>
																						</span>
                                                                                    </div>
                                                                                </a>
                                                                            </li>
                                                                    <?php
                                                                    	}
																	?>
                                                                    <?php
                                                                    	if(intval($pending_campaigns) > 0){
																	?>
                                                                            <li>
                                                                                <a href="index.php?option=com_adagency&controller=adagencyCampaigns&campaign_status=P">
                                                                                    <div class="clearfix">
                                                                                        <span class="pull-left">
                                                                                        	<?php echo JText::_("COM_ADAGENCY_PENDING_CAMPAIGNS"); ?>
                                                                                        </span>
                                                                                        <span class="pull-right orange">
																							<?php echo intval(intval($pending_campaigns)); ?>
																						</span>
                                                                                    </div>
                                                                                </a>
                                                                            </li>
                                                                    <?php
                                                                    	}
																	?>
                                                                    <?php
                                                                    	if(intval($pending_payments) > 0){
																	?>
                                                                            <li>
                                                                                <a href="index.php?option=com_adagency&controller=adagencyOrders&order_status=1">
                                                                                    <div class="clearfix">
                                                                                        <span class="pull-left">
                                                                                        	<?php echo JText::_("COM_ADAGENCY_PENDING_PAYMENTS"); ?>
                                                                                        </span>
                                                                                        <span class="pull-right orange">
																							<?php echo intval(intval($pending_payments)); ?>
																						</span>
                                                                                    </div>
                                                                                </a>
                                                                            </li>
                                                                    <?php
                                                                    	}
																	?>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    <?php
                                                    	}
													?>
                                                     
                                                 </div>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end nav bar-->
</div>
<div class="clearfix"></div>


<div class="clearfix"></div>
</div>