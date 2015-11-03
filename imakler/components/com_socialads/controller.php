<?php
/**
 * @version $Id: header.php 248 2008-05-23 10:40:56Z elkuku $
 * @package		socialads
 * @subpackage
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Tekdi Web Solutions {@link http://www.nik-it.de}
 * @author		Created on 05-Mar-2010
 */

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');
jimport( 'joomla.html.parameter' );
jimport('joomla.filesystem.file');
jimport( 'joomla.filesystem.folder');
/**
 * socialads default Controller
 *
 * @package    socialads
 * @subpackage Controllers
 */
class socialadsController extends JControllerLegacy
{

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	public function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}// function
function test(){

	$session = JFactory::getSession();
	$data = $session->get('SocialData');
print_r($data);
$session->clear('SA_resultads');
		$session->clear('SocialData');

		$data = $session->get('SocialData');
		print_r($data);
		die('done');
}
//function to get ads from server database
	function getAds(){
		$msg['success'] = '0';
		$msg['message'] = 'Error occured : no data send!';
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->post;
		$adData = $jinput->get('adData','',"RAW");
		$callback = $jinput->get('callback','',"CMD");
		$nohtml = $jinput->get('nohtml','',"INT");

		if(!empty($adData))
		{
			$adData = json_decode($adData,true);
		}
		else{
			echo $callback ?  $callback. '(' .json_encode($msg). ');' : json_encode($msg);
			jexit();
		}
//			include_once JPATH_ROOT.'/media/techjoomla_strapper/strapper.php';
//	TjAkeebaStrapper::bootstrap();
		$session = JFactory::getSession();
		$session->set('userData',$adData);
		require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'remote.php');
		$adRetriever = new remoteAdRetriever(1);
		$ads	 = $adRetriever->getnumberofAds($adData,$adData['ads_params']['moduleid'],$adRetriever);
$params = new remoteAdRetriever(1);
$adhtml = '';
$moduleid = $adData['ads_params']['moduleid']; //hard coded from client
		$adhtml .='<div class="sa_mod_'.$moduleid.'">';
		foreach($ads as $key=>$ad_id)
		{
			$addata = $adRetriever->getAdDetails($ad_id);
			if ($nohtml) {
				$addata->ad_image = JUri::root() . $addata->ad_image;
				$ads[$key]->ad = $addata;
			}
			$adhtml .= $adRetriever->getAdHTML($addata);
			if(JVERSION >= '1.6.0')
				$cssfile=JUri::root().'plugins'.DS.'socialadslayout'.DS.'plug_'.$addata->layout.DS.'plug_'.$addata->layout.DS.'layout.css';
			else
				$cssfile=JUri::root().'plugins'.DS.'socialadslayout'.DS.'plug_'.$addata->layout.DS.'layout.css';
$adhtml .= '<link type="text/css" href="'.$cssfile.'" rel="stylesheet">';
//$adhtml .= '<script type="text/javascript" src="'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.9.min.js');"> </script>';
		}
		$adhtml .='</div>';

/*
jimport( 'joomla.application.module.helper' );
$module = JModuleHelper::getModule( 'mod_socialads','Social Ads1' );
$attribs['style'] = 'xhtml';
$adhtml = JModuleHelper::renderModule($module);
		ob_start();
		include(JModuleHelper::getLayoutPath('mod_socialads'));
			$adhtml .= ob_get_contents();
		ob_end_clean();

*/

		if(!empty( $adhtml)){
			$msg['success'] = '1';
			$msg['message'] = 'You got some ads!';
			$msg['data'] = $nohtml ? $ads : $adhtml;
		}
//print_r($msg);
header('Content-Type: application/json');
		echo $callback ?  $callback. '(' .json_encode($msg). ');' : json_encode($msg);
		jexit();
	}
		//function insert transparent image in the mails and count impressions for ad
	function getimprimage()
	{
		$input=JFactory::getApplication()->input;
		$adid = $input->get('adid',0,'INT');
		$frommail = 0;
		$frommail = $input->get('simulate',0,'INT');
		if($frommail != 1 ){
			adRetriever::reduceCredits($adid,0);
			header( 'Content-type: image/gif' );
  # The transparent, beacon image
  echo chr(71).chr(73).chr(70).chr(56).chr(57).chr(97).
      chr(1).chr(0).chr(1).chr(0).chr(128).chr(0).
      chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).
      chr(33).chr(249).chr(4).chr(1).chr(0).chr(0).
      chr(0).chr(0).chr(44).chr(0).chr(0).chr(0).chr(0).
      chr(1).chr(0).chr(1).chr(0).chr(0).chr(2).chr(2).
      chr(68).chr(1).chr(0).chr(59);
		}
		exit();
		die;
	}
	//function called if the ad is click based
	function adredirector()
	{
		require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$adid = $input->get('adid',0,'INT');
		$adRetriever = new adRetriever();
		$statue_adcharge = $adRetriever->getad_status($adid);
		if($statue_adcharge['status_ads']==1)
		{
			$caltype = $input->get('caltype',0,'INT');
			$widget = $input->get('widget','','STRING');
			$adRetriever->reduceCredits($adid,$caltype,$statue_adcharge['ad_charge'],$widget);

			/*START API Trigger*/
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('system');
			$dispatcher->trigger('onSA_Adclick');
			/*END API Trigger*/

			//added for added for sa_jbolo integration
			$chatoption = $input->get('chatoption',0,'INT');
			if($chatoption){
				jexit();
			}
			//end added for added for sa_jbolo integration
			$result = $this->getURL();
			$mainframe->redirect($result);
		}

	}

	function getURL()
	{
		$input=JFactory::getApplication()->input;
		$db = JFactory::getDBO();
		$ad_id = $input->get('adid',0,'INT');
		$query = "SELECT ad_url1,ad_url2
					FROM #__ad_data WHERE ad_id = $ad_id";
		$db->setQuery($query);
		$result = $db->loadObject();
		$urlstring = '';
		$urlstring = $result->ad_url1;
		$urlstring .= '://';
		$urlstring .= $result->ad_url2;
		return $urlstring;

	}//end function

	function group_promote()
		{
			$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
			$gid = $input->get('groupid',0,'INT');
			global $mainframe;
			$mainframe = JFactory::getApplication();
		  	$buildadsession = JFactory::getSession();
			JPluginHelper::importPlugin('community','plug_js_promote');
			$dispatcher = JDispatcher::getInstance();
			$results = $dispatcher->trigger( 'getGroupData', array($gid) );//Call the plugin and get the result

			$imgname = $results[0][0]->thumb;
			$buildadsession->set('ad_image', $imgname);
    		$ad_data=array();
    		$ad_data[0]['ad_url1'] = 'http';
			$url2=str_replace('http://','',JUri::base().'index.php?option=com_community&view=groups&task=viewgroup&groupid='.$input->get('groupid',0,'INT').'&Itemid='.$input->get('Itemid',0,'INT'));
    		$ad_data[1]['ad_url2'] = $url2;
    		$ad_data[2]['ad_title']= $results[0][0]->name;
			$ad_data[3]['ad_body']= $results[0][0]->description;
			$buildadsession->set('ad_data', $ad_data);
			$mainframe->redirect('index.php?option=com_socialads&view=buildad&Itemid='.$input->get('Itemid',0,'INT').'&frm=editad');
		}

	function promote_event()
	{
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
			$eventid = $input->get('eventid',0,'INT');
			global $mainframe;
			$mainframe = JFactory::getApplication();
		  	$buildadsession = JFactory::getSession();
			JPluginHelper::importPlugin('community','plug_js_promote');
			$dispatcher = JDispatcher::getInstance();
			$results1 = $dispatcher->trigger( 'getEventData', array($eventid) );//Call the plugin and get the result
			$imgname = $results1[0][0]->thumb;
			$buildadsession->set('ad_image', $imgname);
    		$ad_data=array();
   			$ad_data[0]['ad_url1'] = 'http';
			$url2=str_replace('http://','',JUri::base().'index.php?option=com_community&view=events&task=viewevent&eventid='.$input->get('eventid',0,'INT').'&Itemid='.$input->get('Itemid',0,'INT'));
    		$ad_data[1]['ad_url2'] = $url2;
    		$ad_data[2]['ad_title']= $results1[0][0]->title;
			$string_desc = strip_tags($results1[0][0]->description);
			$ad_data[3]['ad_body']= $string_desc;

			$buildadsession->set('ad_data', $ad_data);
			$mainframe->redirect('index.php?option=com_socialads&view=buildad&Itemid='.$input->get('Itemid',0,'INT').'&frm=editad');
	}

	function savepublish()
	{
		$user = JFactory::getUser();
		$db= JFactory::getDBO();
		if(!$user->id)
		{
			echo Jtext::_('BUILD_LOGIN');
			jexit();
		}
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$id = $input->get('id',0,'INT');
		$status = $input->get('status',0,'INT');

		if($status==1)
		{
			$query = "UPDATE #__ad_data SET ad_published =".$status." WHERE ad_id =".$id." AND ad_creator=".$user->id." AND (ad_credits_balance>0 OR ad_alternative=0 OR ad_noexpiry=0)";
		}
		else
		{
			$query = "UPDATE #__ad_data SET ad_published =".$status." WHERE ad_id =".$id." AND ad_creator=".$user->id;
		}
		$db->setQuery($query);
		if($db->execute())
		{
			if($status==0)
				echo JText::_('AD_STATUS_UNPUBLISHED');
			else
				echo JText::_('AD_STATUS_PUBLISHED');
		}
		jexit();
	}


	function getzones()
	{
		$db= JFactory::getDBO();
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$typ = $input->get('ad_type','','STRING');
		if($typ == "affiliate")
			$condi = "ad_type LIKE '%".$typ."%'";
		else
			$condi = "ad_type = '".$typ."'";
		$query="SELECT id,zone_name FROM #__ad_zone WHERE ad_type LIKE '%|".$typ."|%' AND published = 1";
		$db->setQuery($query);
		$ad_zones = $db->loadObjectList();
//print_r($ad_zones);
		$query="SELECT params FROM #__modules WHERE published = 1 AND module LIKE '%mod_socialads%'";
		$db->setQuery($query);
		$params=$db->loadObjectList();
		$module = array();

		foreach($params as $params)
		{
			$params1 = str_replace('"','',$params->params);
			if(JVERSION >= '1.6.0')
				$single= explode(",", $params1);
			else
				$single= explode("\n", $params1);
			foreach ($single as $single)
			{
				if(JVERSION >= '1.6.0')
					$name= explode(":", $single);
				else
					$name = explode("=", $single);

				if($name[0] == 'zone')
					$module[] = $name[1];
			}
		}

		$z = array ();
		foreach($ad_zones as $zone){
			if(in_array($zone->id,$module)){
				$z[] = array("zone_id"=>$zone->id,
						"zone_name"=>$zone->zone_name);
			}
		}
		echo json_encode($z);
		jexit();
	}

	function getZonesdata()
	{
		require(JPATH_SITE.DS."components".DS."com_socialads".DS."defines.php");
		$db= JFactory::getDBO();
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$typ = $input->get('zone_id',0,'INT');
		$query="SELECT * FROM #__ad_zone WHERE id ='".$typ."' AND published = 1";
		$db->setQuery($query);
		$zone_data = $db->loadObjectList();

		$z = array ();
		foreach($zone_data as $zone){
			if($zone->ad_type == '|img|'  || $zone->ad_type=='|img||affiliate|')
			{

					$zone->max_title = $title_char;
			}

		$layouts = explode ('|',$zone->layout);
		$z[] = array("zone_id"=>$zone->id,
					"zone_name"=>$zone->zone_name,
					"max_title"=>$zone->max_title,
					"max_des"=>$zone->max_des,
					"img_width"=>$zone->img_width,
					"img_height"=>$zone->img_height,
					"per_click"=>$zone->per_click,
					"per_imp"=>$zone->per_imp,
					"per_day"=>$zone->per_day,
					"layout"=>$layouts,
					"base"=>JUri::Root(),
			);
		}

		echo json_encode($z);
		jexit();
	}

	function changelayout()
	{
	    require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$adseen = 2;
		$document = JFactory::getDocument();
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$layout = $input->get('layout');

		$addata = new stdClass;
		$addata->ad_title = $input->get('title','','STRING');

		if ($addata->ad_title == '')
			$addata->ad_title = JText::_("EXAMPLEAD");

		$addata->ad_body = $input->get('body','','STRING');

		if ($addata->ad_body == '')
			$addata->ad_body =  JText::_('SAMPLEAD');

		//START changed by manoj 2.7.5b2
		$addata->link = '#';
		$addata->ignore = "";
		$upload_area = 'id="upload_area"';
		$plugin = 'plug_'.$layout;

		$addata->ad_adtype_selected = $input->get('adtype');
		$addata->adzone= $input->get('adzone');/*added by manoj 2.7.5 stable*/
		$addata->ad_image='';
		$adHtmlTyped='';
		if($addata->ad_adtype_selected=='text')//if it's 'text ad' don't set image
		{
			//$adHtmlTyped='<div class="preview-bodytext '.$layout.'_ad_prev_third">';
			$adHtmlTyped.=$addata->ad_body;
			//$adHtmlTyped.='</div>';
		}else{
			$addata->ad_image = $input->get('img','','STRING');
			$addata->ad_image = str_replace(JUri::base(),'',$addata->ad_image);
			if($addata->ad_image == '')
				$addata->ad_image= 'components/com_socialads/images/adimage.jpg';
		}
		require_once(JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');
		$adshelper=new adRetriever();
		$adHtmlTyped= $adshelper->getAdHTMLByMedia($upload_area,$addata->ad_image,$addata->ad_body,$addata->link,$layout,$track=0,$addata->adzone);
		//END changed by manoj 2.7.5b2

		//$document->addStyleSheet(JUri::base().'components/com_socialads/css/helper.css');

		//added by sagar
		if(JVERSION >= '1.6.0'){
			$layout = JPATH_SITE.DS.'plugins'.DS.'socialadslayout'.DS.$plugin.DS.$plugin.DS.'layout.php';
			$document->addStyleSheet(JUri::base().'plugins/socialadslayout/'.$plugin.'/'.$plugin.'/layout.css');
			$css = JUri::base().'plugins/socialadslayout/'.$plugin.'/'.$plugin.'/layout.css';
		}
		else{
			$layout = JPATH_SITE.DS.'plugins'.DS.'socialadslayout'.DS.$plugin.DS.'layout.php';
			$document->addStyleSheet(JUri::base().'plugins/socialadslayout/'.$plugin.'/layout.css');
			$css = JUri::base().'plugins/socialadslayout/'.$plugin.'/layout.css';
		}
		//added by sagar
		$document->addScript(JUri::base().'components/com_socialads/js/helper.js');

		if(JFile::exists($layout))
		{
			ob_start();
			include($layout);
	        $html = ob_get_contents();
       		ob_end_clean();
        }
        else
      	{
		  	$html='<!--div for preview ad-image-->
			<div><a id="preview-title" class="preview-title-lnk" href="#">';
				if ($addata->ad_title != '')
					$html .= ''.$addata->ad_title;
				else
					$html .= ''. JText::_("EXAMPLEAD");

			$html .='</a>
			</div>
			<!--div for preview ad-image-->
			<div id="upload_area" >';
			if($addata->ad_image != ''){
				$html .='<img  src="'.$addata->ad_image.'">';
			}
			else{
				$html .='<img  src="'.JUri::Root().'components/com_socialads/images/adimage.jpg">';
			}
			$html .='
			</div>
			<!--div for preview ad-bodytext-->
			<div id="preview-bodytext">';
			if ($addata->ad_body != '')
				$html .=''. $addata->ad_body;
			else
				$html .= ''. JText::_('SAMPLEAD');
			$html .='</div>';
      	}

      	//@TODO
      	//$js should be sent out only for video ads and flash ads
      	$js='
			flowplayer("div.vid_ad_preview",
			{
				src:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.10.swf",
				wmode:"opaque"
			},
			{
				canvas: {
					backgroundColor:"#000000",
					width:300,
					height:300
				},


				//default settings for the play button
				play: {
					opacity: 0.0,
				 	label: null,
				 	replayLabel: null,
				 	fadeSpeed: 500,
				 	rotateSpeed: 50
				},

				plugins:{

					controls: {
						url:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer.controls-3.2.10.swf",
						height:25,
						timeColor: "#980118",
						all: false,
						play: true,
						scrubber: true,
						volume: true,
						time: false,
						mute: true,
						progressColor: "#FF0000",
						bufferColor: "#669900",
						volumeColor: "#FF0000"
					}

				}
			});
		';

		$z = array(
		"html"=>$html,
		"css"=>$css,
		"js"=>$js
		);
		echo json_encode($z);
		jexit();
	}

	function getpoints()
	{
		$user = JFactory::getUser();
		$db= JFactory::getDBO();
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$count = -1;
		$plugin = JPluginHelper::getPlugin( 'payment',$input->get('plugin_name','','STRING'));
		$pluginParams =json_decode( $plugin->params);

		switch($input->get('plugin_name','','STRING'))
		{
			case 'jomsocialpoints':
				$query="SELECT points FROM #__community_users WHERE userid=".$user->id;
				$db->setQuery($query);
				$count = $db->loadResult();
				$conversion1=$pluginParams->conversion;
				echo $count."|".$conversion1;
			break;
// AlphaUserPoints Plugin Payment
		case 'alphauserpoints':
			$query="SELECT points FROM #__alpha_userpoints where userid=".$user->id;
			$db->setQuery($query);
			$count = $db->loadResult();
			$conversion2=$pluginParams->conversion;
			echo $count."|".$conversion2;
		break;

			default: echo $count;
		}
		jexit();
	}

	function getcoupon($c_code='')
	{
		$user = JFactory::getUser();
		$db= JFactory::getDBO();
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
        if(empty($c_code))
        {
			$c_code = $input->get('coupon_code','','STRING');
		}
		$count = '';
		$model = $this->getModel('showad');
		$count = $model->getcoupon($c_code);
		$retdata = '';

		if($count)
		{
			$c[] = array("value"=>$count[0]->value,
			"val_type"=>$count[0]->val_type);
			$retdata = json_encode($c);
		}
		else
		{
			$retdata = 0;
		}

		if(empty($c_code))
        {
			//$c_code = $input->get('coupon_code','','STRING');
			echo $retdata;
		}
		else
		{
			echo $retdata;
		}
		jexit();
	}
	function sa_applycoupon()
	{
		$user = JFactory::getUser();
		$socialadsController = new socialadsController;
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$c_code = $input->get('coupon_code','','STRING');

		$data = $socialadsController->getcoupon($c_code);
		$count = '';
		$model = $this->getModel('showad');
		$count = $model->getcoupon();

			if($count){
			$c[] = array("value"=>$count[0]->value,
			"val_type"=>$count[0]->val_type);
		echo json_encode($c);
			}
			else
				echo 0;
		jexit();
	}

	function gatewayCallBack()
	{
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$gateway = $input->get('value');
		if($gateway)
		{
			JPluginHelper::importPlugin( 'socialadspayment' );
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger( $gateway.'_onGatewayReturn');
		}
	}


	function ignoreAd()
	{
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$database = JFactory::getDBO();
		$my = JFactory::getUser();

		$adid=$input->get('ad_ignore_id',0,'INT');
		$fdid=$input->get('ad_feedback','','STRING');
		if($fdid)
		{
			//Query to find if logged in user has already blocked the same user...
			$qry1= "UPDATE #__ad_ignore SET ad_feedback ='".$fdid."' WHERE userid=".$my->id." AND adid=".$adid;
			$database->setQuery($qry1);
			$database->execute();
		}
		elseif($adid) {
			//Query to find if logged in user has already blocked the same user...
			$qry1= "SELECT userid, adid FROM #__ad_ignore WHERE userid=".$my->id." AND adid=".$adid;
			$database->setQuery($qry1);
			$existing=$database->loadObjectList();

			if(!$existing) {
				$data = new stdClass;
				$data->id = NULL;
				$data->userid = $my->id;
				$data->adid = $adid;
			 if (!$database->insertObject('#__ad_ignore', $data ))
				echo "0";
			 else
				echo "1";
			}
		}

	}

	function undoignoreAd()
	{
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		$input=JFactory::getApplication()->input;
		$adid=$input->get('ad_ignore_id',0,'INT');
		if($adid)
		{
			//Query to find if logged in user has already blocked the same user...
			$qry1= "DELETE FROM #__ad_ignore WHERE userid=".$my->id." AND adid=".$adid;
			$database->setQuery($qry1);
			$database->execute();
		}
	}

	//Payment Return
	function processpayment()
	{
		$input=JFactory::getApplication()->input;
		$ptype = $input->get('ptype');
		$paction = $input->get('paction');
		JPluginHelper::importPlugin( 'socialads', $ptype );
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('_'.$paction);
	}

	//weekly cron mail
	function sendStatsEmail()
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$input=JFactory::getApplication()->input;
		if($socialads_config['week_mail'] ){
			$linechart_img='';

			$pkey = $input->get('pkey');
			if($pkey!=$socialads_config['cron_key'])
			{
				echo JText::_("CRON_KEY_MSG");
				return;
			}
			echo JText::_('COM_SOCIALADS_WEEK_STATMAIL_START');  echo '<br>';echo '<br>';
			$model = $this->getModel('adsummary');
			$adcreators=$model->getAdcreators();
			foreach($adcreators as $userid)
			{
				$statsforpie=$model->statsforpie_mail($userid);
				if($statsforpie)
				{
					$userinfo['userid']=$userid;
					$userinform=$model->getuserdetails($userid,'username,name,email');
					$body=$socialads_config['intro_msg'];// replace the intro text frm config
					//for older than 2.9.6
					$find 		= array ('{username}','{name}');
					$replace	= array($userinform[0]->username,$userinform[0]->name);
					$body		= str_replace($find, $replace, $body);
					$find 		= array ('[SEND_TO_USERNAME]','[SEND_TO_NAME]');
					$replace	= array($userinform[0]->username,$userinform[0]->name);
					$body		= str_replace($find, $replace, $body);
					$userinfo['email']=$userinform[0]->email;

					foreach($statsforpie as $statsforpie_ad)
					{
						if(($statsforpie_ad[0][0]->value) or ($statsforpie_ad[1][0]->value))
						{
	/*						$linechart=$model->getmaillinechartdata($statsforpie_ad[2]);
							if($linechart)
							$linechart_img=$this->makelinechart($linechart);
	*/
							$body.=$this->sendStatsEmail_body($statsforpie_ad,$userinfo);
						}
					}
					$body = nl2br($body);
					$this->sendStatsEmail_user($body,$userinfo);
				}
			}
		}
	}

	/*function not used in v2.6*/
	function makelinechart($statsforbar)
	{
	//print_r($statsforbar);die;

	$imprs=0;
	$clicks=0;
	$max_invite=100;
	$cmax_invite=100;
	$yscale="";
	$titlebar="";
	$daystring="";
	$finalstats_date=array();
	$finalstats_clicks=array();
	$finalstats_imprs=array();
	$day_str_final='';
	$total_no_clicks=0;
	$total_no_impressions=0;
	$click_through_ratio=0;
	$finalstats_date1=array();
	$finalstats_date2=array();
	$barchart='';
	$fromDate=date('Y-m-d', strtotime(date('Y-m-d').' - 7 days'));;
	$toDate=date('Y-m-d');

	$dateMonthYearArr = array();
	$fromDateSTR = strtotime($fromDate);
	$toDateSTR = strtotime($toDate);

	if(empty($statsforbar[0]) && empty($statsforbar[1]))
	{
	  return;
	}
   else
   	{
			if(!empty($statsforbar[1]))
			{

			$cnt=0;
			for ($currentDateSTR = $fromDateSTR; $currentDateSTR <= $toDateSTR; $currentDateSTR += (60 * 60 * 24))
			{
				// use date() and $currentDateSTR to format the dates in between
				$currentDateStr = date("Y-m-d",$currentDateSTR);
				$finalstats_date1[]=date("d",$currentDateSTR);
				$finalstats_clicks[$cnt]=0;
				foreach($statsforbar[1] as $cur_statsforbar)
				{
						$cur_date=$cur_statsforbar->date;
						if($cur_date==$currentDateStr )
						{
							$finalstats_clicks[$cnt]=0+$cur_statsforbar->value;

					 	}
				}
				$cnt++;
			}

			}//if ststsforbar is not empty ends

	      if(!empty($statsforbar[0]))
	     	{

	     	$cnt=0;
			for ($currentDateSTR = $fromDateSTR; $currentDateSTR <= $toDateSTR; $currentDateSTR += (60 * 60 * 24))
			{
				$currentDateStr = date("Y-m-d",$currentDateSTR);
				$finalstats_date2[]=date("d",$currentDateSTR);
				$finalstats_imprs[$cnt]=0;
				foreach($statsforbar[0] as $cur_statsforbar)
				{
						$cur_date=$cur_statsforbar->date;
						if($cur_date==$currentDateStr )
						{
							$finalstats_imprs[$cnt]=0+$cur_statsforbar->value;
					 	}
				}
				$cnt++;
			}

			}
			else
				$imprs=0;

				if(count($finalstats_date1)>count($finalstats_date2))
				$finalstats_date=$finalstats_date1;
				else
				$finalstats_date=$finalstats_date2;
				$fdays = $finalstats_date;

				$daystring = implode('|',$fdays);

			if($finalstats_imprs)
			$imprs=implode(",",$finalstats_imprs);
			//echo "\n-----------";
			if($finalstats_clicks)
			$clicks=implode(",",$finalstats_clicks);
			$max_invite=max($finalstats_imprs);
     	if($max_invite<20)
		{
			 $yscale="0||2||4||6||8||10||12||14||16||18||20";
	    $max_invite=20;
		}
		else if($max_invite<50)
		{
			 $yscale="0||5||10||15||20||25||30||35||40||45||50";
	    $max_invite=50;
		}
	  	else if($max_invite<100)
	   {
	   $yscale="0||10||20||30||40||50||60||70||80||90||100";
	    $max_invite=100;
	   }
	   else
	   {
	   		if($max_invite%50!=0)
	   		{
	     		for($i=0; $i<50; $i++)
	     		{
	        		$max_invite=$max_invite+$i;
	        		if($max_invite%50==0)
	         		break;
	        		$max_invite=max($invted);
	     		}
	   		}
	   		$array_len=$max_invite/50;
	   		$yscale=array();
	   		for($i=0;$i<=$array_len;$i++)
	   		{
	       		$yscale[$i]=50*$i;
	   		}
	   		$yscale=implode('||',$yscale);
	  	}
		$titlebar = JText::_('STATISTICS+FROM');
			$url= "http://1.chart.apis.google.com/chart?chs=500x300&amp;cht=lc&amp;chbh=a,25&amp;chd=t:".$imprs."|".$clicks."&amp;chxt=x,y&amp;chxr=0,0,200&amp;chds=0,100,0,".$max_invite."&amp;chxl=1:|".$yscale."|0:|". $daystring."|";//die;
//die;
			return $url;

   	}
	}
	function sendStatsEmail_body($statsforpie,$userinfo)
	{
		global $mainframe;
		$socialadshelper=new socialadshelper();
		$email=$userinfo['email'];
		$adid=$statsforpie[2];
		$clicks_pie=$imprs_pie = $total_no_ads=0;
		$mainframe = JFactory::getApplication();
		$ad_data=$socialadshelper->getAdInfo($adid,'ad_title');

		$ad_title=($ad_data[0]->ad_title!= '') ? JText::_("PERIDIC_STATS_ADTIT").' <b>"'.$ad_data[0]->ad_title.'"</b>' : JText::_("PERIDIC_STATS_ADID").' : <b>'.$adid.'</b>';
		$itemid = $socialadshelper->getSocialadsItemid('buildad');
	 	$edit_ad_link  = JRoute::_(JUri::base()."index.php?option=com_socialads&view=buildad&adid=".$adid."&Itemid=".$itemid);
		if(isset($statsforpie[1][0]->value))
		{

			$clicks_pie= $statsforpie[1][0]->value;
	  	}

		if(isset($statsforpie[0][0]->value))
		{

			$imprs_pie = $statsforpie[0][0]->value;

		}

		if($clicks_pie  || $imprs_pie)
		{
			$cl_impr=$imprs_pie.','.$clicks_pie;
			$chco='7777CC|76A4FB';
			$chdl='clicks|Impressions';
			$url="http://0.chart.apis.google.com/chart?chs=300x150&cht=p3&chd=t:".$cl_impr."&chdl=Impressions|Clicks";//&amp;chco=".$chco;
			//socialadsController::getlinr

		}
		$CTR=0.00;
		if($clicks_pie and $imprs_pie)
		$CTR=number_format($clicks_pie/$imprs_pie,2);

		$body=JText::_('PERIDIC_STATS_BODY');
		$timestamp = strtotime("-7 days");
		$find=array('[ADTITLE]','[STARTDATE]','[ENDDATE]','[TOTAL_IMPRS]','[TOTAL_CLICKS]','[CLICK_TO_IMPRS]','[STAT_CHART]',
						'[AD_EIDT_LINK]');
		$replace	= array($ad_title,strftime("%d-%m-%Y",$timestamp),date('d-m-Y'),$imprs_pie,$clicks_pie,$CTR,$url,$edit_ad_link);
		$body		= str_replace($find, $replace, $body);
		if(!$ad_title)
		{
			$body		= str_replace('Ad Title', '', $body);
		}

			$body = nl2br($body);
		return $body;

			//die;
	}
	function sendStatsEmail_user($body,$userinfo)
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$sitename='';
		$subject = JText::_('PERIDIC_STATS_SUBJECT');
		$sitename = $mainframe->getCfg('sitename');
		$find 		= array ('[SITENAME]');
		$replace	= array($sitename);
		$body		= str_replace($find, $replace, $body);
		$subject		= str_replace($find, $replace, $subject);
		$email=$userinfo['email'];
		$mainframe = JFactory::getApplication();
		$from = $mainframe->getCfg('mailfrom');
		$fromname = $mainframe->getCfg('fromname');
		$recipient =$email;
		$mode = 1;
		$cc = null;
		$bcc = null;
		$bcc = null;
		$attachment = null;
		$replyto = null;
		$replytoname = null;

		$return=JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
		if(isset($return->code) && $return->code==500)
		{
			echo JText::sprintf('COM_SOCIALADS_MAIL_SENT_FAIL',$recipient);  echo '<br>';
		}
		elseif($return)
		{
			echo JText::sprintf('COM_SOCIALADS_MAIL_SENT_SUCCESS',$recipient);  echo '<br>';
		}
		//die;
		return;
	}


	function getImpCount($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__ad_stats WHERE ad_id=$id AND display_type=0 AND time between SUBDATE(NOW(), 7) AND NOW()";
		$db->setQuery($query);
		$cnt= $db->loadResult();
		return $cnt;
	}
	//click count
	function getClickCount($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__ad_stats WHERE ad_id=$id AND display_type=1 AND time between SUBDATE(NOW(), 7) AND NOW()";
		$db->setQuery($query);
		$cnt= $db->loadResult();
		return $cnt;
	}

 function statsforbar($ad_id) {

	 	$db=JFactory::getDBO();
	 		// for graph
			$j=0;
			$d=0;

			$db	= JFactory::getDBO();

			$day = date('d');
			$month = date('m');
			$year = date('Y');
			$statistics = array();
			$backdate = date('Y-m-d', strtotime(date('Y-m-d').' - 7 days'));
			$query = " SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month FROM #__ad_stats WHERE display_type = 0 AND ad_id = $ad_id  GROUP BY YEAR(time), MONTH(time), DAY(time)";

			$db->setQuery($query);
			$statistics[] = $db->loadObjectList();


			$query = "SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month FROM #__ad_stats WHERE display_type = 1 AND ad_id =$ad_id GROUP BY YEAR(time), MONTH(time), DAY(time)";
				$db->setQuery($query);

			$statistics[] = $db->loadObjectList();
			return $statistics;

	}//function statsforbar ends here

	 function statsforpie($ad_id)
	 {

	 	$db=JFactory::getDBO();
	 		// for graph
			$day = date('d');
			$month = date('m');
			$year = date('Y');
			$statsforpie = array();
			$backdate = date('Y-m-d', strtotime(date('Y-m-d').' - 7 days'));

			$query = " SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month FROM #__ad_stats WHERE display_type = 0 AND ad_id = $ad_id  GROUP BY YEAR(time), MONTH(time)";

				$db->setQuery($query);
				$statsforpie[] = $db->loadObjectList();


				 $query = "SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month FROM #__ad_stats WHERE display_type = 1 AND ad_id =$ad_id GROUP BY YEAR(time), MONTH(time)";
				$db->setQuery($query);

		$statsforpie[] = $db->loadObjectList();
		return $statsforpie;
	 }

 function deletead()
 {
 	  global $mainframe;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
 	  $database =$db= JFactory::getDBO();
  	$my = JFactory::getUser();
  	$query = "DELETE FROM #__ad_data WHERE ad_creator = $my->id AND ad_id =".$input->get('adid',0,'INT');
  	$database->setQuery($query);
  	$database->execute();
  	if(!$database->stderr){
			$query = "SHOW TABLES LIKE '#__ad_fields';";
				$db->setQuery($query);
				$fields = $db->loadResult();
				if($fields)
				{
					//delete social targeting of ad
					$query = "DELETE FROM #__ad_fields WHERE adfield_ad_id=".$input->get('adid',0,'INT');
					$db->setQuery($query);
					$db->execute();
				}

			//delete contextual targeting of ad
			$query = "DELETE FROM #__ad_contextual_target WHERE ad_id=".$input->get('adid',0,'INT');
			$db->setQuery($query);
			$db->execute();
			//delete geo targeting of ad
			$query = "DELETE FROM #__ad_geo_target WHERE ad_id=".$input->get('adid',0,'INT');
			$db->setQuery($query);
			$db->execute();
			//delete stats of ad
			$query = "DELETE FROM #__ad_stats WHERE ad_id =".$input->get('adid',0,'INT');
			$database->setQuery($query);
			$database->execute();
			//delete ignores of ads
			$query = "DELETE FROM #__ad_ignore WHERE adid=".$input->get('adid',0,'INT');
			$db->setQuery($query);
			$db->execute();
			//delete payments of ad
			$query = "DELETE FROM #__ad_payment_info WHERE ad_id=".$input->get('adid',0,'INT');
			$db->setQuery($query);
			$db->execute();
	}
	$msg=JText::_("SA_DEL_AD");
	$mainframe->redirect('index.php?option=com_socialads&view=managead&Itemid='.$input->get('Itemid',0,'INT'),$msg);
 }

 function multipledeleteads()
 {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$post	= JRequest::get('post');
		require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'helper.php');
		$itemid = socialadshelper::getSocialadsItemid('managead');
		$adid=$post['cid'];
		$img_list=array();
 	 	if(!$adid)
		$mainframe->redirect('index.php?option=com_socialads&view=managead&Itemid='.$itemid);
		$db=JFactory::getDBO();

		if(count($adid)>1)
		{
			$adid_str=implode(',',$adid);
			$query1 = "SELECT ad_image FROM #__ad_data WHERE ad_image<>'' AND "." ad_id IN (".$adid_str.")";
  			$db->setQuery($query1);
			$img_list = $db->loadObjectList();
			//delete ads data
		 	$query = "DELETE FROM #__ad_data where ad_id IN (".$adid_str.")";
			$db->setQuery( $query );
	            if (!$db->execute()) {
	                    $this->setError( $this->_db->getErrorMsg() );
	                    return false;
	            }

			$query ='';
			if(!$db->stderr){
				$query = "SHOW TABLES LIKE '#__ad_fields';";
				$db->setQuery($query);
				$fields = $db->loadResult();
				if($fields)
				{
					//delete social targeting of ads
					$query = "DELETE FROM #__ad_fields WHERE adfield_ad_id IN(".$adid_str.")";
					$db->setQuery($query);
					$db->execute();
				}
				//delete contextual targeting of ad
				$query = "DELETE FROM #__ad_contextual_target WHERE ad_id IN(".$adid_str.")";
				$db->setQuery($query);
				$db->execute();
				//delete geo targeting of ads
				$query = "DELETE FROM #__ad_geo_target WHERE ad_id IN(".$adid_str.")";
				$db->setQuery($query);
				$db->execute();
				//delete stats of ads
				$query = "DELETE FROM #__ad_stats WHERE ad_id IN(".$adid_str.")";
				$db->setQuery($query);
				$db->execute();
				//delete ignores of ads
				$query = "DELETE FROM #__ad_ignore WHERE adid IN(".$adid_str.")";
				$db->setQuery($query);
				$db->execute();
				//delete payments of ad
				$query = "DELETE FROM #__ad_payment_info WHERE ad_id IN(".$adid_str.")";
				$db->setQuery($query);
				$db->execute();
			}
		}
		else
		{
			$query1 = "SELECT ad_image FROM #__ad_data WHERE ad_image<>'' AND  ad_id=".$adid[0];
			$db->setQuery($query1);
			$img_list = $db->loadObjectList();

			$query = "DELETE FROM #__ad_data where ad_id=$adid[0]";
			$db->setQuery( $query );
	            if (!$db->execute()) {
	                    $this->setError( $this->_db->getErrorMsg() );
	                    return false;
	            }

			$query ='';
			if(!$db->getErrorMsg()){
				$query = "SHOW TABLES LIKE '#__ad_fields';";
				$db->setQuery($query);
				$fields = $db->loadResult();
				if($fields)
				{
					//delete social targeting of ads
					$query = "DELETE FROM #__ad_fields  WHERE ad_id=$adid[0]";
					$db->setQuery($query);
					$db->execute();
				}
				//delete geo targeting of ad
				$query = "DELETE FROM #__ad_geo_target WHERE ad_id=$adid[0]";
				$db->setQuery($query);
				$db->execute();
				//delete stats of ad
				$query = "DELETE FROM #__ad_stats WHERE ad_id=$adid[0]";
				$db->setQuery($query);
				$db->execute();
				//delete ignores of ads
				$query = "DELETE FROM #__ad_ignore WHERE adid=$adid[0]";
				$db->setQuery($query);
				$db->execute();
				//delete payments of ad
				$query = "DELETE FROM #__ad_payment_info WHERE ad_id=$adid[0]";
				$db->setQuery($query);
				$db->execute();
			}
		}

		$msg=JText::_("SA_DEL_AD");
		if(!$img_list)
			$mainframe->redirect('index.php?option=com_socialads&view=managead&Itemid='.$itemid,$msg);
		$count=0;
		foreach($img_list as $img_to_del)
		{
					$img_to_del=JPATH_SITE .DS.$img_to_del->ad_image;
					if($img_to_del){
						if(!JFile::delete($img_to_del))
						{
							echo JText::_('SA_IMG_DEL_SUC')."[".$img_to_del."]";
							echo "<br>";
						}
						else
						{
							$count++;
							echo "<br>";
							echo JText::_('SA_IMG_DEL_FAIL')."[".$img_to_del."]";
						}
					}
				echo "<br>";
				echo  JText::_('SA_IMG_DEL_COUNT')." ".$count;
		}

  	$mainframe->redirect('index.php?option=com_socialads&view=managead&Itemid='.$itemid,$msg);
 }
		function add()
		{
			require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');
			require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'helper.php');
			$itemid = socialadshelper::getSocialadsItemid('buildad');
			global $mainframe;
			$mainframe = JFactory::getApplication();
			$mainframe->redirect('index.php?option=com_socialads&view=buildad&Itemid='.$itemid);
		}

		/*called from within the SA code to delete ads*/
		function delimages(){
			require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
			JRequest::setVar('pkey',$socialads_config['cron_key']);
			$this->removeimages(1);
		}

		function removeimages_call(){
			$this->removeimages(0);
		}
		/*task to delete the not used images from the folder images/socialads/*/
		/*changed by manoj in 2.7.5 beta 3 for video and flash ads*/
		function removeimages($called_from=0)
		{
			$input=JFactory::getApplication()->input;
			if($called_from != 0)
			{
				require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
				$pkey = $input->get('pkey');
				if($pkey!=$socialads_config['cron_key'])
				{
					echo JText::_("CRON_KEY_MSG");
					return;
				}
			}

			$images_del=array();
			$images=array();
			$current_files=array();
			$results=array();
			$database = JFactory::getDBO();
			$match = $database->escape('images'.DS.'socialads'.DS);
			$query = "SELECT REPLACE(ad_image, '{$match}','') as ad_image  FROM #__ad_data WHERE ad_image<>''";
			$database->setQuery($query);
			$images_del = $database->loadColumn();

			/*for($i=0; $i<count($images_del);$i++)
			{
				$images_del[$i]=JPATH_SITE.DS.$images_del[$i];
			}*/

			//NOTE
			//JFolder::files(folder,filter,recursion_depth,skip_array);
			//we can skip the "frames" folder SAFELY here, it is used for gif resizing
			$current_files=JFolder::files(JPATH_SITE.DS.'images'.DS.'socialads','',0,0,array('frames','index.html'));
			$no_files_del=0;


			if(count($current_files)>count($images_del))
			{
				$results = array_diff($current_files, $images_del);
				if($results)
				{?>
					<div class="alert alert-info">
						<?php echo JText::_("COM_SA_UNUSED_IMG_LIST");?>
					</div>
					<?php
					foreach($results as $img_to_del)
					{
						if($img_to_del)
						if(!JFile::delete(JPATH_SITE.DS.'images'.DS.'socialads'.DS.$img_to_del))
						{
							if($called_from==0)
							{
								echo "[".$img_to_del."] ". JText::_("FILE_DEL_FAL");
								echo "<br>";
							}
						}
						else
						{
							if($called_from==0)
							{
								echo "<br>";
								echo "[".$img_to_del."] ". JText::_("FILE_DEL_SUC");
								$no_files_del++;
							}
						}
					}
				}
				else
				{

					if($called_from==0)
					{
						?>
						<div class="alert alert-info">
							<?php
						echo "<br>";
						echo JText::_("NO_FILE_DEL");  ?>
						</div >
						<?php
					}
				}
			}
			else
			{

				if($called_from==0)
				{
					?>
						<div class="alert alert-info">
							<?php
						echo "<br>";
						echo JText::_("NO_FILE_DEL");  ?>
						</div >
						<?php
				}
			}
			if($called_from==0)
			{

				if($no_files_del)
				{
					?>
						<div class="alert alert-success">
							<?php
						echo "<br>";
						echo JText::_("NUMBER_OF_FILE_DEL").":".$no_files_del; ?>
						</div >
						<?php

				}
			}
			return;
		}

	/*task for archiving stats*/
	function archive_stats(){
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
	$input=JFactory::getApplication()->input;
		if($socialads_config['arch_stats'])
		{
			$pkey = $input->get('pkey');
			if($pkey!=$socialads_config['cron_key'])
			{
				echo JText::_("CRON_KEY_MSG");
				return;
			}

			$log = array();
			$log[] = JText::_("ARCH_STATS_START");
//$socialads_config['arch_stats_day'] = 89;
		 	$db=JFactory::getDBO();
			$backdate = date('Y-m-d  h:m:s', strtotime(date('Y-m-d h:m:s').' - '.$socialads_config['arch_stats_day'].' days'));
			$log[] = JText::sprintf("FRM_TO",$backdate);
			$query = " SELECT id,ad_id,display_type,time FROM #__ad_stats WHERE time < '".$backdate."' ORDER BY time";
			$db->setQuery($query);
			$rawstats = $db->loadObjectList();
			$log[] = JText::sprintf("TOT_ENTRY",count($rawstats));
			if(count($rawstats)){
				$date = date('Y-m-d', strtotime($rawstats[0]->time));
				$final_stats =  array();

				foreach($rawstats as $raw)
				{
					$current_date = date('Y-m-d', strtotime($raw->time));
					if($date != $current_date)
					{
						$date = date('Y-m-d', strtotime($raw->time ));
					}
			/* 0=imprs;1= clks; */
					if($raw->display_type == '0'){
						if(isset($final_stats[$date][$raw->ad_id]['imprs']))
							$final_stats[$date][$raw->ad_id]['imprs']++;
						else
							$final_stats[$date][$raw->ad_id]['imprs'] = 1;
					}
					elseif($raw->display_type == '1'){
						if(isset($final_stats[$date][$raw->ad_id]['clks']))
							$final_stats[$date][$raw->ad_id]['clks']++;
						else
							$final_stats[$date][$raw->ad_id]['clks'] = 1;
					}
				}
			/*jugad start
				$query= "TRUNCATE TABLE `#__ad_archive_stats`";
				$db->setQuery($query);
				$db->execute();
			//jugad ends*/
			//			print_r($final_stats);
				$stats_obj = new stdClass();
				$cnt = 0;
				foreach ($final_stats as $date=>$stats){
					foreach ($stats as $id=>$v){
					$stats_obj = new stdClass();
						$stats_obj->ad_id = $id;
						$stats_obj->date = $date;
						if(isset($v['imprs']))
							$stats_obj->impression = $v['imprs'];
						if(isset($v['clks']))
							$stats_obj->click	 = $v['clks'];
						$cnt++;
						if(!$db->insertObject( '#__ad_archive_stats', $stats_obj, 'id' ))
						{
							echo $db->stderr();
							return false;
						}
					}
				}
				$log[] = JText::sprintf("REDUCE_TO",$cnt);

				$query = "DELETE FROM #__ad_stats WHERE time < '".$backdate."'";
				$db->setQuery( $query );
				if (!$db->execute()) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
				}
			}
			echo implode('<br/>',$log);
			$logfile_path = JPATH_SITE.DS."components".DS."com_socialads".DS."log".DS."archive_stats.txt";
			$old_log_content=file_get_contents($logfile_path);
			if($old_log_content||$old_log_content=='')
			{
				$file_log = implode("\n",  $log);
				$file_log = $old_log_content ."\n\n".$file_log ;
				JFile::write($logfile_path,$file_log);
			}
		}
	}
	//Payment Return

		/*single cron URL for running all the functions*/
	function sa_allfunc_cron(){
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$input=JFactory::getApplication()->input;
		$pkey = $input->get('pkey');
		if($pkey!=$socialads_config['cron_key'])
		{
			echo JText::_("CRON_KEY_MSG");
			return;
		}
		$func = $input->get('func');
		if($func)
			$this->$func();
		else{
			$funcs = array ('archive_stats','sendStatsEmail');	 /*add the function names you need to add here*/
			foreach ($funcs as $func){
			echo '<br>***************************************<br>';
				$this->$func();
			echo '<br>***************************************<br>';
			}
		}
	}


	function getbidvalue(){

		//print_r($_REQUEST['json']); // die('adasdasd');
		//$string=stripslashes($_REQUEST['json']);
		//print_r($string);
		//$jsonData = json_decode($string);
		//die('.....dsdfsdfsdfsdfsd');


		$z[] = array("ad_id"=>'98',
						"ad_bid"=>'5');
		$content = json_encode($z);
		echo $content;
		jexit();
 	}

function testbid(){
	require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');
	$result = adRetriever::sendbidvalue('51');

	//print_r($result);
	die('rererr');
}


	//delete campaign
/*	function deletecampaign()
	{

		$input=JFactory::getApplication()->input;
		$view=$input->get('view','campaign');
		$post =
		//Get some variables from the request
		$cid=$input->get('cid','', 'array');
		print_r($cid); die('delete');
		JArrayHelper::toInteger($cid);
		if($view=="campaign")
		{
			$model=$this->getModel('campaign');
			if($model->deletePayouts($cid)){
				$msg=JText::sprintf('Payout(s) deleted ',count($cid));
			}else{
				$msg=$model->getError();
			}
			$this->setRedirect('index.php?option=com_socialads&view=reports&layout=payouts',$msg);
		}

	}
*/
	 function deletecampaign()
       {
               $input=JFactory::getApplication()->input;
               $getdata=JRequest::get('post');
               //print_r($getdata); die('adsas');
               $view=$input->get('view','campaign');
               //Get some variables from the request
               $cid=$input->get('camp_name','', 'ARRAY');
              // print_r($cid); die('adsas');
               JArrayHelper::toInteger($cid);
               if($view=="campaign")
               {
                       $model=$this->getModel('campaign');
                       if($model->deletecampaign($cid)){
                               $msg=JText::sprintf('Campaign(s) deleted ',count($cid));
                       }else{
                               $msg=$model->getError();
                       }
               $this->setRedirect('index.php?option=com_socialads&view=campaign&list=list',$msg);
               }
       }

       function adreduceCredits()
       {

		 			/*$nodesModel=$this->getModel('nodes');
		//call model function - polling
			$polling=$nodesModel->polling();*/
		//output json response
			$input=JFactory::getApplication()->input;
			if($input->get('token')	==	'1234')
			{
				if($input->get('ad_id'))
				{
					$ad_id	=	$input->get('ad_id');
					require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');
					$adRetriever = new adRetriever();
					$statue_adcharge = $adRetriever->getad_status($ad_id);
					$adRetriever->reduceCredits($ad_id,0,$statue_adcharge['ad_charge']);

					header('Content-type: application/json');
					echo json_encode(1);
				}
			}
			else
				echo json_encode(-1);
				jexit();
		}

		function checkifadsavailable()
		{
			$input=JFactory::getApplication()->input;
			if($input->get('zone_id'))
			{

				require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');
				$adRetriever = new adRetriever();
				$check = $adRetriever->CheckifAdsavailable($input->get('ad_id','','INT'),$input->get('module_id','','STRING'),$input->get('zone_id','','INT'));
				header('Content-type: application/json');
				echo json_encode($check);
				jexit();
			}
		}
		function getAdHTML()
		{
			$input=JFactory::getApplication()->input;
			if($input->get('ad_id')){
				require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');
				require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

				$adRetriever = new adRetriever();

				$ad_data	=	new stdClass;
				$ad_data->ad_id	=	$input->get('ad_id','','INT');
				$module_id =	$input->get('module_id','','STRING');
				$cache = JFactory::getCache('mod_socialads');

				if ($socialads_config['enable_caching'] == 1 )
					$cache->setCaching( 1 );
				else
					$cache->setCaching( 0 );

				$addata  = $cache->call( array( $adRetriever, 'getAdDetails' ), $ad_data);
				$get_ad_forratation	=	1;
				header('Content-type: application/html');
				$adHTML  = $cache->call( array( $adRetriever, 'getAdHTML' ), $addata,0,$get_ad_forratation,$module_id );
				echo $adHTML;
			}
			jexit();
		}
}// class

