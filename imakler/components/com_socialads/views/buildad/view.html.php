<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
/**
 * HTML View class for the socialads Component
 *
 * @package    socialads
 * @subpackage Views
 */
class socialadsViewBuildad extends JViewLegacy
{
    /**
     * Buildad view display method
     * @return void
     **/
	function display($tpl = null)
	{
		$input =JFactory::getApplication()->input;
		$layout =  $input->get('layout','default');
		$user = JFactory::getUser();

		if($user->id)
		{
			//User authorized to view chat history
			if(! JFactory::getUser($user->id)->authorise('core.create_ad', 'com_socialads'))
			{
				$app=JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_SOCIALADS_AUTH_ERROR'),'warning');
				return false ;
			}
		}

		$model	= $this->getModel( 'buildad');
          //$post=$input->post;
   		if($input->get('frm','','STRING')!='editad' && $input->get('frm','','STRING')!='directad' )
			{

				$buildadsession = JFactory::getSession();
				$buildadsession->clear('ad_data');
				$buildadsession->clear('ad_image');
				$buildadsession->clear('upimgcopy');
				$buildadsession->clear('ad_fields');
				$buildadsession->clear('plg_fields');
				$buildadsession->clear('upimg');
				$buildadsession->clear('datefrom');
				$buildadsession->clear('dateto');
				$buildadsession->clear('ad_totaldays');
				$buildadsession->clear('ad_totaldisplay');
				$buildadsession->clear('totalamount');
				$buildadsession->clear('ad_chargeoption');
				$buildadsession->clear('ad_gateway');
				$buildadsession->clear('ad_currency');
				$buildadsession->clear('ad_rate');
				$buildadsession->clear('guestbutton');
				$buildadsession->clear('addatapluginlist');
				$buildadsession->clear('pluginimg');
				$buildadsession->clear('user_points');
				//Added by sagar
				$buildadsession->clear('arb_flag');
				$buildadsession->clear('order_id');
				//Added by sagar

				//added for geo targeting
				$buildadsession->clear('geo_type');
				$buildadsession->clear('geo_fields');
				$buildadsession->clear('geo_target');
				$buildadsession->clear('social_target');
				//added for geo targeting
								$buildadsession->clear('context_target_data_keywordtargeting');

				//$buildadsession->clear('ad_id');
				$buildadsession->clear('camp');
				$buildadsession->clear('value');
				$buildadsession->clear('pricing_opt');

			}

		$model	=  $this->getModel( 'buildad');
		$fields = $this->get('Fields');

		$this->fields= $fields ;

		// url select list
		$url1 = array();
		$url1[] = JHtml::_('select.option','http', JText::_("HTTP"));
		$url1[] = JHtml::_('select.option','https', JText::_("HTTPS"));
		$this->assignRef('url1', $url1);

		//for payment-info. view
		$result = $this->get('Payment');
		$this->paypal = $result ;

		//session variable for ad-data
		$buildadsession =JFactory::getSession();

		$this->managead_adid = $ad_id = $input->get('adid',0,'INT');
		$session=JFactory::getSession();

		if(!$ad_id)
		{
			$ad_id=$session->get('ad_id');
		}
		$session->set('ad_id',$ad_id);

		$this->ad_id=$ad_id;
		$this->allowWholeAdEdit=1;

		$socialadshelper = new socialadshelper();
		$this->Itemid = $socialadshelper->getSocialadsItemid('managead');

		 if($ad_id)
        {
			$builadModel=$this->getModel();

			$this->checkItIsuserAd = $builadModel->checkItIsuserAd($ad_id);
			if(!$this->checkItIsuserAd)
			{
				$session->clear('ad_id');
				return false;
			}

			require_once(JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models'.DS.'managead.php');

			$managead_model=new socialadsModelManageAd();
			//var_dump($managead_model); die('adasdasd');
			$addata_for_adsumary_edit = $managead_model->getData($ad_id);

			//$this->assignRef( 'ad_socialtarget',$addata[0] );
			$this->assignRef( 'addata_for_adsumary_edit',$addata_for_adsumary_edit[1] );
			$this->assignRef( 'social_target',	$addata_for_adsumary_edit[0] );
			$zone = $managead_model->getzone($ad_id);
			$this->assignRef( 'zone',$zone[0]);
			$this->geo_target=$managead_model->getData_geo($ad_id);
			//$this->assignRef( 'geo_target',	$managead_model->getData_geo());
			$Data_context_target=$managead_model->getData_context_target($ad_id);

			$this->assignRef( 'context_target',	$Data_context_target['keywords']);
			$this->assignRef( 'context_target_data_keywordtargeting',$Data_context_target['keywords']);

			$this->pricingData=$builadModel->getpricingData($ad_id);

			$sa_addCredit = $input->get('sa_addCredit',0);
			$this->sa_addCredit = $sa_addCredit;
			// called from add more credit
			$this->editableSteps = array();
			if(!empty($sa_addCredit))
			{
				$socialadshelper = new socialadshelper;
				$user= JFactory::getUser();
				$sa_addCredit = $input->get('sa_addCredit');
				$this->editableSteps = $socialadshelper->adStateForAddMoreCredit($ad_id,$user->id);

			}
			else
			{
				$this->allowWholeAdEdit = $builadModel->allowWholeAdEdit($ad_id);
			}


		//	print_r($this->editableSteps); die;

		}
		else
		{
			$ad_data = $buildadsession->get('ad_data');
			$this->ad_data = $ad_data;
			//added for geo targeting
			//session variable for geo_fields
			$this->geo_target = $buildadsession->get('geo_target');
			$this->geo_type = $buildadsession->get('geo_type' );
			$this->geo_fields = $buildadsession->get('geo_fields');
			$this->context_target_data_keywordtargeting = $buildadsession->get('context_target_data_keywordtargeting');
			$this->social_target = $buildadsession->get('social_target');
		}


		//for camp edit

		//$this->assignRef( 'bid_value',$buildadsession->get('bid_value' ));
		//print_r($this->bid_value); die("im view buildad");
		//$this->assignRef( 'ad_data',$ad_data );

		//Extra code for zone
		$defaultzone_show =1;

		$Check_default_zone 		=  $this->get('defaultzone');
		if(count($Check_default_zone)==1)
		$defaultzone_show = $model->checkdefaultzone($Check_default_zone);


		$this->assignRef('Check_default_zone',$Check_default_zone);
		$this->assignRef('defaultzone_show',$defaultzone_show);
		//Extra code for zone


        $camp_dd = $this->get('campaign');

        $this->assignRef('camp_dd',$camp_dd);

        if($campid = $input->get('campid',0,'INT'))
		{

				$model	= $this->getModel( 'buildad');
				$cname = $model->getcampname($campid);
				$this->assignRef('cname',$cname);
		}

		$this->setLayout($layout);
		//die('asdasdasdasdasdasd');

		// vm: started --------------------------------------
		$this->country=$this->get("Country");

		// load social ads config params
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$selected_gateways = $socialads_config['gateways'];

		//getting GETWAYS
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment');
		//$params->get( 'gateways' ) = array('0' => 'paypal','1'=>'Payu');

		if(  !is_array($selected_gateways) )
		{
			$gateway_param[] = $selected_gateways;
		}
		else
		{
			$gateway_param = $selected_gateways;
		}

		if(!empty($gateway_param))
		{
			$gateways = $dispatcher->trigger('onTP_GetInfo',array($gateway_param));
		}
		$this->ad_gateways = $gateways;
		$this->userbill = array();

		if(!empty($user->id))
		{
			$this->userbill = $model->getbillDetails($user->id);
		}

		//Amol:
		$socialadshelper = new socialadshelper();
		$this->adfieldsTableColumn = $socialadshelper->getTableColumns('ad_fields');
		// vm:  code end
		parent::display($tpl);
	}

}// class

