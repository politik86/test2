 <?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

jimport ("joomla.aplication.component.model");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminModeladagencyFloating extends JModelLegacy {
	var $_licenses;
	var $_license;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
		global $mainframe, $option;
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function last_ad($type){
		$db = JFactory::getDBO();
		$sql = "select id from #__ad_agency_banners WHERE media_type='".$type."' GROUP BY id DESC LIMIT 1";
		$db->setQuery($sql);
		return $db->loadResult();
	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getListPackages();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_tid = $id;
		$this->_package = null;
	}

	function getExistsZone(){
		$db = JFactory::getDBO();
		$sql = "SELECT zoneid FROM `#__ad_agency_zone` WHERE `adparams` LIKE '%floating%' LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadResult();
		if(intval($res) > 0) { return true; } else { return false; }
	}

	function getChannel($bid) {
		$db = JFactory::getDBO();
		if(isset($bid)) {
			$sql = "SELECT * FROM `#__ad_agency_channels` WHERE banner_id =".intval($bid)." LIMIT 1";
			$db->setQuery($sql);
			$result = $db->loadObject();
			if(!isset($result->id)) { return NULL; }
			$sql = "SELECT s.type, s.option, s.logical, s.data FROM `#__ad_agency_channels` AS c, `#__ad_agency_channel_set` AS s WHERE c.id = ".intval($result->id)." AND c.id = s.channel_id ORDER BY s.id ASC";
			$db->setQuery($sql);
			$result->sets = $db->loadObjectList();

			//echo "<pre>";var_dump($result);die();
		} else {
			$result = NULL;
		}
		return $result;
	}

	function getlistPackages () {
		if (empty ($this->_packages)) {
			$db = JFactory::getDBO();
			$sql = "select * from #__ad_agency_order_type";
			$this->_total = $this->_getListCount($sql);
			$this->_packages = $this->_getList($sql);
		}
		return $this->_packages;
	}

	function getCampsByAid($adv_id, $distinct = 0) {
		$db = JFactory::getDBO();
		$sqls = "
			SELECT c.id, c.name, c.params, z.*
			FROM #__ad_agency_campaign AS c
			JOIN #__ad_agency_order_type AS o
			ON c.otid = o.tid
			JOIN #__ad_agency_package_zone AS pz
			ON pz.package_id = o.tid
			JOIN #__ad_agency_zone AS z
			ON z.zoneid = pz.zone_id
			WHERE c.aid = '".$adv_id."' AND z.adparams LIKE '%floating%'
			AND c.status != -1
		";
        if ($distinct == 1) { $sqls .= "GROUP BY c.id"; }
        
		$db->setQuery($sqls);
		$camps = $db->loadObjectList();
        if (is_array($camps))
		foreach($camps as &$element) {
			$element->adparams = @unserialize($element->adparams);
            $element->params = @unserialize($element->params);

            /*// Select total banners for each campaign
            $sql = "SELECT COUNT( campaign_id )
                       FROM `#__ad_agency_campaign_banner`
                       WHERE `campaign_id` = " . $element->id;
            $db->setQuery($sql);
            $element->totalbanners = $db->loadResult();*/
		}
		
		return $camps;
	}

	function processCampZones($camps){
		$zones = array();
		if(isset($camps)&&(is_array($camps))){
			foreach($camps as $camp){
				if(!isset($zones[$camp->id])) { $zones[$camp->id] = $this->getAllZonesForCampByCampId($camps,$camp->id); }
			}
		}
		return $zones;
	}

	function getAllZonesForCampByCampId($camps, $camp_id){
       	$resp = array();
		
		$db = JFactory::getDBO();
		$sql = "SELECT z.`zoneid`, z.`z_title`, z.`adparams` FROM `#__ad_agency_zone` z, `#__ad_agency_campaign` c, `#__ad_agency_package_zone` pz WHERE pz.zone_id=z.zoneid and c.otid=pz.package_id and c.id=".intval($camp_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		if(isset($result) && count($result) > 0){
			foreach($result as $zone){
				$params = $zone["adparams"];
				if(trim($params) != ""){
					$params = unserialize($params);
					if(isset($params["width"])){
						$zone["width"] = $params["width"];
					}
					
					if(isset($params["height"])){
						$zone["height"] = $params["height"];
					}
					
					if(isset($zone["zoneid"])){
						$zone["zoneid"] = $zone["zoneid"];
					}
					
					if(isset($zone["z_title"])){
						$zone["z_title"] = $zone["z_title"];
					}
					$resp[] = $zone;
				}
			}
		}
        return $resp;
    }

	function createSelectBox($czones, $ad_id, $ad){
        $db = JFactory::getDBO();
        $select = array();
        if(isset($czones)&&(is_array($czones))){
			$first = TRUE;
			foreach($czones as $key=>$value){
				if($first){
					$czones[$key] = array();	
				}
				
				$sql = "SELECT z.`zoneid`, m.`title` as z_title, z.`adparams` FROM `#__ad_agency_zone` z, `#__ad_agency_campaign` c, `#__ad_agency_package_zone` pz, `#__modules` m WHERE pz.zone_id=z.zoneid and c.otid=pz.package_id and c.id=".intval($key)." and m.`id`=z.`zoneid`";
				$db->setQuery($sql);
				$db->query();
				$result = $db->loadAssocList();
				
				if(isset($result) && count($result) > 0){
					foreach($result as $res){
						$czones[$key][] = $res;
					}
				}
			}
			
            foreach($czones as $key=>$value){
                $sql = "SELECT `zone` FROM #__ad_agency_campaign_banner WHERE banner_id = ".intval($ad_id)." AND campaign_id = ".intval($key);
				$db->setQuery($sql);
				$db->query();
                $sel_zone = $db->loadColumn();
				@$sel_zone = $sel_zone["0"];
				
                $select[$key] = "<select name='czones[".$key."]' id='czones_".$key."' class='w145'>";
                $select[$key] .= "<option value='0'>".JText::_('ADAG_ZONE_FOR_AD')."</option>";
                if(is_array($value)){
                    foreach($value as $val){
						$disabled = "";
						$params = $val["adparams"];
						$params = unserialize($params);
						$zone_width = $params["width"];
						$zone_height = $params["height"];
						
						if(trim($zone_width) != "" && trim($zone_height) != ""){
							if(trim($zone_width) == $ad->width && trim($zone_height) == $ad->height){
								$disabled = "";
							}							
							else{
								$disabled = 'disabled="disabled"';
							}
						}
						
						if(!isset($params["floating"])){
							$disabled = 'disabled="disabled"';
						}
						
                        if($sel_zone == $val['zoneid']){
							$this_selected = " selected='selected' ";
						}
						else{
							$this_selected = NULL;
						}
						
						if($disabled == ""){
	                        $select[$key] .= "<option value='".$val['zoneid']."' ".$this_selected." ".$disabled.">".$val['z_title']."</option>";
						}
                    }
                }
                $select[$key] .= "</select>";
            }
        }
        return $select;
    }

	function delete_geo($bid, $aid) {
		$db = JFactory::getDBO();
		$temp = NULL;
		if(isset($bid)&&isset($aid)) {
			$sql0 = 'SELECT id FROM #__ad_agency_channels WHERE banner_id = "'.intval($bid).'" AND advertiser_id = "'.intval($aid).'" LIMIT 1';
			$db->setQuery($sql0);
			$id_to_del = $db->loadResult();

			if(isset($id_to_del)&&($id_to_del != NULL)) {
				/*$sql1 = 'DELETE FROM #__ad_agency_channels WHERE id = "'.$id_to_del.'"';
				$db->setQuery($sql1);
				if(!$db->query()) { return false; }*/

				$sql2 = 'DELETE FROM #__ad_agency_channel_set WHERE channel_id = "'.intval($id_to_del).'"';
				$db->setQuery($sql2);
				if(!$db->query()) { return false; }
			}
		}

		if(isset($id_to_del)) { return $id_to_del; } else { return -1; }
	}
	function getad() {
		if (empty ($this->_package)) {
			$this->_package = $this->getTable("adagencyAds");
			$this->_package->load($this->_tid);
			$data = JRequest::get('post');
			if (!$this->_package->bind($data)){
				$this->setError($item->getErrorMsg());
				return false;

			}
			if (!$this->_package->check()) {
				$this->setError($item->getErrorMsg());
				return false;

			}
		}
		return $this->_package;
	}

	function getUIDbyAID($aid){
		$db = JFactory::getDBO();
		$sql = 'SELECT user_id FROM `#__ad_agency_advertis` WHERE aid = "'.intval($aid).'" LIMIT 1';
		$db->setQuery($sql);
		return $db->loadResult();
	}

	function store_geo($bid = NULL){
		require_once('../components/com_adagency/helpers/channel_fcs.php');
	}

	function store () {
		$random_number = rand(1, 100);
		
        JLoader::import( 'adagencyads', JPATH_SITE . DS .'components' . DS . 'com_adagency' . DS . 'models' );
        $front_ads_model = JModelLegacy::getInstance( 'adagencyAds', 'adagencyModel' );
		$database = JFactory::getDBO();
		$item = $this->getTable('adagencyAds');
		$_POST["parameters"]["ad_code"] = $_POST['transitioncode'];
		$data = JRequest::get('post');
		$sendmail = $data['sendmail'];
		$data['key'] = '';
		// Prepare the keywords, trim them
		if(isset($data['keywords'])&&($data['keywords']!='')) {
			$data['keywords'] = implode(',',array_map('trim',explode(',',$data['keywords'])));
		}
		if($data['id']==0){$data['created'] = date("Y-m-d");} else {
			$current_banner = $this->getAdById($data['id']);
		}
		$data['parameters']["ad_code"] = $_POST['transitioncode'];
		/*saving floating ad*/
			$crt=1;
			$string='href="';
			$fisier=$data['parameters']["ad_code"];
			$pos1=strpos($fisier,$string);
			while ($pos1) {
				$fisier=substr($fisier,$pos1+strlen($string),strlen($fisier));
				$pos2=strpos($fisier,'"');
				$url=substr($fisier,0,$pos2);
				$data['parameters']["linktrack"][$crt] = $url;
				$crt++;
				$pos1=strpos($fisier,$string);
			}
		$bordercolor = $data['parameters']["border_color"];
		$backcolor = $data['parameters']["bg_color"];
		$html_content = preg_replace("/(href((\s+)?)=((\s+)?)\")\b((http(s?):\/\/)|(www\.)|(ftp:\/\/))([\w\.]+)([-~\/\w+\.-?\&\,\%\=]+)\b/i","href=\"ad_url",$data['parameters']["ad_code"]);

$closead = JText::_('ADAG_CLOSEAD');
			$script = <<<EOD
<SCRIPT language="JavaScript1.2">
var ie=document.all;
var dom=document.getElementById;
var ns4=document.layers;
var bouncelimit=32;
var direction="up";

function initbox(){
if (!document.all) {
	fafa = document.getElementById("floatit");
	fafa.parentNode.removeChild(fafa);
	document.body.appendChild(fafa); }
if (!dom&&!ie&&!ns4)
return;
crossobj=(dom)?document.getElementById("floatit").style : ie? document.all.floatit : document.floatit;
scroll_top=(ie)? document.body.scrollTop : window.pageYOffset;
crossobj.top=scroll_top-250;
crossobj.visibility=(dom||ie)? "visible" : "show";
dropstart=setInterval("floatit()",50);
}

function floatit(){
scroll_top=(ie)? document.body.scrollTop : window.pageYOffset;
if (parseInt(crossobj.top)<100+scroll_top)
crossobj.top=parseInt(crossobj.top)+40;
else{
clearInterval(dropstart);
bouncestart=setInterval("bouncein()",50);
}
}

function bouncein(){
crossobj.top=parseInt(crossobj.top)-bouncelimit;
if (bouncelimit<0)
bouncelimit+=8;
bouncelimit=bouncelimit*-1;
if (bouncelimit==0){
clearInterval(bouncestart);
}
}

function HideVertFloating(number) {
	var divs = document.getElementsByClassName('floatit'+number);
	for(var i=0; i<divs.length; i++){
		divs[i].parentNode.removeChild(divs[i]);
	}
}

var screenWidth = window.innerWidth ? window.innerWidth*.92 : document.body.offsetWidth*.92;
var screenHeight = window.innerHeight ? window.innerHeight : document.body.offsetHeight;

</SCRIPT>
<div id="floatit" class="floatit{$random_number} mod_ijoomlazone" style="height:{$data['height']}px; width:{$data['width']}px; position:absolute; xtopzz xbottzz xleftzz xrightzz z-index:500; background-color: #$backcolor; border:{$data['parameters']["border"]}px solid #$bordercolor;" >
    <table cellpadding="5" cellspacing="0" width="100%" id="AutoNumber2">
    	<tr>
    		<td width="100%" style="text-align:right;">
    			<a href="" onClick="HideVertFloating({$random_number}); return false;">{$closead}</a>
    		</td>
    	</tr>
    	<tr>
    		<td width="100%">
				<!----start---->
				{$html_content}
				<!----End---->
			</td>
		</tr>
	</table>

	<script type="text/javascript">
		initbox();
		var xxx;
    </script>
</div>


EOD;
		//$adcodu = $script;
		//var_dump($data["parameters"]);die();

		$adcodu=str_replace("xtopzz", "top: ".$data["parameters"]["toppos"]."px;", $script);
		$adcodu=str_replace("xleftzz", "left: ".$data["parameters"]["leftpos"]."px;", $adcodu);
		$adcodu=str_replace("xbottzz", "bottom: ".$data["parameters"]["bottpos"]."px;", $adcodu);
		$adcodu=str_replace("xrightzz", "right: ".$data["parameters"]["rightpos"]."px;", $adcodu);

		//var_dump($adcodu);die();

		$data['parameters'] = serialize($data['parameters']);
		//$data['location'] = json_encode($data['limitation']);

		if(trim($data['ad_start_date']) != ""){
			$data['ad_start_date'] = date("Y-m-d H:i:s", strtotime($data['ad_start_date']));
		}
		else{
			$data['ad_start_date'] = date("Y-m-d H:i:s");
		}
		
		if(trim($data['ad_end_date']) != "Never" && trim($data['ad_end_date']) != ""){
			$data['ad_end_date'] = date("Y-m-d H:i:s", strtotime($data['ad_end_date']));
		}

		if(intval($data["id"]) == 0){
			$sql = "select max(`ordering`) from #__ad_agency_banners";
			$database->setQuery($sql);
			$database->query();
			$max_ordering = $database->loadColumn();
			$max_ordering = $max_ordering["0"];
			$data["ordering"] = $max_ordering + 1;
		}

		if (!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;

		}

		if (!$item->check()) {
			$this->setError($item->getErrorMsg());
			return false;

		}

		if (!$item->store()) {
			$this->setError($item->getErrorMsg());
			return false;

		}

		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");
		$configs = $configs->getConfigs();
		$cids = intval($_POST['id']);
		$db =  JFactory::getDBO();

		if (isset($_POST['id']) && ($_POST['id']!=0)) $where = $_POST['id'];
			else $where = mysql_insert_id();
		if ($where==0) {
			$ask = "SELECT `id` FROM `#__ad_agency_banners` ORDER BY `id` DESC LIMIT 1 ";
			$db->setQuery( $ask );
			$where = $db->loadResult();
		}

		if(isset($where)) {
			$this->store_geo($where);
			require_once(JPATH_BASE . '/components/com_adagency/helpers/jomsocial.php');
			JomSocialTargeting::save($where);			
		}

        $sql = "DELETE FROM #__ad_agency_campaign_banner WHERE banner_id = '".intval($where)."'";
		$db->setQuery($sql);
		$db->query();

        $campLimInfo = $front_ads_model->getCampLimInfo($data['advertiser_id']);

		if(isset($data['adv_cmp'])) {
			foreach ($data['adv_cmp'] as $val) {
                if ( ($val) && (
                    ( !isset($campLimInfo[$val])) || ( $campLimInfo[$val]['adslim'] > $campLimInfo[$val]['occurences'] )
                ) ) {
                	if(isset($data['czones'][$val])){
                        $query = "INSERT INTO `#__ad_agency_campaign_banner` (`campaign_id` ,`banner_id` ,`relative_weighting` ,`thumb`, `zone`) VALUES ('".intval($val)."', '".intval($where)."', '100', NULL, ".$data['czones'][$val].");";
                        $db->setQuery($query);
                        $db->query();
                    }
				}
			}
		}
        
		$sql = "UPDATE #__ad_agency_banners SET `ad_code` = '".addslashes(trim($adcodu))."' WHERE `id`='".intval($where)."'";
		$db->setQuery($sql);
		$db->query();

		if(($sendmail)&&(isset($current_banner)&&($current_banner->approved!=$data['approved'])&&($data['approved']!='P'))) {
			$this->sendmail($current_banner->id,$data['approved']);
		}

		return true;

	}

	function getAdById($id){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_banners WHERE id =".intval($id);
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}

	function sendmail ($cid,$task) {
		$db = JFactory::getDBO();
		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");
		$configs = $configs->getConfigs();
		$item = $this->getTable('adagencyAds');
		
        $sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);
        
		//send email notifications
        $sql = "SELECT b.title, u.name, u.username, u.email FROM #__ad_agency_banners AS b LEFT JOIN #__ad_agency_advertis as a ON a.aid=b.advertiser_id LEFT JOIN #__users as u ON u.id=a.user_id WHERE b.id = '".intval($cid)."' GROUP BY b.id";
        $db->setQuery($sql);
        if(!$result = $db->query()) {
            echo $db->stderr();
            return;
        }
        $user = $db->loadObjectList();
        foreach ( $user as $value) {
			$ok_send_email = 1;
            if($task == 'Y') {
                $subject=$configs->sbadappv;
                $message=$configs->bodyadappv;
                $ok_send_email = $params["send_ban_app"];
            }
            else{
                $subject=$configs->sbaddisap;
                $message=$configs->bodyaddisap;
                $ok_send_email = $params["send_ban_dis"];
            }

            $subject =str_replace('{name}',$value->name,$subject);
            $subject =str_replace('{login}',$value->username,$subject);
            $subject =str_replace('{email}',$value->email,$subject);
            $subject =str_replace('{banner}',$value->title,$subject);
            $message =str_replace('{name}',$value->name,$message);
            $message =str_replace('{login}',$value->username,$message);
            $message =str_replace('{email}',$value->email,$message);
            $message =str_replace('{banner}',$value->title,$message);
            // mail publish campaign
            if($ok_send_email == 1){
            	JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $value->email, $subject, $message, 1);
            }
        }
		//send email notifications
		return 1;
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('adagencyPackage');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;

			}
		}

		return true;
	}


	function publish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('adagencyPackage');
		if ($task == 'publish')
			$sql = "update #__ad_agency_order_type set published='1' where tid in ('".implode("','", $cids)."')";

		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
	}

	function unpublish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('adagencyPackage');
		if ($task == 'unpublish')
			$sql = "update #__ad_agency_order_type set published='0' where tid in ('".implode("','", $cids)."')";

		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return -1;
	}

	function gettransitionlistAdvertisers () {
		if (empty ($this->_package)) {
			$db = JFactory::getDBO();
			$sql = "SELECT a.aid, b.name as company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY company ASC";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$this->_package = $db->loadObjectList();

		}
		return $this->_package;

	}
    
    function getCampZones($campaigns){
		$return = array();
		if(isset($campaigns) && count($campaigns) > 0){
			$db = JFactory::getDBO();
			if(isset($campaigns) && $campaigns != "" && count($campaigns) > 0)
			foreach($campaigns as $key=>$campaign){
				$sql = "SELECT z.`zoneid`, z.`z_title`, z.`adparams` FROM `#__ad_agency_zone` z, `#__ad_agency_campaign` c, `#__ad_agency_package_zone` pz WHERE pz.zone_id=z.zoneid and c.otid=pz.package_id and c.id=".intval($campaign->id);
				$db->setQuery($sql);
				$db->query($sql);
				$zones = $db->loadAssocList();
				if(isset($zones) && count($zones) > 0){
					foreach($zones as $key_zone=>$zone){
						$params = $zone["adparams"];
						$params = unserialize($params);
						$zone["width"] = $params["width"];
						$zone["height"] = $params["height"];
						
						if(!isset($params["floating"])){
							continue;
						}
						
						$temp = $zone["z_title"]." ";
						if(intval($zone["width"]) == 0){
							$temp .= "(".JText::_("ADAG_ANYSIZE").")";
						}
						else{
							$temp .= "(".intval($zone["width"])." x ".intval($zone["height"])." px)";
						}
						
						$return[intval($campaign->id)][$zone["zoneid"]] = $temp;
					}
				}
				else{
					$return[intval($campaign->id)] = array();
				}
			}
			return $return;
		}
		else{
	 		return $return;
		}
	}

};
?>
