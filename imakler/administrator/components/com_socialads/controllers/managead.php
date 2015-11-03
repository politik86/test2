<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT . DS . 'helper.php');
include_once(JPATH_COMPONENT.DS.'controller.php');

class socialadsControllerManagead extends JControllerLegacy
{
	
		/* update reguler ad*/
		function update()
		{
			$input=JFactory::getApplication()->input;
			$socialadshelper= new socialadshelper();
			$itemid = $socialadshelper->getSocialadsItemid('managead');
			$model = $this->getModel('managead');
			$ad_id = $input->get('adid',0,'INT');		
//			print_r($_REQUEST['filename']);die;
			
			if($_REQUEST['filename']!=null)
			{
			
				$model->imageupload();	
					
			}
			else
			{
				if ($model->store()){
					$sacontroller = new socialadsController();
					$sacontroller->execute('delimages');
					$msg = JText::_('UPDATE');
				}
				else
					$msg = JText::_( 'ERROR' );
			}

			$link = JRoute::_("index.php?option=com_socialads&view=managead&Itemid=$itemid",false);
			$this->setRedirect($link, $msg);					
		}
		
		/*update alternate ad */
		function altadupdate()
		{
			$input=JFactory::getApplication()->input;
			$model = $this->getModel('managead');
			$ad_id = $input->get('adid',0,'INT');	

			if ($model->altUpdate()) 
			{
				$sacontroller = new socialadsController();
				$sacontroller->execute('delimages');
				$msg = JText::_('UPDATE');
			} 
			else
			{
				$msg = JText::_( 'ERROR' );
			}

			$itemid = socialadshelper::getSocialadsItemid('managead');
			$link = JRoute::_("index.php?option=com_socialads&view=managead&Itemid=$itemid",false);
			$this->setRedirect($link, $msg);					
		}
}//class end
