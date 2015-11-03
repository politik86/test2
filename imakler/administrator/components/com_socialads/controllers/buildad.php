<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.controller');
require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
	require_once(JPATH_SITE .DS.'components'.DS.'com_socialads'.DS. 'helper.php');
//include_once(JPATH_SITE .DS.'components'.DS.'com_socialads'.DS.'controller.php');


class socialadsControllerBuildad extends JControllerLegacy
{
	function __construct()
	{

		parent::__construct();
		// commented by vm: said to amol
		/*$JSite = new JSite();
		$menu = $JSite->getMenu();
		// pass the link for which you want the ItemId.
		$items = $menu->getItems('link', 'index.php?option=com_socialads&view=socialads');

		if(isset($items[0]))
		{
			$Itemid = $items[0]->id;
		}
		*/
	}

	/*this functon is used for the js promote pulgin which will get the data and pass it to the view*/
	function getPreviewData()
	{
/*
URL format
yourjoomla/index.php?option=com_socialads&controller=buildad&task=getPreviewData&id=plug_promote_jsevents|10&lang=en

testjugad.com/~dipti/shine17/index.php?option=com_socialads&controller=buildad&task=getPreviewData&title=test title&body=test body&image=http://testjugad.com/~dipti/shine17/components/com_community/assets/event.png&url=http://testjugad.com/~dipti/shine17/index.php?option=com_community&amp;view=events&amp;task=viewevent&amp;eventid=10&amp;lang=en&caller=raw&lang=en

*/
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		if($input->get('caller','','STRING')=='raw'){
			$previewdata[0]->title=$input->get('title','','STRING');
			$previewdata[0]->bodytext=$input->get('body','','STRING');
			$previewdata[0]->image = $input->get('image','','STRING');
			$url = $input->get('url', '', 'get', 'PATH',JREQUEST_ALLOWRAW);
			$previewdata[0]->url = urldecode($url);
		}
		else{
			$previewdata = $this->promotedatafetch();
		}

		$filename =JPATH_SITE. DS .'images'.DS .'socialads'.DS.basename(JPATH_SITE.$previewdata[0]->image);
		$mystring = $previewdata[0]->image;
		$findifurl   = 'http';
		$ifurl = strpos($mystring, $findifurl);
		if($ifurl===false)
		$source1 =JPATH_SITE. DS .$previewdata[0]->image;
		else
		{
			$source1 = $previewdata[0]->image;
			$content = file_get_contents($previewdata[0]->image);
			//Store in the filesystem.
			$fp = fopen($filename, "w");
			fwrite($fp, $content);
			fclose($fp);
		}
		if(!JFile::exists($filename))
			JFile::copy($source1,$filename);
//		$previewdata[0]->image = 'images'.DS .'socialads'.DS.basename(JPATH_SITE.$previewdata[0]->image);
		$previewdata[0]->imagesrc = JUri::root().'images/socialads/'.basename(JPATH_SITE.$previewdata[0]->image);

		$previewdata[0]->image = '<img width="100" src="'.JUri::root().'images/socialads/'.basename(JPATH_SITE.$previewdata[0]->image).'" />';

		$url = explode("://", $previewdata[0]->url);
		$previewdata[0]->url1 	= $url[0];
		$previewdata[0]->url2 	= $url[1];

/*data populate part*/
		if(!$input->get('caller')){ //caller not set
			header('Content-type: application/json');
			//pass array in json format
			echo json_encode(array(
					 "url1"=>$previewdata[0]->url1,
					 "url2"=>$previewdata[0]->url2,
					 "title"=>$previewdata[0]->title,
					 "imagesrc"=>$previewdata[0]->imagesrc,
					 "image"=>$previewdata[0]->image,
					 "bodytext"=>$previewdata[0]->bodytext,
	//				 "pluginimg"=>$previewdata[0]->imagesrc
			));
			jexit();
		}
		else{
			$buildadsession = JFactory::getSession();
			$ad_data=array();
			$ad_data[0]['ad_url1'] = $previewdata[0]->url1;
			$ad_data[1]['ad_url2'] = $previewdata[0]->url2;
			$ad_data[2]['ad_title']= $previewdata[0]->title;
			$ad_data[3]['ad_body'] = $previewdata[0]->bodytext;
			$buildadsession->set('ad_data', $ad_data);
			$buildadsession->set('ad_image',$previewdata[0]->imagesrc);
			$link = JRoute::_('index.php?option=com_socialads&view=buildad&Itemid='.$Itemid.'&frm=directad',false);
			$this->setRedirect($link);
		}
/*data populate part*/
	}

	//function to fetch promote data via the plguin trigger
	function promotedatafetch(){
	/*data fetch part*/
	$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$plgnameidstr	= $input->get('id','','STRING');
		$plgnameid 	= explode('|', $plgnameidstr);

		jimport( 'joomla.plugin.helper' );
		// Trigger the Promot Plg Methods to get the preview data
		JPluginHelper::importPlugin('socialadspromote', $plgnameid[0]);
		$dispatcher = JDispatcher::getInstance();

		$previewdata = $dispatcher->trigger('onPromoteData', array($plgnameid[1]));
		$previewdata = $previewdata[0];
	/*data fetch part*/
		return $previewdata;
	}


	 //save an ad details
	function save()
	{
		$model = $this->getModel('buildad');
		if($_REQUEST['filename']!=null)
		{
			$model->imageupload();
		}
		else
		{
			$status=$model->store();
			$socialadshelper = new socialadshelper();
 			$itemid = $socialadshelper->getSocialadsItemid('buildad');
			//$this->setRedirect("index.php?option=com_socialads&view=showad&Itemid=".$itemid);
			if($status)
			$link = JRoute::_("index.php?option=com_socialads&view=showad&Itemid=$itemid",false);
			else
			$link = JRoute::_("index.php?option=com_socialads&view=buildad&Itemid=$itemid",false);
			$this->setRedirect($link);
		}
	}

	// save alt ad
	function altad()
	{

		$model = $this->getModel('buildad');
		$status = $model->storeAltAd();
		if ($status)
		{
			$sacontroller = new socialadsController();
			$sacontroller->execute('delimages');
			$msg = JText::_('DETAILS_SAVE');
			$socialadshelper = new socialadshelper();
			$itemid = $socialadshelper->getSocialadsItemid('managead');

			$link = JRoute::_("index.php?option=com_socialads&view=managead&layout=list&Itemid=$itemid",false);
			$this->setRedirect($link,$msg);
		}
		else{
			$msg = JText::_( 'ERROR_SAVE');
		}

	}

	//Unlimited Ad
	function saveUnlimitedAd()
	{
		$model = $this->getModel('buildad');
		$status = $model->storeUnlimiteAd();
		if ($status)
		{
			$sacontroller = new socialadsController();
			$sacontroller->execute('delimages');
			$msg = JText::_('DETAILS_SAVE');
			$socialadshelper = new socialadshelper();
			$itemid = $socialadshelper->getSocialadsItemid('managead');
			$link = JRoute::_("index.php?option=com_socialads&view=managead&layout=list&Itemid=$itemid",false);
			$this->setRedirect($link,$msg);
		}
		else{
			$msg = JText::_( 'ERROR_SAVE');
		}

	}
	//find the geo locations according the geo db
	function findgeo(){
		$input=JFactory::getApplication()->input;
       // $post=$input->post;
          //$input->get
		$geodata = $_POST['geo'];
		$element	= $input->get('element');
	 $element_val	= $input->get('request_term');
		$query_condi = array();
		$query_table = array();
		$first = 1;
		$first_key = key($geodata);
		$previous_field = '';
		$loca_list = array();

		foreach ($geodata as $key=>$value){

				$value = trim($value);

		//	if(trim($value)){
				//$tablename = explode('_',$key);
				if($first){
					$query_table[] = '#__ad_geo_'.$key.' as '.$key;
				}
				else if($element == $key ){
					$query_table[] = '#__ad_geo_'.$key.' as '.$key.' ON '.$key.'.'.$previous_field.'_code = '.$previous_field.'.'.$previous_field.'_code';

				}
				$value = str_replace("||","','",$value);
				$value = str_replace('|','',$value);
				if($element == $key ){
					$element_table_name = $key;
					$query_condi[] = $key.".".$key." LIKE '%".trim($element_val)."%'";
					if(trim($value)){
						$query_condi[] = $key.".".$key." NOT IN ('".trim($value)."')";
					}
					break;
					/*if($first_key == $key){
						$first = 0;
						break;
					}*/
					$previous_field = $key;
				}
				else if(trim($value) && $first ){
					$query_condi[] = $key.".".$key." IN ('".trim($value)."')";
					$previous_field = $key;
				}
				$first = 0;
			//}
		}
		$tables = (count($query_table) ? ' FROM '.implode("\n LEFT JOIN ", $query_table) : '');
		if($tables){
			$where = (count($query_condi) ? ' WHERE '.implode("\n AND ", $query_condi) : '');
			if($where)
			{
				$db   = JFactory::getDBO();
				$query = "SELECT distinct(".$element_table_name.".".$element.") \n ".$tables." \n ".$where;
				$db->setQuery($query);
				$loca_list = $db->loadRowList();
			}
		}
		$data = array();
		if($loca_list){
			foreach ($loca_list as $row){
				$json = array();
				//$json['value'] = $row['1'];	//id of the location
				$data[] = $row['0']; //name of the location

				//$data[] = $json;
			}
		}


		echo json_encode($data);
		jexit();
	}

	//calculate Estimated No of Reach for each ad
	function calculatereach()
	{
	$plgdata=array();
	$aa=array();
	$aa=json_encode($_POST['mapdata']);
	if(isset($_POST['plgdata']))
	$plgdata=json_encode($_POST['plgdata']);
	$plg_target_field=array();
	$target_field=array();
	$plgmapdata_array=array();
	if(empty($aa) and empty($plgdata))
	jexit();
	if(!empty($aa))
	{
		$mapdata_array=json_decode($aa);
		if(empty($mapdata_array))
		jexit();
		$target_field=socialadsControllerBuildad::calculatereach_parseArray($mapdata_array);
	}
	if(!empty($plgdata))
	{
	$plgmapdata_array=json_decode($plgdata);
	$plg_target_field=socialadsControllerBuildad::calculatereach_parseArray($plgmapdata_array);

	}

	require_once(JPATH_COMPONENT . DS . 'adshelper.php');
	$reach=0;
	$adRetriever=new adRetriever();
	$reach=$adRetriever->getEstimatedReach($target_field,$plg_target_field);
	header('Content-type: application/json');
	  	echo json_encode(array(
				 "reach"=>$reach,
			));
		jexit();
}

function calculatereach_parseArray($mapdata_array)
{
$target_field=array();

	foreach($mapdata_array as $mapdata_obj)
	{

		foreach($mapdata_obj as $mapdata)
		{
			if($mapdata!='')
			{
				$mapdata_arr=socialadsControllerBuildad::parseObjectToArray($mapdata_obj);
			  foreach($mapdata_arr as $key => $value)
			    {

				   $target_key_arr= explode(',',$key);
				   $target_key_arr1= explode('|',$target_key_arr[0]);
				   $target_key=$target_key_arr1[0];
				   if (array_key_exists($target_key, $target_field))
   					$target_field[$target_key]=$target_field[$target_key]."','".$value;
				   else
				    	$target_field[$target_key]=$value;

			    }
			}
		}
	}
return $target_field;

}



function parseObjectToArray($object) {
    $array = array();
    if (is_object($object)) {
        $array = get_object_vars($object);
    }
    return $array;
}

	//This is function to hide the ad type and zone div when there is only one adtype and only one zone
	function checkdefaultzone($adtype='')
	{

		$db   = JFactory::getDBO();
		$query = "SELECT id,ad_type FROM #__ad_zone WHERE published=1 "." AND ad_type='".$adtype[0]."'";
		$db->setQuery($query);
		$count = $db->loadobjectlist();

		if($count)
		{
		$publish_mod=$this->getZoneamodule();

		$results = array_unique($publish_mod);

		$flag=0;





		foreach($results as $publish_asign_zones)
		{
		//if($text_img_flag==1 and $img_flag==1 and $text_flag==1)
		//break;
		foreach($count as $zoneids)
		{

				if($publish_asign_zones==$zoneids->id)
				{
					$query1 = "SELECT ad_type FROM #__ad_zone WHERE id=".$publish_asign_zones." AND published=1 group by ad_type";
					$db->setQuery($query1);
					$ad_type = $db->loadResult();
					if($ad_type)
					{
						if($ad_type=='text_img')
						{

							$flag++;

						}
						if($ad_type=='img')
						{

							$flag++;

						}
						if($ad_type=='text')
						{

							$flag++;

						}
					}

				}

			}
		}


	}
	if($flag==1)
	return 0;
	else if($flag>1)
	return 1;

}
	function autoSave()
	{
		$mainframe=JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$session = JFactory::getSession();
		$post = $input->post;
		$model =$this->getModel('buildad');
		$stepId=$input->get('stepId','','STRING');
		$retdata = array();
		$retdata['stepId'] = $stepId;
		$retdata['payAndReviewHtml'] = '';
		$retdata['adPreviewHtml'] = '';
		$retdata['billingDetail'] = '';

		$socialadshelper = new socialadshelper();
		$Itemid = $socialadshelper->getSocialadsItemid('managead');

		$retdata['Itemid']=$Itemid;

		$adminApproval = 1;
		// Save step-1 : design ad data
		if ($stepId == 'ad-design')
		{
			$model =$this->getModel('buildad');
			$response=$model->saveDesignAd($post, $adminApproval);

			if($response)
			{
			}

		}

		// Save step-2 : targeting ad data
		if ($stepId == 'ad-targeting')
		{
			$model =$this->getModel('buildad');
			$response=$model->saveTargetingData($post);

			if($response)
			{
			}
		}

		// Save ad pricing data
		if ($stepId == 'ad-pricing')
		{
			$response=$model->savePricingData($post);

			if($response)
			{
			}

			//vm:
			$ad_creator_id =  $input->get('ad_creator_id',0);
			$this->country = $model->getCountry();
			$this->userbill = $model->getbillDetails($ad_creator_id);

			if($ad_creator_id)
			{
				$billpath = $socialadshelper->getViewpath('buildad','default_billing','ADMINISTRATOR');
				ob_start();
					include($billpath);
					$html = ob_get_contents();
				ob_end_clean();
				$retdata['billingDetail'] = $html;
			}
		}

		// VM:
		// if billing tab is hide
		$sa_hide_billTab =  $input->get('sa_hide_billTab',0);  // if 0 means billing details are not saved

		if( ( !empty($sa_hide_billTab) && $stepId == 'ad-pricing') || $stepId == 'ad-billing')
		{
			$user = JFactory::getUser();

			$ad_id = $session->get('ad_id');
			$order_id = $model->getIdFromAnyFieldValue($ad_id, 'ad_id',  '#__ad_payment_info');
			$billdata = $post->get('bill',array(),"ARRAY");
			$ad_creator_id =  $input->get('ad_creator_id',0);

			// save billing detail
			if(!empty($billdata) && $ad_creator_id)
			{
				$model->billingaddr($ad_creator_id,$billdata);
			}

			$showBillLink = $sa_hide_billTab;
			$billpath = $socialadshelper->getViewpath('buildad','default_adsummary','ADMINISTRATOR');

			ob_start();
				include($billpath);
				$html = ob_get_contents();
			ob_end_clean();
			$retdata['payAndReviewHtml'] = $html;

		}
		// VM: end

		// Start :-> Amol

		//If campaign is selected then get ad preview html
		if($stepId=='ad-pricing')
		{
			$ad_id = $session->get('ad_id');
			$AdPreviewData=$model->getAdPreviewData($ad_id);

			$path = $socialadshelper->getViewpath('buildad','default_showadscamp','ADMINISTRATOR','ADMINISTRATOR');

			ob_start();
				include($path);
				$html = ob_get_contents();
			ob_end_clean();
			$retdata['adPreviewHtml'] = $html;

		}
		// end :-> Amol


		echo json_encode($retdata);
		jexit();
	}


	/*function adsPlaceOrder()
	{
		$user = JFactory::getUser();
		$jinput = JFactory::getApplication()->input;
		$billdata = array();
		$billdata['addr'] = 'pune vadgaon';
		$billdata['city'] = 'pune';
		$billdata['country'] = 'India';
		$billdata['email1'] = 'vbmundhe@mailinator.com';
		$billdata['fnam'] = 'vidyasagar';
		$billdata['lnam'] = 'mundhe';
		$billdata['phon'] = '567890';
		$billdata['state'] = 'Maharashtra';
		$billdata['zip'] = '23423';

		$model = $this->getModel('buildad');
		$order_id = 1;
		$model->billingaddr($user->id,$billdata,$order_id);


		jexit();
	} */

	function loadState()
	{
		$db= JFactory::getDBO();
		$jinput=JFactory::getApplication()->input;
		$country = $jinput->get('country','','STRING');

		$model = $this->getModel('buildad');

		$state = $model->getuserState($country);
		echo json_encode($state);
		jexit();

	}
	function ad_gatewayHtml()
	{
		$db = JFactory::getDBO();
		$jinput = JFactory::getApplication()->input;
		$country = $jinput->get('country','','STRING');

		$model = $this->getModel('payment');
		$model = new socialadsModelpayment();
		$selectedGateway = $jinput->get('gateway','');
		$order_id = $jinput->get('order_id','');
		$payPerAd = $jinput->get('payPerAd',0,'INT');
		$return = '';

		if(!empty($selectedGateway) && !empty($order_id))
		{
			$model->updateOrderGateway($selectedGateway,$order_id);
			$payhtml = $model->getHTML($selectedGateway,$order_id,$payPerAd);
			$return= !empty($payhtml[0])? $payhtml[0]:''   ;
		}
		echo $return;
		jexit();
	}
	/** This function gives user's billing information*/
	function getbillDetails($userId)
	{
		$model	= $this->getModel( 'buildad');
		return $billDetails = $model->getbillDetails($userId);

	}


	function billUpdate()
	{
		$user = JFactory::getUser();
		$model	= $this->getModel( 'buildad');
		$jinput = JFactory::getApplication()->input;
		$billdata = $jinput->get('bill',array(),"ARRAY");
		$status = 0;

		// save billing detail
		if(!empty($billdata))
		{
			$status = $model->billingaddr($user->id,$billdata);
		}

		$msg =JText::_( 'SA_ERROR_WHILE_UPDATION_BILL_INFO' );
		if($status ==1 )
		{
			$msg =JText::_( 'SA_UPDATED_BILL_INFO' );
		}
		$socialadshelper = new socialadshelper();
		$itemid = $socialadshelper->getSocialadsItemid('buildad');
		$this->setRedirect( 'index.php?option=com_socialads&view=buildad&layout=updatebill&tmpl=component&itemid='.$itemid, $msg );

	}
	function testv()
	{
		$model =$this->getModel('payment');
		$model->SendOrderMAil();
	}
	// vm code end

	function activateAd()
	{
		$model =$this->getModel('buildad');
		$model->activateAd();
		$app = JFactory::getApplication();

		if( $app->isAdmin() )
		{
			$redirectUrl = 'index.php?option=com_socialads&view=approveads';
		}
		else
		{
			$socialadshelper = new socialadshelper();
			$Itemid = $socialadshelper->getSocialadsItemid('managead');
			$redirectUrl = 'index.php?option=com_socialads&view=managead&Itemid='.$Itemid;
		}
		$this->setRedirect( $redirectUrl, $msg );

	}

	function draftAd()
	{
		$model =$this->getModel('buildad');
		$model->draftAd();
		$app = JFactory::getApplication();

		if( $app->isAdmin() )
		{
			$redirectUrl = 'index.php?option=com_socialads&view=approveads';
		}
		else
		{
			$socialadshelper = new socialadshelper();
			$Itemid = $socialadshelper->getSocialadsItemid('managead');
			$redirectUrl = 'index.php?option=com_socialads&view=managead&Itemid='.$Itemid;
		}
		$this->setRedirect( $redirectUrl, $msg );
	}
	// VM:
	function sa_processFreeOrder()
	{
		$socialadshelper = new socialadshelper();
		$socialadshelper->processFreeOrder(1);
	}
	//Amol
	public function windowLocation()
	{
		//Clear ad ID session
		$session = JFactory::getSession();
		$session->clear('ad_id');

		$msg =JText::_( 'SA_UPDATED_BILL_INFO' );
		$this->setRedirect( JUri::base().'index.php?option=com_socialads&view=approveads', $msg );
	}

	//Amol
	//Get selected user data
	function promoterPlugin()
	{
		//get selected user id
		$input = JFactory::getApplication()->input;
		$uid = $input->get('uid','','INT');

		$model =$this->getModel('buildad');

		$result=array();

		$result['select_promote_plg_html'] = $model->getPromoterPlugin($uid);

		echo json_encode($result);
		jexit();

	}
}// class
