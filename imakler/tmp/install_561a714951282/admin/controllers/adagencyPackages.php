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

jimport ('joomla.application.component.controller');

class adagencyAdminControlleradagencyPackages extends adagencyAdminController {
    var $_model = null;

    function __construct () {
        parent::__construct();
        $this->registerTask ("", "listOrders");
		$this->registerTask ("add_package_from_modal", "addPackageFromModal");
        $this->_model = $this->getModel("adagencyPackage");
    }

    function listOrders() {
        $view = $this->getView("adagencyPackage", "html");
        $view->setModel($this->_model, true);
        $model = $this->getModel("adagencyConfig");
        $view->setModel($model);
        $view->display();
    }

    function edit () {
        JRequest::setVar ("hidemainmenu", 1);
        $view = $this->getView("adagencyPackage", "html");
        $view->setLayout("editForm");
        $view->setModel($this->_model, true);
        $model = $this->getModel("adagencyConfig");
        $view->setModel($model);
        $view->editForm();
    }

    function zonePacks()
    {
        global $mainframe;
        $data = JRequest::get('post');
        if ($this->_model->zonePacks()) {
            $msg = JText::_('ADAG_NZ_SAVED');
        } else {
            $msg = JText::_('ADAG_NZ_ERROR');
        }
        $link = 'index.php?option=com_adagency&controller=adagencyZones';
        $mainframe->redirect($link, $msg);
    }

    function preview () {
        JRequest::setVar ("hidemainmenu", 1);
        $view = $this->getView("adagencyPackage", "html");
        $view->setLayout("preview");
        $view->setModel($this->_model, true);
        $view->preview();
    }

    function save () {
        if ($this->_model->store() ) {
            $msg = JText::_('PACKAGESAVED');
        } else {
            $msg = JText::_('PACKAGEFAILED');
        }
        $link = "index.php?option=com_adagency&controller=adagencyPackages";
        $this->setRedirect($link, $msg);
    }

    function remove () {
        if (!$this->_model->delete()) {
            $msg = JText::_('PACKAGEREMERR');
        } else {
            $msg = JText::_('PACKAGEREMSUCC');
        }
        $link = "index.php?option=com_adagency&controller=adagencyPackages";
        $this->setRedirect($link, $msg);
    }

    function cancel () {
        $msg = JText::_('PACKAGECANCEL');
        $link = "index.php?option=com_adagency&controller=adagencyPackages";
        $this->setRedirect($link, $msg);
    }

    function publish () {
        $res = $this->_model->publish();
        if (!$res) {
            $msg = JText::_('PACKAGEBLOCKERR');
        } elseif ($res == -1) {
            $msg = JText::_('PACKAGEUNPUB');
        } elseif ($res == 1) {
            $msg = JText::_('PACKAGEPUB');
        } else {
            $msg = JText::_('PACKAGEUNSPEC');
        }
        $link = "index.php?option=com_adagency&controller=adagencyPackages";
        $this->setRedirect($link, $msg);
    }

    function unpublish () {
        $res = $this->_model->unpublish();
        if (!$res) {
            $msg = JText::_('PACKAGEBLOCKERR');
        } elseif ($res == -1) {
            $msg = JText::_('PACKAGEUNPUB');
        } elseif ($res == 1) {
            $msg = JText::_('PACKAGEPUB');
        } else {
            $msg = JText::_('PACKAGEUNSPEC');
        }
        $link = "index.php?option=com_adagency&controller=adagencyPackages";
        $this->setRedirect($link, $msg);
    }

    function addpack () {
        JRequest::setVar ("hidemainmenu", 1);
        $view = $this->getView("adagencyPackage", "html");
        $view->setLayout("editFormbox");
        $view->setModel($this->_model, true);
        $model = $this->getModel("adagencyConfig");
        $view->setModel($model);
        $view->editFormsbox();
    }

    function saveorder () {
        $msg = $this->_model->saveorder();
        $link = "index.php?option=com_adagency&controller=adagencyPackages";
        $this->setRedirect($link, $msg);
    }
	
	public function saveOrderAjax(){
		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("adagencyPackage");
		// Save the ordering
		$return = $model->saveorder($pks, $order);
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}
	
	function addPackageFromModal(){
		$db = JFactory::getDBO();
		$data = JRequest::get('post');

		$_POST['validity'] = ($_POST['amount']>0 && $_POST['duration']!="") ? $_POST['amount'] . "|" . $_POST['duration'] : "";

		if($data['type'] == 'fr'){
			$validity = $data['amount'].'|'.$data['duration'];
		}
		else{
			$validity = "";
		}
	
		if(substr($data['selected_zones'],strlen($data['selected_zones'])-1,1) == '|'){
			$zones = substr($data['selected_zones'],0,strlen($data['selected_zones'])-1);	
		}
		else{
			$zones = $data['selected_zones'];
		}

		$sql = "INSERT INTO `#__ad_agency_order_type` 
						(
							`description`,
							`pack_description`,
							`quantity`,
							`type`,
							`cost`,
							`validity`,
							`published`,
							`zones`
						)
				VALUES 
						(
							'".addslashes($data['description'])."',
							'".addslashes($data['pack_description'])."',
							'".intval($data['quantity'])."',
							'".$data['type']."',
							'".$data['cost']."',
							'".$validity."',
							'1',
							'".$zones."'
			)";
		
		$db->setQuery($sql);
		$db->Query();
		$aid = JRequest::getVar('advert_id', '', 'post');
		
		if($aid!=NULL){
			$adv_condition=" AND a.aid=".$aid;
		}
		
		if(($aid==NULL)||($aid==0)){
			$lists['package']='-please select advertiser-';
		}
		elseif($aid!=NULL){
			if(!isset($adv_condition)){
				$adv_condition="";
			}
			
			$sql_pack = "SELECT DISTINCT a.notes AS description,b.tid FROM #__ad_agency_order AS a, #__ad_agency_order_type AS b WHERE a.notes = b.description".$adv_condition." GROUP BY description";
			$db->setQuery($sql_pack);
			$rows=$db->loadObjectList($sql_pack);
			$cond2=NULL;
			
			if((isset($rows))&&($rows!= NULL)){
				foreach($rows as $value){
					$cond2.=",".$value->tid;
					$lists['package'].="<option value='".$value->tid."' ".$current_selected.">".$value->description."</option>";
				}
			}
			
			$sql_pack2="SELECT tid,description FROM #__ad_agency_order_type WHERE tid NOT IN (-1".$cond2.") ORDER BY tid DESC";
			$db->setQuery($sql_pack2);
			$rows2=$db->loadRowList();
			$sql_pack3="SELECT tid FROM #__ad_agency_order_type WHERE tid NOT IN (-1".$cond2.") ORDER BY tid DESC LIMIT 1";
			$db->setQuery($sql_pack3);
			$latest_pack=$db->loadResult();
			
			if((isset($rows2))&&($rows2!= NULL)){
				foreach($rows2 as $value){
					if($value[0]==$latest_pack) { $current_selected='selected=selected';}
					@$lists['package'].="<option value='".$value[0]."' ".$current_selected." style='font-size: 12px; color: #FF0000;'>".$value[1]."</option>";
					$current_selected=NULL;
				}
			}
			
			if($lists['package']!=''){
				$lists['package']="<select id='otid' class='inputbox' size='1' name='otid'>
				<option value='0'>select package</option>".$lists['package'];
			} 
			else{
				$lists['package']="<select id='otid' class='inputbox' size='1' name='otid'>".$lists['package'];
			}
			$lists['package'].="</select>";
		}							
		echo $lists['package'];
		die();
	}
};

?>
