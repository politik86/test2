<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class socialadsControllerSettings extends socialadsController
{
	/**
	 * save a ad fields 
	 */
	function save()
	{
	  // Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );
		$input=JFactory::getApplication()->input;
		$model	= $this->getModel( 'settings' );
		$post	= JRequest::get('post');
		// allow name only to contain html
	
		$model->setState( 'request', $post );	

		if ($model->store()) 
		{
			$msg = JText::_( 'C_SAVE_M_S' );
		}
		else 
		{
			$msg = JText::_( 'C_SAVE_M_NS' );
		}
		$task=$input->get('task','','STRING');
		switch ($task) 
		{
			case 'cancel':
			$cancelmsg = JText::_( 'FIELD_CANCEL_MSG' );
			$this->setRedirect( 'index.php?option=com_socialads', $msg );
			break;
			case 'save':

			$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=settings", $msg );
			break;
		}
			
		
	}//function save ends
	
	
	//function for maxmind tables
	function getgeodb(){
		$model = $this->getModel( 'settings' );

		//create maxmind tables
		$result = $model->getgeodb();	
		echo $result;
    jexit();
	}
	
	//function for geo tables
	function populategeoDB(){
		$model = $this->getModel( 'settings' );

		//create maxmind tables
		$result = $model->populategeoDB();
		$finalresult['geoDBinstall']=$result['geoDBinstall'];
				$finalresult['displaymsg']=$result['displaymsg']['0'];
		$json= json_encode($finalresult);
		
		echo $json;
    jexit();
	}
	
}
?>

