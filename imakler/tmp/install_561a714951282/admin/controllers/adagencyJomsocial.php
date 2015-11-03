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

class adagencyAdminControlleradagencyJomsocial extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask("add", "edit");
		$this->registerTask("edit", "edit");
		$this->registerTask("target", "target");
		$this->_model = $this->getModel("adagencyJomsocial");
	}

	function edit(){
		$title = JRequest::getVar("title", "");
		$advertiser_id = JRequest::getVar("advertiser_id", "");
		$approved = JRequest::getVar("approved", "");
		$target_url = JRequest::getVar("target_url", "");
		$image_url = JRequest::getVar("image_url", "");
		$image_content = JRequest::getVar("image_content", "");
		$ad_headline = JRequest::getVar("ad_headline", "");
		$ad_text = JRequest::getVar("ad_text", "");
		$ad_start_date = JRequest::getVar("ad_start_date", "");
		$ad_end_date = JRequest::getVar("ad_end_date", "");
		
		$_SESSION["title"] = $title;
		$_SESSION["advertiser_id"] = $advertiser_id;
		$_SESSION["approved"] = $approved;
		$_SESSION["target_url"] = $target_url;
		$_SESSION["image_content"] = $image_content;
		$_SESSION["ad_headline"] = $ad_headline;
		$_SESSION["ad_text"] = $ad_text;
		$_SESSION["ad_start_date"] = $ad_start_date;
		$_SESSION["ad_end_date"] = $ad_end_date;
		
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyJomsocial", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		
		$adagencyConfig = $this->getModel('adagencyConfig');
		$view->setModel($adagencyConfig);
		
		$view->editForm();
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
	
	function cancel () {
	 	unset($_SESSION["title"]);
		unset($_SESSION["advertiser_id"]);
		unset($_SESSION["approved"]);
		unset($_SESSION["target_url"]);
		unset($_SESSION["image_url"]);
		unset($_SESSION["ad_headline"]);
		unset($_SESSION["ad_text"]);
		unset($_SESSION["ad_start_date"]);
		unset($_SESSION["ad_end_date"]);
		unset($_SESSION["image_url"]);
		unset($_SESSION["image_content"]);
		
		$msg = JText::_('AD_SAVECANCEL');
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link, $msg);
	}
	
	function apply(){
		$model = $this->getModel("adagencyJomsocial");
		$result = $model->store();
		
		if($result === TRUE){
			unset($_SESSION["title"]);
			unset($_SESSION["advertiser_id"]);
			unset($_SESSION["approved"]);
			unset($_SESSION["target_url"]);
			unset($_SESSION["image_url"]);
			unset($_SESSION["ad_headline"]);
			unset($_SESSION["ad_text"]);
			unset($_SESSION["ad_start_date"]);
			unset($_SESSION["ad_end_date"]);
			unset($_SESSION["image_url"]);
			unset($_SESSION["image_content"]);
			
			$id = $model->last_ad("Jomsocial");
			
			$msg = JText::_('AD_ADSAVED');
			$link = "index.php?option=com_adagency&controller=adagencyJomsocial&task=edit&cid[]=".intval($id);
			$this->setRedirect($link, $msg);
		}
		else{
			$msg = JText::_('AD_ADSAVEFAIL');
			$link = "index.php?option=com_adagency&controller=adagencyJomsocial&task=edit&cid[]=0";
			$this->setRedirect($link, $msg);
		}
	}
	
	function save(){
		$model = $this->getModel("adagencyJomsocial");
		$result = $model->store();
		
		if($result === TRUE){
			unset($_SESSION["title"]);
			unset($_SESSION["advertiser_id"]);
			unset($_SESSION["approved"]);
			unset($_SESSION["target_url"]);
			unset($_SESSION["image_url"]);
			unset($_SESSION["ad_headline"]);
			unset($_SESSION["ad_text"]);
			unset($_SESSION["ad_start_date"]);
			unset($_SESSION["ad_end_date"]);
			unset($_SESSION["image_url"]);
			unset($_SESSION["image_content"]);
			
			$msg = JText::_('AD_ADSAVED');
			$id = $result["1"];
			$link = "index.php?option=com_adagency&controller=adagencyAds";
			$this->setRedirect($link, $msg);
		}
		else{
			$msg = JText::_('AD_ADSAVEFAIL');
			$link = "index.php?option=com_adagency&controller=adagencyJomsocial&task=edit&cid[]=0";
			$this->setRedirect($link, $msg);
		}
	}
};
?>