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

class adagencyControlleradagencyAdvertisers extends adagencyController {
	var $model = null;

	function __construct () {

		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "register");
		$this->registerTask ("captcha", "captcha");
		$this->_model = $this->getModel('adagencyAdvertiser');
	}

	function listAdvertisers() {
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}


	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model2 = $this->getModel("adagencyConfig");
		$view->setModel($model2);
		$model = $this->getModel("adagencyPlugin");
		$view->setModel($model);
		$view->editForm();
	}

	function register(){
		$user = JFactory::getUser();
		$model = $this->getModel("adagencyConfig");
		$layout = JRequest::getVar('layout','','get');
        $itemid_adv = $model->getItemid('adagencyadvertisers');
        $itemid_ads = $model->getItemid('adagencyads');
        $itemid_pkg = $model->getItemid('adagencypackage');

        if($itemid_pkg != 0) { $Itemid = "&Itemid=" . intval($itemid_pkg); } else { $Itemid = NULL; }
        if($itemid_adv != 0) { $Itemid_adv = "&Itemid=" . intval($itemid_adv); } else { $Itemid_adv = NULL; }
        if($itemid_ads != 0) { $Itemid2 = "&Itemid=" . intval($itemid_ads); } else { $Itemid2 = NULL; }

		if(isset($user->id)&&($user->id>0)){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=".intval($user->id).$Itemid, false);
			$adv_id = $this->_model->getAdvertiserByUserId($user->id);
			
			if(!isset($adv_id) || ($adv_id==0) || ($layout=='register')){
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=" .intval($user->id) . $Itemid_adv, false);
            }
			$this->setRedirect($link);
		}
		
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setLayout("register");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->register();
	}

	function login($user = NULL, $pass = NULL, $link = NULL, $redirect = 0){
		$data=JRequest::get('post');
		global $mainframe;
		$options = array();
		if(isset($data['remember_me'])) { $option['remember'] = true;} else { $options['remember'] = false;}
        $model = $this->getModel("adagencyConfig");
		$item_id = $model->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=" . intval($item_id); } else { $Itemid = NULL; }
		$options['return'] = 'index.php?option=com_adagency' . $Itemid;
		$credentials = array(); 
		if($user != NULL) {
			$credentials['username'] = $user;
		} else {
			$credentials['username'] = JRequest::getVar('adag_username', '', 'method', 'username');
		}
		if($pass != NULL) {
			$credentials['password'] = $pass;
		} else {
			$credentials['password'] = JRequest::getString('adag_password', '', 'post', JREQUEST_ALLOWRAW);
		}
		//perform the login action
		$error = $mainframe->login($credentials, $options); 
		if($link == NULL){
			$link = JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=register'.$Itemid, false); 
		}
		
		$returnpage = JRequest::getVar("returnpage", "");
		if($returnpage == "buy"){
			$pid = JRequest::getVar("pid", "0");
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0&pid=".$pid.$Itemid, false);
		}
	
		if($redirect == 0) {
			$this->setRedirect($link);
		}
	}
	
	function iJoomlaGetRealIpAddr(){
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		// check if isset REMOTE_ADDR and != empty
		elseif(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '') && ($_SERVER['REMOTE_ADDR'] != NULL))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		// you're probably on localhost
		} else {
			$ip = "127.0.0.1";
		}
		return $ip;
	}
	
	function save () {
		$configs = $this->_model->getConfigs();
		$configs->show = explode(";", $configs->show);
		
		if(isset($configs->show)&&(in_array('captcha',$configs->show))){
			$g_recaptcha_response = JRequest::getVar("g-recaptcha-response", "");
			$plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
			$params = new JRegistry($plugin->params);
			$secret_key = $params->get('private_key','');
			$ip = $this->iJoomlaGetRealIpAddr();
			include_once(JPATH_SITE.DS."components".DS."com_adagency".DS."helpers".DS."recaptchalib.php");
			
			$reCaptcha = new ReCaptcha($secret_key);
			$response = $reCaptcha->verifyResponse($ip, $g_recaptcha_response);
			
			if($response != null && $response->success){
				// is not a spam
			}
			else{
				$data = JRequest::get('post');
				$_SESSION['ad_company'] = $data['company'];
				$_SESSION['ad_description'] = $data['description'];
				$_SESSION['ad_approved'] = $data['approved'];
				$_SESSION['ad_enabled'] = $data['enabled'];
				$_SESSION['ad_username'] = $data['username'];
				$_SESSION['ad_email'] = $data['email'];
				$_SESSION['ad_name'] = $data['name'];
				$_SESSION['ad_website'] = $data['website'];
				$_SESSION['ad_address'] = $data['address'];
				$_SESSION['ad_country'] = $data['country'];
				$_SESSION['ad_state'] = $data['state'];
				$_SESSION['ad_city'] = $data['city'];
				$_SESSION['ad_zip'] = $data['zip'];
				$_SESSION['ad_telephone'] = $data['telephone'];
				
				$Itemid = JRequest::getVar("Itemid", "0");
				$app = JFactory::getApplication();
				$link = JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=0&Itemid='.intval($Itemid));
				$msg = JText::_("ADAG_DSC_CAPTCHA");
				$app->redirect($link, $msg);
				return false;
			}
		}
		
		$db = JFactory::getDBO();
		$data = JRequest::get('post');
		$item_id = JRequest::getInt('Itemid','0');
		$Itemid = "";
		
		if($item_id != 0){
			$Itemid = "&Itemid=".intval($item_id);
		}
			
		$error = "";
		$the_aid=JRequest::getVar("aid");
		
		if ($this->_model->store($error) ) {
			$msg = JText::_('ADVSAVED');
		} else {
			$msg = JText::_('ADVSAVEFAILED');
			$msg .= $error;
		}
		
		// if user updated his profile -> ... , else if he just registered
		if($the_aid!=0) {$msg = JText::_('ADAG_PROFILE_SUCC_UPDATE');}
		//$link = "index.php?option=com_adagency&controller=adagencyCPanel".$Itemid;
        $link = JRoute::_("index.php?option=com_adagency".$Itemid, false);
		$msg2=JRequest::getVar("msgafterreg");
		if (isset($msg2)&&($msg2!='')) $msg = $msg2;
		if($the_aid==0) {
			$sql = "SELECT `show` FROM `#__ad_agency_settings` WHERE `show` LIKE '%wizzard%' LIMIT 1";
			$db->setQuery($sql);
			$isWizzard = $db->loadResult();

			$usr = $this->_model->getLastAdvertiser();
			if(isset($usr->approved)&&($usr->approved=='Y')) {
				$msg = JText::_('ADVSAVED2');
			} else if($isWizzard) {
				$sql = 'SELECT u.block,a.approved FROM `#__users` AS u, `#__ad_agency_advertis` AS a WHERE u.username = "'.addslashes(trim($data['username'])).'" AND u.id = a.user_id';
				$db->setQuery($sql);
				$result = $db->loadObject();
				if(($result->block == '0')&&($result->approved == 'Y')) {
					$this->login($data['username'],$data['password'],NULL,1);
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid, false);
					$msg = NULL;
				}
			}
			elseif(!$isWizzard){
				$_SESSION["register_but_not_wizzard"] = "ok";
			}
		}
		
		$this->setRedirect($link, $msg);
	}

	function manage(){
		$key = JRequest::getVar('key','');
		$action = JRequest::getVar('action','');
		$cid = JRequest::getInt('cid', 0);
		if(($key!='')&&($action!='')&&($cid!=0)){
			$this->_model->manage($key,$action,$cid);
		} else {
			$this->setRedirect("index.php");
		}
	}

	function overview () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyAdvertisers", "html");
        $model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->setModel($this->_model, true);
		$view->setLayout("overview");
		$view->overview();
	}

};

?>
