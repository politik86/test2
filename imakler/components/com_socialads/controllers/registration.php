<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( JPATH_COMPONENT.DS.'controller.php' );

jimport('joomla.application.component.controller');

class socialadsControllerregistration extends JControllerLegacy
{

	function __construct()
	{
		parent::__construct();
		
	}
	
	

	function save()
	{
		$input=JFactory::getApplication()->input;
         
          //$input->get
		$id = $input->get('cid',0,'INT');
        $model = $this->getModel('registration');
        $session = JFactory::getSession();
        //get data from request
        $post = JRequest::get('post');
       
		$socialadsbackurl=$session->get('socialadsbackurl');
 				// let the model save it
        if ($model->store()) 
        {
            $message = "";
            $itemid=$input->get('Itemid',0,'INT');
               
           	$this->setRedirect($socialadsbackurl, $message);
        } 
        else
        {
         	$message = $input->get('message','','STRING'); 
		    $itemid=$input->get('Itemid',0,'INT');    
		    $this->setRedirect('index.php?option=com_socialads&view=registration&Itemid='.$itemid, $message);
        }
        
        
	}
	
	
	
	
	function cancel()
	{
		$input=JFactory::getApplication()->input;
		$msg = JText::_( 'Operation Cancelled' );
		$itemid=$input->get('Itemid',0,'INT');
		$this->setRedirect( 'index.php', $msg );
	}

}

