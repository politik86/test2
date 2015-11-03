<?php				//MODEL FILE
/*
  @package	Jticketing
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */

// no direct access
	defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
//jimport('joomla.filesystem.file');

class socialadsModelpayment extends JModelLegacy
{
	function getdetails($tid)
	{
		$query="SELECT o.ad_id,o.payee_id,o.processor,o.ad_amount,o.ad_original_amt,o.ad_coupon
				FROM #__ad_payment_info  as o
				where o.id=".$tid;
		$this->_db->setQuery($query);
		$details=$this->_db->loadObjectlist();
		$orderdata=array('payment_type'=>'',
		'order_id'=>$tid,
		'pg_plugin'=>$details[0]->processor,
		'user'=>$details[0]->payee_id,
		'adid'=>$details[0]->ad_id,
		'amount'=>$details[0]->ad_amount,
		'original_amount'=>$details[0]->ad_original_amt,
		'coupon'=>$details[0]->ad_coupon,
		'success_message'=>'');
		return $orderdata;
	}

	function getPaymentVars($pg_plugin, $order_id,$payPerAd=0)
	{
		global $mainframe;
		$mainframe  =  JFactory::getApplication();
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$orderdata  =  $this->getdetails($order_id);

		$vars = new stdclass();
		$vars->order_id = $orderdata['order_id'];
		$socialadshelper  =  new socialadshelper();
		$orderdata['ad_title'] = $socialadshelper->getAdInfo($orderdata['adid'],'ad_title'); // ck if not require
		if(!empty($orderdata['payment_type']))
			$vars->payment_type = $orderdata['payment_type'];
		else
			$vars->payment_type = "";

		$order_user  =  JFactory::getUser($orderdata['user']);
		$vars->user_id =  $orderdata['user'];//JFactory::getUser()->id;
		$vars->user_name = $order_user->name;
		$vars->user_firstname = $order_user->name;
		$vars->user_email = $order_user->email;
		if( $socialads_config['select_campaign']==1 )
		{
			$vars->item_name = JText::_('COM_SOCIALADS_ADWALLET_PAYMENT_DISC');
		}
		else
		{
			$vars->item_name = JText::_('COM_SOCIALADS_PER_AD_PAYMENT_DISC').$orderdata['ad_title']['0']->ad_title;
		}

		$msg_fail = JText::_( 'ERROR_SAVE' );

		$calledFrom = "&adminCall=0";

		if($mainframe->isAdmin())
		{
			$calledFrom = "&adminCall=1";
			$vars->return = JRoute::_(JUri::root()."administrator/index.php?option=com_socialads&view=approveads".$calledFrom,false);
		}
		else
		{
			$defaultMsg='';
			if($pg_plugin == 'paypal')
			{
				$defaultMsg="&saDefMsg=1";
			}
			$vars->return = JRoute::_(JUri::root()."index.php?option=com_socialads&view=managead&layout=list".$defaultMsg,false);
		}

		if(empty($payPerAd))
		{
			if($mainframe->isAdmin())
			{
				$vars->submiturl = JRoute::_(JUri::root()."administrator/index.php?com_socialads&view=adorders",$msg_fail);
			}
			else
			{
				$vars->submiturl = JRoute::_("index.php?option=com_socialads&controller=payment&task=confirmpayment&processor={$pg_plugin}",false);
			}
		}
		else
		{
			if($mainframe->isAdmin())
			{
				$vars->submiturl = JRoute::_(JUri::root()."administrator/index.php?com_socialads&view=adorders",$msg_fail);
			}
			else
			{
				$vars->submiturl = JRoute::_("index.php?option=com_socialads&controller=showad&task=confirmpayment&processor={$pg_plugin}",false);
			}
		}

		// CANCEL URL
		if($mainframe->isAdmin())
		{
			$vars->cancel_return = JRoute::_(JUri::root()."administrator/index.php?com_socialads&view=adorders",$msg_fail);
		}
		else
		{
			$vars->cancel_return = JRoute::_(JUri::root()."index.php?option=com_socialads&view=managead&layout=list&processor={$pg_plugin}",$msg_fail);
		}

		if(empty($payPerAd))
		{
			$vars->url=$vars->notify_url= JRoute::_(JUri::root()."?option=com_socialads&controller=payment&task=processpayment&pg_nm={$pg_plugin}&pg_action=onTP_Processpayment&order_id=".$orderdata['order_id']."&original_amt=".$orderdata['original_amount'].$calledFrom,false);
		}
		else
		{

			$vars->url=$vars->notify_url= JRoute::_(JUri::root()."?option=com_socialads&controller=showad&task=processpayment&pg_nm={$pg_plugin}&pg_action=onTP_Processpayment&order_id=".$orderdata['order_id']."&original_amt=".$orderdata['original_amount'].$calledFrom,false);
		}
		$vars->currency_code = $socialads_config['currency'];
		$vars->amount = $orderdata['amount'];
		$vars->client="socialads";
		$vars->success_message = $orderdata['success_message'];
		if($vars->payment_type=='recurring')
		{
			$vars->notify_url= $vars->url=$vars->url."&payment_type=recurring";
			$vars->recurring_startdate=$orderdata['recurring_startdate'];
			$vars->recurring_payment_interval_unit="days";
			$vars->recurring_payment_interval_totaloccurances=$orderdata['recurring_payment_interval_totaloccurances'];
			$vars->recurring_payment_interval_length=$orderdata['recurring_payment_interval_length'];
		}

		$vars->userInfo = $this->userInfo($order_id,$orderdata['user']);
		return $vars;
	}

	function userInfo($order_id,$userid='')
	{
		if(empty($userid))
		{
			$user = JFactory::getUser();
			$userid = $user->id;
		}

		$db = JFactory::getDBO();
		$query = "Select `user_id`,`user_email`,`firstname`,`lastname`,`country_code`,`state_code`,`address`,`city`,`phone`,`zipcode` FROM #__ad_users WHERE user_id=".$userid .' ORDER BY `id` DESC';
		$db->setQuery($query);
		$billDetails = $db->loadAssoc();

		// make address in 2 lines
		$billDetails['add_line1'] = $billDetails['address'];
		$billDetails['add_line2'] = '';

		/*if(!empty($billDetails['address']))
		{
			$count = str_word_count($billDetails['address'], 0)]

			//if only one word
			if($count == 1)
			{
				$billDetails['add_line1'] = $billDetails['address'];
			}
			else
			{

			}
			$words = str_word_count($billDetails['address'], 2)
		}*/
			// remove new line
		$remove_character = array("\n", "\r\n", "\r");
		$billDetails['add_line1'] = str_replace($remove_character ,' ',$billDetails['add_line1']);
		$billDetails['add_line2'] = str_replace($remove_character ,' ',$billDetails['add_line2']);
		return $billDetails;
	}

	function confirmpayment($pg_plugin,$oid)
	{
		$post	= JRequest::get('post');
		$vars = $this->getPaymentVars($pg_plugin,$oid);
		if(!empty($post) && !empty($vars) ){
			JPluginHelper::importPlugin('payment', $pg_plugin);
			$dispatcher = JDispatcher::getInstance();

			$result = $dispatcher->trigger('onTP_ProcessSubmit', array($post,$vars));
		}
		else{
			JFactory::getApplication()->enqueueMessage(JText::_('SOME_ERROR_OCCURRED'), 'error');
		}
	}

	function getAPIpluginData()
	{
		$condtion = array(0 => '\'payment\'');
		$condtionatype = join(',',$condtion);
		if(JVERSION >= '1.6.0')
		{
			$query = "SELECT extension_id as id,name,element,enabled as published FROM #__extensions WHERE folder in ($condtionatype) AND enabled=1";
		}
		else
		{
			$query = "SELECT id,name,element,published FROM #__plugins WHERE folder in ($condtionatype) AND published=1";
		}
		$this->_db->setQuery($query);
		$paymentPluginData = $this->_db->loadobjectList();
		if(JVERSION >= '1.6.0')
		{
			foreach( $paymentPluginData as $payParam)
			{
				//code to get the plugin param name...added by aniket
						$plugin = JPluginHelper::getPlugin('payment', $payParam->element);
						$params = new JRegistry($plugin->params);
						$pluginName = $params->get('plugin_name',$payParam->name,'STRING');
						$payParam->name = $pluginName;
			}
		}
		return $paymentPluginData;
	}


	function createorder($orderdata='')
	{
			$user=JFactory::getUser();
			$db = JFactory::getDBO();
			$paymentdata = new stdClass;
			$paymentdata->id = '';
			$paymentdata->cdate =  date('Y-m-d H:i:s');
			$paymentdata->ip_address = $_SERVER["REMOTE_ADDR"];


		//	$paymentdata->ad_id = $orderdata['adid'];

			$paymentdata->processor = $orderdata['pg_plugin'];
			//$paymentdata->ad_credits_qty = $orderdata['credits'];
			$paymentdata->ad_amount = $orderdata['amount'];
			$paymentdata->ad_original_amt =$orderdata['original_amount'];
			if(empty($orderdata['status']) or $orderdata['status']=='p')
			$paymentdata->status = 'P';
			else
			$paymentdata->status = $orderdata['status'];

			$paymentdata->ad_coupon = $orderdata['coupon'];
			if(empty($orderdata['payee_id']))
			$paymentdata->payee_id = $user->id;
			else
			$paymentdata->payee_id =$orderdata['payee_id'];
			if(isset($orderdata['comment']))
			$paymentdata->comment =$orderdata['comment'];
		$sticketid=$this->checkduplicaterecord($paymentdata);
		if(!$sticketid)
			{

				/*$query = "INSERT INTO #__ad_camp_transc (time,user_id,earn) VALUES (".$paymentdata->cdate.",".$paymentdata->payee_id.",".$paymentdata->ad_original_amt.")";
				$db->setQuery($query);
				$db->execute();*/

				if(!$db->insertObject('#__ad_payment_info', $paymentdata, 'id'))
				{
					echo $db->stderr();

					return false;
				}



				$orderid = $db->insertID();
			}
		else
			{
					//	$this->setSession_ticketid($sticketid);

						$sticketid;
						$paymentdata->processor;
						$query = "UPDATE #__ad_payment_info SET ad_id=0, ad_amount=$paymentdata->ad_amount , processor='$paymentdata->processor' WHERE id=$sticketid";
						$this->_db->setQuery($query);
						$this->_db->execute($query);


						$orderid = $sticketid;
			}
			//send mail for status pending

			$session =JFactory::getSession();
			if($session->has('order_id'))
				$session->clear('order_id');
			$session->set('order_id',$orderid);
			return $orderid;

	}


	function checkduplicaterecord($res1)
	{
		//clone object for php
		$res2=clone($res1);




		$db =JFactory::getDBO();

		$res2->ad_original_amt=number_format($res2->ad_original_amt, 2, '.', '');
		//$res2->fee=number_format($res2->fee, 2, '.', '');

		$res2->cdate=date('Y-m-d',strtotime($res2->cdate));

		$query = "select id from #__ad_payment_info where payee_id=".$db->quote($res2->payee_id)." AND  status='P' AND DATE_FORMAT(cdate,'%Y-%m-%d')=".$db->quote($res2->cdate)." AND ad_original_amt=".$res2->ad_original_amt;


		$db->setQuery($query);

		return $id = $db->loadresult();


	}


	function processpayment($post,$pg_nm,$pg_action,$order_id,$org_amt)
	{
		$return_resp=array();
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		$db =JFactory::getDBO();
		//Authorise Post Data
		if($post['plugin_payment_method']=='onsite')
		$plugin_payment_method=$post['plugin_payment_method'];
		//get VARS
		$vars = $this->getPaymentVars($pg_nm,$order_id);
		//END vars
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment', $pg_nm);
		$data = $dispatcher->trigger($pg_action, array($post,$vars));
		$data = $data[0];

		$socialadshelper = new socialadshelper();
		$buildad_itemid = $socialadshelper->getSocialadsItemid('managead');
		$billing_itemid = $socialadshelper->getSocialadsItemid('billing');


		//get order id
		if(empty($order_id))
			$order_id=$data['order_id'];
		$return_resp['return']=$data['return'];
		$processed=0;
		$res=$this->storelog($pg_nm,$data);
		$processed=$this->Dataprocessed($data['transaction_id'],$order_id);

		$query="SELECT o.ad_amount
				FROM #__ad_payment_info  as o
				where o.id=".$order_id;
		$this->_db->setQuery($query);
		$order_amount=$this->_db->loadResult();
		$return_resp['status']='0';

		if($data['status']=='C' && $order_amount == $data['total_paid_amt'])
		{
			if($processed==0)
			{
				$this->saveOrder($data,$order_id,$pg_nm);
				//$return_resp['return']=JRoute::_(JUri::root().'index.php?option=com_socialads&view=payment&layout=success');
				$return_resp['return']= JUri::root().substr(JRoute::_('index.php?option=com_socialads&view=billing&itemid='.$billing_itemid,false),strlen(JUri::base(true))+1);
			}
			$return_resp['msg']=$data['success'];
			$return_resp['status']='1';

		}
		else if(!empty($data['status']))
		{

		 	if($plugin_payment_method and  $data['status']=='P')
			{
				//to do for comfirm
				//	$transc = $this->add_transc($org_amt,$order_id);
				$return_resp['return']= JUri::root().substr(JRoute::_('index.php?option=com_socialads&view=billing&itemid='.$billing_itemid,false),strlen(JUri::base(true))+1);
			}
			else if($plugin_payment_method and  $data['status']!='P')
				$return_resp['return']=JRoute::_(JUri::root().'index.php?option=com_socialads&view=showad&layout=cancelorder');

			if($order_amount != $data['total_paid_amt']){
				$data['status'] = 'E';
				$this->cancelOrder($data,$order_id,$pg_nm);
			}
			else if($data['status']!='C'){
				$data['status'] = 'P';
				$this->cancelOrder($data,$order_id,$pg_nm);
			}
			else if($data['status']!='C' and $processed==0){
				$data['status'] = 'P';
				$this->updateOrderStatus($data,$order_id,$pg_nm);
			}

			$return_resp['status']='0';
			$res->processor = $data['processor'];
			$return_resp['msg']=$data['error']['code'].$data['error']['desc'];

		}

		//$this->SendOrderMAil($order_id,$pg_nm);  //as we have not going to send any mail till order confirm
		return $return_resp;


	}



	function saveOrder($data,$orderid,$pg_nm)
	{
		$db = JFactory::getDBO();
		$paymentdata = new stdClass;
		$paymentdata->id = $orderid;
		$paymentdata->transaction_id = $data['transaction_id'];

		if($data['status']== 'C')
		{
			$paymentdata->status='C';
			if(!empty($data['payment_type']) && $data['payment_type']=='recurring')
			{
				$paymentdata->subscription_id	=$data['subscription_id'];
				if(empty($data['payment_number']))
				{
						$paymentdata->status='P';
				}

			}

			$query = "SELECT ad_original_amt FROM #__ad_payment_info WHERE id =".$orderid;
			$db->setQuery($query);
			$tol_amt = $db->loadresult();
			$comment="ADS_PAYMENT";
			$transc = $this->add_transc($tol_amt,$orderid,$comment);

		}

		$paymentdata->extras = $data['raw_data'];
		if(!$db->updateObject('#__ad_payment_info', $paymentdata, 'id'))
		{
			echo $db->stderr();
	   		return false;
		}

		if($paymentdata->status == 'C')
		{
			// added by VM:
			$socialadsModelpayment = new socialadsModelpayment();
			$sendmail=$socialadsModelpayment->SendOrderMAil($orderid,$pg_nm);
			// end Vm
			return true;

		}
		else
		{
			return false;
		}

	}



	function cancelOrder($data,$order_id,$pg_nm)
	{
			$query = "UPDATE #__ad_payment_info SET status ='{$data['status']}',extras='{$data['raw_data']}' WHERE id =".$order_id;
			$this->_db->setQuery($query);
			if(!$this->_db->execute())
			{
			echo $db->stderr();
	   		return false;
			}

	}


	function updateOrderStatus($data,$order_id,$pg_nm)
	{

			$query = "UPDATE #__ad_payment_info SET status ='{$data['status']}',extras='{$data['raw_data']}' WHERE id =".$order_id;
			$this->_db->setQuery($query);
			if(!$this->_db->execute())
			{
			echo $db->stderr();
	   		return false;
			}

	}


	function Dataprocessed($transaction_id,$order_id)
	{
		$where='';
		$db= JFactory::getDBO();

		$query= "SELECT * FROM #__ad_payment_info WHERE id={$order_id} AND status='C'".$where;
		$db->setQuery($query);
		$paymentdata=$db->loadResult();
		if(!empty($paymentdata))
		return 1;
		else
		return 0;
	}

	function storelog($pg_plugin,$data)
	{
    $data1=array();
    $data1['raw_data']=$data['raw_data'];
		$data1['JT_CLIENT']="com_socialads";

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment', $pg_plugin);
		$data = $dispatcher->trigger('onTP_Storelog', array($data1));

	}


	//function SendOrderMAil($order_id="75",$pg_nm ="byorder")
	function SendOrderMAil($order_id,$pg_nm,$payPerAd=0)
	{
		require_once(JPATH_SITE.DS."components".DS."com_socialads".DS."helper.php");  // require when we call from backend
		/*$db = JFactory::getDBO();
		$query = "SELECT p.ad_amount, p.processor,p.comment,p.status,p.extras
							FROM #__ad_payment_info as p
							 WHERE p.id=".$order_id;
		$db->setQuery($query);
		$details = $db->loadobjectlist();*/

		// vm: start
		$socialadshelper = new socialadshelper();
		$sendInvoice = 1;
		if($sendInvoice == 1)
		{
			//$status = $socialadshelper->sendInvoice($order_id,$pg_nm);
			$details = $socialadshelper->getInvoiceDetail($order_id,$pg_nm,$payPerAd);
		}
		// vm: end

		//for payment details send through email
		//$details = socialadshelper::paymentdetails($adid);
	/*	if($details)
		{
			$details[0]->payment_method=$pg_nm;
			$mail = $socialadshelper->new_pay_mail($order_id, $details);
			//for send mail to admin approval when payment is done
		}*/
   }

	function add_transc($org_amt,$order_id,$comment)
	{


				$db =JFactory::getDBO();
				$query = "SELECT payee_id FROM #__ad_payment_info WHERE id =".$order_id;
				$db->setQuery($query);
				$userid = $db->loadresult();

				$date = microtime(true);
				$date1 = date('Y-m-d');
				$query = "SELECT balance FROM #__ad_camp_transc WHERE time = (SELECT MAX(time)  FROM #__ad_camp_transc WHERE user_id=".$userid.")";
							$db->setQuery($query);
							$bal = $db->loadresult();
				$balance= $bal + $org_amt;
				$amount_due = new stdClass;
				$amount_due->id = '';
				$amount_due->time = $date;
				$amount_due->user_id = $userid;
				$amount_due->spent = '';
				$amount_due->earn = $org_amt;
				$amount_due->balance = $balance;
				$amount_due->type = 'O';
				$amount_due->type_id = $order_id;
				$amount_due->comment = $comment;
				if(!$db->insertObject('#__ad_camp_transc', $amount_due, 'id'))
				{
					echo $db->stderr();

					return false;
				}
		return $db->insertID();

	}
	//vm
	function getHTML($pg_plugin,$order_id,$payPerAd=0)
	{
		$vars = $this->getPaymentVars($pg_plugin,$order_id,$payPerAd);
		$pg_plugin = trim($pg_plugin);
		JPluginHelper::importPlugin('payment',$pg_plugin);
		$dispatcher = JDispatcher::getInstance();
		$html = $dispatcher->trigger('onTP_GetHTML', array($vars));
		return $html;
	}

	/** VM: This function update order gateway on change of gateway*/
	function updateOrderGateway($selectedGateway,$order_id)
	{
		$db   =  JFactory::getDBO();
		$row  =  new stdClass;
		$row->id = $order_id;
		$row->processor = $selectedGateway;
		if(!$this->_db->updateObject('#__ad_payment_info', $row, 'id'))
		{
			echo $this->_db->stderr();
			return 0;
		}
		return 1;
	}
}
?>
