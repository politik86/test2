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

class adagencyAdminViewadagencyPackage extends JViewLegacy {

	function display ($tpl =  null ) {
        $nz = JRequest::getInt('newzone', '0', 'get');
        $database = JFactory::getDBO();
        JToolBarHelper::title(JText::_('Package Manager'), 'generic.png');

        if (!$nz) {
            JToolBarHelper::publishList();
            JToolBarHelper::unpublishList();            
			JToolBarHelper::addNew('edit','New');
			JToolBarHelper::editList();		
			
            JToolBarHelper::deleteList(JText::_('AGENCYCONFIRMDEL'));
        } else {
            $session = JFactory::getSession();
            $session->clear("newzone-{$nz}", 'adag');
        }

		$configs = $this->_models['adagencyconfig']->getConfigs();
		$orders = $this->get('listPackages');
		$orders = $this->_models['adagencypackage']->getZonesForPacks($orders);
		$pagination = $this->get( 'Pagination' );

		if ($orders) {
			foreach ($orders as $rowb) {
			$database->setQuery("SELECT count(tid) FROM #__ad_agency_order WHERE `status`='paid' AND `tid`='{$rowb->tid}'");
			$database->query();
			$ordersnr[$rowb->tid]=$database->loadResult();
			}
		} else $ordersnr='';

        if ($nz && !count($orders)) {
            global $mainframe;
            $mainframe->redirect('index.php?option=com_adagency&controller=adagencyZones', JText::_('ZONESAVED'));
        }

		//select zone drop-down
		$javascript = 'onchange="document.topform1.submit();"';
		$zones[] = JHTML::_('select.option',  "0", JText::_('All Zones'), 'zoneid', 'z_title' );
			$db = JFactory::getDBO();
			$sql = "SELECT zoneid, z_title FROM #__ad_agency_zone WHERE 1=1 ORDER BY `z_title` ASC";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$zonesloaded = $db->loadObjectList();

	    $zones 	= array_merge( $zones, $zonesloaded );

		if(isset($_SESSION['package_zone']))
			$zone_id = $_SESSION['package_zone'];
		else
			$zone_id = 0;

	    $lists['package_zone']  =  JHTML::_( 'select.genericlist', $zones, 'package_zone', 'class="inputbox" size="1"'.$javascript,'zoneid', 'z_title', $zone_id);
		//end select zone drop-down

		//select type drop-down
		$javascript = 'onchange="document.topform1.submit();"';
		$types[] = JHTML::_('select.option',  "0", JText::_('all'), 'type', 'type' );
		$types[] = JHTML::_('select.option',  "cpm", "cpm", 'type', 'type' );
		$types[] = JHTML::_('select.option',  "pc", "pc", 'type', 'type' );
		$types[] = JHTML::_('select.option',  "fr", "fr", 'type', 'type' );
		$types[] = JHTML::_('select.option',  "in", "in", 'type', 'type' );
		/*$db =& JFactory::getDBO();
		$sql = "SELECT distinct `type` FROM #__ad_agency_order_type ORDER BY `type` ASC";
		$db->setQuery($sql);
		if (!$db->query()) {
			echo $db->stderr();
			return;
		}
		$typesloaded = $db->loadObjectList();
	    $types 	= array_merge( $types, $typesloaded );*/

		if(isset($_SESSION['type_package']))
			$type_package = $_SESSION['type_package'];
		else
			$type_package = 0;

	    $lists['type_package']  =  JHTML::_( 'select.genericlist', $types, 'type_package', 'class="inputbox" size="1"'.$javascript,'type', 'type', $type_package);
		//end select type drop-down

        $this->assignRef("nz", $nz);
		$this->assignRef("ordersnr", $ordersnr);
		$this->assignRef("configs", $configs);
		$this->assignRef('packages', $orders);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('lists', $lists);
		parent::display($tpl);

	}

	function editForm($tpl = null) {
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$database = JFactory::getDBO();
		$order = $this->get('package');
		$type=$order->type;
		$isNew = ($order->tid < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		if (!isset($type) || ($type=='')) $type='cpm';
		JToolBarHelper::title(JText::_('Package').":<small>[".$text."]</small>");

		if ($isNew) {
			JToolBarHelper::save('save', 'Save');
			JToolBarHelper::cancel ('cancel', 'Cancel');

        } else {
            JToolBarHelper::save('save', 'Save');
            JToolBarHelper::cancel ('cancel', 'Cancel');
        }
        $editable='';
        if (isset($_GET['id'])) {
            $tidd=$_REQUEST['id'];
            $sql="SELECT count(*) FROM #__ad_agency_campaign WHERE otid='{$tidd}'";
            $database->setQuery($sql);
            $editable=$database->loadResult();
        }
		
		// start check if package assigned to one campaign
		$joomla_date = new JDate();
		$current_date = $joomla_date->toSql();
		//$sql = "select count(*) from #__ad_agency_campaign where `otid`=".intval($order->tid)." and (`validity` >= '".$current_date."' or `validity` = '0000-00-00 00:00:00') and `start_date` <= '".$current_date."' and `approved`='Y' and `status` = 1";
		$sql = "select count(*) from #__ad_agency_campaign where `otid`=".intval($order->tid);
		
		$database->setQuery($sql);
		$database->query();
		$count = $database->loadColumn();
		$count = $count["0"];
		$disabled = "";
		if(intval($order->tid) != 0 && $count != 0){
			$disabled = 'disabled="disabled"';
		}
		// stop check if package assigned to one campaign


	if ($type == "fr" || $type == "in") {
		if ($order->validity != "") {
			$validity = explode("|", $order->validity, 2);
		} else {
			$validity[0]='';
			$validity[1]='';
			$validity[2]='';
		}
		//amount lists
		if (!$order->tid) $javascript = '';
	  		else if ($order->tid && $editable) $javascript = 'disabled="disabled"';
				else $javascript='';
		$lists['amount'] = JHTML::_('select.integerlist',   1, 100, 1, 'amount', $disabled." id='amount' ".$javascript, $validity[0] );

		//duration lists
		$duration[] = JHTML::_('select.option',  "day", JText::_('AGENCY_DAY'), 'value', 'option' );
		$duration[] = JHTML::_('select.option',  "week", JText::_('AGENCY_WEEK'), 'value', 'option' );
		$duration[] = JHTML::_('select.option',  "month", JText::_('AGENCY_MONTH'), 'value', 'option' );
		$duration[] = JHTML::_('select.option',  "year", JText::_('AGENCY_YEAR'), 'value', 'option' );

	if (!$order->tid) $javascript = '';
	  else if ($order->tid && $editable) $javascript = 'disabled="disabled"';
	  	else $javascript='';
		$lists['duration']  =  JHTML::_( 'select.genericlist', $duration, 'duration', $disabled.' class="inputbox" size="1"'.$javascript,'value', 'option', $validity[1]);
	}

	if ($type == "cpm") {
		//impressions lists
		if (!$order->tid) $javascript = '';
	 		else if ($order->tid && $editable) $javascript = 'disabled="disabled"';
				else $javascript='';
	}

		
		$this->assign("package", $order);
		// Build type list
		$javascript = 'onchange="document.adminForm.submit();"';
		$TypeOptions[] 	=  JHTML::_('select.option', 'cpm', JText::_('AGENCY_ORDERTYPE_CPM'), 'value', 'option' );
		$TypeOptions[] 	=  JHTML::_( 'select.option', 'pc', JText::_('AGENCY_ORDERTYPE_PC'), 'value', 'option' );
		$TypeOptions[] 	=  JHTML::_( 'select.option', 'fr',JText::_('AGENCY_ORDERTYPE_FR'), 'value', 'option' );
		$TypeOptions[] 	=  JHTML::_( 'select.option', 'in',JText::_('AGENCY_ORDERTYPE_IN'), 'value', 'option' );
		$lists['type']  =  JHTML::_( 'select.genericlist', $TypeOptions, 'type', $disabled.' class="inputbox" size="1"'.$javascript,'value', 'option', $type);
		
		$configs = $this->_models['adagencyconfig']->getConfigs();
		$database->setQuery('SELECT DISTINCT `position` FROM #__modules WHERE 1=1 ORDER BY `position` ASC');
		$zones = $database->loadAssocList();
		if(!isset($clientid)){$clientid=NULL;}
	    //$newzones = & $this->_models['adagencypackage']->getThePositions($clientid);
		$newzones =  $this->_models['adagencypackage']->getZones();

		//select available positions (positions that have zones assigned to them)
		$sqlZ="SELECT DISTINCT z_position FROM `#__ad_agency_zone`";
		$database->setQuery($sqlZ);
		$the_zones_positions=$database->loadResultArray();

		if((isset($order->tid)) && (intval($order->tid)>0) ) {
			$sel_zones =  $this->_models['adagencypackage']->getZonesByPackId($order->tid);
		} else {
			$sel_zones = array();
		}

		$this->assign("disabled",$disabled);
		$this->assign("sel_zones",$sel_zones);
		$this->assign("configs", $configs);
		$this->assign("editable", $editable);
		$this->assign("lists", $lists);
		$this->assign("type", $type);
		$this->assign("data", $data);
		$this->assign('available_positions',$the_zones_positions);
		$this->assign('newzones', $newzones);

		parent::display($tpl);
	}

	function editFormsbox($tpl = null) {
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$database = JFactory::getDBO();
		$order = $this->get('package');

		$type=$order->type;
		$isNew = ($order->tid < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		if (!isset($type) || ($type=='')) $type='cpm';
		JToolBarHelper::title(JText::_('Package').":<small>[".$text."]</small>");

		if ($isNew) {
			JToolBarHelper::save('save', 'Save');
			JToolBarHelper::cancel ('cancel', 'Cancel');

		} else {
			JToolBarHelper::save('save', 'Save');
			JToolBarHelper::cancel ('cancel', 'Cancel');
		}
	$editable='';
	if (isset($_GET['id'])) {
		$tidd=$_REQUEST['id'];
		$sql="SELECT count(*) FROM #__ad_agency_campaign WHERE otid='{$tidd}'";
		$database->setQuery($sql);
		$editable=$database->loadResult();
	}

	//if ($type == "fr")
	{
		$javascript = '';

		$lists['amount'] = JHTML::_('select.integerlist',   1, 100, 1, 'amount', ' id="amount" '.$javascript, 0 );

		//duration lists
		$duration[] = JHTML::_('select.option',  "day", JText::_('AGENCY_DAY'), 'value', 'option' );
		$duration[] = JHTML::_('select.option',  "week", JText::_('AGENCY_WEEK'), 'value', 'option' );
		$duration[] = JHTML::_('select.option',  "month", JText::_('AGENCY_MONTH'), 'value', 'option' );
		$duration[] = JHTML::_('select.option',  "year", JText::_('AGENCY_YEAR'), 'value', 'option' );

	if (!$order->tid) $javascript = '';
	  else if ($order->tid && $editable) $javascript = 'disabled="disabled"';
	  	else $javascript='';
		$lists['duration']  =  JHTML::_( 'select.genericlist', $duration, 'duration', 'class="inputbox" size="1"'.$javascript,'value', 'option', 0);
	}

	//if ($type == "cpm")
	{
		//impressions lists
		if (!$order->tid) $javascript = '';
	 		else if ($order->tid && $editable) $javascript = 'disabled="disabled"';
				else $javascript='';
		//$lists['quantity'] = JHTML::_('select.integerlist', 1000, 1000000, 1000, 'quantity', $javascript, 0 );
	}

		$this->assign("package", $order);
		// Build type list
		$javascript = 'onchange="change_package(this.value);"';
		$TypeOptions[] 	=  JHTML::_('select.option', 'cpm', JText::_('AGENCY_ORDERTYPE_CPM'), 'value', 'option' );
		$TypeOptions[] 	=  JHTML::_( 'select.option', 'pc', JText::_('AGENCY_ORDERTYPE_PC'), 'value', 'option' );
		$TypeOptions[] 	=  JHTML::_( 'select.option', 'fr',JText::_('AGENCY_ORDERTYPE_FR'), 'value', 'option' );
		$TypeOptions[] 	=  JHTML::_( 'select.option', 'in',JText::_('AGENCY_ORDERTYPE_IN'), 'value', 'option' );
		$lists['type']  =  JHTML::_( 'select.genericlist', $TypeOptions, 'type', 'class="inputbox" size="1"'.$javascript,'value', 'option', $type);

		$configs = $this->_models['adagencyconfig']->getConfigs();
		$this->assign("configs", $configs);
		$this->assign("editable", $editable);
		$this->assign("lists", $lists);
		$this->assign("type", $type);
		$this->assign("data", $data);


		$database->setQuery('select distinct `position` from #__modules where 1=1 order by `position` asc');
		$zones = $database->loadAssocList();
	    $newzones = @$this->_models['adagencypackage']->getThePositions($clientid);
		//select available positions (positions that have zones assigned to them)
 		$sqlZ="SELECT DISTINCT z_position FROM `#__ad_agency_zone`";
		$database->setQuery($sqlZ);
		$the_zones_positions=$database->loadResultArray();
		$this->assign('available_positions',$the_zones_positions);

		$this->assign('newzones', $newzones);

		parent::display($tpl);
	}


	function preview ($tpl = null) {
		$database = JFactory::getDBO();

		$sql="SELECT template FROM #__template_styles WHERE client_id='0' AND home='1' ";
		$database->setQuery($sql);
		$default_template=$database->loadResult();

		$sql="SELECT zones FROM #__ad_agency_order_type WHERE tid='".intval($_GET['cid'][0])."' ";
		$database->setQuery($sql);
		$the_zones=$database->loadResult();

		$this->assign('default_template', $default_template);
		$this->assign('the_zones', $the_zones);

		parent::display($tpl);
	}

}

?>
