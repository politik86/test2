<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Aniket Component
 */


class socialadsViewbilling extends JViewLegacy
{
        // Overwriting JView display method
        function display($cachable = false, $urlparams = false,$tpl = null)
        {

			$user = JFactory::getUser();

			if($user->id)
			{
				//User authorized to view chat history
				if(! JFactory::getUser($user->id)->authorise('core.make_payment', 'com_socialads'))
				{
					$app=JFactory::getApplication();
					$app->enqueueMessage(JText::_('COM_SOCIALADS_AUTH_ERROR'),'warning');
					return false ;
				}
			}

			$mainframe= JFactory::getApplication();
			$input=JFactory::getApplication()->input;
          //$post=$input->post;
			$option = $input->get('option','','STRING');
			//$filter_order=$mainframe->getUserStateFromRequest('com_socialads.filter_order','filter_order','','int');
			$month = $mainframe->getUserStateFromRequest( $option.'month', 'month','', 'int' );
			$year = $mainframe->getUserStateFromRequest( $option.'year', 'year','', 'int' );


			$billing = $this->get('billing');
			$this->assignRef( 'billing', $billing);

			$lists['month']=$month;
			$lists['year']=$year;
			$this->lists=$lists;

			parent::display($tpl);


        }
}

