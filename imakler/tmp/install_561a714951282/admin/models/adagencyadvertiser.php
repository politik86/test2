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

jimport ("joomla.aplication.component.model");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminModeladagencyAdvertiser extends JModelLegacy {
	var $_customers;
	var $_customer;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');

		$this->setId((int)$cids[0]);
		global $mainframe, $option;
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		if(JRequest::getVar("limitstart") == JRequest::getVar("old_limit")){
			JRequest::setVar("limitstart", "0");		
			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');
		}

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getlistAdvertisers();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_id = $id;
		$this->_customer = null;
	}

	function getUserToAdv($x = NULL) {
		$data = JRequest::get('post');
		if(($x == NULL)&&(isset($data['username']))) {
			$x = $data['username'];
			$_SESSION['temp_user'] = $data['username'];
		}
		$db = JFactory::getDBO();
		if($x == NULL) {return NULL;}
		//$sql = "SELECT id, name, username, email FROM #__users WHERE id NOT IN (SELECT user_id FROM #__ad_agency_advertis) AND username = '".$x."'";
		$sql = "SELECT id, name, username, email FROM #__users WHERE username = '".stripslashes($x)."'";
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}

	function getProvinces() {
		$db = JFactory::getDBO();
		$data = JRequest::get('get');
		$output = '<select id="sel_province" name="state">';
		if(isset($data['country'])) {
			$sql = "SELECT state FROM #__ad_agency_states WHERE country = '".stripslashes($data['country'])."'";
			$db->setQuery($sql);
			$result = $db->loadObjectList();
			if(isset($result)) {
				foreach($result as $element) {
					$output .= '<option value="'.$element->state.'">'.$element->state.'</option>';
				}
			}
		}
		$output .= '</select>';
		echo $output;die();
	}

	function getlistAdvertisers () {
		$and_filter="WHERE 1=1 AND user.id<>''";
		$and_limit=NULL;
		$limit_cond=NULL;
		$db = JFactory::getDBO();
		if(isset($_GET['apr'])&&($_GET['apr']==true)) { $and_filter.= " AND advertis.approved='P' "; }
		/* adding the search condition for Advertisers  - start */
		if(isset($_REQUEST['search_advertiser']))
		{
			$_SESSION['search_advertiser'] = $_REQUEST['search_advertiser'];
			$search_advertiser = $_REQUEST['search_advertiser'];
		}
		elseif(isset($_SESSION['search_advertiser']))
			$search_advertiser = $_SESSION['search_advertiser'];
			
		if(isset($search_advertiser) && $search_advertiser!=''){
			$and_filter = $and_filter." AND (advertis.company LIKE '%".stripslashes($search_advertiser)."%' OR advertis.aid LIKE '%".stripslashes($search_advertiser)."%' OR advertis.description LIKE '%".stripslashes($search_advertiser)."%' OR advertis.website LIKE '%".stripslashes($search_advertiser)."%' OR advertis.address LIKE '%".stripslashes($search_advertiser)."%' OR advertis.country LIKE '%".stripslashes($search_advertiser)."%' OR advertis.city LIKE '%".stripslashes($search_advertiser)."%' OR advertis.state LIKE '%".stripslashes($search_advertiser)."%' OR advertis.telephone LIKE '%".stripslashes($search_advertiser)."%' OR advertis.fax LIKE '%".stripslashes($search_advertiser)."%' OR user.name LIKE '%".stripslashes($search_advertiser)."%' OR user.email LIKE '%".stripslashes($search_advertiser)."%' OR user.username LIKE '%".stripslashes($search_advertiser)."%')"; }
		/* adding the search condition for Advertisers - stop */


		/* adding the status campaign select condition - start */
		if(isset($_REQUEST['advertiser_status']) && $_REQUEST['advertiser_status']!=""){
				$advertiser_status = JRequest::getVar("advertiser_status", "YN");
				$_SESSION['advertiser_status']=  JRequest::getVar("advertiser_status", "YN");
		}
		else if (isset ($_SESSION['advertiser_status'])){
			$advertiser_status=$db->escape($_SESSION['advertiser_status']);
		}
		
		if(isset($advertiser_status) && $advertiser_status!="" && $advertiser_status!="YN")
			$and_filter.=" AND advertis.approved LIKE '%".stripslashes($advertiser_status)."%'";
		/* adding the status campaign select condition - stop */

		/* adding the status campaign select condition - start */
		if(isset($_POST['advertiser_enable']) && $_POST['advertiser_enable']!=""){

				$advertiser_enable=$_POST['advertiser_enable'];
				$_SESSION['advertiser_enable']=intval($_POST['advertiser_enable']);
		}
		else if (isset ($_SESSION['advertiser_enable'])){
			$advertiser_enable=$_SESSION['advertiser_enable'];
		}
		if(isset($advertiser_enable) && $advertiser_enable!="" && $advertiser_enable!=-1)
			$and_filter.=" AND user.block='".$advertiser_enable."'";
		/* adding the status campaign select condition - stop */
		/* pagination fix included + get advertisers - START */
		if (empty ($this->_customers)) {
		// if(isset($_SESSION['__default']["registry"]->_registry["global"]["data"]->list->limit)){
		// 	$long_session=$_SESSION['__default']["registry"]->_registry["global"]["data"]->list->limit;}
		// else {$long_session=NULL;}
		// $limitstart=$this->getState('limitstart');
		$long_session=$this->getState('limit');
		$db = JFactory::getDBO();
			if (isset($_POST['limit'])&&($_POST['limit']!='')&&($_POST['limit']!=0)&&($_POST['limitstart']!=0)&&($_POST['limitstart']!=''))
			{
				$and_limit=" LIMIT ".$_POST['limitstart'].",".$_POST['limit'];
			}
			elseif (isset($_POST['limit'])&&($_POST['limit']!='')&&($_POST['limit']!=0))
			{
				$and_limit=" LIMIT ".$_POST['limit'];
			}
			elseif (isset($_SESSION['limit'])&&($_SESSION['limit']!='')&&($_SESSION['limit']!=0))
			{
				$and_limit=" LIMIT ".$_SESSION['limit'];
			}
			elseif ($long_session)
			{
				$and_limit=" LIMIT ".$long_session;
			}
			$sql = "SELECT user.id, advertis.aid, advertis.company, advertis.ordering, advertis.approved, advertis.user_id, user.name, user.email, user.block, user.username, user.registerDate, count(c.id) count FROM #__ad_agency_advertis as advertis LEFT OUTER JOIN #__users as user on user.id=advertis.user_id LEFT JOIN #__ad_agency_campaign as c on c.aid=advertis.aid ".$and_filter." GROUP BY advertis.aid ORDER BY advertis.ordering ASC".$and_limit;
			//echo "<pre>";var_dump($sql);die();

			$sql2 = "SELECT user.id, advertis.aid, advertis.ordering, advertis.company, advertis.approved, advertis.user_id, user.name, user.email, user.block, user.username, user.registerDate, count(c.id) count FROM #__ad_agency_advertis as advertis LEFT OUTER JOIN #__users as user on user.id=advertis.user_id LEFT JOIN #__ad_agency_campaign as c on c.aid=advertis.aid ".$and_filter." GROUP BY advertis.aid ORDER BY advertis.ordering ASC";
			$this->_total = $this->_getListCount($sql2);
			$this->_customers = $this->_getList($sql);
		}
		//echo "<strong>".$sql."</strong><br />";
		/* pagination fix included + get advertisers - END */
		return $this->_customers;
	}

	function getAdvertiser() {
		if (empty ($this->_customer)) {
			$this->_customer = $this->getTable("adagencyAdvertiser");
			$this->_customer->load($this->_id);
		}
		return $this->_customer;
	}

	function storeExistent(){
		$data =  JRequest::get('post');
		//$data['user_id'] = $this->getUserToAdv($data['username']);
		//echo "<pre>";var_dump($data);die();
		if(!$data['user_id']) {
			$res = false;
			$_SESSION['adv_ext'] = $data;
		} else {
			$_SESSION['adv_ext'] = NULL;
			unset($_SESSION['adv_ext']);
			$item = $this->getTable('adagencyAdvertiser');
			if(isset($data['user_id'])){
				$sql = "UPDATE `#__users` SET `name` = '".$data['fullname']."', `email` = '".$data['email']."' WHERE `id` = ".intval($data['user_id']).";";
			}
			$res = true;
			if (!$item->bind($data)){
				$res = false;
			}

			if (!$item->check()) {
				$res = false;
			}

			if (!$item->store()) {
				$res = false;
			}
		}

		return $res;
	}

	function store (&$error) {
		jimport("joomla.database.table.user");
		$db = JFactory::getDBO();
		$user = new JUser();
        $my = new stdClass;
		$item = $this->getTable('adagencyAdvertiser');
		$data = JRequest::get('post');
		@$data['paywith'] = $data['payment_type'];
		$sendmail = $data['sendmail'];
		$get = JRequest::get('get');
		$approval_status = $data['approved'];
		global $mainframe;
		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$query = "SELECT title FROM `#__usergroups` WHERE id=".intval($usersConfig->get( 'new_usertype' ))."";
		$db->setQuery($query);
		$usergroupName = $db->loadColumn();
		$usergroupName = $usergroupName["0"];	
		if (!$data['user_id'])	{$data['password2'] = $data['password'];}
		if($data['task']== 'save_graybox') { $tmpl = '&tmpl=component'; } else { $tmpl = ''; }
		
		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);
		
        if ( isset($data['aid']) && isset($data['user_id']) && isset($data['email'])
            && ($data['aid'] > 0) && ($data['user_id'] > 0) )
        {
            $sql = "SELECT id FROM #__users WHERE email = '{$data['email']}' AND id <> {$data['user_id']}";
            $db->setQuery($sql);
            $exists_email = $db->loadColumn();
			@$exists_email = $exists_email["0"];
            // echo "<pre>";var_dump($exists_email);die();
            if ($exists_email) {
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
                $mainframe->redirect("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]={$data['user_id']}".$tmpl, JText::_('ADAG_EINUSE'), 'notice');
            }
        }

		if(((!$data['user_id'])||($data['user_id']==''))&&isset($data['email'])) {
			$sql = "SELECT id FROM #__users WHERE email = '".$data['email']."'";
			$db->setQuery($sql);
			$isEmail = $db->loadColumn();
			$isEmail = $isEmail["0"];
			if(isset($isEmail)&&($isEmail!=NULL)) {
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
				$mainframe->redirect('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=0'.$tmpl, JText::_('ADAG_EINUSE'),'notice');
			}
		}
		//echo "<pre>";var_dump($data);die();

		// iJoomla fix - start
		if(isset($_POST['email_daily_report']) && $_POST['email_daily_report'] == 'Y')
			$data['email_daily_report'] = 'Y';
		else
			$data['email_daily_report'] = 'N';

		if(isset($_POST['email_weekly_report']) && $_POST['email_weekly_report'] == 'Y')
			$data['email_weekly_report'] = 'Y';
		else
			$data['email_weekly_report'] = 'N';

		if(isset($_POST['email_month_report']) && $_POST['email_month_report'] == 'Y')
			$data['email_month_report'] = 'Y';
		else
			$data['email_month_report'] = 'N';

		if(isset($_POST['email_campaign_expiration']) && $_POST['email_campaign_expiration'] == 'Y')
			$data['email_campaign_expiration'] = 'Y';
		else
			$data['email_campaign_expiration'] = 'N';
		// iJoomla fix - stop

		// ADAGENCYONEFIVE-104 - new way of making Advertisers
		$advgroup = '';		
		
		////////////// IF USER EXISTS BUT ISN'T ADVERTISER ///////////////////
		$query = "SELECT * FROM `#__users` WHERE username='".$data['username']."'";
		$db->setQuery($query);
		$usr_details = $db->loadObject();
		if(isset($usr_details->id)) {
			$is_username = $usr_details->id;
			// Get info for current advertiser
			$query = "SELECT * FROM `#__ad_agency_advertis` WHERE user_id=".intval($is_username)."";
			$db->setQuery($query);
			$current = $db->loadObject();
		}

		if(isset($is_username) && ($is_username!='')) {
			$query = "SELECT aid FROM `#__ad_agency_advertis` WHERE user_id=".intval($is_username)."";
			$db->setQuery($query);
			$is_advertiser = $db->loadColumn();
			$is_advertiser = $is_advertiser["0"];
		}
		if((isset($is_username))&&(!isset($is_advertiser) || ($is_advertiser==''))){
			if (!$data['user_id']) $data['user_id'] = $is_username;

			if (!$item->bind($data)){
				$res = false;
			}

			if (!$item->check()) {
				$res = false;
			}

			if (!$item->store()) {
				$res = false;
			}

			$query = "SELECT aid FROM `#__ad_agency_advertis` WHERE user_id=".intval($is_username)."";
			$db->setQuery($query);
			$aid2 = $db->loadColumn();
			$aid2 = $aid2["0"];
			
			if(intval($aid2)>0){
				$the_aid = "&gb_aid=".$aid2;
			} else {
				$the_aid = NULL;
			}

			if($data['task']== 'save_graybox') { $the_link = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=save_graybox&tmpl=component&gb_aid='.intval($the_aid); } else { $the_link = 'index.php?option=com_adagency&controller=adagencyAdvertisers'.$tmpl.$the_aid; }

			$mainframe->redirect($the_link,'The advertiser you\'ve added already existed on our user database. We\'ve added advertiser rights to this user, and we didn\'t modify the password.');
			exit();

		}
		////////////// IF USER EXISTS BUT ISN'T ADVERTISER ///////////////////
		// ADAGENCYONEFIVE-104 - new way of making Advertisers $usergroupID
			
		if(isset($usr_details->usertype)) {
			$user->usertype = $usr_details->usertype;
			$user->gid = $usr_details->gid;
			$user->registerDate = $usr_details->registerDate;
		} else {
			$user->usertype = $usergroupName;
			$user->gid = $advgroup;
			$user->registerDate = date( 'Y-m-d H:i:s' );
		}
		if ($data['user_id']>0) { $data['id'] = $data['user_id']; }
		
		$user->bind($data);
		//if ($data['approved']=='1') $data['approved']='Y'; else $data['approved']='N';
		if ($data['enabled']=='1') $user->block='0'; else $user->block='1';

		$res = true;
		$my->id = $data['user_id'];
		
		$usrid = $data['user_id'];
		if ($usrid > 0) {
			$sql = "SELECT `password` FROM #__users WHERE id='".intval($usrid)."'";
			$db->setQuery($sql);
			$oldpass = $db->loadColumn();
			$oldpass = $oldpass["0"];
			$user->password = $oldpass;
		}
        
		if (isset($my->id) && ($my->id>0)) {
			if (!$user->save()) {
				$error = $user->getError();
				echo $error;
				$res = false;
			}
		} else {
            if (!$user->save()) {
                die();
				$error = $user->getError();
				echo $error;
				$res = false;
			}
			//$user->id = mysql_insert_id();
			if ($user->id==0) {
				$ask = "SELECT `id` FROM `#__users` ORDER BY `id` DESC LIMIT 1 ";
				$db->setQuery( $ask );
				$where = $db->loadColumn();
				$where = $where["0"];
				$user->id = $where;
			}
            
            $sql = "SHOW tables";
            $db->setQuery($sql);
            $res_tables = $db->loadColumn();
			
            $jconfigs = JFactory::getConfig();
            $dbprefix = $jconfigs->get('config.dbprefix');
                
            if(in_array($dbprefix . "comprofiler", $res_tables) && $user->id) {
                $sql = "INSERT INTO `#__comprofiler` (`id`, `user_id`) VALUES ('".intval($user->id)."', '".intval($user->id)."');";
                $db->setQuery($sql);
                $db->query();
            }
            
		}
        $sql = "SELECT `id` FROM #__usergroups WHERE `title`='".$usergroupName."'";
        $db->setQuery($sql);
        $advgroup = $db->loadColumn();
		$advgroup = $advgroup["0"];
		
		$sql = "select count(*) from #__user_usergroup_map where user_id=".intval($user->id)." and group_id=".intval($advgroup);
		$db->setQuery($sql);
		$ress = $db->loadColumn();
		$ress = $ress["0"];
		
		if($ress == 0){
			$sql = "INSERT INTO `#__user_usergroup_map` (`user_id` ,`group_id`)
			VALUES ('" . intval($user->id) . "', '" . intval($advgroup) . "');";
			$db->setQuery($sql);
			$db->query($sql);
		}
		      
        $configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");
        $configs = $configs->getConfigs();

        $subject_aa = $configs->sbafterreg;
        $message_aa = $configs->bodyafterreg;

        $subject_aa = str_replace('{name}',  $user->name,        $subject_aa);
        $subject_aa = str_replace('{login}', $user->username,    $subject_aa);

        $message_aa = str_replace('{name}', $user->name, $message_aa);
        $message_aa = str_replace('{login}', $user->username, $message_aa);

        $message_aa = html_entity_decode($message_aa, ENT_QUOTES);
        
		if($params["send_after_reg"] == 1){
			JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $user->email, $subject_aa, $message_aa, 1);
		}
		
		if(!$res) {
			$mainframe->enqueueMessage($user->getError(), 'error');
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
			$mainframe->redirect('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=0'.$tmpl,JText::_('ADAG_INUSE'));
			exit();
		}

		if (!$data['user_id']) $data['user_id'] = $user->id;
		$data['key'] = '';

		if (!$item->bind($data)){
		 	$res = false;
		}

		if (!$item->check()) {
			$res = false;
		}

		if (!$item->store()) {
			$res = false;
		}


		if ($res) {
			unset($_SESSION['ad_company']);
			unset($_SESSION['ad_description']);
			unset($_SESSION['ad_approved']);
			unset($_SESSION['ad_enabled']);
			unset($_SESSION['ad_username']);
			unset($_SESSION['ad_email']);
			unset($_SESSION['ad_name']);
			unset($_SESSION['ad_website']);
			unset($_SESSION['ad_address']);
			unset($_SESSION['ad_country']);
			unset($_SESSION['ad_state']);
			unset($_SESSION['ad_city']);
			unset($_SESSION['ad_zip']);
			unset($_SESSION['ad_telephone']);
		}

		if(($sendmail)&&(isset($current) && ($current->approved != $approval_status)&&($approval_status!='P'))) {
			$cid[] = $current->aid;
			$this->approve($approval_status, $cid);
		}

		if($data['task']== 'save_graybox') {
			$query = "SELECT aid FROM `#__ad_agency_advertis` ORDER BY aid DESC LIMIT 1";
			$db->setQuery($query);
			$aid2 = $db->loadColumn();
			$aid2 = $aid2["0"];
			
			return $aid2;
		} else {
			return $res;
		}
	}

	function getAdvertisersAjax(){
		$db = JFactory::getDBO();
		$sql = "SELECT a.aid, u.name FROM #__ad_agency_advertis AS a
				LEFT JOIN #__users AS u ON a.user_id = u.id
				ORDER BY u.name ASC
		";
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		$output = '';
		if(isset($res)&&(count($res)>0)&&(is_array($res))){
			$output = array();
			foreach($res as $element){
				$output[] = $element->aid.','.$element->name;
			}
			$output = implode('|',$output);
		}
		echo $output;
		die();
	}

	function getLastAdvertiser(){
		$db = JFactory::getDBO();
		$sql = "SELECT aid FROM #__ad_agency_advertis ORDER BY aid DESC LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadColumn();
		$res = $res["0"];
		return $res;
	}

	function delete () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		jimport("joomla.database.table.user");
		$db = JFactory::getDBO();
		$user = new JUser();
		$item = $this->getTable('adagencyAdvertiser');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	function block () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('adagencyAdvertiser');
		if ($task == 'block'){
			$sql = "update #__users set block='1' where id in (select user_id from #__ad_agency_advertis where aid in ('".implode("','", $cids)."'))";
			$ret = -1;
		} else {
			$ret = 1;
			$sql = "update #__users set block='0' where id in (select user_id from #__ad_agency_advertis where aid in ('".implode("','", $cids)."'))";
		}
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return true;
	}

	function approve ($task = NULL, $cid = NULL, $sendmail = 1) {
		$database = JFactory::getDBO();

		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");
		$configs = $configs->getConfigs();
		
		$sql = "select `params` from #__ad_agency_settings";
		$database->setQuery($sql);
		$database->query();
		$params = $database->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);
		
		$item = $this->getTable('adagencyAdvertiser');
		if ($task == 'Y'){
			$sql = "update #__ad_agency_advertis set approved='Y' where aid in ('".implode("','", $cid)."')";
		} elseif ($task == 'N') {
			$sql = "update #__ad_agency_advertis set approved='N' where aid in ('".implode("','", $cid)."')";
		} elseif ($task == 'P'){
			$sql = "update #__ad_agency_advertis set approved='P' where aid in ('".implode("','", $cid)."')";
		}

		$database->setQuery($sql);
		if (!$database->query() ){
			$this->setError($database->getErrorMsg());
			return false;
		}
		$query = "SELECT user_id FROM #__ad_agency_advertis WHERE aid IN ('".intval(implode("','", $cid))."')";
		$database->setQuery($query);
		if (!$database->query()) {
			exit();
		}
		$res_tables = $database->loadColumn();
		$cides = $res_tables;
		
		$cides[] = "0";
		$cids = implode( ',', $cides );
		$query = "SELECT id, name, username, email FROM #__users WHERE id IN ($cids)";
		$database->setQuery($query);
		if (!$database->query()) {
			exit();
		}
		$results = $database->loadObjectList();
		
		if(($task != 'P')&&($sendmail == 1)) {
			if(!isset($results[1])) {
					$name 		= $results[0]->name;
					$email 		= $results[0]->email;
					$username 	= $results[0]->username;

					if ($task == 'Y') {
						$subject=$configs->sbafterreg;
						$message=$configs->bodyafterreg;
					}
					else {
						$subject=$configs->sbadvdis;
						$message=$configs->bodyadvdis;
					}

					$subject = str_replace('{name}',$name,$subject);
					$subject = str_replace('{login}',$username,$subject);
					$subject = str_replace('{email}',$email,$subject);
					$message = str_replace('{name}',$name,$message);
					$message = str_replace('{login}',$username,$message);
					$message = str_replace('{email}',$email,$message);

					$subject = html_entity_decode($subject, ENT_QUOTES);
					$message = html_entity_decode($message, ENT_QUOTES);
					// mail publish advertiser  // Send email to user
					if($params["send_after_reg"] == 1){
						//JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $email, $subject, $message, 1);
					}
					
			} else {
				foreach ($results as $k => $v) {
					$name 		= $v->name;
					$email 		= $v->email;
					$username 	= $v->username;

					if ($approve == "Y") {
						$subject=$configs->sbafterreg;
						$message=$configs->bodyafterreg;
					}
					else {
						$subject=$configs->sbadvdis;
						$message=$configs->bodyadvdis;
					}

					$subject = str_replace('{name}',$name,$subject);
					$subject = str_replace('{login}',$username,$subject);
					$subject = str_replace('{email}',$email,$subject);
					$message = str_replace('{name}',$name,$message);
					$message = str_replace('{login}',$username,$message);
					$message = str_replace('{email}',$email,$message);

					$subject = html_entity_decode($subject, ENT_QUOTES);
					$message = html_entity_decode($message, ENT_QUOTES);
					// mail publish advertiser  // Send email to user
					if($params["send_after_reg"] == 1){
						//JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $email, $subject, $message, 1);
					}
					
				}
			}
		}
		return true;
	}
	
	function saveorder($idArray = null, $lft_array = null){
		// Get an instance of the table object.
		$table = $this->getTable("adagencyAdvertiser");

		if(!$table->saveorder($idArray, $lft_array)){
			$this->setError($table->getError());
			return false;
		}
		// Clean the cache
		$this->cleanCache();
		return true;
	}
	
	function approveAction(){
		$cid = JRequest::getVar("cid", array(), "post", "array");
		$db = JFactory::getDbo();
		
		if(isset($cid) && count($cid) > 0){
			foreach($cid as $key=>$id){
				$sql = "update `#__ad_agency_advertis` set `approved`='Y' where `aid`=".intval($id);
				$db->setQuery($sql);
				if(!$db->query()){
					return false;
				}
			}
		}
		return true;
	}
	
	function declineAction(){
		$cid = JRequest::getVar("cid", array(), "post", "array");
		$db = JFactory::getDbo();
		
		if(isset($cid) && count($cid) > 0){
			foreach($cid as $key=>$id){
				$sql = "update `#__ad_agency_advertis` set `approved`='N' where `aid`=".intval($id);
				$db->setQuery($sql);
				if(!$db->query()){
					return false;
				}
			}
		}
		return true;
	}

};
?>
