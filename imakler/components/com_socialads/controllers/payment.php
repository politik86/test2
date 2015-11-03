<?php			//CONTROLLER FILE
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT . DS . 'helper.php');
include_once(JPATH_COMPONENT.DS.'controller.php');
require(JPATH_SITE.DS."components".DS."com_socialads".DS."controllers".DS."showad.php");


class socialadsControllerpayment extends JControllerLegacy
{


function makepayment()
	{

		$input=JFactory::getApplication()->input;
		$arb_flag = ($input->get('arb_flag')) ? $input->get('arb_flag') : 0;

		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		$model = $this->getModel('showad');

		$amount=$input->get('amount','','FLOAT');
		//$session = JFactory::getSession();
		//$cop_check=$session->get('coupon_value_for_apply');
		$amt=$amount;
		$cop=$input->get('cop','','STRING');

		$processor=$input->get('processor','','STRING');
		$cop_dis_opn_hide=$input->get('cop_dis_opn_hide','','INT');

		JRequest::setVar('coupon_code',$cop);
		$model = $this->getModel('showad');
		$mod = $this->getModel('payment');
		if($cop_dis_opn_hide==0)
		{
			$adcop = $model->getcoupon();

		if($adcop)
		{
			if($adcop[0]->val_type == 1) //discount rate
				$val = ($adcop[0]->value/100)*$amount;
			else
				$val = $adcop[0]->value;
		}
		else
		{
			$val = 0;
		}
		$amt = round($amount - $val,2);
	}
		if($amt <= 0)
			$amt=0;
	//	$temp = $buildadsession->get('totalamount');
	//	$buildadsession->set('totalamount',$amt);
	//	$buildadsession->set('orgi_totalamount',$temp);


	/*	$temp = $amount;
		$buildadsession->set('totalamount',$amt);
		$buildadsession->set('orgi_totalamount',$temp);		*/
			$user = JFactory::getUser();
			$option = $processor;
			JPluginHelper::importPlugin( 'socialads', $option );
			$dispatcher = JDispatcher::getInstance();


			if($amt <= 0 && $adcop)
			{
					$paymentdata = new stdClass;
					$paymentdata->id = '';
					$paymentdata->ad_id = 0;
					$paymentdata->cdate =  date('Y-m-d H:i:s');
					$paymentdata->processor = $option;

					$paymentdata->ad_amount = $amt;
					$paymentdata->ad_original_amt = $amount;
					$paymentdata->status = 'C';
					$paymentdata->ad_coupon = $cop;
					$paymentdata->payee_id = $user->id;
					$paymentdata->ip_address = $_SERVER["REMOTE_ADDR"];
					$sticketid=$this->checkduplicaterecord($paymentdata);

					if(!$sticketid)
					{
						if(!$db->insertObject('#__ad_payment_info', $paymentdata, 'id'))
						{
							echo $db->stderr();
							return false;
						}
					}
					else
					{
						$this->setSession_ticketid($sticketid);
						return $sticketid;
					}
				echo "<div class='coupon_discount_all'> </div>";
				jexit();
			}
			else
			{
				$payment_type=$recurring_startdate="";
				$success_msg='';
				$totalamt=$amt;

				if($option=='jomsocialpoints' or $option=='alphauserpoints')
				{
					$plugin = JPluginHelper::getPlugin( 'payment',$option);
					$pluginParams = json_decode( $plugin->params );
					//$totalamt= $amt/$pluginParams->get('conversion');
					$totalamt = $amt;
					$success_msg=JText::sprintf( 'TOTAL_POINTS_DEDUCTED_MESSAGE', $amt);
				}
				//	$paymentdata->extras = 'occurrences='.$credits;
				//$model->setSession($new_value);

				$orderdata=array('payment_type'=>$payment_type,'order_id'=>'','pg_plugin'=>$option,'user'=>$user,'adid'=>0, 'amount'=>$totalamt,'original_amount'=>$amount,'coupon'=>$cop,'success_message'=>$success_msg);
				//Here orderid is id in payment_info table
				$orderid=$mod->createorder($orderdata);

				if(!$orderid)
				{
					echo $msg = JText::_( 'ERROR_SAVE' );
					exit();
				}
				$orderdata['order_id']=$orderid;
				//$html=$this->getHTML($orderdata);
				$html=$mod->getHTML($processor,$orderid);

				if(!empty($html))
					echo $html[0];
				jexit();
			}

	}//make payment ends here...

	// vm: don't use any where. (Hv to del funtion after chking whether it is used or not)
	function getHTML($orderdata)
	{

			require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

			$pg_plugin=$orderdata['pg_plugin'];
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('payment',$pg_plugin);
			$session = JFactory::getSession();
			$vars=new stdclass();
			$vars->order_id=$orderdata['order_id'];
			$socialadshelper = new socialadshelper();
			$orderdata['ad_title']=$socialadshelper->getAdInfo($orderdata['adid'],'ad_title');
			if(!empty($orderdata['payment_type']))
			$vars->payment_type=$orderdata['payment_type'];
		else
			$vars->payment_type="";
		$vars->user_id=JFactory::getUser()->id;
		$vars->user_name=JFactory::getUser()->name;
		$vars->user_firstname=JFactory::getUser()->name;
		$vars->user_email=JFactory::getUser()->email;
	//	$vars->item_name = JText::_('Advertisement-').$orderdata['ad_title']['0']->ad_title;
		$msg_fail=JText::_( 'ERROR_SAVE' );
		$vars->return = JRoute::_(JUri::root()."index.php?option=com_socialads&view=managead&layout=list");
		$vars->submiturl = JRoute::_("index.php?option=com_socialads&controller=payment&task=confirmpayment&processor={$pg_plugin}");
		$vars->cancel_return = JRoute::_(JUri::root()."index.php?option=com_socialads&view=managead&layout=list&processor={$pg_plugin}",$msg_fail);
		$vars->url=$vars->notify_url= JRoute::_(JUri::root()."?option=com_socialads&controller=payment&task=processpayment&pg_nm={$pg_plugin}&pg_action=onTP_Processpayment&order_id=".$orderdata['order_id']."&original_amt=".$orderdata['original_amount']);
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
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment',$pg_plugin);
		$html = $dispatcher->trigger('onTP_GetHTML', array($vars));
		return $html[0];

		jexit();
	}//Html engs here...

	function confirmpayment(){
		$model= $this->getModel( 'payment');
		$session =JFactory::getSession();
		$jinput=JFactory::getApplication()->input;
		$order_id = $session->get('order_id');
		$pg_plugin = $jinput->get('processor');

		$response=$model->confirmpayment($pg_plugin,$order_id);
	}

	function processpayment()
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$mainframe=JFactory::getApplication();
		$input=JFactory::getApplication()->input;
       // $post=$input->post;
          //$input->get
		$session =JFactory::getSession();
		if($session->has('payment_submitpost')){
			$post = $session->get('payment_submitpost');
			$session->clear('payment_submitpost');
		}
		else
			$post= JRequest::get( 'post' );

		$org_amt = $input->get('original_amt','','FLOAT');

		$pg_nm = $input->get('pg_nm');
		$pg_action = $input->get('pg_action');
		$model=  $this->getModel('payment');
		$order_id = $input->get('order_id',0,'INT');
		if(empty($post) || empty($pg_nm) ){
			JFactory::getApplication()->enqueueMessage(JText::_('SOME_ERROR_OCCURRED'), 'error');
			return;
		}
		$response=$model->processpayment($post,$pg_nm,$pg_action,$order_id,$org_amt);
		$response['msg']=trim($response['msg']);

		if(empty($response['msg']))
		{
			$response['msg'] = JText::_('DETAILS_SAVE');
			/*if($socialads_config['approval']==1 )
			{
				$response['msg'] .='<br>'.JText::_('AD_REVIEW');
			}*/

		}
		$mainframe->redirect($response['return'],$response['msg']);

	}

	function add_payment()
	{
		$mainframe=JFactory::getApplication();
		$input=JFactory::getApplication()->input;
       // $post=$input->post;
		$user=JFactory::getUser();
		$coupon_code = $input->get('coupon_code','','STRING');
		$value =  $input->get('value','','FLOAT');
		$mod = $this->getModel('payment');
		$comment = 'COUPON_ADDED';
		$success_msg=JText::sprintf( 'TOTAL_POINTS_DEDUCTED_MESSAGE', $value);
		$orderdata=array('order_id'=>'','pg_plugin'=>'','user'=>$user,'adid'=>0, 'amount'=>$value,'original_amount'=>$value,'coupon'=>$coupon_code,'success_message'=>$success_msg,'status'=>'C','comment'=>$comment);
		$orderid=$mod->createorder($orderdata);

		$transc = $mod->add_transc($value,$orderid,$comment);
		$json = $orderid;
		$content = json_encode($json);
		echo $content;
		jexit();
	}


}
