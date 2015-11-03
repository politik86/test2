<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Aniket Component
 */
class socialadsViewcampaign extends JViewLegacy
{
        // Overwriting JView display method
        function display($tpl = null)
        {
			$mainframe=JFactory::getApplication();
			$input=JFactory::getApplication()->input;

			$user = JFactory::getUser();

			if($user->id)
			{
				//User authorized to view chat history
				if(! JFactory::getUser($user->id)->authorise('core.campaigns', 'com_socialads'))
				{
					$app=JFactory::getApplication();
					$app->enqueueMessage(JText::_('COM_SOCIALADS_AUTH_ERROR'),'warning');
					return false ;
				}
			}

			$dis_camp = $this->get('display_camp');
			$this->assignRef( 'display', $dis_camp);

			$list_camp = $this->get('list');
			$this->assignRef( 'list', $list_camp);

			//GET INFO OF CAMP TO EDIT
			if($input->get('edit','','STRING')=='edit')
			{
					$camp_id = $input->get('campid',0,'INT');
					$model	= $this->getModel( 'campaign' );
					$camp_info = $model->getcamp_info($camp_id);
					$this->assignRef( 'camp_info', $camp_info);
			}
			$filter_order_Dir=$mainframe->getUserStateFromRequest('com_socialads.filter_order_Dir','filter_order_Dir','desc','word');
			$filter_type=$mainframe->getUserStateFromRequest('com_socialads.filter_order','filter_order','goal_amount','string');

			$lists['order_Dir']=$filter_order_Dir;
			$lists['order']=$filter_type;
			$this->lists=$lists;
			parent::display($tpl);
        }
}

