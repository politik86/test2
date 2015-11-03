<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.html.html' );
jimport( 'joomla.html.parameter' );
jimport( 'joomla.filesystem.folder');
class socialadshelper
{
	/**
	 * This function return array of js files which is loaded from tjassesloader plugin.
	 *
	 * @param   array  $jsFilesArray  Js file's array.
	 * @param   array  $firstThingsScriptDeclaration  javascript to be declared first.
	 *
	 */
	public function getSocialadsJsFiles(&$jsFilesArray, &$firstThingsScriptDeclaration)
	{
		$app    = JFactory::getApplication();
		$input  = JFactory::getApplication()->input;
		$option = $input->get('option', '');
		$view   = $input->get('view', '');
		$layout = $input->get('layout', '');

		// Frontend Js files
		if (! $app->isAdmin())
		{
			if ($option == "com_socialads")
			{
				// Load the view specific js
				switch ($view)
				{
					case "buildad":
						$jsFilesArray[] = 'components/com_socialads/js/fuelux2.3loader.min.js';
						$jsFilesArray[] = 'components/com_socialads/js/steps.js';
						$jsFilesArray[] = 'components/com_socialads/js/buildad.js';

						require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

						if($socialads_config['geo_target'] && file_exists(JPATH_SITE.DS."components".DS."com_socialads".DS."geo".DS."GeoLiteCity.dat"))
						{
							if($socialads_config['load_jqui'] == 1)
							{
								$jsFilesArray[] = 'media/techjoomla_strapper/js/akeebajqui.js';
							}
							$jsFilesArray[] = 'components/com_socialads/js/geo/geo.js';
						}
					break;
					default:
					break;
				}
			}
		}

		$reqURI = JUri::root();

		// If host have wwww, but Config doesn't.
		if (isset($_SERVER['HTTP_HOST']))
		{
			if ((substr_count($_SERVER['HTTP_HOST'], "www.") != 0) && (substr_count($reqURI, "www.") == 0))
			{

				$reqURI = str_replace("://", "://www.", $reqURI);
			}
			elseif ((substr_count($_SERVER['HTTP_HOST'], "www.") == 0) && (substr_count($reqURI, "www.") != 0))
			{
				// Host do not have 'www' but Config does
				$reqURI = str_replace("www.", "", $reqURI);
			}
		}

		// Defind first thing script declaration.
		$loadFirstDeclarations          = "var root_url = '" . $reqURI . "';";
		$firstThingsScriptDeclaration[] = $loadFirstDeclarations;

		return $jsFilesArray;
	}

	function getSlabDetails($duration)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		foreach($socialads_config['slab'] as $slab)
		{
			if($slab['duration'] == $duration)
			{
				return $slab;
			}
		}
	}
function getRecurringGateways()
{
   	require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
JPluginHelper::importPlugin( 'payment' );
$dispatcher = JDispatcher::getInstance();
$re_selectbox=array();
$newvar = JPluginHelper::getPlugin( 'payment' );
if($newvar){
			foreach ($newvar as $myparam)
			{
				if(!in_array($myparam->name,$socialads_config['gateways']))
				continue;
				$plugin = JPluginHelper::getPlugin( 'payment',$myparam->name);

				$gateway_style="";

				$pluginParams = json_decode( $plugin->params );
				$vv=$pluginParams->arb_support;

				if($vv){

					$re_selectbox[] = $myparam->name;

				}

			}
			return implode(',',$re_selectbox);

}
return '';
}
	/*Function to show and get payment tabs and info*/
	function getPaymentTab($showimpclick = 0)
	{
	}


	/* function to show ad preview*/
	function getAdPreview($adid,$showlink = 0)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::base().'components/com_socialads/css/helper.css');
		$document->addScript(JUri::base().'components/com_socialads/js/helper.js');
		$user = JFactory::getUser();
			if($adid==0)
			{
				//Still unable to get the title and body. Check if it is stored in the following variables or not.
				$buildadsession = JFactory::getSession();
				$addata = $buildadsession->get('ad_data');
			  $pluginlist = $buildadsession->get('addatapluginlist');
        $upimg = $buildadsession->get('upimg');
        //by madhura
				/*if($pluginlist != ''){
					 $img = $buildadsession->get('pluginimg');
				}
				else
				{
					$img = $buildadsession->get('upimg');
				}
				else
				{
				  $img = $buildadsession->get('upimgcopy');
				}*/

				if($buildadsession->get('upimg') != ''){
						 $img = $buildadsession->get('upimg');
				}
				else if($buildadsession->get('ad_image') != '')
				{
						 $img = $buildadsession->get('ad_image');
				}
				else
				{
						 $img = $buildadsession->get('upimgcopy');
				}

				$title = $addata[2]['ad_title'];
				$body = $addata[3]['ad_body'];
				$link="#";

			}
			else
			{
				$db = JFactory::getDBO();
				$query = "SELECT * FROM #__ad_data WHERE ad_id =".$adid;
				$db->setQuery($query);
				$addata = $db->loadObject();
				$link = JUri::base()."index.php?option=com_socialads&view=redirector&adid=".$addata->ad_id;
				$title = $addata->ad_title;
				$img = $addata->ad_image;
				$body = $addata->ad_body;
			}

			if($socialads_config['ignore']==0 || $user->id==0)
				$showlink = 2;

			$html='<div class="ad_prev_main">
				<div class="ad_prev_wrap">';
				if($showlink == 0)
				{
					$html .= '<img class="ad_ignore_button" src="'.JUri::Root().'components/com_socialads/images/fbcross.gif" onClick="ignore_ads(this,'.$adid.','.$socialads_config["feedback"].');" />
				   <div class="ad_prev_first">';
				   if($link=='#')
					{
						$html .= '<a class="ad_prev_anchor" href="'.$link.'">'.$title .'</a>';
					}
					else
					{
						$html .= '<a class="ad_prev_anchor" href="'.$link.'" target="_blank">'.$title .'</a>';
					}

					$html .= '</div>';
					if($img != '')
						$html .= '
					<div class="ad_prev_second">
						<a href="'.$link.' " target="_blank">
							<img class="ad_prev_img" width="'.$socialads_config['image_dimensions'].'" "src="'.JUri::Root().$img.'" border="0" />
						</a>
					</div>';
			  } else if ($showlink==2) {
					$html .= '<div class="ad_prev_first">';
				    if($link=='#')
						{
							$html .= '<a class="ad_prev_anchor" href="'.$link.'">'.$title .'</a>';
						} else {
							$html .= '<a class="ad_prev_anchor" href="'.$link.'" target="_blank">'.$title .'</a>';
						}
					$str = JUri::base();
					$img = str_replace($str, '', $img);
					$html .= '</div>';
					if($img != '')
						$html .= '
					<div class="ad_prev_second">
						<a href="'.$link.' " target="_blank">
							<img class="ad_prev_img" width="'.$socialads_config['image_dimensions'].'" src="'.JUri::Root().$img.'" border="0" />
						</a>
					</div>'	;

				} else {
					$html .='<div class="ad_prev_first">';
					$html .= $title;
					$html .= '</div>';
					if($img != '')
					{	$html .= '
					<div class="ad_prev_second">';
					$str = JUri::base();
					$img = str_replace($str, '', $img);
					$html.='<img class="ad_prev_img" width="'.$socialads_config['image_dimensions'].'" src="'.JUri::Root().$img.'" border="0" />
					</div>'	;
					}
			     }

			      $html .= '<div class="ad_prev_third">';
				  $html .= $body;
				  $html .= '</div>
				</div>
			</div>';

			return $html;
	}

	function subCredits($adid)
	{
		$db=JFactory::getDBO();
		$sql="UPDATE #__ad_data SET ad_credits_balance = ad_credits_balance-1 WHERE ad_id='".$adid."' AND ad_credits_balance>0";
		$db->setQuery($sql);
		$db->execute();
		return;
	}

	/*function to get an ItemId
	 *@param $view string eg. managead&layout=list
	 * */
	function getSocialadsItemid($view='')
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		if($view && !($mainframe->isAdmin()) ){
			$JSite = new JSite();
			$menu = $JSite->getMenu();
			$items= $menu->getItems('link',"index.php?option=com_socialads&view=$view");
			if(isset($items[0])){
				$itemid = $items[0]->id;
			}
		}
		else{
			$db=JFactory::getDBO();
			$query = "SELECT id FROM #__menu WHERE link LIKE '%index.php?option=com_socialads";

			$query .='&view='.$view;
			$query .= "%' AND published = 1 LIMIT 1";
			$db->setQuery($query);
			$itemid = $db->loadResult();

		}
		if(!isset($itemid))
			{
				$itemid = $input->get('Itemid',0,'INT');
			}
		return $itemid;
	}


	//when new ads created newadmail function get called
	function newadmail($adid, $details, $plugin_mail='')
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$db = JFactory::getDBO();
		global $mainframe;
		$mainframe = JFactory::getApplication();


		$query = "SELECT a.ad_title, a.ad_approved, u.username, u.email FROM #__ad_data as a, #__users as u
							WHERE a.ad_creator=u.id
							AND a.ad_id=".$adid;
		$db->setQuery($query);
		$result	= $db->loadObjectList();
		if($result[0]->ad_approved != 1 || $details[0]->processor == "bycheck" || $details[0]->processor == "byorder")
		{
			if(JVERSION >= '1.6.0')
			{
				$query = "SELECT user_id FROM #__user_usergroup_map WHERE group_id IN('7','8')";
				$db->setQuery($query);
				$admins	= $db->loadColumn();
				$gWhere = '(id =' . implode( ' OR id =', $admins ) . ')';
			}
			else
			{	$acl = JFactory::getACL();
				$admins = $acl->get_group_objects( '25', 'ARO' );
				$gWhere = '(id =' . implode( ' OR id =', $admins['users'] ) . ')';
			}
				$query = 'SELECT email'
			. ' FROM #__users'
			. ' WHERE ' . $gWhere
			. ' AND sendEmail=1';
			if(JVERSION >= '1.6.0'){
			$query .= ' AND id<>42';
			}
			else{
			$query .= ' AND id<>62';
			}

			$db->setQuery( $query );
			$emails = $db->loadColumn();
		}
		$ad_title=($result[0]->ad_title!= '') ? JText::_("PERIDIC_STATS_ADTIT").' <b>"'.$result[0]->ad_title.'"</b>' : JText::_("PERIDIC_STATS_ADID").' : <b>'.$adid.'</b>';
			//mail sent to user and Admin in case of offline payment plugins
		if($details[0]->processor == "bycheck" || $details[0]->processor =="byorder")
		{
				$post_data = json_decode($details[0]->extras,true);
			// mail to all Admins
			$body = JText::_('AD_PAYMENT_BODY');
			if(isset($post_data['comment']))
			{
			$comment	= str_replace('{comment}', trim($post_data['comment']), JText::_("AD_PAY_COMMENT"));
			$find 	= array ('{title}','{username}','{paymentby}','{amount}','{currency}', '{comment}');
			$replace= array($ad_title,$result[0]->username,$details[0]->payment_method,$details[0]->ad_amount,$socialads_config['currency'],$comment);
			}
			else
			{
			$find 	= array ('{title}','{username}','{paymentby}','{amount}','{currency}','{comment}');
			$replace= array($ad_title,$result[0]->username,$details[0]->payment_method,$details[0]->ad_amount,$socialads_config['currency'],'');
			}

			$body	= str_replace($find, $replace, $body);
			$from = $mainframe->getCfg('mailfrom');
			$fromname = $mainframe->getCfg('fromname');
			$recipient = $mainframe->getCfg('mailfrom');
			$subject = JText::_('AD_PAYMENT_SUBJECT');
			$body = nl2br($body);
			$mode = 1;
			$cc =array_values($emails);
			$bcc = null;
			$bcc = null;
			$attachment = null;
			$replyto = null;
			$replytoname = null;

			JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);

			// mail to the AD owner
			$body = JText::_('PAY_USERMAIL_BODY');
			$find 	= array ('{title}','{paymentby}','{amount}','{currency}');
			$replace= array($ad_title,$details[0]->payment_method,$details[0]->ad_amount,$socialads_config['currency']);
			$body	= str_replace($find, $replace, $body);
			if(!isset($post_data['mail_addr']))
			{
				$plugin_mail = JText::_('NO_ADDRS');
			}
			else{
				$plugin_mail = $post_data['mail_addr'];
			}
			$body	= str_replace('{plgmail}', $plugin_mail, $body);
			$from = $mainframe->getCfg('mailfrom');
			$fromname = $mainframe->getCfg('fromname');
			$recipient = $result[0]->email;
			$subject = JText::_('PAY_USERMAIL_SUBJECT');
			$body = nl2br($body);
			$mode = 1;
			$cc = null;
			$bcc = null;
			$bcc = null;
			$attachment = null;
			$replyto = null;
			$replytoname = null;

			JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);


		}
			//if ad is approved then mail has not been sent.
		if($result[0]->ad_approved != 1)
		{	//mail to all admins
			$body 	= JText::_('AD_AAPROVAL_BODY');
			$body	= str_replace('{title}', $ad_title, $body);
			$body	= str_replace('{username}', $result[0]->username, $body);
			$body	= str_replace('{link}', JUri::base().'administrator/index.php?option=com_socialads&view=approveads', $body);
			$body	= str_replace('{paymentby}', $details[0]->payment_method, $body);
			$body	= str_replace('{amount}', $details[0]->ad_amount, $body);
			$body	= str_replace('{currency}', $socialads_config['currency'], $body);

			$from = $mainframe->getCfg('mailfrom');
			$fromname = $mainframe->getCfg('fromname');
			$recipient = $mainframe->getCfg('mailfrom');
			$subject = JText::_('MAIL_SUBJECT');
			$body = nl2br($body);
			$mode = 1;
			$cc =array_values($emails);
			$bcc = null;
			$attachment = null;
			$replyto = null;
			$replytoname = null;
			JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);

			return true;
		}
	}//function ends newadmail

	//function for payment details attched in approval mail
	function paymentdetails($adid)
	{
	  $db = JFactory::getDBO();
		$query = "SELECT d.ad_payment_type, p.ad_amount, p.processor,p.comment,p.extras
			 					 FROM #__ad_data AS d
			 					 LEFT JOIN #__ad_payment_info AS p on d.ad_id=p.ad_id
								 WHERE p.ad_id=$adid
								ORDER BY p.id DESC";
		$db->setQuery($query);
		$details = $db->loadobjectlist();

		return $details;
	}

  //function for payment plugin payby check/ purchase order layout
  function getPaymentplugin($plg,$plg_mail)
  {

  	$html = "
  						<form action='' method='post' enctype='multipart/form-data' name='checkForm'>
			<table>
						<tr>
								<td class='ad-price-lable'>".JText::_('COMMENT')."</td>
								<td>
										<textarea id='comment' name='comment' rows='3' maxlength='135' size='28'></textarea>
								</td>
						</tr>
						<tr>
								<td class='ad-price-lable'>".JText::_('CON_PAY_PRO')." : </td>
								<td>";
						if($plg_mail=="")
							$html .= JText::_('NO_ADDRS');
						else
							$html .= $plg_mail;
						$html .="
								</td>
						</tr>
						<tr>
								<td>
									<input type='submit' name='btn_check' id='btn_check'  value='".JText::_('SUBMIT')."'>
								</td>
						</tr>
			</table>
			<input type='hidden' name='option' value='com_socialads'>
			<input type='hidden' name='controller' value='showad'>
			<input type='hidden' name='task' value='make_check_order'>
			<input type='hidden' name='plg' value='".$plg."'>
</form>";

  return $html;
  }

  //checking if table "ad_fields" exists or not in buildad and manage ad view
	function chkadfields()
	{
		$db = JFactory::getDBO();
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$dbname = $mainframe->getCfg( 'db' );
		$dbprefix = $mainframe->getCfg( 'dbprefix' );
		$tablename = $dbprefix.'ad_fields';
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*)
						FROM information_schema.tables
						WHERE table_schema = '$dbname'
						AND table_name = '$tablename'";
		$db->setQuery($query);
		$adfields = $db->loadresult();

		if(!$adfields){
		  return '';
		}
		else{
		  return 1;
		}
	}

	function cbchk()
	{
	  //$db = JFactory::getDBO();
		//$query = "show tables like '#__comprofiler_field_values'";
	  //$db->setQuery($query);
		//$cbchk = $db->loadobjectlist();

		$cbpath = JPATH_ROOT.DS.'components'.DS.'com_comprofiler';
		if( JFolder::exists($cbpath) )
		  return 1;
		else
			return '';
	}

	function jschk()
	{
		//$db = JFactory::getDBO();
		//$query = "show tables like '#__community_fields'";
	  //$db->setQuery($query);
		//$jschk = $db->loadobjectlist();

	  $jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
		if( file_exists($jspath) )
		  return 1;
		else
			return '';

	}
	function eschk()
	{
		//$db = JFactory::getDBO();
		//$query = "show tables like '#__community_fields'";
	  //$db->setQuery($query);
		//$jschk = $db->loadobjectlist();

	  $jspath = JPATH_ROOT.DS.'components'.DS.'com_easysocial';
		if( file_exists($jspath) )
		  return 1;
		else
			return '';

	}

	function getAdInfo($adid=0,$adinfo='*')
	{

		$db = JFactory::getDBO();
		$query = "SELECT ".$adinfo." FROM #__ad_data  WHERE ad_id=$adid ";
		$db->setQuery($query);
		$details = $db->loadobjectlist();
		return $details;

	}

	//this function is to update end date for date type ad
	function adddays($ad_id,$no_days)
	{
		$db = JFactory::getDBO();
		$qry= "SELECT * FROM #__ad_data WHERE ad_id =".$ad_id;
		$db->setQuery($qry);
		$adDetails=$db->loadObjectlist();

		$addata=new stdClass;
		$addata->ad_id=$ad_id;
		if($adDetails[0]->ad_enddate=="0000-00-00")
			$timestmp = strtotime(date("Y-m-d", strtotime($adDetails[0]->ad_startdate)) . " +".$no_days." day");
		else
			$timestmp = strtotime(date("Y-m-d", strtotime($adDetails[0]->ad_enddate)) . " +".$no_days." day");

		$addata->ad_enddate=date("Y-m-d H:i:s",$timestmp);
		$db->updateObject('#__ad_data', $addata, 'ad_id');

	}




	/*function sobichk()
	{
		//$db = JFactory::getDBO();
		//$query = "show tables like '#__community_fields'";
	  //$db->setQuery($query);
		//$jschk = $db->loadobjectlist();

	  $sobipath = JPATH_ROOT.DS.'components'.DS.'com_sobi2';
		if( JFolder::exists($sobipath) )
		  return 1;
		else
			return '';

	}*/
  /*This function is used to get parameter of plugins
	*	@group:string  group of plugin
	*	@api:string name of plugin
	*	@params:string name of required parameters seperated by comma(,)
	*/
		function getpluginparams($group,$api,$paramstr)
		{

			if(!$group and !$api	and !$paramstr)
			return '';
			if(JVERSION>=1.6)
				{
					$plugin = JPluginHelper::getPlugin($group, $api);
					$pluginParams = new JRegistry();
					if(!empty($plugin->params))
					$pluginParams->loadString($plugin->params);
				}
				else
				{
					$plugin = JPluginHelper::getPlugin($group, $api);
					if(!empty($plugin->params))
					$pluginParams = json_decode($plugin->params);

				}

				if($pluginParams)
				{
						$params=explode(',',$paramstr);
						$params_data=array();
						foreach($params as $param)
						{
									if($pluginParams->get($param))
									$params_data[$param] = $pluginParams->get($param);

						}
				}

				return $params_data;
		}

	function getbalance()
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$user = JFactory::getUser();
		$user_id= $user->id;
		if(!$user_id || $socialads_config['select_campaign']==0)
		{
			return NULL;
		}
		$db = JFactory::getDBO();
		$query = "SELECT balance FROM `#__ad_camp_transc` where time = (select MAX(time) from #__ad_camp_transc where user_id =".$user_id.")";
		$db->setQuery($query);
		$init_balance=$db->loadresult();
		if($init_balance < $socialads_config['camp_currency_pre'])
		{
			return '0.00';
		}
		else{
			return '1.00';
		}
	}

	/**vm: General send mail function */

	function sendmail($recipient,$subject,$body,$bcc_string,$singlemail=1,$attachmentPath="")
	{
		jimport('joomla.utilities.utility');
		global $mainframe;
		$mainframe = JFactory::getApplication();
			$from = $mainframe->getCfg('mailfrom');
			$fromname = $mainframe->getCfg('fromname');
			$recipient = trim($recipient);
			$mode = 1;
			$cc = null;
			$bcc=array();
			if($singlemail==1)
			{
				if($bcc_string){
					$bcc = explode(',',$bcc_string);
				}
				else{
					$bcc = array('0'=>$mainframe->getCfg('mailfrom') );
				}
			}
			//$bcc = array('0'=>$mainframe->getCfg('mailfrom') );
			$attachment = null;
			if(!empty($attachmentPath)) {

				$attachment = $attachmentPath;
			}
			$replyto = null;
			$replytoname = null;
		return	JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
	}
	/** vm: This function returns orders payee id*/
	function getOrderUserId($order_id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT p.payee_id FROM #__ad_payment_info as p WHERE p.id=".$order_id;
		$db->setQuery($query);
		return  $db->loadResult();
	}

   /** vm: send invoice is  when order is confirmd from backend and front end ( for pay per ad as well as wallet ad)
    ** */
    //function sendInvoice($order_id,$pg_nm)
    function getInvoiceDetail($order_id,$pg_nm,$payPerAd=1)
    {
		if(empty($order_id))
		{
			return ;
		}
		$mainframe = JFactory::getApplication();
		$site = $mainframe->getCfg('sitename');
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$db = JFactory::getDBO();
		$query = "SELECT p.id as order_id,p.`ad_id`,p.mdate,p.ad_amount,p.transaction_id,p.status,p.processor,p.payee_id,u.username,u.id, u.email
		FROM #__ad_payment_info as p, #__users as u
			WHERE p.payee_id=u.id
			AND p.id=".$order_id;
		$db->setQuery($query);
		$orderDetails	= $db->loadObject();

		$body = JText::_('PAY_PAYMENT_BODY');
		$socialadshelper = new socialadshelper();

		// GET USER ID FOR BILL INFO
		//$userId = $socialadshelper->getOrderUserId($order_id);

		//GET BILL INFO
		JLoader::import('buildad', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
		$socialadsModelBuildad = new socialadsModelBuildad;
		$billinfo = $socialadsModelBuildad->getbillDetails($orderDetails->id);

		// DEFINE ORDER STATUS
		if($orderDetails->status=='C')
		{
			$orderstatus = JText::_('ADS_INVOICE_STATUS_COMPLEATE');
		}
		else if($orderDetails->status=='P')
		{
			$orderstatus = JText::_('ADS_INVOICE_STATUS_PENDING');
		}
		else if($orderDetails->status=='E')
		{
			$orderstatus = JText::_('ADS_INVOICE_STATUS_COMPLEATE_ERROR_OCCURED');
		}
		else
		{
			$orderstatus = JText::_('ADS_INVOICE_AMOUNT_CANCELLED');
		}

		$inv_adver_Html="";
		$sa_displayblocks = array();

		// if ad wallet
		if($payPerAd==0)
		{
			$sa_displayblocks=array('invoiceDetail'=>1,'billingDetail'=>0,'adsDetail'=>0);
		}
		$billpath = $socialadshelper->getViewpath('payment','invoice');
		ob_start();
			include($billpath);
			$inv_adver_Html = ob_get_contents();
		ob_end_clean();

		//-----------------------------

		jimport('joomla.utilities.utility');

		global $mainframe;
		$app = JFactory::getApplication();
		$mainframe = JFactory::getApplication();
		$sitelink	= JUri::root();

		$manageAdLink = "<a href='".$sitelink."administrator".DS."index.php?option=com_socialads&view=approveads' targe='_blank'>".JText::_( "COM_SOCIALADS_EMAIL_THIS_LINK" )."</a>";
					// GET config details
		$frommail  	= $mainframe->getCfg('mailfrom');
		$fromname = $mainframe->getCfg('fromname');

		$adTitle = $socialadshelper->getAdTitle($orderDetails->ad_id);
		$siteName = $mainframe->getCfg('sitename');
		$today		= date('Y-m-d H:i:s');

		if(empty($orderDetails->payee_id))
		{
			// return payee id not found
			return ;

		}
		$user = JFactory::getUser($orderDetails->payee_id);
		$adUserName =  $user->username;
		$recipient =  $user->email;
		$displayOrderid = sprintf("%05s", $order_id);

		// if paper mode
		if($payPerAd==1)
		{
			$approve_msg = JText::_( "COM_SOCIALADS_INVOICE_MAIL_ADMIN_APPROCE_NO_MSG" );
			if($socialads_config['approval']==1)
			{
				$approve_msg = JText::_( "COM_SOCIALADS_INVOICE_MAIL_ADMIN_APPROCE_YES_MSG" );
			}


			// FOR ADVERTISER INVICE AND ORDER CONFIRM MAIL

			$advertiserEmailBody =JText::_( "COM_SOCIALADS_INVOICE_MAIL_CONTENT" );

			// NOW find & REPLACE TAG
			$find = array('[SEND_TO_NAME]','[ADVERTISER_NAME]','[SITENAME]','[SITELINK]','[ADTITLE]','[CONTENT]', '[TIMESTAMP]','[ORDERID]','[ADMIN_APPROVAL_MSG]');
			$replace = array($adUserName,$adUserName,$siteName,$sitelink,$adTitle,$content,$today,$displayOrderid,$approve_msg);
			$advertiserEmailBody	    = str_replace($find, $replace, $advertiserEmailBody);

			$advertiserEmailBody = $advertiserEmailBody . "<br> <br>".$inv_adver_Html;

			$subject = JText::sprintf('ADS_INVOICE_MAIL_SUB',$displayOrderid);
			$status  = $socialadshelper->sendmail($recipient,$subject,$advertiserEmailBody,'',0,"");

			// -------------- ADMIN INVOICE MAIL  COPY --------------------
			$adminEmailBody = JText::_( "COM_SOCIALADS_INVOICE_MAIL_CONTENT_ADMIN_COPY" );
			$orderPrice = $orderDetails->ad_amount ." ".$socialads_config["currency"];
			$admin_approve_msg = '';
			//!$app->isAdmin() &&

			$admin_approve_msg = '';
			if($socialads_config['approval']==1)
			{
				$admin_approve_msg = JText::sprintf("COM_SOCIALADS_APPRVE_MAIL_TO_ADMIN_ADD_MSG" ,$manageAdLink);
			}
			$find 		= array('[SEND_TO_NAME]','[ADVERTISER_NAME]','[SITENAME]','[VALUE]','[ORDERID]','[ADMIN_APPROVAL_MSG]');
			$replace 	= array($fromname,$adUserName,$siteName,$orderPrice,$displayOrderid,$admin_approve_msg);
			$adminEmailBody	    = str_replace($find, $replace, $adminEmailBody);
			$adminEmailBody	    = $adminEmailBody. "<br> <br>".$inv_adver_Html;
			$subject = JText::sprintf('COM_SOCIALADS_INVOICE_MAIL_ADVERTISER_ADMIN_SUBJECT',$displayOrderid);
			$status  = $socialadshelper->sendmail($frommail,$subject,$adminEmailBody,'',0,"");
		}
		else
		{

			// ADVERTISER MAIL
			$advertiserEmailBody = JText::_( "COM_SOCIALADS_WALLET_ADDED_BALACE_ADVETISER_EMAIL" );
			// NOW find & REPLACE TAG
			$find = array('[SEND_TO_NAME]','[SITENAME]','[ORDERID]');
			$replace = array($adUserName,$siteName,$displayOrderid);
			$advertiserEmailBody	    = str_replace($find, $replace, $advertiserEmailBody);
			$advertiserEmailBody	    = $advertiserEmailBody. "<br> <br>".$inv_adver_Html;
			$subject = JText::sprintf('COM_SOCIALADS_WALLET_ADDED_BALACE_ADVETISER_EMAIL_SUBJECT',$displayOrderid);
			$status  = $socialadshelper->sendmail($recipient,$subject,$advertiserEmailBody,'',0,"");

			// ADMIN INVOICE MAIL  COPY
			$adminEmailBody = JText::_( "COM_SOCIALADS_INVOICE_MAIL_CONTENT_ADMIN_COPY" );
			$orderPrice = $orderDetails->ad_amount ." ".$socialads_config["currency"];

			$find 		= array('[SEND_TO_NAME]','[ADVERTISER_NAME]','[SITENAME]','[VALUE]','[ORDERID]','[ADMIN_APPROVAL_MSG]');
			$replace 	= array($fromname,$adUserName,$siteName,$orderPrice,$displayOrderid,'');
			$adminEmailBody	    = str_replace($find, $replace, $adminEmailBody);
			$adminEmailBody	    = $adminEmailBody. "<br> <br>".$inv_adver_Html;
			$subject = JText::sprintf('COM_SOCIALADS_INVOICE_MAIL_ADVERTISER_ADMIN_SUBJECT',$displayOrderid);
			$status  = $socialadshelper->sendmail($frommail,$subject,$adminEmailBody,'',0,"");
			// ad wallet mode
		}
	}

	// vm: return ad title
	function getAdTitle($ad_id)
	{
		if(empty($ad_id))
		{
			return ;
		}
		$db = JFactory::getDBO();
		$query = "SELECT a.ad_title FROM `#__ad_data` as a WHERE a.ad_id=".$ad_id;
		$db->setQuery($query);
		return  $db->loadResult();
	}
	function new_pay_mail($order_id)
	{
		$mainframe = JFactory::getApplication();
		require_once(JPATH_SITE.DS."components".DS."com_socialads".DS."helper.php");  // require when we call from backend
		$socialadshelper = new socialadshelper;
		$db = JFactory::getDBO();
		$query = "SELECT p.payee_id,u.username, u.email,p.status FROM #__ad_payment_info as p, #__users as u
							WHERE p.payee_id=u.id
							AND p.id=".$order_id;
		$db->setQuery($query);
		$result	= $db->loadObject();

			$body = JText::_('PAY_PAYMENT_BODY');
			$find 	= array ('[SEND_TO_NAME]','[ORDERID]','[SITENAME]','[STATUS]');


			if($result->status=='P')
			{
				$orderstatus =  JText::_('ADS_INVOICE_STATUS_PENDING');
			}
			else  if($result->status =='RF')
			{
				$orderstatus= JText::_('SA_REFUNDED');
			}
			else
			{
				$orderstatus = JText::_('ADS_INVOICE_AMOUNT_CANCELLED');
			}


		$recipient = $result->email;
		$siteName = $mainframe->getCfg('sitename');

		$displayOrderid = sprintf("%05s", $order_id);
		$replace= array($result->username,$displayOrderid,$siteName,$orderstatus);
		$body	= str_replace($find, $replace, $body);
		$subject = JText::sprintf("SA_STATUS_CHANGED_MAIL_SUBJECT",$displayOrderid);

		$status  = $socialadshelper->sendmail($recipient,$subject,$body,'',0,"");
	}

	function geteasysocial_field_title($id,$value)
	{
		$db = JFactory::getDBO();
		$query = "SELECT title FROM #__social_fields_options WHERE parent_id=".$id." AND value='".$value."'";
		$db->setQuery($query);
		return $db->loadresult();
	}

	//this will load any javascript only once
	function loadScriptOnce($script)
	{
		$doc = JFactory::getDocument();
		$flg=0;
		foreach($doc->_scripts as $name=>$ar)
		{
			if($name==$script){
				$flg=1;
			}
		}
		if($flg==0){
			$doc->addScript($script);
		}
	}
	/** vm:checks for view override
		@parms $viewname :: (string) name of view
				$searchTmpPath ::(string) it may be admin or site. it is side(admin/site) where to search override view
				$useViewpath ::(string) it may be admin or site. it is side(admin/site) which VIEW shuld be use IF OVERRIDE IS NOT FOUND
			   $layout:: (string) layout name eg order
		@return :: if exit override view then return path
	*/
	function getViewpath($viewname,$layout="",$searchTmpPath='SITE',$useViewpath='SITE')
	{
		$searchTmpPath = ($searchTmpPath=='SITE')?JPATH_SITE:JPATH_ADMINISTRATOR;
		$useViewpath = ($useViewpath=='SITE')?JPATH_SITE:JPATH_ADMINISTRATOR;
		$app = JFactory::getApplication();

		if(!empty($layout))
		{
			$layoutname=$layout.'.php';
		}
		else
		{
			$layoutname="default.php";
		}
		$override = $searchTmpPath.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.'com_socialads'.DS.$viewname.DS.$layoutname;

		if(JFile::exists($override) )
		{
			return $view = $override;
		}
		else
		{
			return $view=$useViewpath.DS.'components'.DS.'com_socialads'.DS.'views'.DS.$viewname.DS.'tmpl'.DS.$layoutname;
		}
	}
	// end of getViewpath()

	/** Vm: Fetch ad detail and recaculate and sync order details
	* @param $order_id $order_id
	* @param $syncDetail int wherther to sync order detail or not. While order detail page with status = p then only keep to 1
	* */
	function getOrderAndAdDetail($order_id, $syncDetail=0)
	{
		$socialadshelper = new socialadshelper();
		if($syncDetail == 1)
		{
			$socialadshelper->syncOrderDetail($order_id);
		}
		$db=JFactory::getDBO();

		$query = "SELECT   b.ad_id FROM #__ad_payment_info AS b
				WHERE b.id= $order_id";
		$db->setQuery($query);
		$adid= $db->loadResult();

		if(!empty($adid ) )
		{
			// add pay per ad
			$query = "SELECT   a.*, b.* FROM #__ad_data as a
			LEFT JOIN  #__ad_payment_info AS b
			ON b.ad_id=a.ad_id
			WHERE b.id= $order_id";
		}
		else
		{
			// add wallet mode
			$query =  "SELECT   b.* FROM #__ad_payment_info AS b
				WHERE b.id= $order_id";
		}
		$db->setQuery($query);
		return $addata= $db->loadAssoc();
	}

	/** vm:This function deduct tax amount from discounted amount and store in orders final amount
	 *  */
	function syncOrderDetail($order_id)
	{
		$db=JFactory::getDBO();
		$query = "SELECT   a.`ad_original_amt`,a.`ad_coupon`,a.`ad_tax`,a.ad_tax_details
		FROM  #__ad_payment_info AS a
		WHERE a.id= $order_id AND a.status != 'C'";
		$db->setQuery($query);
		$orderData = $db->loadAssoc();
 		$val = 0;  // for coupon discount

		if(!empty($orderData) &&  !empty($orderData['ad_coupon']))
		{
			// get payment HTML
			JLoader::import('showad', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
			$showadmodel = new socialadsModelShowad();
			$adcop = $showadmodel->getcoupon($orderData['ad_coupon']);

			if($adcop)
			{
				if($adcop[0]->val_type == 1) 		//discount rate
				{
					$val = ($adcop[0]->value/100) * $orderData['ad_original_amt'];
				}
				else
					$val = $adcop[0]->value;
			}
			else
			{
				$val = 0;
			}
		}

		$discountedPrice = $orderData['ad_original_amt'] - $val;

		//<!-- TAX CALCULATION-->
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('adstax');//@TODO:need to check plugim type..
		$taxresults=$dispatcher->trigger('addTax',array($discountedPrice));//Call the plugin and get the result

		$appliedTax = 0;

		if( !empty($taxresults) )
		{
			foreach($taxresults as $tax)
			{
					if( !empty($tax) )
					{
							$appliedTax += $tax[1];
					}
			}
		}

		$amountAfterTax = $discountedPrice + $appliedTax;

		if ($amountAfterTax <= 0)
		{
			$amountAfterTax = 0;
		}

		$row = new stdClass;
		$row->ad_coupon = '';

		if(!empty($val))
		{
			$row->ad_coupon = $orderData['ad_coupon'];
		}

		$row->id = $order_id;
		$row->ad_tax_details = json_encode($taxresults);
		$row->ad_amount = $amountAfterTax;
		$row->ad_tax = $appliedTax;
		if(!$db->updateObject('#__ad_payment_info', $row, 'id'))
		{
			echo $this->_db->stderr();
		}
		return $row;
	}
	// vm:
	function adStateForAddMoreCredit($ad_id,$userid)
	{

		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		if($socialads_config['select_campaign']==1)
		{
			return 1;
		}

		$db=JFactory::getDBO();

		// chek orderid against  ads  and status if exist.
		$query='SELECT p.`status` FROM `#__ad_payment_info` as p
				WHERE p.ad_id='.$ad_id.' AND p.`payee_id`='.$userid . ' ORDER BY `id` DESC' ;
		$db->setQuery($query);
		$orderDetails = $db->loadAssocList();

		$editableSteps = array();
		// currently there may be only on pending order against ad

		// check for order id against ad
		if(!empty($orderDetails))
		{
			// now check for alredy confirm orders
			$query='SELECT p.`id` FROM `#__ad_payment_info` as p
				WHERE p.ad_id='.$ad_id.' AND p.`payee_id`='.$userid . ' AND p.`status`=\'C\' ORDER BY `id` DESC' ;
			$db->setQuery($query);
			$preCinfirmOrders = $db->loadResult();
			if(!empty($preCinfirmOrders))
			{
				// already some order is confirmed via add more credit. In this case, no price releated thing  should edit. Allow to apply coupon
				$editableSteps['adInfo'] = 1;
				$editableSteps['targetting'] = 1;
				$editableSteps['pricing'] = 0;
				$editableSteps['applyCoupon'] = 1;
			}
			else
			{
				// only one order is placed ( can edit all details)
				$editableSteps['adInfo'] = 1;
				$editableSteps['targetting'] = 1;
				$editableSteps['pricing'] = 1;
				$editableSteps['applyCoupon'] = 1;
			}

		}
		else
		{
			// that mean no order has been placed yet
			$editableSteps['adInfo'] = 1;
			$editableSteps['targetting'] = 1;
			$editableSteps['pricing'] = 1;
			$editableSteps['applyCoupon'] = 1;
		}
		return $editableSteps;
	}
	// vm:
	function processFreeOrder($adminCall=0)
	{
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id','','STRING');
		$socialadshelper = new socialadshelper();
		$adDetail = $socialadshelper->syncOrderDetail($order_id);

	// if order amount is 0 due to coupon
		if($adDetail->ad_amount == 0  && !empty($adDetail->ad_coupon))
		{
			$db=JFactory::getDBO();
			$row = new stdClass;
			$row->status = 'C';
			$row->id = $order_id;
			if(!$db->updateObject('#__ad_payment_info', $row, 'id'))
			{
				echo $this->_db->stderr();
			}

			$modelPath=JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models'.DS.'showad.php';

			if(!class_exists('socialadsModelShowad'))
			{
			  //require_once $path;
			   JLoader::register('socialadsModelShowad', $modelPath );
			   JLoader::load('socialadsModelShowad');
			}

			$data=array();
			$data['status']= 'C';
			$data['payment_type']='';
			$data['raw_data']='';
			$pg_nm='';

			$socialadsModelShowad=new socialadsModelShowad();
			$socialadsModelShowad->saveOrder($data,$order_id,$pg_nm);

		}
		$mainframe=JFactory::getApplication();
		$response['msg'] = JText::_('DETAILS_SAVE');

		if($adminCall == 1 )
		{
			$link = 'index.php?option=com_socialads&view=approveads';
		}
		else
		{
			$Itemid = $socialadshelper->getSocialadsItemid('managead');
			$link = JUri::base().substr(JRoute::_('index.php?option=com_socialads&view=managead&layout=list&Itemid='.$Itemid,false),strlen(JUri::base(true))+1);
		}
		$mainframe->redirect($link,$response['msg']);
	}

	// Amol:
	//@params table name
	function getTableColumns($tablename)
	{
		$db=JFactory::getDBO();
		$app= JFactory::getApplication();
	    $dbprefix = $app->getCfg('dbprefix');

		$query = "SHOW TABLES LIKE '".$dbprefix.$tablename."'";
		$db->setQuery($query);
		$isTableExist = $db->loadResult();

		$paramlist=array();

		if($isTableExist)
		{
			$query_to_get_column="SHOW COLUMNS FROM #__".$tablename;
			$db->setQuery($query_to_get_column);
			$paramlist= $db->loadColumn();

		}
		return $paramlist;
	}

}//class ends
?>

