<?php

/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$controller_req = JRequest::getVar("controller", "");
$layout = JRequest::getVar("layout", "");
$task = JRequest::getVar("task", "");
$task2 = JRequest::getVar("task2", "");
$action = JRequest::getVar("action", "");
$pending = JRequest::getVar("pending", "0");
$state = JRequest::getVar("state", "");

$display_settings = "none";
$display_managers = "none";
$display_new_ads = "none";
$display_geotargeting = "none";
$display_reports = "none";

$li_settings = "";
$li_managers = "";
$li_new_ads = "";
$li_geotargeting = "";
$li_reports = "";
$li_payments = "";
$li_about = "";

include_once("components/com_adagency/models/adagencyconfig.php");
$ad_configs = new adagencyAdminModeladagencyConfig();
$is_js_installed = $ad_configs->isJomSocialStreamAd();

if($controller_req == "adagencyConfigs"){
    $display_settings = "block";
    $li_settings = 'class="open"';
}
elseif($controller_req == "adagencyAds" || $controller_req == "adagencyZones" || $controller_req == "adagencyCampaigns" || $controller_req == "adagencyAdvertisers" || $controller_req == "adagencyOrders" || $controller_req == "adagencyPackages" || $controller_req == "adagencyPromocodes" || $controller_req == "adagencyBlacklist"){
    $display_managers = "block";
    $li_managers = 'class="open"';
}
elseif($controller_req == "adagencyTransition" || $controller_req == "adagencyFloating" || $controller_req == "adagencyFlash" || $controller_req == "adagencyPopup" || $controller_req == "adagencyAdcode" || $controller_req == "adagencyTextlink" ||  $controller_req == "adagencyStandard" ||  $controller_req == "adagencyJomsocial"){
    $display_new_ads = "block";
    $li_new_ads  = 'class="open"';
}
elseif($controller_req == "adagencyGeo") {
	$display_geotargeting  = "block";
    $li_geotargeting = 'class="open"';
}
elseif($controller_req == "adagencyReports") {
    $display_reports = "block";
	$li_reports = 'class="open"';
}
elseif($controller_req == "adagencyPlugins") {
    $li_payments = 'class="active"';
}
elseif($controller_req == "adagencyAbout") {
    $li_about = 'class="active"';
}

?>


<div id="sidebar" class="sidebar">

<ul class="nav nav-list">

<li <?php if($controller_req == ""){ echo 'class="active"';} ?>>
    <a href="index.php?option=com_adagency">
        <i class="icon-home"></i>
        <?php echo JText::_("ADAG_CONTROL_PANEL"); ?>
    </a>
</li>

<li <?php echo $li_settings; ?>>
    <a class="dropdown-toggle" href="#">
        <i class="icon-cog"></i>
        <span class="menu-text">  <?php echo JText::_("VIEWTREESETTINGMANAGER"); ?> </span>
        <b class="arrow fa fa fa-chevron-down"></b>
    </a>

    <ul class="submenu" style="display:<?php echo $display_settings; ?>;">
        <li <?php if($controller_req == "adagencyConfigs" && $task2 == "general"){ echo 'class="active"';} ?> >
            <a href="index.php?option=com_adagency&controller=adagencyConfigs&task2=general">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEGENERAL"); ?>
            </a>
        </li>

        <li <?php if($controller_req == "adagencyConfigs" && $task2 == "payments"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyConfigs&task2=payments">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEPAYMENTS"); ?>
            </a>
        </li>

        <li <?php if($controller_req == "adagencyConfigs" && $task2 == "email"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyConfigs&task2=email">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEEMAILS"); ?>
            </a>
        </li>

        <li <?php if($controller_req == "adagencyConfigs" && $task2 == "overview"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyConfigs&task2=overview">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEOVERVIEW"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyConfigs" && $task2 == "registration"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyConfigs&task2=registration">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("ADAG_REGISTRATION"); ?>
            </a>
        </li>
        
         <li <?php if($controller_req == "adagencyConfigs" && $task2 == "approvals"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyConfigs&task2=approvals">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("ADAG_APPROVALS"); ?>
            </a>
        </li>
         <li <?php if($controller_req == "adagencyConfigs" && $task2 == "jomsocial"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyConfigs&task2=jomsocial">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("ADAG_JOMSOC_ADS"); ?>
            </a>
        </li>
    </ul>
</li>

<li <?php echo $li_managers; ?>>
    <a class="dropdown-toggle" href="#">
        <i class="icon-list"></i>
        <span class="menu-text">  <?php echo JText::_("VIEWTREEMANAGERS"); ?> </span>
        <b class="arrow fa fa-chevron-down"></b>
    </a>
    <ul class="submenu" style="display:<?php echo $display_managers; ?>;">
    	
        <li <?php if($controller_req == "adagencyAds"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyAds">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADS"); ?>
            </a>
        </li>

        <li <?php if($controller_req == "adagencyZones"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyZones">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEZONES"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyCampaigns"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyCampaigns">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREECAMPAIGNS"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyAdvertisers"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyAdvertisers">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADVERTISERS"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyOrders"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyOrders">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEORDERS"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyPackages"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyPackages">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEPACKAGES"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyPromocodes"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyPromocodes">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEPROMOCODES"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyBlacklist"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyBlacklist">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEBLACKLIST"); ?>
            </a>
        </li>
    </ul>
</li>

<li <?php echo $li_new_ads; ?>>
     <a class="dropdown-toggle" href="#">
        <i class="icon-plus"></i>
        <span class="menu-text">  <?php echo JText::_("VIEWTREEADDNEW"); ?> </span>
        <b class="arrow fa fa-chevron-down"></b>
    </a>
    <ul class="submenu" style="display:<?php echo $display_new_ads; ?>;">
    	
        <li <?php if($controller_req == "adagencyStandard"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]=0">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADDSTANDARD"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyTextlink"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=0">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADDTEXTLINK"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyAdcode"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=00">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADDADCODE"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyPopup"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid[]=0">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADDPOPUP"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyFlash"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]=0">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADDFLASH"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyFloating"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid[]=0">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADDFLOATING"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyTransition"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid[]=0">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADDTRANSITION"); ?>
            </a>
        </li>
        
        <?php
        	if(intval($is_js_installed) >= 4){
		?>
        
        <li <?php if($controller_req == "adagencyJomsocial"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyJomsocial&task=edit&cid[]=0">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEADDJOMSOCIAL"); ?>
            </a>
        </li>
        
        <?php
        	}
		?>
    </ul>
</li>

<li <?php echo $li_geotargeting; ?>>
      <a class="dropdown-toggle" href="#">
        <i class="icon-pin"></i>
        <span class="menu-text">  <?php echo JText::_("ADAG_GEOT"); ?> </span>
        <b class="arrow fa fa-chevron-down"></b>
    </a>
    <ul class="submenu" style="display:<?php echo $display_geotargeting; ?>;">
    	
        <li <?php if($controller_req == "adagencyGeo" && $task == "settings"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyGeo&task=settings">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("ADAG_GEOSET"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyGeo" && $task == "channels"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyGeo&task=channels">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("ADAG_GEOCH"); ?>
            </a>
        </li>        
    </ul>
</li>

<li <?php echo $li_reports; ?>>
	<a class="dropdown-toggle" href="#">
        <i class="icon-archive"></i>
        <span class="menu-text">  <?php echo JText::_("VIEWTREEREPORTS"); ?> </span>
        <b class="arrow fa fa-chevron-down"></b>
    </a>
    <ul class="submenu" style="display:<?php echo $display_reports; ?>;">
        <li <?php if($controller_req == "adagencyReports" && $task == "overview"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyReports&task=overview">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("ZONE_LINK_TAKE_TO_OVERVIEW"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyReports" && $task == "advertisers"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyReports&task=advertisers">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWDSADMINADVERTISERS"); ?>
            </a>
        </li>
        
        <li <?php if($controller_req == "adagencyReports" && $task == "campaigns"){ echo 'class="active"';} ?>>
            <a href="index.php?option=com_adagency&controller=adagencyReports&task=campaigns">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREECAMPAIGNS"); ?>
            </a>
        </li>
	</ul>
</li>

<li <?php echo $li_payments; ?>>
    <a href="index.php?option=com_adagency&controller=adagencyPlugins">
        <i class="icon-cart"></i>
        <?php echo JText::_("VIEWTREEPLUGINS"); ?>
    </a>
</li>

<li>
    <a class="dropdown-toggle" href="#">
        <i class="icon-question-sign"></i>
        <span class="menu-text">  <?php echo JText::_("VIEWTREEHELP"); ?> </span>
        <b class="arrow fa fa-chevron-down"></b>
    </a>

    <ul class="submenu">
    	 <li class="">
            <a href="http://www.ijoomla.com/redirect/adagency/course.htm" target="_blank">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("ADAG_COURSE"); ?>
            </a>
        </li>
    	
        <li class="">
            <a href="http://www.ijoomla.com" target="_blank">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEIJOOMLA"); ?>
            </a>
        </li>

        <li class="">
            <a href="http://www.ijoomla.com/redirect/general/support.htm" target="_blank">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREESUPPORT"); ?>
            </a>
        </li>

        <li class="">
            <a href="http://www.ijoomla.com/redirect/adagency/forum.htm" target="_blank">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEFORUM"); ?>
            </a>
        </li>

        <li class="">
            <a href="http://www.ijoomla.com/redirect/adagency/manual.htm" target="_blank">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEMANUAL"); ?>
            </a>
        </li>

        <li class="">
            <a href="http://www.ijoomla.com/redirect/general/templates.htm" target="_blank">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREETEMPLATES"); ?>
            </a>
        </li>

        <li class="">
            <a href="http://www.ijoomla.com/redirect/general/latestversion.htm" target="_blank">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREELV"); ?>
            </a>
        </li>

        <li class="">
            <a href="http://www.ijoomla.com/redirect/general/othercomponents.htm" target="_blank">
                <i class="fa fa-angle-double-right"></i>
                <?php echo JText::_("VIEWTREEOTHER"); ?>
            </a>
        </li>
    </ul>
</li>

<li <?php echo $li_about; ?>>
    <a href="index.php?option=com_adagency&controller=adagencyAbout">
        <i class="icon-star"></i>
        <?php echo JText::_("VIEWTREEABOUT"); ?>
    </a>
</li>

</ul>

<div id="sidebar-collapse" class="sidebar-collapse">
    <i class="fa fa-angle-double-left"></i>
</div>

</div>