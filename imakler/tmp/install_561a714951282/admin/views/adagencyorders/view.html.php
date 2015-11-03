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

class adagencyAdminViewadagencyOrders extends JViewLegacy {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('Orders Manager'), 'generic.png');		
		JToolBarHelper::addNew();		
		
		JToolBarHelper::deleteList(JText::_('AGENCYCONFIRMDEL'));
		$orders = $this->get('listOrders');
		$this->assignRef('orders', $orders);
		$pagination =  $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);
		$configs = $this->_models['adagencyconfig']->getConfigs();
		
		$configs->params = @unserialize($configs->params);
		if(!isset($configs->params['timeformat'])){ $configs->params['timeformat'] = "-1"; }
		
		$this->assign("configs", $configs);
		//select advertiser
		$javascript = 'onchange="document.adminForm.submit();"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('all advertisers'), 'aid', 'company' );	
		$db = JFactory::getDBO();
		$sql = "SELECT a.aid, b.name as company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY company ASC";
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
		//end select advertiser
		
		//select package
		$javascript = 'onchange="document.adminForm.submit();"';
		$packages[] = JHTML::_('select.option',  "0", JText::_('all packages'), 'tid', 'description' );	
		$db = JFactory::getDBO();
		$sql = "SELECT tid, description from #__ad_agency_order_type order by description asc";
		$db->setQuery($sql);
		if (!$db->query()) {
			echo $db->stderr();
			return;
		}
		$packagesloaded = $db->loadObjectList();

	    $packages 	= array_merge( $packages, $packagesloaded );
		if(isset($_SESSION['package_id']))
			$package_id = $_SESSION['package_id'];
		else	
			$package_id = 0;
	    $lists['package_id']  =  JHTML::_( 'select.genericlist', $packages, 'package_id', 'class="inputbox" size="1"'.$javascript,'tid', 'description', $package_id);
		//end select package
		
		//select payment method 
		$javascript = 'onchange="document.adminForm.submit();"';

		$payments[] = JHTML::_('select.option',  "all methods", JText::_('all methods'), 'payment_type', 'alias' );	
			$db = JFactory::getDBO();
			$sql = "SELECT distinct payment_type, IF( payment_type = 'twocheckout', '2CO', payment_type ) AS alias from #__ad_agency_order where payment_type!='' order by payment_type asc";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$paymentsloaded = $db->loadObjectList();

		
		if(isset($paymentsloaded) && count($paymentsloaded) > 0){
			$find = false;
			foreach($paymentsloaded as $key=>$value){
				if($value->payment_type == "paypal_payment" || $value->payment_type == "paypal"){
					if(!$find){
						$find = true;
					}
					else{
						unset($paymentsloaded[$key]);
					}
				}
			}
		}

	    $payments 	= array_merge( $payments, $paymentsloaded );
		if(isset($_SESSION['payment_method']))
			$payment_method = $_SESSION['payment_method'];
		else	
			$payment_method = "";
	    $lists['payment_method']  =  JHTML::_( 'select.genericlist', $payments, 'payment_method', 'class="inputbox" size="1"'.$javascript,'payment_type', 'alias', $payment_method);
		//end payment method 
		$this->assignRef('lists', $lists);
		$db->setQuery("SELECT filename,display_name FROM #__ad_agency_plugins");
		$plugs=$db->loadRowList();
		$this->assignRef('plugs', $plugs);
		parent::display($tpl);
	}

	function editForm($tpl = null) {
		$db = JFactory::getDBO();
		$order = $this->get('order');
		$isNew = ($order->oid < 1);
		JToolBarHelper::title(JText::_('Order Detail'));
		if ($isNew) {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::cancel ('cancel', 'Close');
		}
		$configs = $this->_models['adagencyconfig']->getConfigs();
		
		$configs->params = @unserialize($configs->params);
		if(isset($configs->params['timeformat'])){
			$configs->params = $configs->params['timeformat'];
		} else { $configs->params = "-1"; }
		
		$this->assign("configs", $configs);
		$this->assign("order", $order);
		parent::display($tpl);
	}

	function addOrder($tpl = null) {
		$db = JFactory::getDBO();
		$sql = "SELECT max(`oid`) from #__ad_agency_order";
		$db->setQuery($sql);
		if (!$db->query()) {
			echo $db->stderr();
			return;
		}
		$orderid = $db->loadResult();
		$orderid++;

		JToolBarHelper::title(JText::_('ADDORDERS_NEW_ORDER'));
		JToolBarHelper::save('savenew', 'Save');
		JToolBarHelper::cancel();

		//select advertiser
		$javascript = '';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('ADDORDERS_SELECT_AN_ADV'), 'aid', 'company' );	

			$db = JFactory::getDBO();
			$sql = "SELECT a.aid, b.name as company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY a.company ASC";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$advertisersloaded = $db->loadObjectList();

	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
		
	    $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript,'aid', 'company', 0);
		//end select advertiser

		//select package
		$javascript = '';
		$packages[] = JHTML::_('select.option',  "0", JText::_('ADDORDERS_SELECT_A_PACKAGE'), 'tid', 'description' );	
			$db = JFactory::getDBO();
			$sql = "SELECT tid, description from #__ad_agency_order_type order by description asc";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$packagesloaded = $db->loadObjectList();

	    $packages 	= array_merge( $packages, $packagesloaded );

	    $lists['package_id']  =  JHTML::_( 'select.genericlist', $packages, 'package_id', 'class="inputbox" size="1" '.$javascript,'tid', 'description', 0);
		//end select package
		
		//select payment method 
		$javascript = 'onchange="document.adminForm.submit();"';

		$payments[] = JHTML::_('select.option',  "Free", "Free", 'name', 'alias' );	
			$db = JFactory::getDBO();
			$sql = "SELECT distinct id,name, IF( name = 'twocheckout', '2CO', name ) AS alias from #__ad_agency_plugins where type='payment' order by name desc";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$paymentsloaded = $db->loadObjectList();

		if(isset($paymentsloaded) && count($paymentsloaded) > 0){
			$find = false;
			foreach($paymentsloaded as $key=>$value){
				if(@$value->payment_type == "paypal_payment" || @$value->payment_type == "paypal"){
					if(!$find){
						$find = true;
					}
					else{
						unset($paymentsloaded[$key]);
					}
				}
			}
		}

	    $payments 	= array_merge( $payments, $paymentsloaded );
		
		$payment_method="paypal";
	    $lists['payment_method']  =  JHTML::_( 'select.genericlist', $payments, 'payment_method', 'class="inputbox" size="1" '.$javascript,'name', 'alias', $payment_method);
		//end payment method 
		
		$configs = $this->_models['adagencyconfig']->getConfigs();
		
		$configs->params = @unserialize($configs->params);
		if(!isset($configs->params['timeformat'])){ $configs->params['timeformat'] = "-1"; }
		
		$this->assign("configs", $configs);
		$this->assign("orderid", $orderid);
		$this->assign("lists", $lists);
		parent::display($tpl);
	}
	
	function promoValid(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_promocodes where published=1 and codestart <= ".time()." and (codeend = 0 OR codeend >= ".time().")";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}

}
?>
