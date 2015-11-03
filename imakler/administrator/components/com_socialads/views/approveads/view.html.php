<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the socialads Component
 *
 * @package    socialads
 * @subpackage Views
 */
class socialadsViewApproveads extends JViewLegacy
{
    /**
     * Importfields view display method
     * @return void
     **/
	function display($tpl = null)
	{
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$option = $input->get('option','','STRING');
		$pay_msg = $input->get('SA_PAYMSG','','RAW');
		$adminCall = $input->get('adminCall',0,'INT');
		if($pay_msg)
		{
			JFactory::getApplication()->enqueueMessage($pay_msg);
		}
		else if($adminCall==1)
		{
JFactory::getApplication()->enqueueMessage(JText::_("COM_SOCIALADS_AD_CREATED_MSG"));
		}

		$model = $this->getModel('approveads');

 		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'desc',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order', 'a.ad_id',			'string' );
	 	$filter_state = $mainframe->getUserStateFromRequest( $option.'filter_state', 'filter_state', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $option.'search', 'search','', 'string' );
		$search = JString::strtolower( $search );
		if($search==null)
			$search='-1';

		//status select box
		$status = array();
		$status[] = JHtml::_('select.option','0', JText::_('SA_PENDIN'));
		$status[] = JHtml::_('select.option','1',  JText::_('SA_APPROVE'));
		$status[] = JHtml::_('select.option','2', JText::_('STATUS_REJECTED_NEW'));
		$this->assignRef('status', $status);

		$sstatus = array();
		//$sstatus[] = JHtml::_('select.option','-1', JText::_('SA_SELONE'));
		$sstatus[] = JHtml::_('select.option','0', JText::_('SA_PENDIN'));
		$sstatus[] = JHtml::_('select.option','1',  JText::_('SA_APPROVE'));
		$sstatus[] = JHtml::_('select.option','2', JText::_('STATUS_REJECTED_NEW'));
		$sstatus[] = JHtml::_('select.option','3', JText::_('SA_ORPHANAD'));


	//-----------For Zone Filter---------//
		$search_zone = $mainframe->getUserStateFromRequest( $option.'search_zone', 'search_zone','', 'string' );
		$search_zone = JString::strtolower( $search_zone );

		$status_zone = array();
		$zonelist=$model->adzonelist();
		//$status_zone[] = JHtml::_('select.option','0', JText::_('SA_SELONE_ZONE'));
		foreach($zonelist as $key=>$zone)
		{
			$zone_id=$zone->id;
			$zone_nm=$zone->zone_name;

			$status_zone[] = JHtml::_('select.option',$zone_id, $zone_nm);
		}

	//-----------End For Zone Filter---------//



		$search_camp = $mainframe->getUserStateFromRequest( $option.'search_camp', 'search_camp','', 'string' );
		$search_camp = JString::strtolower( $search_camp );

			$camp_dd = $this->get('campaign_dd');
			$this->assignRef('camp_dd',$camp_dd);
//----------camp filter-------------------//

	$campselect = array();
			//$campselect[] = JHtml::_('select.option','0', JText::_('SA_CAMP_SELECT'));
			foreach($this->camp_dd as $camp)
			{
				$campname = ucfirst(str_replace('plugpayment', '',$camp->campaign));
				$campselect[] = JHtml::_('select.option',$camp->campaign, $campname);
			}

	//----------end camp filter--------------..//


		//filter for 2.5
		if(JVERSION < 3.0)
		{
			$campselect[] = JHtml::_('select.option','0', JText::_('SA_CAMP_SELECT'));
			$status_zone[] = JHtml::_('select.option','0', JText::_('SA_SELONE_ZONE'));
			$sstatus[] = JHtml::_('select.option','-1', JText::_('SA_SELONE'));
		}

		$this->assignRef('campselect', $campselect);
		$this->assignRef('status_zone', $status_zone);
		$this->assignRef('sstatus', $sstatus);
		//-----

		$total =  $this->get( 'Total');
		$this->assignRef('total', $total);
		$pagination =  $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);

		$approveads = $this->get('ApproveAds');
		$this->assignRef('approveads',$approveads);

				/* Call the state object */
		$state = $this->get( 'state' );
		/* Get the values from the state object that were inserted in the model's construct function */
		$lists['order_Dir'] 		= $filter_order_Dir;
		$lists['order']     		= $filter_type;
	 	// search filter
		$lists['search']				= $search;
		$lists['search_zone']		= $search_zone;
		$lists['filter_state']	= $filter_state;
		$lists['search_camp']     = $search_camp;
		// Get data from the model
		$this->assignRef('lists', $lists);
				$this->_setToolBar();
		if(JVERSION>=3.0)
			$this->sidebar = JHtmlSidebar::render();

	  parent::display($tpl);

	}//function display ends here

	function _setToolBar()
	{	// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::addNew($task = 'addNew', $alt = JText::_('New'));
		JToolBarHelper::deleteList('', 'deleteads');
		//JToolBarHelper::save('ad_csvexport',JText::_('CSV_EXPORT'));
		$button = "<a class='toolbar' class='button' type='submit' onclick=\"javascript:document.getElementById('task').value = 'ad_csvexport';document.adminForm.submit();document.getElementById('task').value = '';\" href='#'><span title='Export' class='icon-32-save'></span>".JText::_('ADSCSV_EXPORT')."</a>";
        $bar =  JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Custom', $button);
		JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
		JToolBarHelper::title( JText::_( 'AP_TITLE' ), 'icon-48-social.png' );

		if(JVERSION>=3.0)
		{
			JHtmlSidebar::setAction('index.php?option=com_socialads');
			//@Type filter
			JHtmlSidebar::addFilter(
				JText::_('SA_SELONE'),
				'search',
				//JHtml::_('select.options',$this->campaign_type_filter_options, 'value', 'text', $this->lists['filter_campaign_type'], true)
				JHtml::_('select.options', $this->sstatus, "value", "text", $this->lists['search'],true)
			);
			JHtmlSidebar::addFilter(
				JText::_('SA_CAMP_SELECT'),
				'search_camp',
				JHtml::_('select.options', $this->campselect, "value", "text",$this->lists['search_camp'],true)
			);
			JHtmlSidebar::addFilter(
				JText::_('SA_SELONE_ZONE'),
				'search_zone',
				JHtml::_('select.options', $this->status_zone,"value", "text", $this->lists['search_zone'],true)
			);
		}
		//JToolBarHelper::cancel( 'cancel', 'Close' );
	}

}// class
