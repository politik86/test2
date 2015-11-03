<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Aniket Component
 */
class socialadsViewpayment extends JViewLegacy
{
        // Overwriting JView display method
        function display($tpl = null)
        {
			$user = JFactory::getUser();

			if($user->id)
			{
				//User authorized to view chat history
				if(! JFactory::getUser($user->id)->authorise('core.transaction', 'com_socialads'))
				{
					$app=JFactory::getApplication();
					$app->enqueueMessage(JText::_('COM_SOCIALADS_AUTH_ERROR'),'warning');
					return false ;
				}
			}
				$gatewayplugin = $this->get('APIpluginData');
				$this->gatewayplugin=$gatewayplugin;

			parent::display($tpl);
        }
}

