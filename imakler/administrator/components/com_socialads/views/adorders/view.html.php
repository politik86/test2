<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');


class socialadsViewAdorders extends JViewLegacy
{

	function display($tpl = null)
	{
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
        //$post=$input->post;
		$option = $input->get('option','','STRING');

		$model = $this->getModel('Adorders');
		$pstatus=array();
		$pstatus[]=JHtml::_('select.option','P', JText::_('SA_PENDIN'));
		$pstatus[]=JHtml::_('select.option','C', JText::_('SA_CONFR'));
		$pstatus[]=JHtml::_('select.option','RF', JText::_('SA_REFUN'));
		$pstatus[]=JHtml::_('select.option','E', JText::_('SA_ERR'));
		/*$pstatus[]=JHtml::_('select.option','3','Cancelled');*/
		$this->assignRef('pstatus',$pstatus);

		$sstatus = array();
		//$sstatus[] = JHtml::_('select.option','-1',  JText::_('SA_SELONE'));
		$sstatus[] = JHtml::_('select.option','P',  JText::_('SA_PENDIN'));
		$sstatus[] = JHtml::_('select.option','C',  JText::_('SA_CONFR'));
		$sstatus[] = JHtml::_('select.option','RF',  JText::_('SA_REFUN'));
		$sstatus[]=JHtml::_('select.option','E', JText::_('SA_ERR'));

		//filter for ad orders and normal payment
		$pay=array();

		//$pay[]=JHtml::_('select.option','1', JText::_('NORMAL_PAY'));
		$pay[]=JHtml::_('select.option','2', JText::_('AD_ORDERS'));

		/*$pstatus[]=JHtml::_('select.option','3','Cancelled');*/





		$filter_state = $mainframe->getUserStateFromRequest( $option.'search_list', 'search_list', '', 'string' );
		$search_pay = $mainframe->getUserStateFromRequest( $option.'search_pay', 'search_pay','', 'string' );
		//Added by Sagar For Gateway Filter
			$search_gateway = $mainframe->getUserStateFromRequest( $option.'search_gateway', 'search_gateway','', 'string' );
			$search_gateway = JString::strtolower( $search_gateway );
			$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'desc',			'word' );
			$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order', 		'ad_id',			'string' );

			$sstatus_gateway = array();
			//$sstatus_gateway[] = JHtml::_('select.option','0', JText::_('FILTER_GATEWAY'));
			$gatewaylist=$model->gatewaylist();
			if($gatewaylist)
			{
				foreach($gatewaylist as $key=>$gateway)
				{
					$gateway_nm=$gateway->processor;
					$sstatus_gateway[] = JHtml::_('select.option',$gateway_nm, $gateway_nm);
				}
			}

		//End Added by Sagar For Gateway Filter


		//filter for 2.5
		if(JVERSION < 3.0)
		{
			$sstatus[] = JHtml::_('select.option','-1',  JText::_('SA_SELONE'));
			$pay[]=JHtml::_('select.option','1', JText::_('NORMAL_PAY'));
			$sstatus_gateway[] = JHtml::_('select.option','0', JText::_('FILTER_GATEWAY'));
		}

		$this->assignRef('sstatus', $sstatus);
		$this->assignRef('sstatus_gateway', $sstatus_gateway);
		$this->assignRef('pay',$pay);

		//----

		$filter_state = $mainframe->getUserStateFromRequest( $option.'search_list', 'search_list', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $option.'search_select', 'search_select','', 'string' );

		$search = JString::strtolower( $search );
		if($search==null)
		$search='-1';

		// Get data from the model
		$total =  $this->get( 'Total');
		$pagination =  $this->get( 'Pagination' );
		$adorders = $this->get('AdOrders');
		$adfileds = $this->get('Adfileds');
		// search filter
		$lists['search_select']= $search;
		$lists['search_pay']= $search_pay;
		$lists['search_list']= $filter_state;
		$lists['search_gateway']		= $search_gateway;
		$lists['order_Dir'] 				= $filter_order_Dir;
		$lists['order']     				= $filter_type;
		// Get data from the model
		$this->assignRef('lists', $lists);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('adorders',$adorders);
		$this->_setToolBar();
		if(JVERSION>=3.0)
			$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);

	}//function display ends here

	function _setToolBar()
	{	// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
		JToolBarHelper::title( JText::_( 'AD_ORDERS' ), 'icon-48-social.png' );
		//JToolBarHelper::save('payment_csvexport',JText::_('CSV_EXPORT'));
		$button = "<a class='toolbar' class='button' type='submit' onclick=\"javascript:document.getElementById('task').value = 'payment_csvexport';document.adminForm.submit();document.getElementById('task').value = '';\" href='#'><span title='Export' class='icon-32-save'></span>".JText::_('CSV_EXPORT')."</a>";
        $bar =  JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Custom', $button);
		//JToolBarHelper::cancel( 'cancel', 'Close' );

		if(JVERSION>=3.0)
		{
			JHtmlSidebar::setAction('index.php?option=com_socialads');
			//@Type filter
			JHtmlSidebar::addFilter(
				 JText::_('SA_SELONE'),
				'search_select',
				//JHtml::_('select.options',$this->campaign_type_filter_options, 'value', 'text', $this->lists['filter_campaign_type'], true)
				JHtml::_('select.options', $this->sstatus, "value", "text",JString::strtoupper($this->lists['search_select']),true)
			);
			JHtmlSidebar::addFilter(
				JText::_('NORMAL_PAY'),
				'search_pay',
				//JHtml::_('select.options',$this->campaign_type_filter_options, 'value', 'text', $this->lists['filter_campaign_type'], true)

				JHtml::_('select.options', $this->pay, "value", "text", $this->lists['search_pay'],true)
			);
			JHtmlSidebar::addFilter(
				JText::_('FILTER_GATEWAY'),
				'search_gateway',
				//JHtml::_('select.options',$this->campaign_type_filter_options, 'value', 'text', $this->lists['filter_campaign_type'], true)
				JHtml::_('select.options', $this->sstatus_gateway, "value", "text", $this->lists['search_gateway'],true)
			);
		}


	}

}// class
