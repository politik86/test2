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

jimport ("joomla.application.component.view");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminViewadagencyAds extends JViewLegacy {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('ADAGENCY_ADS_MANAGER'), 'generic.png');
		JToolBarHelper::addNew();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', 'Copy', true );
		JToolBarHelper::deleteList(JText::_('ADG_ADS_DEL'));
		$ads = $this->get('listAds');

		if(is_array($ads))
		foreach($ads as $ad){
			$ad->advertiser =  $this->_models['adagencyads']->getAdvByAID($ad->advertiser_id2);
		}
		//echo "<pre>";var_dump($ads);die();

		$this->assignRef('ads', $ads);

		$pagination = $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);

		$javascript = 'onchange="document.topform1.submit();"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('AD_AGENCY_ALL_ADV'), 'aid', 'company' );

		$db = JFactory::getDBO();
		$sql = "SELECT a.aid, b.name as company, a.user_id FROM #__ad_agency_advertis as a, #__users as b, #__ad_agency_banners banner WHERE a.user_id = b.id and banner.advertiser_id=a.aid group by a.aid ORDER BY company ASC";
		$db->setQuery($sql);
		if (!$db->query()) {
			echo $db->stderr();
			return;
		}
		$advertisersloaded = $db->loadObjectList();

	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );

		if(isset($_SESSION['advertiser_id']))
			$advertiser_id = $_SESSION['advertiser_id'];
		else
			$advertiser_id = 0;
	    $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);
		//select advertisers drop-down

		//select zone drop-down
		$javascript = 'onchange="document.adminForm.submit();"';
		$zones[] = JHTML::_('select.option',  "0", JText::_('AD_AGENCY_ALL_ZONES'), 'zoneid', 'z_title' );

			$sql = "SELECT distinct(z.`zoneid`), z.`z_title` FROM #__ad_agency_zone z, #__ad_agency_campaign_banner cb where z.zoneid=cb.zone ORDER BY z.`z_title` ASC";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$zonesloaded = $db->loadObjectList();

	    $zones 	= array_merge( $zones, $zonesloaded );

		if(isset($_SESSION['zone_id']))
			$zone_id = $_SESSION['zone_id'];
		else
			$zone_id = 0;

	    $lists['zone_id']  =  JHTML::_( 'select.genericlist', $zones, 'zone_id', 'class="inputbox" size="1"'.$javascript,'zoneid', 'z_title', $zone_id);
		//end select zone drop-down

		$query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($query);
		$params = $db->loadResult();
		$params = @unserialize($params);
		if(isset($params['timeformat'])){
			$params = $params['timeformat'];
		} else { $params = "-1"; }

		//echo $params;die();
		$this->assignRef('params', $params);
		$this->assignRef('lists', $lists);

		parent::display($tpl);

	}
	
	function add() {
		parent::display();
	}

	function approve( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' ){
		$imgP = "components/com_adagency/images/pending.gif";
		$icon_class = "icon-time";
		if($row->approved=='Y') {
			$img = 'templates/bluestork/images/admin/'.$imgY;
			$task = "pending";
			$alt = JText::_('Approve');
			$action = JText::_('ADAG_CHTPEN');
			$icon_class = "fa fa-check";
		}
		elseif ($row->approved=='N'){
			$img = 'templates/bluestork/images/admin/'.$imgX;
			$task = "approve";
			$alt = JText::_('Unapprove');
			$action = JText::_('Approve item');
			$icon_class = "fa fa-ban";
		}
		elseif ($row->approved=='P'){
			$img = $imgP;
			$task = "unapprove";
			$alt = JText::_("ADAG_PENDING");
			$action = "Unnapprove Item";
			$icon_class = "fa fa-clock-o";
		}
		else{
			return false;
		}
		//$href = '<img src="'. $img .'" border="0" alt="'. $alt .'" />';
		$href = '<i class="'.$icon_class.'"></i>';
		return $href;
	}

	function editForm($tpl = null) {

		$db = JFactory::getDBO();
		$license = $this->_models['adagencylicense']->getLicense();
		$isNew = isset($license->id)&&($license->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('License').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::cancel();

		} else {
			JToolBarHelper::cancel ('cancel', 'Close');

		}

		$this->assign("license", $license);

		$configs = $this->_models['adagencyconfig']->getConfigs();
		$lists = array();

		$prods = $this->_models['adagencyproduct']->getListProducts();
		$opts = array();
		$opts[] = JHTML::_('select.option',  "", JText::_("Select product") );
		foreach ( $prods as $prod ) {
			$opts[] = JHTML::_('select.option',  $prod->id, $prod->name );
		}
		$lists['productid'] = JHTML::_('select.genericlist',  $opts, 'productid', 'class="inputbox" size="1" ', 'value', 'text', isset($license->productid)?$license->productid:"");

		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		$this->assign("currency_options", array());
		$plugin_handler = new stdClass;
		$plugin_handler->encoding_plugins = array();
		$this->assign("plugin_handler", $plugin_handler);
		parent::display($tpl);
	}

	function getAllAdvertisers(){
		$db = JFactory::getDBO();
		$sql = "select a.`aid`, u.`name` from #__ad_agency_advertis a, #__users u where u.`id`=a.`user_id`";
		$db->setQuery($sql);
		$db->query();
		$advertisers = $db->loadAssocList();
		return $advertisers;
	}
}

?>
