<?php			//CONTROLLER FILE
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT . DS . 'helper.php');
include_once(JPATH_COMPONENT.DS.'controller.php');

class socialadsControllercampaign extends JControllerLegacy
{	
		function add_camp()
		{
			
				$model = $this->getModel('campaign');
				$camp = $model->save();
				
				$redirect=JUri::base().'index.php?option=com_socialads&view=campaign&list=list';
				$msg= JText::_('SAVE');
		
		$this->setRedirect( $redirect, $msg ); 
		}

		function change_status()
		{
			$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
				
				$id=$input->get('campid',0,'INT');
				//print_r($id); die('asdasdasdasd');
				$model = $this->getModel('campaign');
				echo $camp = $model->status($id);
				//print_r($camp);die();
				
				jexit();
		}


}

?>
