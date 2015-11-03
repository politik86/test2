<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT . DS . 'helper.php');
include_once(JPATH_COMPONENT.DS.'controller.php');

class socialadsControllerShowad extends JControllerLegacy
{
	/* redirect to edit ad form*/
	function editad()
	{
		$model = $this->getModel('showad');
		$itemid = socialadshelper::getSocialadsItemid('buildad');
		$link = JRoute::_("index.php?option=com_socialads&view=buildad&Itemid=$itemid&frm=editad",false);
		$this->setRedirect($link);
	}

	/*  save and activate function  */
	function save()
	{
			$activate = 1;
			$this->draft($activate);

	}



	/* redirect to manage ads and saves an ad*/
	function draft($activate='')
	{
			require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
			$input=JFactory::getApplication()->input;
		$model = $this->getModel('showad');
		$ad_id = $input->get('adid',0,'INT');
		$socialadshelper = new socialadshelper();
		$itemid = $socialadshelper->getSocialadsItemid('managead');
		$buildadsession = JFactory::getSession();
		if(!$buildadsession->get('ad_id')){
			if($activate)
			{
				$adid = $model->store('save');
			}
			else
			{
				$adid = $model->store('1');
			}
		}
		else{
			$adid = $buildadsession->get('ad_id');
		}

		if ($adid)
		{

			$sacontroller = new socialadsController();
			$sacontroller->execute('delimages');
			$msg = JText::_('DETAILS_SAVE');
			if($socialads_config['approval']==1)
			{
				$msg .='<br>'.JText::_('AD_REVIEW');
			}

			$link = JRoute::_("index.php?option=com_socialads&Itemid=$itemid&view=managead",false);
			$this->setRedirect($link, $msg);
		}
		else
		{
			$msg = JText::_( 'ERROR_SAVE');
		}

	}

	//Paypal function for payment
	function makepayment()
	{
			$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		//$data = JRequest::get( 'post' );
		$cop=	($input->get('cop','','STRING')) ? $input->get('cop','','STRING') : '';
		$arb_flag = ($input->get('arb_flag')) ? $input->get('arb_flag') : 0;

		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		$model = $this->getModel('showad');
		$buildadsession = JFactory::getSession();

		$data = JRequest::get( 'post' );

		if(!empty($arb_flag) and $arb_flag==1)
			$buildadsession->set('arb_flag',1);
		$amount=$buildadsession->get('totalamount');
		$amt=$amount;

			//interchange amt and cop_amt
		$cop_dis_opn_hide=$input->get('cop_dis_opn_hide','','INT');
		 JRequest::setVar('coupon_code',$cop);
		$model = $this->getModel('showad');
		$adcop='';
	if($cop_dis_opn_hide==0){
		$adcop = $model->getcoupon();
		if($adcop)
		{
				if($adcop[0]->val_type == 1) 		//discount rate
					$val = ($adcop[0]->value/100)*$buildadsession->get('totalamount');
				else
					$val = $adcop[0]->value;
		}
		else
				$val = 0;
		$amt = round($buildadsession->get('totalamount')- $val,2);

		//
		if($adcop)
		{
				if($adcop[0]->val_type == 1) 		//discount rate
					$valchrg = ($adcop[0]->value/100)*$buildadsession->get('sa_ad_totalamount');
				else
					$valchrg = $adcop[0]->value;
		}
		else
				$valchrg = 0;
		$amtchrg = round($buildadsession->get('sa_ad_totalamount')- $valchrg,2);
		//

	}
		if($amt <= 0)
		$amt=0;
		$temp = $buildadsession->get('totalamount');
		$buildadsession->set('totalamount',$amt);
		$buildadsession->set('orgi_totalamount',$temp);

		if(!$buildadsession->get('ad_id') ){
			$adid = $model->store();
		}
		else{
			$adid = $buildadsession->get('ad_id');
		}

		if ($adid)
		{
			$sacontroller = new socialadsController();
			$sacontroller->execute('delimages');

			$msg = JText::_('DETAILS_SAVE');
			if($socialads_config['approval']==1 )
			{
				$msg .='<br>'.JText::_('AD_REVIEW');
			}



			$user = JFactory::getUser();
			$option = $buildadsession->get('ad_gateway');
			JPluginHelper::importPlugin( 'socialads', $option );
			$dispatcher = JDispatcher::getInstance();
		  	$ad_chargeoption =  $buildadsession->get('ad_chargeoption');

			if($ad_chargeoption >= 2){
					$credits =$buildadsession->get('ad_totaldays','');

			}
			else{
				$credits = $buildadsession->get('ad_totaldisplay');
			}
			$buildadsession->set('credits', $credits);
			$buildadsession->set('cop', $cop);
			$paymentdata = new stdClass;
			if($amt <= 0 && $adcop){

					$db = JFactory::getDBO();	//TODO ad payment for date type ads
						//added  for date type ads
						$query = "SELECT ad_payment_type FROM #__ad_data WHERE ad_id =".$adid;
						$db->setQuery($query);
						$ad_payment_type = $db->loadResult();
						if(($ad_payment_type!=2))
						{
							$query = "UPDATE #__ad_data SET ad_credits = ad_credits + $credits, ad_credits_balance = $credits WHERE ad_id=".$adid;
							$db->setQuery($query);
							$db->execute();
						}
						else if($ad_payment_type>=2){
								socialadshelper::adddays($adid,$credits);
						}


					$paymentdata->id = '';
					$paymentdata->ad_id = $adid;
					$paymentdata->cdate =  date('Y-m-d H:i:s');
					$paymentdata->processor = $buildadsession->get('ad_gateway');

					if($ad_chargeoption>=2)
					{
							$credits =$buildadsession->get('ad_totaldays','');
					}
					$paymentdata->ad_credits_qty = $credits;
					$paymentdata->ad_amount = $buildadsession->get('totalamount');
					$paymentdata->ad_original_amt = $buildadsession->get('orgi_totalamount');
					$paymentdata->status = 'C';
					$paymentdata->ad_coupon = $cop;
					$paymentdata->payee_id = $user->id;
					$paymentdata->ip_address = $_SERVER["REMOTE_ADDR"];
					if(!$db->insertObject('#__ad_payment_info', $paymentdata, 'id'))
					{
						echo $db->stderr();
						return false;
					}
				echo "<div class='coupon_discount_all'> </div>";
				jexit();
			}
			else{

			if(($buildadsession->get('ad_chargeoption')>2 ) and ($buildadsession->get('arb_flag')!=1 ) )
			$credits = $buildadsession->get('ad_chargeoption') * $credits;
			else if(($buildadsession->get('ad_chargeoption')>2 ) and ($buildadsession->get('arb_flag')==1 ) ){
			$credits = $buildadsession->get('ad_chargeoption') ;
			$paymentdata->extras = 'occurrences='.$credits;

		}

			$payment_type=$recurring_startdate="";
				if($arb_flag==1 and $ad_chargeoption > 2)
				{
					$payment_type="recurring";
					$recurring_startdate=$startDate=$buildadsession->get('datefrom','');

				}
				$success_msg='';
				$totalamt=$buildadsession->get('totalamount');
				if($option=='jomsocialpoints' or $option=='alphauserpoints')
				{
					$plugin = JPluginHelper::getPlugin( 'payment',$buildadsession->get('ad_gateway'));
					$pluginParams = json_decode( $plugin->params );
					if(isset($pluginParams->conversion))
					$totalamt= $buildadsession->get('totalamount')/$pluginParams->conversion;
					$success_msg=JText::sprintf( 'TOTAL_POINTS_DEDUCTED_MESSAGE', $buildadsession->get('totalamount'));
				}
				$paymentdata->extras = 'occurrences='.$credits;
				$orderdata=array('payment_type'=>$payment_type,'order_id'=>'','pg_plugin'=>$option,'user'=>$user, 'adid'=>$adid, 'amount'=>$totalamt,'original_amount'=>$buildadsession->get('orgi_totalamount'),'coupon'=>$cop,'credits'=>$credits,'recurring_startdate'=>$recurring_startdate,'recurring_payment_interval_length'=>$buildadsession->get('ad_chargeoption',''),'recurring_payment_interval_totaloccurances'=>$buildadsession->get('ad_totaldays',''),'success_message'=>$success_msg);
				//Here orderid is id in payment_info table
				$orderid=$model->createorder($orderdata);


				if(!$orderid)
				{
					echo $msg = JText::_( 'ERROR_SAVE' );
					exit();
				}
				$orderdata['order_id']=$orderid;

				$html=$this->getHTML($orderdata);

				if(!empty($html))
				echo $html;
				jexit();
			}
		}
		else
		{
			$msg = JText::_( 'ERROR_SAVE' );
		}

	}

	function confirmpayment(){
		$model= $this->getModel( 'showad');
		$session =JFactory::getSession();
		$jinput=JFactory::getApplication()->input;
		$order_id = $session->get('order_id');
		$pg_plugin = $jinput->get('processor');

		$response=$model->confirmpayment($pg_plugin,$order_id);
	}

	function getHTML($orderdata)
		{

			require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

			$pg_plugin=$orderdata['pg_plugin'];
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('payment',$pg_plugin);
			$session = JFactory::getSession();
			$vars = new stdclass;
			$vars->order_id=$orderdata['order_id'];
			$socialadshelper= new socialadshelper();
			$orderdata['ad_title']=$socialadshelper->getAdInfo($orderdata['adid'],'ad_title');
			if(!empty($orderdata['payment_type']))
			$vars->payment_type=$orderdata['payment_type'];
			else
			$vars->payment_type="";
			$vars->user_id=JFactory::getUser()->id;
			$vars->user_name=JFactory::getUser()->name;
			$vars->user_firstname=JFactory::getUser()->name;
			$vars->user_email=JFactory::getUser()->email;
			$vars->item_name = JText::_('ADVERTISEMENT').$orderdata['ad_title']['0']->ad_title;
			$msg_fail=JText::_( 'ERROR_SAVE' );
			$vars->return = JRoute::_(JUri::root()."index.php?option=com_socialads&view=managead&layout=list");
			$vars->submiturl = JRoute::_("index.php?option=com_socialads&controller=showad&task=confirmpayment&processor={$pg_plugin}");
			$vars->cancel_return = JRoute::_(JUri::root()."index.php?option=com_socialads&view=managead&layout=list&processor={$pg_plugin}",$msg_fail);
			$vars->url=$vars->notify_url= JRoute::_(JUri::root()."?option=com_socialads&controller=showad&task=processpayment&pg_nm={$pg_plugin}&pg_action=onTP_Processpayment&order_id=".$orderdata['order_id']);
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
			$html = $dispatcher->trigger('onTP_GetHTML', array($vars));
			return $html[0];

			jexit();
	}


	function processpayment()
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$mainframe=JFactory::getApplication();
			$input=JFactory::getApplication()->input;
		$session =JFactory::getSession();

		if($session->has('payment_submitpost')){
			$post = $session->get('payment_submitpost');
			$session->clear('payment_submitpost');
		}
		else
		$post= JRequest::get( 'post' );
		$pg_nm = $input->get('pg_nm','','STRING');
		$pg_action = $input->get('pg_action');
		$model=  $this->getModel('showad');
		$order_id = $input->get('order_id',0,'INT');
		$adminCall = $input->get('adminCall',0,'INT');

/*
		//-----------------
		$order_id = 305;
		$pg_nm ="paypal";
		$pg_action="onTP_Processpayment";
		$test='{"mc_gross":"56.25","protection_eligibility":"Eligible","address_status":"confirmed","payer_id":"KYCMB66E86NJ6","tax":"0.00","address_street":"1 Main St","payment_date":"02:34:14 Apr 21, 2014 PDT","payment_status":"Completed","charset":"windows-1252","address_zip":"95131","first_name":"amol","mc_fee":"29.30","address_country_code":"US","address_name":"amol Gh","notify_version":"3.7","custom":"5","payer_status":"verified","business":"sagar_c-facilitator@tekdi.net","address_country":"United States","address_city":"San Jose","quantity":"1","verify_sign":"AR9MdXiX.C.pIkQaeAZK6vcYLP1pA-0ghZ4J2ieEOTQP6.GSzPFB8Wqr","payer_email":"amol_g@tekdi.net","txn_id":"4F491985R9812780J","payment_type":"instant","last_name":"Gh","address_state":"CA","receiver_email":"sagar_c-facilitator@tekdi.net","payment_fee":"29.30","receiver_id":"MKR5A5SU2W9VL","txn_type":"web_accept","item_name":"","mc_currency":"USD","item_number":"","residence_country":"US","test_ipn":"1","handling_amount":"0.00","transaction_subject":"5","payment_gross":"56.25","shipping":"0.00","ipn_track_id":"8912c2141deac"}';

		$post = json_decode($test,true);
*/
		//-----------------

		if(empty($post) || empty($pg_nm) ){
			JFactory::getApplication()->enqueueMessage(JText::_('SOME_ERROR_OCCURRED'), 'error');
			return;
		}
		$response=$model->processpayment($post,$pg_nm,$pg_action,$order_id);
		$response['msg']=trim($response['msg']);

		if(empty($response['msg']))
		{
			$response['msg'] = JText::_('DETAILS_SAVE');
			if($socialads_config['approval']==1 )
			{
				//$response['msg'] .='<br>'.JText::_('AD_REVIEW');
			}
		}

		if(strpos($response['return'], "administrator")  || $adminCall == 1)
		{
			$admin_url= $response['return']."&SA_PAYMSG=".$response['msg'];
			$mainframe->redirect($admin_url);
		}
		else
		{
			$mainframe->redirect($response['return'],$response['msg']);
		}
		//$mainframe->redirect($response['return'],$response['msg']);
//		$this->setRedirect( $response['return'], $response['msg']);

	}

	//this is for automated Recurring Billing when response from silent url comes and payment is approved
	function confirm_recurring_payment_bill()
	{
		$model=  $this->getModel('showad');
		//$json='{"option":"com_socialads","controller":"showad","task":"confirm_recurring_payment_bill","tmpl":"component","processor":"authorizenet","method":"recurring","x_method":"CC","x_test_request":"false","x_trans_id":"317","x_invoice_num":"256660","x_description":"This is a test Silent Post from Free Authnet Scripts","x_amount":"10.00","x_cust_id":"5297","x_first_name":"John","x_last_name":"Conde","x_company":"Free Authnet Scripts","x_address":"132 Main Street","x_city":"Townsville","x_state":"TN","x_zip":"12345","x_country":"US","x_phone":"800-555-1234","x_fax":"800-555-5678","x_email":"john.conde@example.com","x_ship_to_first_name":"John","x_ship_to_last_name":"Conde","x_ship_to_company":"Free Authnet Scripts","x_ship_to_address":"500 Broad Street","x_ship_to_city":"Big City","x_ship_to_state":"TN","x_ship_to_zip":"90210","x_ship_to_country":"US","x_tax":"0.00","x_duty":"0.00","x_freight":"0.00","x_tax_exempt":"false","x_po_num":"123456","x_MD5_Hash":"e4d909c290d0fb1ca068ffaddf22cbd0","x_cavv_response":"4","api":"ARB","x_response_code":"1","x_response_subcode":"1","x_response_reason_code":"1","x_response_reason_text":"This transaction has been approved.","x_auth_code":"3ns9fx","x_avs_code":"N","x_type":"AUTH_CAPTURE","x_subscription_id":"1465373","x_subscription_paynum":"1","Itemid":null}';//json_encode($_REQUEST);	//'{"x_response_code":"1","x_response_reason_code":"1","x_response_reason_text":"This transaction has been approved.","x_avs_code":"Y","x_auth_code":"ZRREJM","x_trans_id":"2167777655","x_method":"CC","x_card_type":"Discover","x_account_number":"XXXX0012","x_first_name":"afaf","x_last_name":"PATIL","x_company":"","x_address":"","x_city":"","x_state":"","x_zip":"","x_country":"","x_phone":"","x_fax":"","x_email":"","x_invoice_num":"171","x_description":"","x_type":"auth_capture","x_cust_id":"","x_ship_to_first_name":"","x_ship_to_last_name":"","x_ship_to_company":"","x_ship_to_address":"","x_ship_to_city":"","x_ship_to_state":"","x_ship_to_zip":"","x_ship_to_country":"","x_amount":"250.00","x_tax":"0.00","x_duty":"0.00","x_freight":"0.00","x_tax_exempt":"FALSE","x_po_num":"","x_MD5_Hash":"A6E76CE31827CBEE73E4AA060684F386","x_cvv2_resp_code":"","x_cavv_response":"2","x_test_request":"false","x_subscription_id":"1261519","x_subscription_paynum":"1"}';
		$json=json_encode($_REQUEST);
		$data=json_decode($json,true);
		JPluginHelper::importPlugin( 'payment', $data['processor'] );
		$dispatcher =JDispatcher::getInstance();
		$response=$dispatcher->trigger('confirm_recurring_payment_Update', array($json));
		if(!empty($response[0]))
		$model->confirm_recurring_payment_bill($response[0]);



	}

	//this is for cancel automated Recurring Billing
	function cancelsubscription()
	{
		$db=JFactory::getDBO();
			$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
  		$subid = $input->get('subscriptionid');
  		$id=$input->get('id',0,'INT');
 		$gateway = $input->get('processor');
 		$ad_id=$input->get('ad_id',0,'INT');

		if($subid)
		{
			$data=array($subid,$id,$gateway,$ad_id);
			JPluginHelper::importPlugin( 'payment', $gateway );
			$dispatcher =JDispatcher::getInstance();
			$response=$dispatcher->trigger('cancelsubscription', array($data));
			if($response['0']['status']=='C')
			{

			$paymentdata=new stdClass;
			$paymentdata->id =$response['0']['transaction_id'];
			$paymentdata->subscription_id='' ;
			$paymentdata->status='P' ;
			$paymentdata->extras="subscription cancelled-".$subid;
			$db->updateObject('#__ad_payment_info', $paymentdata, 'id');
			global $mainframe;
			$mainframe = JFactory::getApplication();
			$msg=JText::_( 'AUTH_SUB_CANCEL_SUCCESS' );

			}
			else
			$msg=JText::_( 'AUTH_SUB_CANCEL_SUCCESS' );


		}
		$mainframe->redirect('index.php?option=com_socialads&view=managead&Itemid='.$input->get('Itemid',0,'INT'),$msg);

	}


	function makemorepayment(){
			require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
			$model = $this->getModel('showad');
			$buildadsession = JFactory::getSession();
			$input=JFactory::getApplication()->input;
			$data = JRequest::get( 'post' );

			$adid = $data['adid'];
			$buildadsession->set('adid', $data['adid']);
			$buildadsession->set('ad_totalamount', $data['totalamount']);
			$buildadsession->set('ad_totaldisplay', $data['totaldisplay']);
			$buildadsession->set('ad_gateway', $data['gateway']);
			//interchange amt and cop_amt
			$cop=	($input->get('cop','','STRING')) ? $input->get('cop','','STRING') : '';
			$arb_flag = ($input->get('arb_flag')) ? $input->get('arb_flag') : 0;
			$amt=$data['totalamount'];

			//interchange amt and cop_amt
			$cop_dis_opn_hide=$input->get('cop_dis_opn_hide','','INT');
			JRequest::setVar('coupon_code',$data['cop']);
			$model = $this->getModel('showad');
			$adcop='';
			if($cop_dis_opn_hide==0){
			$adcop = $model->getcoupon();
			if($adcop)
			{
					if($adcop[0]->val_type == 1) 		//discount rate
						$val = ($adcop[0]->value/100)*$data['totalamount'];
					else
						$val = $adcop[0]->value;
			}
			else
					$val = 0;

			$amt = round($data['totalamount']- $val,2);
		}
			if($amt <= 0)
				$amt=0;

			$temp = $data['totalamount'];
			$buildadsession->set('ad_totalamount',$amt);
			$buildadsession->set('orgi_totalamount',$temp);

			$ad_chargeoption =  $buildadsession->get('ad_chargeoption');
			if($ad_chargeoption >= 2){
				if(!isset($data['ad_chargeoption_day']) || !$data['ad_chargeoption_day'] ){
						// code to put start date in ad data table
					$db = JFactory::getDBO();
					$addata=new stdClass;
					$addata->ad_id=$adid;

					$addata->ad_startdate=$data['datefrom'];
					$db->updateObject('#__ad_data', $addata, 'ad_id');
				}
				$credits =$data['ad_totaldays'];
			}
			else{
				$credits = $data['totaldisplay'];
			}

			$user = JFactory::getUser();
			$option = $data['gateway'];
			$success_msg='';
			$totalamt=$buildadsession->get('ad_totalamount');
				if($option=='jomsocialpoints' or $option=='alphauserpoints')
				{
					$plugin = JPluginHelper::getPlugin( 'payment',$buildadsession->get('ad_gateway'));
					$pluginParams = json_decode( $plugin->params );
					$totalamt= $buildadsession->get('ad_totalamount')/$pluginParams->conversion;
					$success_msg=JText::sprintf( 'TOTAL_POINTS_DEDUCTED_MESSAGE', $buildadsession->get('ad_totalamount'));
				}
			JPluginHelper::importPlugin( 'payment', $option );
			$dispatcher = JDispatcher::getInstance();
			if($amt <= 0 && $adcop){
						$db = JFactory::getDBO();
						//added  for date type ads
						$query = "SELECT ad_payment_type FROM #__ad_data WHERE ad_id =".$adid;
						$db->setQuery($query);
						$ad_payment_type = $db->loadResult();
						if(($ad_payment_type!=2))
						{


							$query = "UPDATE #__ad_data SET ad_credits = ad_credits + $result->ad_credits_qty, ad_credits_balance = ad_credits_balance + $result->ad_credits_qty WHERE ad_id=".$result->ad_id;
							$db->setQuery($query);
							$db->execute();
						}
						else if($ad_payment_type>=2){
								socialadshelper::adddays($adid,$result->ad_credits_qty);
						}

						$paymentdata = new stdClass;
						$paymentdata->id = '';
						$paymentdata->ad_id = $adid;
						$paymentdata->cdate =  date('Y-m-d H:i:s');
						$paymentdata->processor = $buildadsession->get('ad_gateway');
						$paymentdata->ad_credits_qty = $credits;
						$paymentdata->ad_amount =$buildadsession->get('orgi_totalamount');
						$paymentdata->ad_original_amt = $buildadsession->get('orgi_totalamount');
						$paymentdata->status = 'C';
						$paymentdata->ad_coupon = $data['cop'];
						$paymentdata->payee_id = $user->id;
						$paymentdata->ip_address = $_SERVER["REMOTE_ADDR"];
						if(!$db->insertObject('#__ad_payment_info', $paymentdata, 'id'))
						{
							echo $db->stderr();
							return false;
						}
				global $mainframe;
				$mainframe = JFactory::getApplication();
				$mainframe->redirect('index.php?option=com_socialads&view=managead&Itemid='.$input->get('Itemid',0,'INT'));


				}
				else{
					$orderdata=array('order_id'=>'','pg_plugin'=>$option,'user'=>$user, 'adid'=>$adid, 'amount'=>$totalamt,'original_amount'=>$buildadsession->get('orgi_totalamount'),'coupon'=>$cop,'credits'=>$credits,'success_message'=>$success_msg);
					//Here orderid is id in payment_info table
					$orderid=$model->createorder($orderdata);
					if(!$orderid)
					{
						echo $msg = JText::_( 'ERROR_SAVE' );

					}
					$orderdata['order_id']=$orderid;

					$html=$this->getHTML($orderdata);

					if(!empty($html))
					echo $html;

				}



			}
			/*function test()
			{
					$model = $this->getModel('showad');
					$model->SendOrderMAil();
			}*/


}// class end


