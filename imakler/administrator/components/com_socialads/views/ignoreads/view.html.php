<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
jimport( 'joomla.application.component.view');

class socialadsViewIgnoreads extends JViewLegacy
{
    
	function display($tpl = null)
	{

      $ignoredata = $this->get('Data');  
      $this->assignRef('ignoredata', $ignoredata);  
        
      $pagination = $this->get('Pagination');    
      $this->assignRef('pagination', $pagination);	
		
			parent::display($tpl);
		
	}//function display ends here
	
	
	function _setToolBar()
	{	
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
		JToolBarHelper::title( JText::_( 'AP_TITLE' ), 'icon-48-social.png' );
		JToolBarHelper::save();
		//JToolBarHelper::cancel( 'cancel', 'Close' );
	}
	
}// class
