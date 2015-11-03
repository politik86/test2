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
?>

<script type="text/javascript">
<!--
function addagencytree() {
d = new dTree('d');

d.add(0,-1,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEDSCP'));?>','index.php?option=com_adagency','','','components/com_adagency/images/lm/icon.png');

d.add(801,0,'&nbsp;<?php echo addslashes(JText::_('ADAG_SCOURSE'));?>','http://www.ijoomla.com/redirect/adagency/course.htm','Ad Agency Course', '_blank','components/com_adagency/images/guru_icon.png');

d.add(840,0,'&nbsp;<?php  echo addslashes(JText::_('VIEWTREESETTINGMANAGER'));?>','','Settings','','components/com_adagency/images/lm/gen_settings.png','components/com_adagency/images/lm/gen_settings.png');

d.add(842,840,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEGENERAL'));?>','index.php?option=com_adagency&controller=adagencyConfigs&task2=general','','','components/com_adagency/images/lm/settings.png','components/com_adagency/images/lm/settings.png');
d.add(843,840,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEPAYMENTS'));?>','index.php?option=com_adagency&controller=adagencyConfigs&task2=payments','','','components/com_adagency/images/lm/payments.png','components/com_adagency/images/lm/payments.png');
d.add(844,840,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEEMAILS'));?>','index.php?option=com_adagency&controller=adagencyConfigs&task2=email','','','components/com_adagency/images/lm/content.png','components/com_adagency/images/lm/content.png');
d.add(845,840,'&nbsp;<?php echo addslashes(JText::_('VIEWTREELANGUAGE'));?>','index.php?option=com_adagency&controller=adagencyConfigs&task2=content','','','components/com_adagency/images/lm/language.png');
d.add(846,840,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEOVERVIEW'));?>','index.php?option=com_adagency&controller=adagencyConfigs&task2=overview','','','components/com_adagency/images/lm/content.png');  
d.add(847,840,'&nbsp;<?php echo addslashes(JText::_('ADAG_REGISTRATION'));?>','index.php?option=com_adagency&controller=adagencyConfigs&task2=registration','','','components/com_adagency/images/lm/settings.png','components/com_adagency/images/lm/settings.png'); 
d.add(848,840,'&nbsp;<?php echo addslashes(JText::_('ADAG_APPROVALS'));?>','index.php?option=com_adagency&controller=adagencyConfigs&task2=approvals','','','components/com_adagency/images/lm/settings.png','components/com_adagency/images/lm/settings.png'); 
d.add(849,840,'&nbsp;<?php echo addslashes(JText::_('ADAG_JOMSOC_TARGETING'));?>','index.php?option=com_adagency&controller=adagencyConfigs&task2=jomsocial','','','components/com_adagency/images/lm/settings.png','components/com_adagency/images/lm/settings.png'); 
 
d.add(810,0,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEMANAGERS'));?>','','','','components/com_adagency/images/lm/banners.png','components/com_adagency/images/lm/banners.png');
d.add(811,810,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADS'));?>','index.php?option=com_adagency&controller=adagencyAds','','','components/com_adagency/images/lm/banners.png','components/com_adagency/images/lm/banners.png');
d.add(812,810,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEZONES'));?>','index.php?option=com_adagency&controller=adagencyZones','','','components/com_adagency/images/lm/zones.png');
d.add(815,810,'&nbsp;<?php echo addslashes(JText::_('VIEWTREECAMPAIGNS'));?>','index.php?option=com_adagency&controller=adagencyCampaigns','','','components/com_adagency/images/lm/campaigns.png','components/com_adagency/images/lm/campaigns.png');
d.add(814,810,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADVERTISERS'));?>','index.php?option=com_adagency&controller=adagencyAdvertisers','','','components/com_adagency/images/lm/advertisers.png','components/com_adagency/images/lm/advertisers.png');
d.add(813,810,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEORDERS'));?>','index.php?option=com_adagency&controller=adagencyOrders','','','components/com_adagency/images/lm/orders.png','components/com_adagency/images/lm/orders.png');
d.add(816,810,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEPACKAGES'));?>','index.php?option=com_adagency&controller=adagencyPackages','','','components/com_adagency/images/lm/packages.png','components/com_adagency/images/lm/packages.png');
d.add(817,810,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEPROMOCODES'));?>','index.php?option=com_adagency&controller=adagencyPromocodes','','','components/com_adagency/images/lm/packages.png','components/com_adagency/images/lm/packages.png');

d.add(850,0,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADDNEW'));?>','','','','components/com_adagency/images/lm/payments.png','components/com_adagency/images/lm/payments.png');
d.add(821,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADDSTANDARD'));?>','index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]=0','Standard','','components/com_adagency/images/lm/banners.png');

d.add(822,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADDTRANSITION'));?>','index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid[]=0','Transition','','components/com_adagency/images/lm/banners.png');
d.add(824,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADDFLOATING'));?>','index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid[]=0','Floating','','components/com_adagency/images/lm/banners.png');
d.add(825,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADDFLASH'));?>','index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]=0','Flash','','components/com_adagency/images/lm/banners.png');
d.add(826,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADDPOPUP'));?>','index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid[]=0','Popup','','components/com_adagency/images/lm/banners.png');
d.add(827,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADDADCODE'));?>','index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=0','Ad Code','','components/com_adagency/images/lm/banners.png');
d.add(828,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEADDTEXTLINK'));?>','index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=0','Text Link','','components/com_adagency/images/lm/banners.png');

d.add(860,0,'&nbsp;<?php echo addslashes(JText::_('ADAG_GEOT'));?>','index.php?option=com_adagency&controller=adagencyGeo','Geo Targeting','','components/com_adagency/images/lm/reports.png');
d.add(861,860,'&nbsp;<?php echo addslashes(JText::_('ADAG_GEOSET'));?>','index.php?option=com_adagency&controller=adagencyGeo&task=settings','Geo Targeting - Settings','','components/com_adagency/images/lm/gen_settings.png');
d.add(862,860,'&nbsp;<?php echo addslashes(JText::_('ADAG_GEOCH'));?>','index.php?option=com_adagency&controller=adagencyGeo&task=channels','Geo Targeting - Settings','','components/com_adagency/images/lm/reports.png');

d.add(880,0,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEREPORTS'));?>','index.php?option=com_adagency&controller=adagencyReports','View Reports','','components/com_adagency/images/lm/reports.png');

d.add(870,0,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEPLUGINS'));?>','index.php?option=com_adagency&controller=adagencyPlugins','Plugins','','components/com_adagency/images/lm/content.png');

d.add(850,0,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEHELP'));?>','','','','components/com_adagency/images/lm/help.png','components/com_adagency/images/lm/help.png');
d.add(821,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEIJOOMLA'));?>','http://www.ijoomla.com','iJoomla website','_blank','components/com_adagency/images/lm/ijoomla.png');

d.add(822,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREESUPPORT'));?>','http://www.ijoomla.com/redirect/general/support.htm','iJoomla Support','_blank','components/com_adagency/images/lm/support.png');

d.add(824,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEFORUM'));?>','http://www.ijoomla.com/redirect/adagency/forum.htm','iJoomla Forum','_blank','components/com_adagency/images/lm/forum.png');
d.add(825,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEMANUAL'));?>','http://www.ijoomla.com/redirect/adagency/manual.htm','adagency Manual','_blank','components/com_adagency/images/lm/manual.png');
d.add(826,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREETEMPLATES'));?>','http://www.ijoomla.com/redirect/general/templates.htm','Joomla Templates','_blank','components/com_adagency/images/lm/templates.png');
d.add(827,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREELV'));?>','http://www.ijoomla.com/redirect/general/latestversion.htm','iJoomla adagency Latest Version','_blank','components/com_adagency/images/lm/templates.png');
d.add(828,850,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEOTHER'));?>','http://www.ijoomla.com/redirect/general/othercomponents.htm','iJoomla adagency Latest Version','_blank','components/com_adagency/images/lm/templates.png');

d.add(830,0,'&nbsp;<?php echo addslashes(JText::_('VIEWTREEABOUT'));?>','index.php?option=com_adagency&controller=adagencyAbout','About iJoomla adagency','','components/com_adagency/images/lm/about.png');

document.getElementById("dtreespan").innerHTML = d;
}

window.addEvent("domready", addagencytree);
//-->
</script>