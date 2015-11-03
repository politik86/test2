<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class socialadsControllerManagecoupon extends socialadsController
{
	/**
	 * save a ad fields 
	 */
	 
	function save()
	{
	  // Check for request forgeries
	  
		//JSession::checkToken() or jexit( 'Invalid Token' );
		$model	= $this->getModel( 'managecoupon' );
		$post	= JRequest::get('post');
		$input=JFactory::getApplication()->input;
			
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
		//echo $this->_task;die;
		switch ( $task ) 
		{
			case 'cancel':
			$cancelmsg = JText::_( 'FIELD_CANCEL_MSG' );
			$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=managecoupon&layout=default", $cancelmsg );
			break;
			case 'save':
			$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=managecoupon", $msg );
			break;
		}
			
		
	}
	//function save ends
	
	function getcode()
	{
		$input=JFactory::getApplication()->input;
		$selectedcode = $input->get('selectedcode','','STRING');
		$model	= $this->getModel( 'managecoupon' );
		
		$coupon_code=$model->getcode(trim($selectedcode));
		echo $coupon_code;
		exit();
	}
	function getselectcode()
	{
		$input=JFactory::getApplication()->input;
		$selectedcode	  = $input->get('selectedcode','','STRING');
		$couponid= $input->get('couponid',0,'INT');
		$model	= $this->getModel( 'managecoupon' );
		$coupon_code=$model->getselectcode(trim($selectedcode),$couponid);
		echo $coupon_code;
		exit();
	}
	
	
}
?>
