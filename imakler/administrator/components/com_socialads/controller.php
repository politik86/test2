<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');

/**
 * socialads default Controller
 *
 * @package    socialads
 * @subpackage Controllers
 */
class socialadsController extends JControllerLegacy
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'display'  );
		$this->registerTask( 'edit'  , 	'display'  );

		$this->registerTask( 'apply', 	'save'  );
		$this->registerTask( 'flogout', 'logout');
		$this->registerTask( 'unblock', 'block' );


	}
	function cancel()
	{
		$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=managezone&layout=default");
	}

	function cancelcoupon()
	{
		$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=managecoupon&layout=default");
	}

	function cancel1()
	{
		$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=dashboard");
	}
	public function display($cachable = false, $urlparams = false)
	{
		//parent::display();
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$vName = $input->get('view', 'dashboard','STRING');
		$controllerName = $input->get( 'controller', 'dashboard' ,'STRING');
		$settings		=	'';
		$importfields	=	'';
		$approveads	=	'';
		$adorders	=	'';

		$managezone ='';
		$managecoupon = '';
		$dashboard = '';
		switch($vName)
		{

			case 'importfields':
				$importfields	=	true;
			break;
			case 'settings':
				$settings	=	true;
			break;
			case 'approveads':
				$approveads	=	true;
			break;
			case 'adorders':
				$adorders	=	true;
			break;
			case 'ignoreads':
				$ignoreads = true;
			break;
			case 'dashboard':
				$dashboard = true;
			break;
			///////Extra code For Zone Management//
			case 'managezone':
				$managezone = true;
			break;
			///////Extra code For Zone Management//
			case 'managecoupon':
				$managecoupon = true;
			break;

			case 'buildad':
				$buildad = true;
			break;
		}
			if(JVERSION>=3.0)
               {
					//JHtmlSidebar::addEntry(JText::_('SA_CP'), 'index.php?option=com_socialads&view=cp',$cp);
					JHtmlSidebar::addEntry(JText::_('DASHBOARD'), 'index.php?option=com_socialads&view=dashboard',$dashboard);
					JHtmlSidebar::addEntry(JText::_('SETTINGS'), 'index.php?option=com_socialads&view=settings',$settings);
					JHtmlSidebar::addEntry(JText::_('IMPORT_FIELDS'), 'index.php?option=com_socialads&view=importfields',$importfields);
					JHtmlSidebar::addEntry(JText::_('APPROVE_ADS'), 'index.php?option=com_socialads&view=approveads',$approveads);
					JHtmlSidebar::addEntry(JText::_('AD_ORDERS'), 'index.php?option=com_socialads&view=adorders',$adorders);

					JHtmlSidebar::addEntry(JText::_('MANAGE_ZONE'), 'index.php?option=com_socialads&view=managezone',$managezone);
					JHtmlSidebar::addEntry(JText::_('MANAGE_COUPAN'), 'index.php?option=com_socialads&view=managecoupon',$managecoupon);

			   }
		else
			   {
			//JSubMenuHelper::addEntry(JText::_('SA_CP'), 'index.php?option=com_socialads&view=cp',$cp);
			JSubMenuHelper::addEntry(JText::_('DASHBOARD'), 'index.php?option=com_socialads&view=dashboard',$dashboard);
			JSubMenuHelper::addEntry(JText::_('SETTINGS'), 'index.php?option=com_socialads&view=settings',$settings);
			JSubMenuHelper::addEntry(JText::_('IMPORT_FIELDS'), 'index.php?option=com_socialads&view=importfields',$importfields);
			JSubMenuHelper::addEntry(JText::_('APPROVE_ADS'), 'index.php?option=com_socialads&view=approveads',$approveads);
			JSubMenuHelper::addEntry(JText::_('AD_ORDERS'), 'index.php?option=com_socialads&view=adorders',$adorders);

			JSubMenuHelper::addEntry(JText::_('MANAGE_ZONE'), 'index.php?option=com_socialads&view=managezone',$managezone);
			JSubMenuHelper::addEntry(JText::_('MANAGE_COUPAN'), 'index.php?option=com_socialads&view=managecoupon',$managecoupon);
		}
		$mName ='';
		switch ($vName)
		{


			case 'importfields':
				$mName = 'importfields';
				$vLayout = $input->get( 'layout', 'Importfields','STRING');

			break;

			case 'settings':
			default:
				$vName = 'settings';
				$vLayout = $input->get( 'layout', 'default','STRING');
				$mName = 'settings';
			break;

			case 'approveads':
			default:
				$mName = 'Approveads';
				$vLayout = $input->get( 'layout', 'Approveads','STRING');
				break;

			case 'ignoreads':
			default:
				$mName = 'ignoreads';
				$vLayout = $input->get( 'layout', 'default','STRING');
			break;

			case 'adorders':
			default:
				$mName = 'Adorders';
				$vLayout = $input->get( 'layout', 'Adorders','STRING');
			break;
			case 'dashboard':
			default:
			$mName = 'Dashboard';
			$vLayout = $input->get( 'layout', 'Dashboard','STRING');
			break;
			///////Extra code For Zone Management//
			case 'managezone':
			default:
				$mName = 'Managezone';
				$vLayout = $input->get( 'layout', 'default','STRING');
			break;
			///////Extra code For Zone Management//
			case 'managecoupon':
			default:
				$mName = 'Managecoupon';
				$vLayout = $input->get( 'layout', 'default','STRING');
			break;

			case 'buildad':
				$mName = 'buildad';
				$vLayout = $input->get( 'layout', 'default','STRING');

			break;

			///////Extra code For Zone Management//

		}

		$document = JFactory::getDocument();
		$vType	  = $document->getType();

		// Get/Create the view
		$view = $this->getView( $vName, $vType);

		// Get/Create the model
		if ($model = $this->getModel($mName)) {
			// Push the model into the view (as default)
			/*if($vName=='cp')
			$view->setModel( $this->getModel( 'dashboard' ) );*/
			$view->setModel($model, true);
		}

		if($mName=="Managezone")
		{

		JRequest::setVar( 'edit', '' );

			switch($this->getTask())
			{

				case 'add'     :
				{

					JRequest::setVar( 'hidemainmenu', 1 );
					JRequest::setVar( 'layout', 'form'  );
					JRequest::setVar( 'view', 'managezone' );
					$vLayout="form";
					JRequest::setVar( 'edit', '' );
				} break;
				case 'edit'    :
				{

					JRequest::setVar( 'hidemainmenu', 1 );
					JRequest::setVar( 'layout', 'form'  );
					JRequest::setVar( 'view', 'managezone' );
					$vLayout="form";
					JRequest::setVar( 'edit', '1' );

				} break;
			}
		}


		if($mName=="Managecoupon")
		{

		JRequest::setVar( 'edit', '' );

			switch($this->getTask())
			{

				case 'add'     :
				{

					JRequest::setVar( 'hidemainmenu', 1 );
					JRequest::setVar( 'layout', 'form'  );
					JRequest::setVar( 'view', 'managecoupon' );
					$vLayout="form";
					JRequest::setVar( 'edit', '' );
				} break;
				case 'edit'    :
				{

					JRequest::setVar( 'hidemainmenu', 1 );
					JRequest::setVar( 'layout', 'form'  );
					JRequest::setVar( 'view', 'managecoupon' );
					$vLayout="form";
					JRequest::setVar( 'edit', '1' );

				} break;
			}
		}
		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();
	}// function



	function deletezone()
	{
		$model	= $this->getModel( 'managezone' );
		$post	= JRequest::get('post');
		$zoneid=$post['cid'];

		if ($model->deletezone($zoneid))
		{
			$msg = JText::_( 'C_ZONE_DEL_SAVE_M_S' );
		}
		else
		{
			$msg = JText::_( 'C_ZONE_DEL_SAVE_M_NS' );
		}
	if(JVERSION >= '1.6.0')
		$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=managezone&layout=default",$msg);
	else
		$this->setRedirect( JUri::base()."index2.php?option=com_socialads&view=managezone&layout=default",$msg);

	}


		function deletecoupon()
	{

		$model	= $this->getModel( 'managecoupon' );
		$post	= JRequest::get('post');
		$zoneid=$post['cid'];

		if ($model->deletecoupon($zoneid))
		{
			$msg = JText::_( 'COM_SA_DELETE_COPON' );
		}
		else
		{
			$msg = JText::_( 'COM_SA_DELETE_COPON_ERROR' );
		}
		if(JVERSION >= '1.6.0')
			$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=managecoupon&layout=default",$msg);
		else
		$this->setRedirect( JUri::base()."index2.php?option=com_socialads&view=managecoupon&layout=default",$msg);

	}
		function deleteads()
	{

		$model	= $this->getModel( 'Approveads' );
		$post	= JRequest::get('post');
		$adid=$post['cid'];
		if ($model->deleteads($adid))
		{
			$msg = JText::_( 'C_ADD_DELETED' );
		}
		else
		{
			$msg = JText::_( 'C_SAVE_M_NS' );
		}
		if(JVERSION >= '1.6.0')
			$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=approveads",$msg);
		else
		$this->setRedirect( JUri::base()."index2.php?option=com_socialads&view=approveads",$msg);

	}
	function getVersion()
	{
		echo $recdata = file_get_contents('http://techjoomla.com/vc/index.php?key=abcd1234&product=socialads');
		jexit();
	}

	function publish()
	{

		//if()
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );
		$input=JFactory::getApplication()->input;
		$view = $input->get( 'view', 'default','STRING');

		// Get some variables from the request

		$cid	= $input->get( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if($view=="managecoupon")
		{
			$model = $this->getModel( 'managecoupon' );
			if ($model->setItemState($cid, 1)) {
				$msg = JText::sprintf( 'Coupan  Enabled', count( $cid ) );
			} else {
				$msg = $model->getError();



			}
			$this->setRedirect('index.php?option=com_socialads&view=managecoupon&layout=default', $msg);
		}
		else
		{
		$model = $this->getModel( 'managezone' );

			if ($model->setItemState($cid, 1)) {
				$msg = JText::sprintf( 'Zone Items Published', count( $cid ) );
			} else {
				$msg = $model->getError();


			}
			$this->setRedirect('index.php?option=com_socialads&view=managezone&layout=default', $msg);

		}

	}

	/**
	* Save the item(s) to the menu selected
	*/
	function unpublish()
	{
		//if()
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );
		$input=JFactory::getApplication()->input;
		$view = $input->get( 'view', 'default','STRING');

		// Get some variables from the request

		$cid	= $input->get( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if($view=="managecoupon")
		{
			$model =& $this->getModel( 'managecoupon' );
			if ($model->setItemState($cid, 0)) {
				$msg = JText::sprintf( 'Coupan  Enabled', count( $cid ) );
			} else
			{
				$msg = $model->getError();


			}
				$this->setRedirect('index.php?option=com_socialads&view=managecoupon&layout=default', $msg);
		}
		else
		{
		$model = $this->getModel( 'managezone' );

			if ($model->setItemState($cid, 0)) {
				$msg = JText::sprintf( 'Zone Items Published', count( $cid ) );
			} else
			{
				$msg = $model->getError();

			}
			$this->setRedirect('index.php?option=com_socialads&view=managezone&layout=default', $msg);
		}

	}

	//added for 2.7
	function populateGeolocation()
	{
		$document =JFactory::getDocument();
		$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css');
		$model = $this->getModel( 'settings' );
		//$resultarr1=$model->populategemaxmindDB();
		$resultarr2=$model->populategeoDB();
		$resultstr1='';//implode('<br/><br/>',$resultarr1['displaymsg']);
		$resultstr2=implode('',$resultarr2['displaymsg']);
		echo $resultstr1.' '.$resultstr2;
		jexit();

	}

	function populategeoDB()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css');
		$model = $this->getModel( 'settings' );
		$result=$model->populategeoDB();
		echo json_encode($result);
		jexit();

	}

	function populategemaxmindDB()
	{
		$document =JFactory::getDocument();
		$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css');
		$model = $this->getModel( 'settings' );

		echo $result=$model->populategemaxmindDB();
		echo json_encode($result);
		jexit();
	}

		// migration function
	function getmigration()
	{

		$input=JFactory::getApplication()->input;
		$document =JFactory::getDocument();
		//$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css');
		$model = $this->getModel( 'settings' );
		$migrate_status = $input->get('call','','STRING');
		if($input->get('camp_or_old',0,'INT')=='1')   	// for migrating from old to camp_budget
		{

				$json=$model->migrateads_camp($migrate_status);
		}
		else
		{
				$json=$model->migrateads_old($migrate_status);				//for migrating camp_budget to old
		}

		$content = json_encode($json);
			echo $content;
			jexit();



	}
	//create New ad
	function addNew(){

		$session=JFactory::getSession();
		$session->clear('ad_id');
		$redirect=JRoute::_('index.php?option=com_socialads&view=buildad&layout=default',false);
		$this->setRedirect($redirect,$msg);
	}

}// class
