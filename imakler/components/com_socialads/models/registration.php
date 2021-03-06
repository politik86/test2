<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.database.table.user' );


class socialadsModelregistration extends JModelLegacy
{


	function __construct()
	  {
	 	parent::__construct();
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
	  }


    function getData()
    {
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$id = $input->get('cid',0,'INT');
		$user = JFactory::getUser();
		/*$query="SELECT events.*,eventdetails.ticket_price,eventdetails.time_zone,ticket.id FROM #__ticket_sales AS ticket  LEFT JOIN #__event_details AS eventdetails
			 ON ticket.event_details_id = eventdetails.id LEFT JOIN #__eventlist_events AS events ON events.id = eventdetails.event_id WHERE ticket.status IN (".$paymentallow.") AND  ticket.transaction_id IS NOT NULL AND ticket.user_id='".$user->id."'";
		//$this->_db->setQuery($query);
		//$result = $this->_db->loadobjectlist();*/

	    return $this->_data;
	}


	/* Method to store a client record	 */
	function store()
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$id =$input->get('cid',0,'INT');
		$session = JFactory::getSession();
		$db 	= JFactory::getDBO();
		$data 	= JRequest::get( 'post' );
		$user = JFactory::getUser();

				/////////////joomla user entry///////////////*
				if(!$user->id)
				{

					$query = "SELECT id FROM #__users WHERE email = '".$data['user_email']."' or username = '".$data['user_name']."'";
					$this->_db->setQuery($query);
					$userexist = $this->_db->loadResult();
					$userid="";
					$randpass	="";
					if(!$userexist)
					{

						// Generate the random password & create a new user
						$randpass	= $this->rand_str(6);
						$userid 	= $this->createnewuser($data, $randpass);

					}
					else
					{
							$message=JText::_('USER_EXIST');
							JRequest::setVar('message',$message);
							return false;
					}
					if($userid)
					{
						JPluginHelper::importPlugin('user');
						if(!$userexist)
						$this->SendMailNewUser($data, $randpass);
						$user 	= array();
						$options = array('remember'=>JRequest::getBool('remember', false));
						// tmp user details
						$user 	= array();
						$user['username'] = $data['user_name'];
						$options['autoregister'] = 0;
						$user['email'] = $data['user_email'];
						$user['password'] = $randpass;
						$mainframe->login(array('username'=>$data['user_name'], 'password'=>$randpass), array('silent'=>true));
						//$mainframe->triggerEvent('onLoginUser', array($user, $options));

					}

       }


		return true;

	}//end store fn

	function createnewuser($data, $randpass)
	{
		global $message;
		jimport('joomla.user.helper');
		$authorize 	=  JFactory::getACL();
		$user 		= clone(JFactory::getUser());
		$user->set('username', $data['user_name']);
		$user->set('password1', $randpass);
		$user->set('name', $data['user_name']);
		$user->set('email', $data['user_email']);

		// password encryption
		$salt  = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($user->password1, $salt);
		$user->password = "$crypt:$salt";

		// user group/type
		$user->set('id', '');
		$user->set('usertype', 'Registered');
		if(JVERSION >= '1.6.0')
		{
			//$userConfig = JComponentHelper::getParams('com_users');
			// Default to Registered.
			//$defaultUserGroup = $userConfig->get('new_usertype', 2);

			$params=JComponentHelper::getParams('com_socialads');
			$default_usergroup = $params->get('sa_usergroup',2);
			$user->set('groups', array($default_usergroup));

		}
		else
		$user->set('gid', $authorize->get_group_id( '', 'Registered', 'ARO' ));

		$date = JFactory::getDate();
		$user->set('registerDate', $date->toSql());


		// true on success, false otherwise
		if(!$user->save())
		{
			echo $message="not created because of ".$user->getError();
			return false;
		}
		else
		{
		 	$message="created of username-".$user->username."and send mail of details please check";

		}
		return $user->id;
	}


	// Create a random character generator for password
	function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
	   // Length of character list
	   $chars_length = (strlen($chars) - 1);

	   // Start our string
	   $string = $chars{rand(0, $chars_length)};

	   // Generate random string
	   for ($i = 1; $i < $length; $i = strlen($string))
	   {
		   // Grab a random character from our list
		   $r = $chars{rand(0, $chars_length)};

		   // Make sure the same two characters don't appear next to each other
		   if ($r != $string{$i - 1}) $string .=  $r;
	   }

	   // Return the string
	   return $string;
	}


	function SendMailNewUser($data, $randpass)
	{

		$app = JFactory::getApplication();
		$mailfrom=$app->getCfg('mailfrom');
		$fromname=$app->getCfg('fromname');
		$sitename=$app->getCfg('sitename');

		$email=$data['user_email'];
		$subject=JText::_('SA_REGISTRATION_SUBJECT');
		$find1=array('{sitename}');
		$replace1	= array($sitename);
		$subject	= str_replace($find1, $replace1, $subject);

		$message=JText::_('SA_REGISTRATION_USER');
		$find=array('{firstname}','{sitename}','{register_url}','{username}','{password}');
		$replace	= array($data['user_name'],$sitename,JUri::root(),$data['user_name'],$randpass);
		$message	= str_replace($find, $replace, $message);

		JFactory::getMailer()->sendMail($mailfrom, $fromname, $email, $subject, $message);
		$messageadmin=JText::_('SA_REGISTRATION_ADMIN');
		$find2=array('{sitename}','{username}');
		$replace2	= array($sitename,$data['user_name']);
		$messageadmin	= str_replace($find2, $replace2, $messageadmin);

		JFactory::getMailer()->sendMail($mailfrom, $fromname, $mailfrom, $subject, $messageadmin);
		//die;
		return true;
	}


}

