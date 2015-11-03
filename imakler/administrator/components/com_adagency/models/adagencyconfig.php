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

function fk_slashes($string) {
    while(strstr($string, '\\')) {
        $string = stripslashes($string);
    }
    return $string;
}

class adagencyAdminModeladagencyConfig extends JModelLegacy {
    var $_configs = null;
    var $_id = null;

    function __construct () {
        parent::__construct();
        $this->_id = 1;
    }

	function isJomSocial(){
		$db = JFactory::getDBO();
		$sql = "SHOW tables";
		$db->setQuery($sql);
		$res_tables = $db->loadColumn();
		
		$jconfigs = new JConfig();
		$dbprefix = $jconfigs->dbprefix;
		if(!in_array($dbprefix."community_fields",$res_tables)){
			return false;
		}
		return true;
	}
	
	function isJomSocialStreamAd(){
		$db = JFactory::getDBO();
		$sql = "SHOW tables";
		$db->setQuery($sql);
		$res_tables = $db->loadColumn();
		
		$jconfigs = new JConfig();
		$dbprefix = $jconfigs->dbprefix;
		if(!in_array($dbprefix."community_fields",$res_tables)){		
			return false;
		}
		
		$sql = "select `manifest_cache` from #__extensions where `element`='com_community' and `type`='component'";
		$db->setQuery($sql);
		$db->query();
		$manifest_cache = $db->loadColumn();
		$manifest_cache = json_decode(@$manifest_cache["0"], true);
		$version = $manifest_cache["version"];
		
		$version = str_split($version);
		
		if(count($version) == 1 && trim($version["0"]) == ""){
			return false;
		}
		else{
			return intval($version["0"]);
		}
	}
	
	function getJomFields() {
		$db = JFactory::getDBO();
		$sql = "
		SELECT * FROM #__community_fields 
		WHERE 
		`published` = 1 AND
		`type` IN (
			'select', 'singleselect', 'list', 'radio', 'checkbox', 'birthdate'
		) ORDER BY `id` ASC";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}

    function getConfigs() {
        if ( empty($this->_configs) ) {
            $this->_configs = $this->getTable("adagencyConfig");
            $this->_configs->load( $this->_id );
        }
        return $this->_configs;
    }

    function getConf() {
        $db =  JFactory::getDBO();
        $sql = "SELECT * FROM #__ad_agency_settings LIMIT 1";
        $db->setQuery( $sql );
        $result = $db->loadObject();
        return $result;
    }

    function getApprovals() {
        $db =  JFactory::getDBO();
        $query = "SHOW columns FROM #__ad_agency_banners WHERE field='approved'";
        $db->setQuery( $query );
        $result = $db->loadRow();
        $approval['ads'] = $result[4];
        $query = "SHOW columns FROM #__ad_agency_advertis WHERE field='approved'";
        $db->setQuery( $query );
        $result = $db->loadRow();
        $approval['adv'] = $result[4];
		$query = "SHOW columns FROM #__ad_agency_campaign WHERE field='approved'";
        $db->setQuery( $query );
        $result = $db->loadRow();
        $approval['cmp'] = $result[4];
		$query = "SHOW columns FROM #__ad_agency_campaign WHERE field='renewcmp'";
        $db->setQuery($query);
        $result = $db->loadRow();
        $approval['renewcmp'] = $result[4];
		
		$sql = "select `allow_add_keywords` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$allow_add_keywords = $db->loadResult();
		$approval["allow_add_keywords"] = $allow_add_keywords;
		
        return $approval;
    }

    function getAdvById($id){
        $db = JFactory::getDBO();
        $id = intval($id);
        $sql = "SELECT * FROM #__ad_agency_advertis WHERE aid = " . intval($id);
        $db->setQuery( $sql );
        $res =  $db->loadObject();
        return $res;
    }
	
function addFullUrl($text){
		preg_match_all('/href="(.*)"/msU', $text, $all_href);
		if(isset($all_href) && count($all_href) > 0 && count($all_href["0"]) > 0){
			foreach($all_href["1"] as $key=>$href){
				if(strpos($href, "&") != FALSE || strpos($href, "/") != FALSE){
					$domain = $_SERVER['HTTP_HOST'];
					if(strpos($href, $domain) == FALSE){
						$text = str_replace($href, JURI::root().$href, $text);
					}
				}
			}
		}
		return $text;
	}
function fixIMG($text){
		$lines=preg_split('/img/',$text);	
		for($i=0; $i<count($lines);$i++){
			if (preg_match('/src="(.*?)"/',$lines[$i])){
				$string='src="';
				$pos=strpos($lines[$i],$string)+5;
				$lines[$i] = substr_replace($lines[$i], JURI::root(), $pos, 0);
			}
		}
	 	//$lines = implode('img',$lines);
		return implode('img',$lines);
}
    function store() {
        jimport('joomla.filesystem.folder');
        $item = $this->getTable('adagencyConfig');
        $imagepath = str_replace("/administrator", "", JPATH_BASE);
        $imagepath = $imagepath . "/images/stories/";
        $data = JRequest::get('post');
        $newimgfolder = $data['imgfolder'];
        $full_path = JFolder::makeSafe($imagepath . $newimgfolder);
        if (!JFolder::exists($full_path)) {
            JFolder::create($full_path);
            // mkdir ( $imagepath . $newimgfolder );
        }
        if (JPath::canChmod($full_path)) {
            JPath::setPermissions($full_path);
        }	
        $data['payment'] = @serialize($data['payment']);
        $show = ''; $mandatory = '';
        $database =  JFactory::getDBO();
        if (isset($data['show'])) {
            foreach ($data['show'] as $key => $value) {
                if ($value == 1) { $show.=$key . ';'; }
            }
        }
        if ( isset($data['mandatory']) ) {
            foreach ($data['mandatory'] as $key => $value) {
                if($value==1) {$mandatory.=$key.';';}
            }
        }
		
        $data['params'] = @serialize($data['params']);
        $data['show'] = $show;
        $data['mandatory'] = $mandatory;
		
        $data['txtafterreg'] = JRequest::getVar("txtafterreg", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodyafterreg'] = JRequest::getVar("bodyafterreg", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodyafterregaa'] = JRequest::getVar("bodyafterregaa", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodyactivation'] = JRequest::getVar("bodyactivation", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodyrep'] = JRequest::getVar("bodyrep", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodycmpappv'] = JRequest::getVar("bodycmpappv", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodycmpdis'] = JRequest::getVar("bodycmpdis", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodyadappv'] = JRequest::getVar("bodyadappv", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodyaddisap'] = JRequest::getVar("bodyaddisap", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodyadvdis'] = JRequest::getVar("bodyadvdis", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodynewad'] = JRequest::getVar("bodynewad", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodynewcmp'] = JRequest::getVar("bodynewcmp", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodycmpex'] = JRequest::getVar("bodycmpex", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodynewuser'] = JRequest::getVar("bodynewuser", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['overviewcontent'] = JRequest::getVar("overviewcontent", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['sbcmpexpadm'] = JRequest::getVar("sbcmpexpadm", "", "post", "string", JREQUEST_ALLOWHTML);
        $data['bodycmpexpadm'] = JRequest::getVar("bodycmpexpadm", "", "post", "string", JREQUEST_ALLOWHTML);
		$data['forcehttps'] = JRequest::getVar("forcehttps", "", "post", "string", JREQUEST_ALLOWHTML);
		$data['sbadchanged'] = JRequest::getVar("sbadchanged", "", "post", "string", JREQUEST_ALLOWHTML);
		$data['boadchanged'] = JRequest::getVar("boadchanged", "", "post", "string", JREQUEST_ALLOWHTML);

        $data['txtafterreg'] = fk_slashes($data['txtafterreg']);
        $data['bodyafterreg'] = fk_slashes($data['bodyafterreg']);
        $data['bodyafterregaa'] = fk_slashes($data['bodyafterregaa']);
        $data['bodyactivation'] = fk_slashes($data['bodyactivation']);
        $data['bodyrep'] = fk_slashes($data['bodyrep']);
        $data['bodycmpappv'] = fk_slashes($data['bodycmpappv']);
        $data['bodycmpdis'] = fk_slashes($data['bodycmpdis']);
        $data['bodyadappv'] = fk_slashes($data['bodyadappv']);
        $data['bodyaddisap'] = fk_slashes($data['bodyaddisap']);
        $data['bodyadvdis'] = fk_slashes($data['bodyadvdis']);
        $data['bodynewad'] = fk_slashes($data['bodynewad']);
        $data['bodynewcmp'] = fk_slashes($data['bodynewcmp']);
        $data['bodycmpex'] = fk_slashes($data['bodycmpex']);
        $data['bodynewuser'] = fk_slashes($data['bodynewuser']);
        $data['overviewcontent'] = fk_slashes($data['overviewcontent']);
        $data['sbcmpexpadm'] = fk_slashes($data['sbcmpexpadm']);
        $data['bodycmpexpadm'] = fk_slashes($data['bodycmpexpadm']);
		
		$data['txtafterreg'] = $this->addFullUrl($data['txtafterreg']);
        $data['bodyafterreg'] = $this->addFullUrl($data['bodyafterreg']);
        $data['bodyafterregaa'] = $this->addFullUrl($data['bodyafterregaa']);
        $data['bodyactivation'] = $this->addFullUrl($data['bodyactivation']);
        $data['bodyrep'] = $this->addFullUrl($data['bodyrep']);
        $data['bodycmpappv'] = $this->addFullUrl($data['bodycmpappv']);
        $data['bodycmpdis'] = $this->addFullUrl($data['bodycmpdis']);
        $data['bodyadappv'] = $this->addFullUrl($data['bodyadappv']);
        $data['bodyaddisap'] = $this->addFullUrl($data['bodyaddisap']);
        $data['bodyadvdis'] = $this->addFullUrl($data['bodyadvdis']);
        $data['bodynewad'] = $this->addFullUrl($data['bodynewad']);
        $data['bodynewcmp'] = $this->addFullUrl($data['bodynewcmp']);
        $data['bodycmpex'] = $this->addFullUrl($data['bodycmpex']);
        $data['bodynewuser'] = $this->addFullUrl($data['bodynewuser']);
        $data['overviewcontent'] = $this->addFullUrl($data['overviewcontent']);
        $data['sbcmpexpadm'] = $this->addFullUrl($data['sbcmpexpadm']);
        $data['bodycmpexpadm'] = $this->addFullUrl($data['bodycmpexpadm']);
		
		  
		 /* Pictures in Emails */
		$data['txtafterreg'] = $this->fixIMG($data['txtafterreg']);
        $data['bodyafterreg'] = $this->fixIMG($data['bodyafterreg']);
        $data['bodyafterregaa'] = $this->fixIMG($data['bodyafterregaa']);
        $data['bodyactivation'] = $this->fixIMG($data['bodyactivation']);
        $data['bodyrep'] = $this->fixIMG($data['bodyrep']);
        $data['bodycmpappv'] = $this->fixIMG($data['bodycmpappv']);
        $data['bodycmpdis'] = $this->fixIMG($data['bodycmpdis']);
        $data['bodyadappv'] = $this->fixIMG($data['bodyadappv']);
        $data['bodyaddisap'] = $this->fixIMG($data['bodyaddisap']);
        $data['bodyadvdis'] = $this->fixIMG($data['bodyadvdis']);
        $data['bodynewad'] = $this->fixIMG($data['bodynewad']);
        $data['bodynewcmp'] = $this->fixIMG($data['bodynewcmp']);
        $data['bodycmpex'] = $this->fixIMG($data['bodycmpex']);
        $data['bodynewuser'] = $this->fixIMG($data['bodynewuser']);
        $data['overviewcontent'] = $this->fixIMG($data['overviewcontent']);
        $data['sbcmpexpadm'] = $this->fixIMG($data['sbcmpexpadm']);
        $data['bodycmpexpadm'] = $this->fixIMG($data['bodycmpexpadm']);
				
		if (isset($_POST['jomfields']) && is_array($_POST['jomfields'])) {
			$data['jomfields'] = json_encode($_POST['jomfields']);
		} else {
			$data['jomfields'] = '';
		}
		
        if (!$item->bind($data)){
            return JError::raiseError( 500, $database->getErrorMsg() );
            return false;
        }
        if (!$item->check()) {
            return JError::raiseError( 500, $database->getErrorMsg() );
            return false;
        }
        if (!$item->store()) {
            return JError::raiseError( 500, $database->getErrorMsg() );
            return false;
        }
        
        /*Salvam PLUGIN*/
        $plugin_handler = adagencyAdminModeladagencyConfig::makePluginList();
        if (count($plugin_handler) > 0 )
        foreach ($plugin_handler as $name => $plugin) {
            $name = $plugin->name;
            $value = JRequest::getVar($name."_account", array(0), 'array');
            $value = implode (" ", $value);
            $default = JRequest::getVar("default", null, 'post');
            $sandbox    = JRequest::getVar($name."_sandbox", 0, 'post');
            $reqhttps = JRequest::getVar($name."_reqhttps", 0, 'post');
            $default = substr( $default, 0, strlen($default) - 4 ) ;
            $value = $database->escape($value);
            if ($plugin->type == 'encoding' && file_exists($value)) {
                $query = "update #__ad_agency_plugins set value='{$value}', sandbox='{$sandbox}' where name='{$name}';";
            }  else if ($plugin->type !='encoding'){
                $query = "update #__ad_agency_plugins set value='{$value}', sandbox='{$sandbox}', reqhttps='{$reqhttps}' where name='{$name}';";
               } else {
                $query = '';
            }
            $database->setQuery($query);
            $database->query();
            $query = "update #__ad_agency_plugins set def='' where def='default';";
            $database->setQuery($query);
            $database->query();
            $query = "update #__ad_agency_plugins set def='default' where name='".stripslashes($default)."';";
            $database->setQuery($query);
            $database->query();
        }
		
        if(isset($data['aa'])){
            foreach($data['aa'] as $key=>$value){
				$query = "";
				if($key == "campaign_2"){
					$query = "ALTER TABLE `#__ad_agency_campaign` CHANGE `renewcmp` `renewcmp` INT( 3 ) NOT NULL default ".$value;
				}
				else{
                	$query="ALTER TABLE `#__ad_agency_".$key."` CHANGE `approved` `approved` ENUM( 'Y', 'N', 'P' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '".$value."'";
				}
                $database->setQuery($query);
                $database->query();
            }
			
			$allow_add_keywords = JRequest::getVar("allow_add_keywords", "0");
			$sql = "UPDATE `#__ad_agency_settings` SET `allow_add_keywords` = '".intval($allow_add_keywords)."' WHERE `id`=1";
			$database->setQuery($sql);
            $database->query();
        }

        return true;
    }

    function makePluginList () {
        $database = JFactory::getDBO();
        $plugin_path = JPATH_ROOT.'/administrator/components/com_adagency/plugins/';
        if ( is_dir ($plugin_path) ) {
            $plugin_dir = opendir($plugin_path);
            while ( ($plugin_file = readdir ($plugin_dir) ) ) {
                if (substr ($plugin_file, -3) == 'php' ) {
                    //Select plugin data for current filename from database.
                    $query = "select * from #__ad_agency_plugins where filename='".$plugin_file."'";
                    $database->setQuery($query);
                    $res = $database->loadObjectList();
                    //If no entry for this file - remove it.
                    if (!isset($res[0]) ) {
                        @unlink (JPATH_ROOT."/administrator/components/com_adagency/plugins".$plugin_file);
                    } else {
                        $plugins[$res[0]->name] = $res[0];
                    }
                } //if substr
            } //while readdir
            closedir($plugin_dir);
        }// if plugin_path
        return $plugins;
    }

};

?>