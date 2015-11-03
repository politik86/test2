<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');

class socialadsViewRedirector extends JViewLegacy
{
  /* Showad view display method */
	function display($tpl = null)
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$model = $this->getModel('redirector');
		$model->store();
	
		$result = $this->get('URL');
		$mainframe->redirect($result);		
	}
}// class
