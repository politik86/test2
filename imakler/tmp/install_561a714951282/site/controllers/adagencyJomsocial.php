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

class adagencyControlleradagencyJomsocial extends adagencyController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask("add", "edit");
		$this->registerTask("", "edit");
		$this->registerTask("target", "target");
		$this->_model = $this->getModel("adagencyJomsocial");	
	}
	
	function edit(){
		global $mainframe;
		JRequest::setVar("hidemainmenu", 1);
		$view = $this->getView("adagencyJomsocial", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		
		$model2 = $this->getModel("adagencyAds");
		$view->setModel($model2);
		
		$my	= JFactory::getUser();
		$item_id = $model->getItemid('adagencyadvertiser');
		if($item_id != 0){
			$Itemid = "&Itemid=".intval($item_id);
		}
		else{
			$Itemid = NULL;
		}
		$link = "index.php?option=com_adagency".$Itemid;
		$adv_id = $this->_model->getCurrentAdvertiser();
		
		// Check if user is logged in and if user is advertiser
		if($my->id == 0){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		}
		elseif(!$adv_id->aid){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		} 
		else{
			$isWizzard = $model->isWizzard();
			
			$shown = explode(";", $model->getConfigs()->show);
			$isBanners = 0;
			foreach($shown as $element){
				if($element == "nwone"){
					$isBanners = 1;
				}
			}
			
			if($isBanners == 1){
				$adv_id->approved = 'Y';
			}
			
			// check if the user is not approved as an advertiser
			if(($adv_id->approved == 'N')||(($adv_id->approved == 'P')&&(!$isWizzard))){
				$mainframe->redirect($link, JText::_('AD_FAILEDAPPROVE'));
			}
			$view->editForm();
		}
	}
	
	function target(){
		$total = 0;
		$result = 0;
		$db = JFactory::getDBO();
		$get = JRequest::get("get");
		
		$sql = "select count(*) from #__community_users";
		$db->setQuery($sql);
		$db->query();
		$sum = $db->loadColumn();
		$total = @$sum["0"];
		
		$fields = array();
		foreach($get as $key=>$value){
			if(strpos(" ".$key, "target") !== FALSE){
				$temp = explode("_", $key);
				$fields[$temp["1"]][] = $value;
			}
		}
		
		$result_array = array();
		$start_search = false;
		if(isset($fields) && count($fields) > 0){
			foreach($fields as $id=>$array_values){
				$or = array();
				$skip_date = false;
				foreach($array_values as $value_key=>$value){
					if($id == 3){ // Age, Birthdate
						if($skip_date){
							continue;
						}
						else{
							$search_start_date = "";
							$search_stop_date = "";
							
							if($array_values["0"] > 1){
								$search_start_date = strtotime("-".intval($array_values["0"])." year", strtotime(date("Y-m-d")));
							}
							
							if($array_values["1"] != 120){
								$search_stop_date = strtotime("-".intval($array_values["1"])." year", strtotime(date("Y-m-d")));
							}
							
							if($search_start_date != "" && $search_stop_date != ""){
								$or[] = "STR_TO_DATE(`value`, '%Y-%m-%d')<='".date("Y-m-d", $search_start_date)."' and STR_TO_DATE(`value`, '%Y-%m-%d')>='".date("Y-m-d", $search_stop_date)."'";
							}
							elseif($search_start_date != "" && $search_stop_date == ""){
								$or[] = "STR_TO_DATE(`value`, '%Y-%m-%d')<='".date("Y-m-d", $search_start_date)."'";
							}
							elseif($search_start_date == "" && $search_stop_date != ""){
								$or[] = "STR_TO_DATE(`value`, '%Y-%m-%d')>='".date("Y-m-d", $search_stop_date)."'";
							}
							elseif($search_start_date == "" && $search_stop_date == ""){
								$or[] = " 1=1 ";
							}
							
							$skip_date = true;
						}
					}
					else{
						$or[] = "`value` like '%".$value."%'";
					}
				}
				$sql = "select distinct(`user_id`) from #__community_fields_values where (`field_id`='".$id."' and (".implode(" OR ", $or)."))";
				$db->setQuery($sql);
				$db->query();
				$temp = $db->loadColumn();
				if(!is_array($temp)){
					$temp = array($temp);
				}
				
				if(count($result_array) == 0 && $start_search === FALSE){
					$result_array = $temp;
				}
				else{
					$result_array = array_intersect($result_array, $temp);
				}
				$start_search = true;
			}
		}
		$result = count($result_array);
		
		$return = $result."-".($total - $result);
		die($return);
	}
	
	function upload(){
		$view = $this->getView("adagencyJomsocial", "html");
		$view->setLayout("editForm");
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$model = $this->getModel("adagencyAds");
		$view->setModel($model);
		$model = $this->getModel("adagencyJomsocial");
		$view->setModel($model);
		$url = $view->uploadbannerimage();
		die($url);
	}
	
	function uploadImageContent(){
		$view = $this->getView("adagencyJomsocial", "html");
		$view->setLayout("editForm");
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$model = $this->getModel("adagencyAds");
		$view->setModel($model);
		$model = $this->getModel("adagencyJomsocial");
		$view->setModel($model);
		$url = $view->uploadbannerimagecontent();
		die($url);
	}
	
	function save(){
		$item_id = JRequest::getInt('Itemid','0');
		
		if($item_id != 0){
			$Itemid = "&Itemid=".intval($item_id);
		}
		else{
			$Itemid = NULL;
		}
		
		if($this->_model->store()){
			$msg = JText::_('AD_BANNERSAVED');
		}
		else{
			$msg = JText::_('AD_BANNERFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAds".$Itemid;
		$this->setRedirect($link, $msg);
	}
	
	function save_and_new_camp(){
		$item_id = JRequest::getInt('Itemid','0');
		
		if($item_id != 0){
			$Itemid = "&Itemid=".intval($item_id);
		}
		else{
			$Itemid = NULL;
		}
		
		$this->_model->store("new_camp");
	}
};
?>