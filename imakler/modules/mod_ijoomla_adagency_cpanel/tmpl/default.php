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
$my = JFactory::getUser();
$type = $params->get('itemid', 'default');
$static = $params->get('static','0');
$doc->addStyleSheet('modules/mod_ijoomla_adagency_cpanel/tmpl/mod_adagencycpanel.css');
$doc->addStyleSheet('modules/mod_ijoomla_adagency_cpanel/tmpl/font-awesome.min.css');
if(($type == 'specified')&&($static != 0)) {
    $Itemid_adv = '&Itemid=' . intval($static);
    $Itemid_ads = '&Itemid=' . intval($static);
    $Itemid_pkg = '&Itemid=' . intval($static);
    $Itemid_cmp = '&Itemid=' . intval($static);
    $Itemid_rep = '&Itemid=' . intval($static);
    $Itemid_ord = '&Itemid=' . intval($static);
} else {
	$Itemid_adv = '&Itemid=' . intval($itemid->adagencyadvertisers);
    $Itemid_ads = '&Itemid=' . intval($itemid->adagencyads);
    $Itemid_pkg = '&Itemid=' . intval($itemid->adagencypackage);
    $Itemid_cmp = '&Itemid=' . intval($itemid->adagencycampaigns);
    $Itemid_rep = '&Itemid=' . intval($itemid->adagencyreports);
    $Itemid_ord = '&Itemid=' . intval($itemid->adagencyorders);
}?>
<div id="cpanel_module" class="clearfix">
			<?php if ( !$adv_param ) { ?>
				
					<?php
					if(isset($my->id)&&($my->id!=0)) {
						?>
						<div class="adg_row">
							<div class="adg_cell">
								<div>
									<div>
										<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=register' . $Itemid_adv); ?>">
											<i class="fa fa-user"></i>
											<?php echo JText::_('AD_MOD_CP_REGISTER'); ?>
										</a>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					else {
						?>
						<div class="adg_row">
							<div class="adg_cell">
								<div>
									<div>
										<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=register' . $Itemid_adv); ?>">
											<i class="fa fa-user"></i>
											<?php echo JText::_('AD_MOD_CP_LOGIN_REGISTER'); ?>
										</a>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					?>
					<div class="adg_row">
						<div class="adg_cell">
							<div>
								<div>
									<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencypackage' . $Itemid_pkg); ?>">
										<i class="fa fa-gear"></i>
										<?php echo JText::_('AD_MOD_CP_VP'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
			
			<?php } else { ?>
					<div class="adg_row">
						<div class="adg_cell">
							<div>
								<div>
									<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit' . $Itemid_adv); ?>">
										<i class="fa fa-user"></i>
										<?php echo JText::_('AD_MOD_CP_PROFILE'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="adg_row">
						<div class="adg_cell">
							<div>
								<div>
									<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=default' . $Itemid_ads); ?>">
										 <i class="fa fa-bars"></i>
										<?php echo JText::_('AD_MOD_CP_ADS'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="adg_row">
						<div class="adg_cell">
							<div>
								<div>
									<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders&task=default' . $Itemid_ord); ?>">
										 <i class="fa fa-shopping-cart"></i>
										<?php echo JText::_('AD_MOD_CP_ORDERS'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="adg_row">
						<div class="adg_cell">
							<div>
								<div>
									<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=addbanners' . $Itemid_ads); ?>">
										<i class="fa fa-plus"></i>
										<?php echo JText::_('AD_MOD_CP_ADD_NB'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="adg_row">
						<div class="adg_cell">
							<div>
								<div>
									<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyReports' . $Itemid_rep); ?>">
										<i class="fa fa-bar-chart-o"></i>
										<?php echo JText::_('AD_MOD_CP_REPORTS'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="adg_row">
						<div class="adg_cell">
							<div>
								<div>
									<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns&task=default' . $Itemid_cmp); ?>">
										<i class="fa fa-calendar-o"></i>
										<?php echo JText::_('AD_MOD_CP_CMPS'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="adg_row">
						<div class="adg_cell">
							<div>
								<div>
									<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&view=adagencypackage' . $Itemid_pkg); ?>">
										<i class="fa fa-gear"></i>
										<?php echo JText::_('AD_MOD_CP_VP'); ?>
									</a>									
								</div>
							</div>
						</div>
					</div>
					<div class="adg_row">
						<div class="adg_cell">
							<div>
								<div>
									<a class="items_adg" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns&task=edit' . $Itemid_cmp); ?>">
										<i class="fa fa-plus-square"></i>
										<?php echo JText::_('AD_MOD_CP_ADD_NC'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
			<?php
			}
			?>
</div>