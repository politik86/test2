<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class socialadsControllerIgnoreads extends socialadsController
{
	function __construct() 	
	{
		
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	
	function cancel() 
	{
		
		$msg = JText::_( 'FIELD_CANCEL_MSG' );
		$this->setRedirect( 'index.php?option=com_socialads', $msg );
	}
	
		
}
?>
