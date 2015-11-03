<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.file');
require(JPATH_ADMINISTRATOR .DS.'includes'.DS.'toolbar.php');

/**
 * HTML View class for the socialads Component
 *
 * @package    socialads
 * @subpackage Views
 */
class socialadsViewManageAd extends JViewLegacy
{

    /**
     * ManageAd view ManageAddisplay method
     * @return void
     **/
	function display($tpl = null)
	{
		require_once(JPATH_COMPONENT . DS . 'helper.php');


		$user = JFactory::getUser();

		if($user->id)
		{
			//User authorized to view chat history
			if(! JFactory::getUser($user->id)->authorise('core.manage_ad', 'com_socialads'))
			{
				$app=JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_SOCIALADS_AUTH_ERROR'),'warning');
				return false ;
			}
		}

		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;

		// parameter is set than show default msg
		$saDefMsg = $input->get('saDefMsg',0,'INT');
		if($saDefMsg)
		{
			JFactory::getApplication()->enqueueMessage(JText::_("COM_SA_CREATE_AD"));
		}
		//Clear session
		$session = JFactory::getSession();
		$session->clear('ad_id');

		//$post=$input->post;
		//$input->get
		$option = $input->get('option','','STRING');

		//get fields from model query
		$model = $this->getModel('managead');

		//$itemid = $this->get( 'SocialadsItemid' );
		$itemid = socialadshelper::getSocialadsItemid('managead');
		//$itemid = socialadshelper::getSocialadsItemid('buildad');
		$this->assignRef( 'itemid',	$itemid );

		//check guest-ad
		$guestad = $this->get( 'CheckGuestad' );
		$this->assignRef( 'guestad',	$guestad );

		if($input->get('adid',0,'INT')!=null)
		{
			/*check valid ad*/
			$adcheck = $this->get( 'adcheck' );
			$this->assignRef( 'adcheck',	$adcheck );
			//get fields from model query
			$fields = $this->get( 'Fields' );
			$this->assignRef( 'fields',	$fields );
			// url select list
			$url1 = array();
			$url1[] = JHtml::_('select.option','http', 'http');
			$url1[] = JHtml::_('select.option','https', 'https');
			$this->assignRef('url1', $url1);

				//get plugin fields from model query
			$plgfields = $this->get( 'Plgfields' );
			$this->assignRef( 'plgfields',	$plgfields );

			//fetching all inserted details from DB
			$addata = $this->get( 'Data' );
			$this->assignRef( 'ad_socialtarget',	$addata[0] );
			$this->assignRef( 'addata',	$addata[1] );
			$this->assignRef( 'geo_target',	$this->get( 'Data_geo' ) );
			$Data_context_target=$this->get( 'Data_context_target' );

			$this->assignRef( 'context_target',	$Data_context_target['keywords']);
			$this->assignRef( 'context_target_data_keywordtargeting',$Data_context_target['keywords']);
			$zone = $this->get( 'zone' );
			$this->assignRef( 'zone',	$zone[0] );
			$this->setLayout('default');
		}
		else
		{

			//-----------For Zone Filter---------//
			$Itemid = $input->get('ad_camp_id',0,'INT');
			if($Itemid)
				{
				$model = $this->getModel('managead');
				$statsforbar =$model->statsforbar($Itemid);

				}
				else
				{
			$statsforbar =$model->statsforbar();
				}
			$this->assignRef('statsforbar', $statsforbar);
			/*if($statsforbar[2]) //commented in 2.7.5 beta3
			{
				if($statsforbar[2][0]->fromdate);
				$fromdate=$statsforbar[2][0]->fromdate;
			}*/
			$this->assignRef('fromdate', $fromdate);

			$search_zone = $mainframe->getUserStateFromRequest( $option.'search_zone', 'search_zone','', 'string' );
			$search_zone = JString::strtolower( $search_zone );

			$status_zone = array();
			$zonelist=$model->adzonelist();
			$status_zone[] = JHtml::_('select.option','0', JText::_('SA_SELONE_ZONE'));


			foreach($zonelist as $key=>$zone)
			{
				$zone_id=$zone->id;
				$zone_nm=$zone->zone_name;
				$status_zone[] = JHtml::_('select.option',$zone_id, $zone_nm);
			}

			$this->assignRef('status_zone', $status_zone);
		//-----------End For Zone Filter---------//
		//---------CAMPAIGN FILTER-----------//

			$search_camp = $mainframe->getUserStateFromRequest( $option.'search_camp', 'search_camp','', 'string' );
			$search_camp = JString::strtolower( $search_camp );
			$campselect = array();

			$camp_dd = $this->get('campaign_dd');

			$campselect[] = JHtml::_('select.option','0', JText::_('SA_CAMP_SELECT'));

			foreach($camp_dd as $key=>$camp)
			{

				$camp_id = $camp->camp_id;
				$campname = $camp->campaign;
				$campselect[] = JHtml::_('select.option',$camp_id, $campname);
			}
			$this->assignRef('camp_dd',$campselect);



			/* Call the state object */
			$state = $this->get( 'state' );

			/* Get the values from the state object that were inserted in the model's construct function */
			$lists['order_Dir'] = $state->get( 'filter_order_Dir' );
			$lists['order']     = $state->get( 'filter_order' );
			$lists['search_zone']     = $search_zone;
			$lists['search_camp']     = $search_camp;

			$this->assignRef( 'lists', $lists );

			//show myads list

			if($Itemid)
				{
				$model = $this->getModel('managead');
				$myads = $model->getAds($Itemid);

				}
			else
			{
			$myads = $this->get( 'Ads' );
			}

			$this->assignRef( 'myads',	$myads );
			$this->setLayout('list');


		}

		//pagination starts here
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$option = $input->get('option','','STRING');
		//Put Your JToolBarHelpers here

		$filter_state = $mainframe->getUserStateFromRequest( $option.'filter_state', 'filter_state', '', 'string' );



		// search filter
		//$lists['search']= $search;

		// Get data from the model
		$items =  $this->get( 'Managead');
		$this->assignRef('items', $items);

		$total =  $this->get( 'Total');
		$this->assignRef('total', $total);

		$pagination =  $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);

    //check for paypal log
    if(isset($_POST['payment_status']) == 'C')
    {
				//get session for adid
				$paypalsession = JFactory::getSession();
				$adid = $paypalsession->get('adid');

				$file = JPATH_COMPONENT.DS."log".DS.'paypal.txt';

		    $log['Adid'] = $adid;
				$log['IPN'] = $_POST['test_ipn'];
				$log['Status'] = $_POST['payment_status'];
				$log['Transaction Id'] = $_POST['txn_id'];
				$log['newln'] = "----------------------------------- \n";

				foreach($log as $k=>$v)
				{
					$opt[] = "{$k} - " . addslashes($v) . "";
				}
		    $text = implode("\n", $opt);

				$fh = fopen($file, 'a');
				fwrite($fh, $text);
				fclose($fh);


				//clear session adid
		    $paypalsession->clear('adid');
    }



		parent::display($tpl);

	}

}// class

