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

class adagencyViewadagencyPackage extends JViewLegacy {

	function display ($tpl =  null ) {
		$configs = $this->get('Conf');
	 	$advertiserid = $this->get('Aid');
		$orders = $this->get('listPackages');
		$pagination =  $this->get( 'Pagination' );
		$showpreview = $configs->showpreview;
		$currencydef = trim($configs->currencydef," ");
		$orders =  $this->_models['adagencypackage']->getZonesForPacks($orders);
		$showZoneInfo =  $this->get('ShowZInfo');
        $itemid = $this->getModel("adagencyConfig")->getItemid('adagencypackage');
        $itemid_adv = $this->getModel("adagencyConfig")->getItemid('adagencyadvertisers');
        $itemid_cmp = $this->getModel("adagencyConfig")->getItemid('adagencycampaign');
        $itemid_cpn = $this->getModel("adagencyConfig")->getItemid('adagencycpanel');


        $this->assignRef('itemid', $itemid);
        $this->assignRef('itemid_adv', $itemid_adv);
        $this->assignRef('itemid_cmp', $itemid_cmp);
        $this->assign("itemid_cpn", $itemid_cpn);
		$this->assignRef('showZoneInfo', $showZoneInfo);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('packages', $orders);
		$this->assignRef('advertiserid', $advertiserid);
		$this->assignRef('showpreview',$showpreview);
		$this->assignRef('currencydef', $currencydef);
		$this->assignRef('configs', $configs);
		
		parent::display($tpl);
	}

	function packs ($tpl =  null ) {
		$configs = $this->get('Conf');
		$advertiserid = $this->get('Aid');
		$orders = $this->get('listPackages');
		$pagination = $this->get( 'Pagination' );
		$showpreview = $configs->showpreview;
		$currencydef = trim($configs->currencydef," ");

		$this->assignRef('showpreview',$showpreview);
		$this->assignRef('packages', $orders);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('advertiserid', $advertiserid);
		$this->assignRef('currencydef', $currencydef);
		parent::display($tpl);
	}

	function editForm($tpl = null) {
		parent::display($tpl);
	}

	function preview ($tpl = null) {
		$database = JFactory::getDBO();
		$default_template = $this->get('Template');
		$get = JRequest::get('request');

		$link = 'index.php?template='.$default_template;
		if(isset($get['cid'])) {
			$ItemidLink = $this->get('ItemidLink');
			if($ItemidLink != NULL) {
                $link = $ItemidLink;
			}
		}
        $link .= '&tp=1';
		$this->assign("get", $get);
		$this->assign("default_template", $default_template);
		$this->assign("link", $link);
		parent::display($tpl);
	}
	
	function getInventoryNextDate($id){
		$model = $this->getModel("adagencypackage");
		$next_date = $model->checkInventoryPackage($id);
		return $next_date;
	}
}
?>
