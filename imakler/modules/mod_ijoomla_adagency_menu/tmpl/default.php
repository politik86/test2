<?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html) * @author  iJoomla.com webmaster@ijoomla.com
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at http://www.ijoomla.com/licensing/
*/
defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
$doc->addStyleSheet('modules/mod_ijoomla_adagency_menu/tmpl/mod_adagencymenu.css');
$my = JFactory::getUser();
$type = $params->get('itemid', 'default');
$static = $params->get('static','0');
if(($type == 'specified')&&($static != 0)) {
    $Itemid_cpn = '&Itemid=' . intval($static);
    $Itemid_adv = '&Itemid=' . intval($static);
    $Itemid_ads = '&Itemid=' . intval($static);
    $Itemid_pkg = '&Itemid=' . intval($static);
    $Itemid_cmp = '&Itemid=' . intval($static);
    $Itemid_rep = '&Itemid=' . intval($static);
    $Itemid_ord = '&Itemid=' . intval($static);
} else {
    $Itemid_cpn = '&Itemid=' . intval($itemid->adagencycpanel);
	$Itemid_adv = '&Itemid=' . intval($itemid->adagencyadvertisers);
    $Itemid_ads = '&Itemid=' . intval($itemid->adagencyads);
    $Itemid_pkg = '&Itemid=' . intval($itemid->adagencypackage);
    $Itemid_cmp = '&Itemid=' . intval($itemid->adagencycampaigns);
    $Itemid_rep = '&Itemid=' . intval($itemid->adagencyreports);
    $Itemid_ord = '&Itemid=' . intval($itemid->adagencyorders);
}

if(!$adv_param){
?>
	<ul class="nav">
    	<li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=overview' . $Itemid_adv); ?>" class="mainlevel"><?php echo JText::_('JAS_OVERVIEW');?></a>
        </li>
        <li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyPackage' . $Itemid_pkg); ?>" class="mainlevel"><?php echo  JText::_('JAS_MENU_PACKAGES');?></a>
        </li>
        <li>
        	<?php
            	if(isset($my->id)&&($my->id!=0)){ 
			?>
					<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit' . $Itemid_adv); ?>" class="mainlevel"><?php echo JText::_('JAS_REGISTER');?></a>																																																																																																									 			<?php
				}
				else{
			?>
					<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=register' . $Itemid_adv); ?>" class="mainlevel"><?php echo JText::_('JAS_REGISTER');?></a>
			<?php
            	}
			?>
        </li>
	</ul>
<?php
}
else{
?>        
     <ul class="nav">
     	<li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&view=adagencyCPanel' . $Itemid_cpn); ?>" class="mainlevel"><?php echo JText::_('JAS_CONTROL_PANEL');?></a>
        </li>
        <li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit' . $Itemid_adv) ?>" class="mainlevel"><?php echo JText::_('JAS_MENU_PROFILE');?></a>
        </li>
        <li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=default' . $Itemid_ads); ?>" class="mainlevel"><?php echo JText::_('JAS_MODMENU_BANNERS');?></a>
        </li>
        <li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns&task=default' . $Itemid_cmp);; ?>" class="mainlevel"><?php echo JText::_('JAS_MENU_CAMPAIGNS');?></a>
        </li>
        <li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyReports' . $Itemid_rep); ?>" class="mainlevel"><?php echo JText::_('JAS_MENU_REPORTS');?></a>
        </li>
        <li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&view=adagencypackage' . $Itemid_pkg); ?>" class="mainlevel"><?php  echo  JText::_('JAS_MENU_PACKAGES') ;?></a>
        </li>
        <li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders&task=default' . $Itemid_ord); ?>" class="mainlevel"><?php echo JText::_('JAS_MY_ORDERS');?></a>
        </li>
        <li>
        	<a href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=overview' . $Itemid_adv); ?>" class="mainlevel"><?php echo JText::_('JAS_OVERVIEW');?></a>
        </li>
     </ul>

<?php
}
?>
