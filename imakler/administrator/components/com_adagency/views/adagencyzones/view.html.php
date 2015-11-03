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
//require_once(JPATH_SITE.'/libraries/legacy/html/menu.php');

class adagencyAdminViewadagencyZones extends JViewLegacy {

	function display ($tpl =  null ) {
		$db = JFactory::getDBO();
		JToolBarHelper::title(JText::_('ADAGENCY_ZONES_MANAGER'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList('duplicate',JText::_('ADAG_DUPLICATE'));
		JToolBarHelper::addNew();
		JToolBarHelper::editList();				
		JToolBarHelper::deleteList(JText::_('AGENCYCONFIRMDEL'));
		$configs = $this->_models['adagencyconfig']->getConfigs();
		$this->assign("configs", $configs);
		//for the default zone
		$sql = "SELECT `id` FROM #__modules WHERE title='Ad Agency Zone'";
		$db->setQuery($sql);
		$exista = $db->loadResult();
		if (intval($exista) > 0) {
			$sqls = "SELECT `z_title` FROM #__ad_agency_zone WHERE zoneid='".intval($exista)."'";
			$db->setQuery($sqls);
			$exists = $db->loadResult();
			if (!$exists) {
				$sqln = "INSERT INTO `#__ad_agency_zone` ( `zoneid` , `banners` , `z_title` , `z_ordering` , `z_position` , `show_title` , `suffix` )
							 VALUES ('".intval($exista)."', '1', 'Ad Agency Zone', '0', 'left' , '1', '')";
				$db->setQuery($sqln);
				$db->query();
			}
		}
		//end default zone
		$javascript = 'onchange="document.adminForm.submit();"';
		if(isset($_POST['module_position']))
			$module_position = $_POST['module_position'];
		else
			if(isset($_SESSION['module_position']))
				$module_position = $_SESSION['module_position'];
			else
				$module_position =0;
		$positions[] = JHTML::_('select.option',  "0", trim(JText::_('AD_ZONES_ALL_POS')), 'position', 'position' );
			$db = JFactory::getDBO();
			$sql = "SELECT DISTINCT(position) FROM `#__modules` WHERE module ='mod_ijoomla_adagency_zone'";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$positionsloaded = $db->loadObjectList();

        $positions 	= array_merge( $positions, $positionsloaded );
	    $lists['module_position']  =  JHTML::_( 'select.genericlist', $positions, 'module_position', 'class="inputbox" size="1"'.$javascript,'position', 'position', $module_position);
		$this->assignRef('lists', $lists);
		$zones = $this->get('listZones');
		$pagination =  $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);
		$this->assignRef('zones', $zones);
		parent::display($tpl);
	}

	function editForm($tpl = null) {
		$db = JFactory::getDBO();
		$zone = $this->get('zone');
		$zone->adparams = @unserialize($zone->adparams);
		$cids = JRequest::getVar('cid', array(0), 'method', 'array');
		$isNew = ($zone->zoneid < 1);
		if($isNew) {
			$isLock = false;
		} else {
			$isLock =  $this->_models['adagencyzone']->getLockZone($zone->zoneid);
		}
		$text = $isNew?JText::_('New'):JText::_('Edit');
		$client	= JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$modul = JTable::getInstance('module');
		if (!$isNew) $modul = $this->_models['adagencyzone']->getTheModule($zone->zoneid);
		$clientid = $client->id;

		$positions =  $this->_models['adagencyzone']->getThePositions($clientid);
		$approved_ads =  $this->_models['adagencyzone']->getApprovedAds();
		JToolBarHelper::title(JText::_('AD_ZONE').":<small>[".$text."]</small>");
		JToolBarHelper::apply('apply', 'Apply');
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::cancel ('cancel', 'Close');
		}
		$this->assign("zone", $zone);
		$configs = $this->_models['adagencyconfig']->getConfigs();
		$lists = NULL;
		////////////////////
		$db = JFactory::getDBO();
		$query = 'SELECT position, ordering, showtitle, title'
		. ' FROM #__modules'
		. ' WHERE 1=1'
		. ' ORDER BY ordering'
		;

		$db->setQuery( $query );
		if ( !($orders = $db->loadObjectList()) ) {
			echo $db->stderr();
			return false;
		}
		$orders2 	= array();
		$l = 0;
		$r = 0;
		for ($i=0, $n=count( $orders ); $i < $n; $i++) {
			$ord = 0;
			if (array_key_exists( $orders[$i]->position, $orders2 )) {
				$ord =count( array_keys( $orders2[$orders[$i]->position] ) ) + 1;
			}

			$orders2[$orders[$i]->position][] = JHTML::_('select.option',  $ord, $ord.'::'.addslashes( $orders[$i]->title ) );
		}
		// build the html select list for published
		
	
		if (!$isNew)
	
			$lists['published'] = $modul[0]->published;
		else
			$lists['published'] = '1';
		// modified here from $modul->published to '1'
		// build the html select list for show title
		if (!$isNew)
			$lists['showtitle'] = $modul[0]->showtitle;
		else
			$lists['showtitle'] = "1";
		// build the html select list for keyword search
		if (!$isNew)
			$lists['showkeyws'] = $zone->keywords;
		else
			$lists['showkeyws'] = "0";
		// build the html select list for rotate banners
		if (!$isNew)
			$lists['rotatebanners'] = $zone->rotatebanners;
		else
			$lists['rotatebanners'] = '0';

		// build the html select list for rotate randomize
		if (!$isNew)
			$lists['rotaterandomize'] = $zone->rotaterandomize;
		else
			$lists['rotaterandomize'] = '1';
		// get selected pages for $lists['selections']
		if ( $cids[0] ) {
			$query = 'SELECT menuid AS value'
			. ' FROM #__modules_menu'
			. ' WHERE moduleid = '.(int) $modul[0]->id
			;
			$db->setQuery( $query );
			$lookup = $db->loadObjectList();
			if (empty( $lookup )) {
				$lookup = array( JHTML::_('select.option',  '-1' ) );
				$modul[0]->pages = 'none';
			} elseif (count($lookup) == 1 && $lookup[0]->value == 0) {
				$modul[0]->pages = 'all';
			} else {
				$modul[0]->pages = null;
			}
		} else {
			$lookup = array( JHTML::_('select.option',  0, JText::_( 'All' ) ) );
			if (!$isNew)
				$modul[0]->pages = 'all';
			else
				$modul->pages = 'all';

		}
	if (!$isNew) {
		if ( $modul[0]->access == 99 || $modul[0]->client_id == 1 ) {
			$lists['access'] 			= 'Administrator';
			$lists['selections'] 		= 'N/A';
		} else {
			if ( $client->id == '1' ) {
				$lists['access'] 		= 'N/A';
				$lists['selections'] 	= 'N/A';
			} else {
				//echo'<pre>'; print_r($modul[0]); die();
				/*$lists['access'] 		= JHTML::_('list.accesslevel',  $modul[0] );*/
				//$selections				= JHTML::_('menu.linkoptions');
				$selections = $this->linkoptionsAd();
				$lists['selections']	= JHTML::_('select.genericlist',   $selections, 'selections[]', 'class="inputbox" size="15" multiple="multiple"', 'value', 'text', $lookup, 'selections' );
			}
		}
	} else if ($isNew) {
			if ( $modul->access == 99 || $modul->client_id == 1 ) {
			$lists['access'] 			= 'Administrator';
			$lists['selections'] 		= 'N/A';
		} else {
			if ( $client->id == '1' ) {
				$lists['access'] 		= 'N/A';
				$lists['selections'] 	= 'N/A';
			} else{				
					/*$lists['access'] 		= JHTML::_('list.accesslevel',  $modul );*/
					//$selections				= JHTML::_('menu.linkoptions');	
					$selections = $this->linkoptionsAd();
					$lists['selections']	= JHTML::_('select.genericlist',   $selections, 'selections[]', 'class="inputbox" size="15" multiple="multiple"', 'value', 'text', $lookup, 'selections' );
				}
		}
	}
		//----------------------------------------------------------------------
		if ($zone->zoneid>0) { $cellpadding=$zone->cellpadding;} else {$cellpadding=1;}
		$javascript='';
		$disabled = "";
		
		if($zone->inventory_zone == 1 && $zone->zoneid > 0){
			$disabled = " disabled='disabled' ";
		}
		
		$lists['adsinzone'] = JHTML::_('select.integerlist',   1, 25, 1, 'banners', $javascript.$disabled, $zone->banners );
		$lists['adsinzone_cols'] = JHTML::_('select.integerlist',   1, 6, 1, 'banners_cols', $javascript.$disabled, $zone->banners_cols );
		
		$lists['cellpadding'] = JHTML::_('select.integerlist',   0, 25, 1, 'cellpadding', $javascript, $cellpadding);
		$sel_packs =  $this->_models['adagencyzone']->getZonePacks2Select($zone->z_position);
		$sel_packsALL =  $this->_models['adagencyzone']->getZonePacks2SelectALL();

		//$defaultad=$zone->defaultad;
		$adlist[] 	= JHTML::_('select.option',  "0", JText::_('ADAG_NONE'), 'id', 'title' );
		$adlist 	= array_merge( $adlist, $approved_ads );
		$lists['adlist'] = JHTML::_( 'select.genericlist', $adlist, 'defaultad', 'class="inputbox" size="1"','id', 'title', $zone->defaultad);

		$jomsocial_positions = array('js_side_top','js_side_bottom','js_profile_top','js_profile_bottom','js_profile_side_top','js_profile_side_bottom','js_profile_feed_top','js_profile_feed_bottom','js_groups_side_top','js_groups_side_bottom');
		$positions 	= @array_unique(@array_merge( $positions, $jomsocial_positions ));
		@sort($positions);

		$zone->textadparams = @unserialize($zone->textadparams);

		if(!isset($zone->textadparams['ia'])) { $zone->textadparams['ia'] = 'l'; }
		$alignments2[] = JHTML::_('select.option',  "t", JText::_('AD_TOP'), 'value', 'option' );
		$alignments2[] = JHTML::_('select.option',  "l", JText::_('AD_LEFT'), 'value', 'option' );
		$alignments2[] = JHTML::_('select.option',  "r", JText::_('AD_RIGHT'), 'value', 'option' );
		$lists['ia']  =  JHTML::_( 'select.genericlist', $alignments2, 'textadparams[ia]', 'class="inputbox" size="1"','value', 'option', $zone->textadparams['ia']);

		
		if(!isset($zone->textadparams['wrap_img'])) { $zone->textadparams['wrap_img'] = '1'; }
	
		$yes_cheched ="";
		if($zone->textadparams['wrap_img'] =='1'){$yes_cheched ='checked="checked"';}
		$wrapimgvar ='<input type="hidden" name="textadparams[wrap_img]" value="0">
					  <input type="checkbox" '.$yes_cheched.'  value="1" class="ace-switch ace-switch-5" name="textadparams[wrap_img]">
					  <span class="lbl"></span>';
		$lists['wrap_img'] =$wrapimgvar;
		
		
		if (!$isNew) {
			$this->assign("modul", $modul[0]);
		} else {
			$this->assign("modul", $modul);
		}
		$this->assign("isLock", $isLock);
		$this->assign("positions", $positions);
		$this->assign("orders2", $orders2);
		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		$this->assign("sel_pack",$sel_packs);
		$this->assign("sel_packALL",$sel_packsALL);
		parent::display($tpl);
	}
	function linkoptionsAd($all = false, $unassigned = false){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Get a list of the menu items
		$query->select('m.id, m.parent_id, m.title, m.menutype');
		$query->from($db->quoteName('#__menu') . ' AS m');
		$query->where($db->quoteName('m.published') . ' = 1');
		$query->order('m.menutype, m.parent_id');
		$db->setQuery($query);

		$mitems = $db->loadObjectList();

		if (!$mitems)
		{
			$mitems = array();
		}

		// Establish the hierarchy of the menu
		$children = array();

		// First pass - collect children
		foreach ($mitems as $v)
		{
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		// Second pass - get an indent list of the items
		$list = JHtmlMenu::TreeRecurse((int) $mitems[0]->parent_id, '', array(), $children, 9999, 0, 0);

		// Code that adds menu name to Display of Page(s)

		$mitems = array();
		if ($all | $unassigned)
		{
			$mitems[] = JHtml::_('select.option', '<OPTGROUP>', JText::_('JOPTION_MENUS'));

			if ($all)
			{
				$mitems[] = JHtml::_('select.option', 0, JText::_('JALL'));
			}
			if ($unassigned)
			{
				$mitems[] = JHtml::_('select.option', -1, JText::_('JOPTION_UNASSIGNED'));
			}

			$mitems[] = JHtml::_('select.option', '</OPTGROUP>');
		}

		$lastMenuType = null;
		$tmpMenuType = null;
		foreach ($list as $list_a)
		{
			if ($list_a->menutype != $lastMenuType)
			{
				if ($tmpMenuType)
				{
					$mitems[] = JHtml::_('select.option', '</OPTGROUP>');
				}
				$mitems[] = JHtml::_('select.option', '<OPTGROUP>', $list_a->menutype);
				$lastMenuType = $list_a->menutype;
				$tmpMenuType = $list_a->menutype;
			}

			$mitems[] = JHtml::_('select.option', $list_a->id, $list_a->title);
		}
		if ($lastMenuType !== null)
		{
			$mitems[] = JHtml::_('select.option', '</OPTGROUP>');
		}

		return $mitems;
	}
	
	function getZonesAds(){
		$zones_ads = $this->get("ZonesAds");
		return $zones_ads;
	}
	
	function getThePositions(){
		$template_positions = $this->get("TemplatePositions");
		return $template_positions;
	}
	
	function existAdsForZome($zoneid){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_campaign_banner where `zone`=".intval($zoneid);
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = $count["0"];
		if($count > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function isJomsocialInstalled(){
		$return = FALSE;
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__extensions where `name`='community' and `element`='com_community' and `type`='component'";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = $count["0"];
		if($count > 0){
			return TRUE;
		}
	}
}
?>
