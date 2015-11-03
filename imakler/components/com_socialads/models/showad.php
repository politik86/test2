<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.utilities.utility' );
jimport( 'joomla.html.parameter' );

require_once(JPATH_SITE. DS."components".DS."com_socialads". DS . 'helper.php');

/* Showad Model */
class socialadsModelShowad extends JModelLegacy
{
	function getdetails($tid){
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
	function getPaymentVars($pg_plugin, $order_id)
	{

		if(!class_exists('socialadsModelpayment'))
		{
			//require_once $path;
			 JLoader::register('socialadsModelpayment', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models'.DS.'payment.php');
			 JLoader::load('socialadsModelpayment');
		}
		$socialadsModelpayment = new socialadsModelpayment();
		return $socialadsModelpayment->getPaymentVars($pg_plugin, $order_id,$payPerAd=1);
/*
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$orderdata = $this->getdetails($order_id);
			$pg_plugin=$orderdata['pg_plugin'];
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
			return $vars;
			* */
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
	//get the points
	function getJomSocialPoints()
	{
		$buildadsession = JFactory::getSession();
		$plugin = JPluginHelper::getPlugin( 'payment',$buildadsession->get('ad_gateway'));
	  $pluginParams = json_decode( $plugin->params );
	  return $buildadsession->get('ad_totalamount') * $pluginParams->conversion;
	}

	/* Gets the ad data  */
	function getAdDetails()
	{
		$input=JFactory::getApplication()->input;
		$ad_id = $input->get('adid',0,'INT');
		$query = "SELECT d.*, f.*
					FROM #__ad_data AS d, #__ad_fields AS f
					WHERE d.ad_id = f.adfield_ad_id AND d.ad_id = $ad_id";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();

		return $result;
	}//end function getAdDetails

	// function to get payment info
	function getPayment()
	{
		$input=JFactory::getApplication()->input;
		$ad_id = $input->get('adid',0,'INT');
		$query = "SELECT d.*, f.*
					FROM #__ad_data AS d, #__ad_fields AS f
					WHERE d.ad_id = f.adfield_ad_id AND d.ad_id = $ad_id";
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();
		return $result;

	}//end function getpayment

	function getAds($adid = '')
	{
		require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');

		if(empty($adid))
		{
			$adid = 0;
		}
		$adRetriever = new adRetriever();
		$preview =  $adRetriever->getAdHTML($adid, 1);
		return $preview;
	}

	function getcoupon($c_code='')
	{
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$user = JFactory::getUser();
		$db= JFactory::getDBO();

		if(empty($c_code))
		{
			$c_code = $input->get('coupon_code','','STRING');
		}


		$count='';
		if($c_code)
		{
	 	$query="SELECT value,val_type
				FROM #__ad_coupon
				WHERE ( ( CURDATE( ) BETWEEN from_date AND exp_date)	 OR		 from_date = '0000-00-00 00:00:00')
				AND (max_use  > (SELECT COUNT(api.ad_coupon) FROM #__ad_payment_info as api WHERE api.ad_coupon =".$db->quote($db->escape($c_code)). " AND api.status='C') OR max_use=0)
				AND (max_per_user > (SELECT COUNT(api.ad_coupon) FROM #__ad_payment_info as api WHERE api.ad_coupon = ".$db->quote($db->escape($c_code))." AND api.payee_id= ".$user->id." AND api.status='C') OR max_per_user=0)
				AND published = 1
				AND code=".$db->quote($db->escape($c_code));
		$db->setQuery($query);
		$count = $db->loadObjectList();
		}
		return $count;
	}

	/* store function
	 param draft= 1; called by save drafts
	*/
	function store($draft = '')
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$user = JFactory::getUser();
		$buildadsession = JFactory::getSession();
		$build = new stdClass;
		$build->ad_id = '';
		$build->ad_creator = $user->id;

		foreach($buildadsession->get('ad_data') as $addata)
		{
			foreach($addata as $k=>$ad)
			{
		     	$build->$k = $ad;
			}
		}

		$ad_image=$buildadsession->get('ad_image');
	  if($ad_image == '')
	  {
	  $ad_image = $buildadsession->get('upimgcopy');
	  }
		$ad_image = str_replace(JUri::base(),'',$ad_image);
		$build->ad_image = $ad_image;
		$build->ad_noexpiry = '';



	 	if( $socialads_config['select_campaign']==0)
		{
			if(!$draft && $buildadsession->get('ad_chargeoption')>=2  )
			{
				$build->ad_startdate = $buildadsession->get('datefrom');
			}

			if($buildadsession->get('ad_chargeoption')>=2)
			{
				$build->ad_payment_type=2;
			}
			else
			{
				$build->ad_payment_type = $buildadsession->get('ad_chargeoption');
			}

		}
		// if end....condition for save price

		$build->ad_enddate = '';
		$build->ad_created_date = date('Y-m-d H:i:s');
		$build->ad_modified_date = date('Y-m-d H:i:s');
		/* if non draft ad then make it published */
		if($draft == 'save' || !$draft)
		{
		$build->ad_published = 1;
		}
		else
		{
			$build->ad_published = 0;
		}
		//Extra Code For Zone
		$build->ad_zone 	 =	$buildadsession->get('adzone');
		$build->layout 		 =	$buildadsession->get('layout');
		//Extra Code For Zone

		if( $socialads_config['select_campaign']==1)
		{
			$db= JFactory::getDBO();

			$query = "SELECT campaign FROM #__ad_campaign WHERE campaign ='".$buildadsession->get('camp')."'";
			$db->setQuery($query);
			$campaign = $db->loadResult();

			if(empty($campaign))
			{
				$query = "INSERT INTO #__ad_campaign (campaign,user_id,daily_budget,camp_published) VALUES ('".$buildadsession->get('camp')."',".$user->id.",".$buildadsession->get('value').",1)";
				$db->setQuery($query);
				$db->execute();
			}

			$query = "SELECT camp_id FROM #__ad_campaign WHERE campaign ='".$buildadsession->get('camp')."'";
			$db->setQuery($query);
			$camp_id = $db->loadResult();
			$build->camp_id	 =	$camp_id;
			$build->ad_payment_type	 =	$buildadsession->get('pricing_opt');
			if($socialads_config['bidding']==1)
				{
						$build->bid_value	 =	$buildadsession->get('bid_value');
				}
		}


		$geoflag = 0;
		$geo_fields = $buildadsession->get('geo_fields');
		if( !empty($geo_fields) ){
			foreach( $buildadsession->get('geo_fields') as $geo){
				if(!empty($geo)){
					$geoflag=1;
					break;
				}
			}
		}

		if( $buildadsession->get('geo_target')=="on"
			&& !$geoflag
			&& !($buildadsession->get('social_target')=="on")
			|| !($buildadsession->get('geo_target')=="on")
			&& !($buildadsession->get('social_target')=="on") )
		{

			if(($buildadsession->get('context_target')!="on")
			|| ($buildadsession->get('context_target')=="on" &&!($buildadsession->get('context_target_data_keywordtargeting'))))
			{
				$build->ad_guest = 1;
			}
		}

		//code for guest
		if($socialads_config['approval']==0)
		{
			$build->ad_approved = 1;
		}


		//insert fields
		if (!$this->_db->insertObject( '#__ad_data', $build, 'ad_id' ))
		{
			echo $this->_db->stderr();
			return false;
		}

	$buildadsession->set('ad_id',$build->ad_id);

		$buildadsession->set('ad_storedid',$this->_db->insertid());

	  $session_adfields = $buildadsession->get('ad_fields');
		$profile=$buildadsession->get('plg_fields');
	/*start of geo*/
if($socialads_config['geo_target']  && $buildadsession->get('geo_target')=="on" ){
	  $geo_adfields = $buildadsession->get('geo_fields');
		if($geoflag)
		{

			$first_key = array_keys($buildadsession->get('geo_fields'));
			$type = str_replace("by","",$buildadsession->get('geo_type'));
			$fielddata = new stdClass;
			$fielddata->ad_id = $build->ad_id;
			foreach($buildadsession->get('geo_fields') as $key => $value)
			{
					if($first_key[0] == $key){
						$fielddata->$key = $value;
					}
					else if($type == $key)
						$fielddata->$key = $value;
					else if($buildadsession->get('geo_type') == "everywhere")
						break;
			}

			if (!$this->_db->insertObject( '#__ad_geo_target', $fielddata, 'id' ))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
}
/*end of geo*/

/*start of context*/
if($socialads_config['context_target']  && $buildadsession->get('context_target')=="on"){
	  $context_adfields = $buildadsession->get('context_target_data_keywordtargeting');
		if($context_adfields)
		{
			$fielddata = new stdClass;
			$fielddata->id='';
			$fielddata->ad_id = $build->ad_id;
			$fielddata->keywords=strtolower(trim($context_adfields));

			if (!$this->_db->insertObject( '#__ad_contextual_target',$fielddata,'id' ))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
}

/*end of context*/

	 	if((!empty($session_adfields) || !empty($profile) )   && $buildadsession->get('social_target')=="on"){

		//For saving demographic details
		$fielddata = new stdClass;
		$fielddata->adfield_id = '';
		$fielddata->adfield_ad_id = $build->ad_id;

		$date_low = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,1910));
		$date_high = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,2030));
		$grad_low=0;
    $grad_high=2030;
   if(!empty($session_adfields))
	 {
		foreach($buildadsession->get('ad_fields') as $mapdata)
		{
			foreach($mapdata as $m=>$map)
			{
				if($m)
				{	if(strstr($m,','))
					{
						$selcheck = explode(',',$m);
						$var = isset($fielddata->$selcheck[0]);
					}
					else{
						$var = isset($fielddata->$m);
					}
					if(!$var)
					{
						if(strstr($m,'|'))
						{
							$rangecheck = explode('|',$m);
							if($rangecheck[2]==0)
							{
								if($map)
								{
									if($rangecheck[1]=='daterange')
										$date_low = $map;
									elseif($rangecheck[1]=='numericrange')
										$grad_low = $map;
								}
								if($rangecheck[1]=='daterange')
									$fielddata->$rangecheck[0] = $date_low;  //1900
								elseif($rangecheck[1]=='numericrange')
									$fielddata->$rangecheck[0] = $grad_low;  //0

							}
							elseif($rangecheck[2]==1)
							{
								if($map)
								{
									if($rangecheck[1]=='daterange')
										$date_high = $map;
									elseif($rangecheck[1]=='numericrange')
										$grad_high = $map;
								}
								if($rangecheck[1]=='daterange')
									$fielddata->$rangecheck[0] = $date_high;  //2030
								elseif($rangecheck[1]=='numericrange')
									$fielddata->$rangecheck[0] = $grad_high;  //2030
							}
						}
						elseif(strstr($m,','))
						{
							$selcheck = explode(',',$m);
							if($selcheck[1]=='select')
							{
								if($map)
								{	$fielddata->$selcheck[0] = '|'.$map.'|';}
								else
								{	$fielddata->$selcheck[0] = $map;}
							}
						}
						else
						{
							$fielddata->$m = $map;
						}
					}
					else
					{
						if(strstr($m,','))
						{
							$selcheck = explode(',',$m);
							if($selcheck[1]=='select')
							{
								$fielddata->$selcheck[0] .= '|'.$map.'|';
							}
						}
						else
						{
							$fielddata->$m .= '|'.$map.'|';
						}
						//$fielddata->$m .= ','.$map;
					}
				}
			}
		}
	}
		JPluginHelper::importPlugin('socialadstargeting');
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onFrontendTargetingSave',array($profile));
		for($i=0; $i<count($results); $i++)
		{
			if($results[$i] !="")
			{
				foreach($results[$i] as $key => $value)
				{
					$fielddata->$key = $value;
				}
			}
		}
		//insert fields

		if (!$this->_db->insertObject( '#__ad_fields', $fielddata, 'adfield_id' ))
		{
	   		 echo $this->_db->stderr();
	   		 return false;
		}

	}//empty condition checkin ends



		return $build->ad_id;

	} // end store function

	function getzonedata($zonid)
	{
		$db= JFactory::getDBO();

		$query= 'SELECT * FROM #__ad_zone where id = ' . (int) $zonid;
		$db->setQuery($query);
		$zone_data = $db->loadObjectList();
		return $zone_data;
	}

	function processpayment($post,$pg_nm,$pg_action,$order_id)
	{
		$input=JFactory::getApplication()->input;
		$isadmin = $input->get('adminCall',0,'INTEGER');

		$return_resp=array();
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		//Authorise Post Data
		if(isset($post['plugin_payment_method']) && $post['plugin_payment_method']=='onsite')
			$plugin_payment_method=$post['plugin_payment_method'];
		//get VARS
		$vars = $this->getPaymentVars($pg_nm,$order_id);
		//END vars
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment', $pg_nm);
		$data = $dispatcher->trigger($pg_action, array($post,$vars));
		$data = $data[0];

		//get order id
		if(empty($order_id))
		{
			$order_id=$data['order_id'];
		}
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
				$link = empty($isadmin)?'index.php?option=com_socialads&view=managead&layout=list': 'administrator/index.php?option=com_socialads&view=approveads';
				$return_resp['return']=JRoute::_(JUri::root().$link,false);
			}
			$return_resp['msg']=$data['success'];
			$return_resp['status']='1';

		}
		else 	if(!empty($data['status']))
		{
		 	if($plugin_payment_method and  $data['status']=='P')
		 	{
				$link = empty($isadmin)?'index.php?option=com_socialads&view=managead&layout=list': 'administrator/index.php?option=com_socialads&view=approveads';
				$return_resp['return']=JRoute::_(JUri::root().$link,false);
			}
			else if($plugin_payment_method and  $data['status']!='P')
			{
				$link = empty($isadmin)?'index.php?option=com_socialads&view=showad&layout=cancelorder': 'administrator/index.php?option=com_socialads&view=approveads';
				$return_resp['return']=JRoute::_(JUri::root().$link,false);
			}

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
			//$res->processor = $pg_nm;//$data['processor'];
			@$return_resp['msg']=$data['error']['code'].$data['error']['desc'];
		}

		$res=trim($return_resp['msg']);


		$userid = $vars->userInfo['user_id'];
		$user = JFactory::getUser($userid);

		$adminApproval=0;

		if (isset($user->groups['8']) || isset($user->groups['7']) || isset($user->groups['Super Users']) || isset($user->groups['Administrator']) || $user->usertype == "Super Users" || isset($user->groups['Super Users']) || isset($user->groups['Administrator']) || $user->usertype == "Super Administrator" || $user->usertype == "Administrator" )
		{
			$adminApproval = 1;
		}

		if($adminApproval==0)
		{

			if(!$res AND $pg_nm=='bycheck')
			{
				$return_resp['msg']=JText::_('COM_SA_ORDER_PLACED_NOTIFICATION');
			}

			if(!$res AND $pg_nm=='byorder')
			{
				$return_resp['msg']=JText::_('COM_SA_ORDER_PLACED_NOTIFICATION_FOR_PAYBYORDER');
			}

			if($socialads_config['approval'])
			{
				$msg = $return_resp['msg'];
				$return_resp['msg'] = $msg.' '.JText::_('COM_SA_ADMIN_APPROVAL_NOTICE');
			}

		}
		else
		{
			$msg = $return_resp['msg'];
			$return_resp['msg'] = $msg.' '.JText::_('COM_SA_CREATE_AD');
		}
		//$this->SendOrderMAil($order_id,$pg_nm);  //as we have not going to send any mail till order confirm
		return $return_resp;


	}


	//function SendOrderMAil($order_id="92",$pg_nm ="byorder")
	function SendOrderMAil($order_id,$pg_nm)
	{
		$db = JFactory::getDBO();
		$query = "SELECT ad_id	FROM #__ad_payment_info WHERE id =".$order_id;
		$db->setQuery($query);
		 $adid = $db->loadResult();

		// vm: start
		$socialadshelper = new socialadshelper();
		$sendInvoice = 1;
		if(!empty($sendInvoice))
		{
			//$status = $socialadshelper->sendInvoice($order_id,$pg_nm);
			$details = $socialadshelper->getInvoiceDetail($order_id,$pg_nm,$payPerAd=1);
		}
		// vm: end

		//for payment details send through email
		//commented by vm: as this mail coverin invoice mail
		/*$details = socialadshelper::paymentdetails($adid);
		if($details)
		{
			$details[0]->payment_method=$pg_nm;
			$mail = socialadshelper::newadmail($adid, $details);
		//for send mail to admin approval when new ad created
		}
		*/
   }

	function cancelOrder($data,$order_id,$pg_nm)
	{
		$db = JFactory::getDBO();
		$raw_data = json_encode($data['raw_data']);
		$orderdetail = new stdClass;
		$orderdetail->id = $order_id;
		$orderdetail->status = $data['status'];
		$orderdetail->extras = json_encode($raw_data);

		if (!$db->updateObject( '#__ad_payment_info', $orderdetail, 'id' ))
		{
			echo $db->stderr();
			return false;
		}
		/*$query = "UPDATE #__ad_payment_info SET status ='{$data['status']}',extras=\"".$raw_data."\" WHERE id =".$order_id;

		$this->_db->setQuery($query);
		if(!$this->_db->execute())
		{
			echo $db->stderr();
			return false;
		}
*/
	}

	function updateOrderStatus($data,$order_id,$pg_nm)
	{

		$db = JFactory::getDBO();
		$raw_data = json_encode($data['raw_data']);
		$orderdetail = new stdClass;
		$orderdetail->id = $order_id;
		$orderdetail->status = $data['status'];
		$orderdetail->extras = json_encode($raw_data);

		if (!$db->updateObject( '#__ad_payment_info', $orderdetail, 'id' ))
		{
			echo $db->stderr();
			return false;
		}



			/*$query = "UPDATE #__ad_payment_info SET status ='{$data['status']}',extras='{$data['raw_data']}' WHERE id =".$order_id;
			$this->_db->setQuery($query);
			if(!$this->_db->execute())
			{
			echo $db->stderr();
	   		return false;
			}*/

	}
	function confirm_recurring_payment_bill($response)
	{
			$db = JFactory::getDBO();
			$query = "SELECT ad_id,payee_id,processor,ad_credits_qty,subscription_id,ad_amount,ad_original_amt,status,ad_coupon FROM #__ad_payment_info WHERE id =".$response['order_id'];
			$db->setQuery($query);
			$result = $db->loadObject();
			if($response['payment_number']>=1)
			{

				$orderdata=array(
				'adid'=>$result->ad_id,
				'payee_id'=>$result->payee_id,
				'pg_plugin'=>$result->processor,
				'credits'=>$result->ad_credits_qty,
				'amount'=>$result->ad_amount,
				'original_amount'=>$result->ad_original_amt,
				'status'=>'P',
				'coupon'=>$result->ad_coupon,
				'subscription_id'=>$response['subscription_id'],
				'transaction_id'=>$response['transaction_id'],
				'payment_type'=>$response['payment_type'],
				'raw_data'=>$response['raw_data'],
				'payment_number'=>$response['payment_number'],

				);
				if($response['payment_number']>1)
				$orderid=$this->createorder($orderdata);
				else
				$orderid=$response['order_id'];

				$orderdata['status']=$response['status'];
				$pg_nm=$result->processor;
				if($orderid)
				{
					$this->saveOrder($orderdata,$orderid,$pg_nm);
				}
			}

	}



	function createorder($orderdata)
	{

	 	$user=JFactory::getUser();
		$db = JFactory::getDBO();
		$paymentdata = new stdClass;
		$paymentdata->id = '';
		$paymentdata->ad_id = $orderdata['adid'];
		$paymentdata->cdate =  date('Y-m-d H:i:s');
		$paymentdata->processor = $orderdata['pg_plugin'];
		$paymentdata->ad_credits_qty = $orderdata['credits'];
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

		$paymentdata->ip_address = $_SERVER["REMOTE_ADDR"];

		if(!$db->insertObject('#__ad_payment_info', $paymentdata, 'id'))
		{
			echo $db->stderr();
	   		return false;
		}
		$orderid = $db->insertID();
		$session =JFactory::getSession();
		if($session->has('order_id'))
			$session->clear('order_id');
		$session->set('order_id',$orderid);
	return $orderid;

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
			if(!empty($data['payment_type']))
			{
				if($data['payment_type']=='recurring')
				{
					$paymentdata->subscription_id	=$data['subscription_id'];
					if(empty($data['payment_number']))
					{
							$paymentdata->status='P';
					}

				}
			}

			$query = "SELECT subscription_id,ad_credits_qty,ad_id FROM #__ad_payment_info WHERE id =".$orderid;
			$db->setQuery($query);
			$result = $db->loadObject();

			if(!$result->ad_credits_qty)
			{
				$result->ad_credits_qty=0;
			}

			//added  for date type ads
			$adid=$result->ad_id;
			$query = "SELECT ad_payment_type FROM #__ad_data WHERE ad_id =".$adid;
			$db->setQuery($query);
			$ad_payment_type = $db->loadResult();

			if(($ad_payment_type==2))
			{
				socialadshelper::adddays($adid,$result->ad_credits_qty);
			}
			else
			{
				$query = "UPDATE #__ad_data SET ad_credits = ad_credits + $result->ad_credits_qty, ad_credits_balance = ad_credits_balance + $result->ad_credits_qty WHERE ad_id=".$result->ad_id;
				$db->setQuery($query);
				$db->execute();
			}
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
			$socialadsModelShowad = new socialadsModelShowad();
			$sendmail=$socialadsModelShowad->SendOrderMAil($orderid,$pg_nm,1);
			// end Vm
			return true;
		}
		else
			return false;


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

}//class end



