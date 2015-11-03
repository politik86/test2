<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');


class socialadsViewManagezone extends JViewLegacy
{


	function display($tpl = null)
	{
		$this->_setToolBar();
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$option = $input->get('option','','STRING');
		$view = $input->get('view','','STRING');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir',	'desc','word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_type",		'filter_type', 		0,			'string' );
		$filter_state = $mainframe->getUserStateFromRequest( $option.'search_list', 'search_list', '', 'string' );
		$search = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		$search = JString::strtolower($search);
		$limit = '';
		$limitstart = '';
			
		$cid=0;
		if($search==null)
		$search='';		
		$edit		= $input->get( 'edit','' );
		$layout		= $input->get( 'layout','' );		
		$cid		= $input->get( 'cid','','ARRAY' );
		$model		=  $this->getModel( 'managezone' );
		
		 if($cid) 
		 { 
		 	$total 		=  $this->get( 'Total');
			$pagination =  $this->get( 'Pagination' );
			$zones = $model->Editlist($cid[0]);
			JLoader::import('buildad', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
			$Importfields=new socialadsModelBuildad();
			$fields=$Importfields->getFields();
			$this->fields = $fields;
			$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', 'limit', 'int' );
			$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
			$model->setState('limit', $limit); // Set the limit variable for query later on
		    $model->setState('limitstart', $limitstart);
		 }
		 else
		 { 
		 	$total 		=  $this->get( 'Total');
			$pagination =  $this->get( 'Pagination' );
			$zones 		=  $this->get( 'Managezone' );				
			$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', 'limit', 'int' );
			$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
			$model->setState('limit', $limit); // Set the limit variable for query later on
		    $model->setState('limitstart', $limitstart);
		 }
		
		// search filter
		$lists['search_select']	= $search;
		$lists['search']		= $search;
		$lists['search_list']	= $filter_state;
		$lists['order']			= $filter_type;
		$lists['order_Dir']		= $filter_order_Dir;
		$lists['limit']			= $limit;
		$lists['limitstart']	= $limitstart;

		$modules 	=  $this->get( 'Zoneamodule' );
		$this->assignRef('modules',$modules);
	
		// Get data from the model
		$this->assignRef('lists', $lists);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('zones',$zones);
		if(JVERSION>=3.0)
			$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		
	}//function display ends here
	
	function _setToolBar()
	{	// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'AD_ZONE_TITLE' ), 'icon-48-social.png' );
		
		//JToolBarHelper::cancel( 'cancel', 'Close' );
	}
	
}// class
