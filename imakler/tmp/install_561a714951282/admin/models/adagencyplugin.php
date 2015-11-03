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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

jimport ("joomla.aplication.component.model");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminModeladagencyPlugin extends JModelLegacy {
	var $_plugins;
	var $_plugin;
	var $plugin_instances = array();
	var $_id = null;
	var $allowed_types = array("payment", "encoding");
	var $req_methods = array("getFEData", "getBEData");
	var $_installpath;
	var $plugins_loaded = 0;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
		$this->loadPlugins();
	}

	function setId($id) {
		$this->_id = $id;
		$this->_installpath = JPATH_COMPONENT.DS."plugins".DS;
		$this->_plugin = null;
	}


	function getlistPlugins () {
		if (empty ($this->_plugins)) {
			$sql = "select * from #__ad_agency_plugins";
			$this->_plugins = $this->_getList($sql);
		}
		return $this->_plugins;
	}	

	function loadPlugins () {
		if ($this->plugins_loaded == 1) return;		
		$plugins = $this->getlistPlugins();
		foreach ($plugins as $plugin) {
			$this->registerPlugin($plugin->filename, $plugin->classname);
		        if ($plugin->published == '1') {
	        	//add published plugins to arrays respective to
	            //their types
			  	if ($plugin->type == 'payment') {
					$this->payment_plugins[$plugin->name] = $plugin;
					if ($plugin->def == 'default'){
						$this->default_payment = $plugin; //also select default gateway	 
					}
				}
				if ($plugin->type == 'encoding') {
					$this->encoding_plugins[$plugin->name] = $plugin;
				}
			} 
		}
		$this->plugins_loaded = 1;
		return;
	}

	function getPlugin() {
		if (empty ($this->_plugin)) {
			$this->_plugin = $this->getTable("adagencyPlugin");
			$this->_plugin->load($this->_id);
			$this->_plugin->instance = $this->registerPlugin($this->_plugin->filename, $this->_plugin->classname);
			$db = JFactory::getDBO();
			$sql = "select setting,description,value from #__ad_agency_plugin_settings where pluginid='".intval($this->_plugin->id)."' LIMIT 100";
			$db->setQuery($sql);
			$conf = $db->loadObjectList();
			$config = new stdClass;
			$config->headers = array();
			$config->values = array();
			foreach ($conf as $value) {
				$config->headers[] = $value->setting;
				$config->values[] = $value->value;
				$config->descrs[] = $value->description;
			}
			$this->_plugin->config = $config;
		}                                          
		return $this->_plugin;

	}

	function getEncoders() {
		$db = JFactory::getDBO();
		$sql = "select id,name,classname from #__ad_agency_plugins where type='encoding'";
		$db->setQuery($sql);
		$encs = $db->loadObjectList();
		$this->_plugins = array();
		foreach ($encs as $enc) {
		}
		return $encs;
	}

	
	function store () {
		$db = JFactory::getDBO();
		$item = $this->getTable('adagencyPlugin');
		$data = JRequest::get('post');
		if(isset($data['id'])&&($data['id'] != NULL)&&($data['id']>0)) {
			$sql = 'SELECT sandbox FROM #__ad_agency_plugins WHERE id = '.intval($data['id']);
			$db->setQuery($sql);
			$result = $db->loadColumn();
			$data['sandbox'] = $result["0"];
		} else {
			$data['sandbox'] = JRequest::getVar("sandbox", 0, 'post');
		}
		if (!$item->bind($data)){
			$item->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->check()) {
			$item->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->store()) {
			$item->setError($item->getErrorMsg());
			return false;
		}

		$sql = "select setting from #__ad_agency_plugin_settings where pluginid='".intval($item->id)."'";
		$db->setQuery($sql);
		$conf = $db->loadObjectList();
		foreach ($conf as $i => $v) {
			$req = JRequest::getVar($v->setting, '', 'post');
			$sql = "update #__ad_agency_plugin_settings set value='".$req."' where pluginid='".intval($item->id)."' and setting='".$v->setting."'";
			$db->setQuery($sql);
			$db->query();
		}
		return true;
	}	
	
	function registerPlugin ($filename, $classname) {
		$install_path = $this->_installpath;
		if (!file_exists($install_path.$filename)) {
			return 0;
		}
		require_once ($install_path.$filename);
		$plugin = new $classname;
		if (!is_object($plugin) ) {

			return 0;
		}
		foreach ($this->req_methods as $method) {
			if (!method_exists ($plugin, $method) ) {
				return 0;
			}
		}
		//plugin passed basic checks - add it to registered plugins
		if (isset($this->_plugins[$classname])) {
			$this->_plugins[$classname]->instance =& $plugin;
		} else {
			$this->_plugins[$classname] = new stdClass;
			$this->_plugins[$classname]->instance = &$plugin;
		}
		
		return $plugin;
	}

	function installPlugin($path, $plugin_file = '') {
		$db = JFactory::getDBO();
		$plugin_file = trim ($plugin_file);
		if (strlen($plugin_file) < 1) return JText::_('MODPLUGNOFILENAME');
		$ext = substr ($plugin_file, strrpos($plugin_file, ".") + 1);
		if ($ext != 'zip') return JText::_('MODPLUGNOZIP');
		jimport('joomla.filesystem.archive');	
		if (!JArchive::extract($path.$plugin_file, $path)) {
			return JText::_('MODPLUGEXTRACTERR');
		}
		if (!file_exists($path."install")) return JText::_("MODPLUGMISSINGINSTALL");
		$install = parse_ini_file($path."install");
		if (count ($install) < 3) return JText::_("MODPLUGINSTALLCORRUPT");
		if (!isset($install['type']) || !in_array($install['type'], $this->allowed_types)) return JText::_('Bad plugin type');
		$query = "select count(*) from #__ad_agency_plugins where type='".$install['type']."' and name='".$install['name']."'";
  		$db->setQuery($query);
   		$isthere = $db->loadColumn();
		
		if ($isthere["0"]) return JText::_('MODPLUGALLEXIST');// 
		
		$install_path = $this->_installpath;
      		JFile::copy ($path.$install['filename'], $install_path.$install['filename']);    
      		@chmod($install_path.$install['filename'],0755);
	        //Add uploaded plugin to db but do not publish it.
        	if (!is_object($this->registerPlugin($install['filename'], $install['classname']))) return JText::_("MODPLUGREGERR");
	        if(isset($install['display_name'])) {
	        $query = "insert into #__ad_agency_plugins 
				(name, classname, value, filename, type, published, def, sandbox, reqhttps, display_name)
				 values 
				('".$install['name']."', '".$install['classname']."', '',
				'".$install['filename']."', '".$install['type']."', 0, '', '', ".$install['reqhttps'].", '".$install['display_name']."');";
				}
			else {
			$query = "insert into #__ad_agency_plugins 
				(name, classname, value, filename, type, published, def, sandbox, reqhttps)
				 values 
				('".$install['name']."', '".$install['classname']."', '',
				'".$install['filename']."', '".$install['type']."', 0, '', '', ".$install['reqhttps'].");";
				}
			$db->setQuery($query);
	        $db->query();
			$sql = $db->setQuery("SELECT id FROM #__ad_agency_plugins ORDER BY id DESC LIMIT 1");
			$db->query();
			$pluginid = $db->loadColumn();
			$pluginid = $pluginid["0"];
			
			$pluginame = $install['name'];
    	if ($install['type'] == 'payment') {
			$currency = $this->_plugins[$install['classname']]->instance->insert_currency();
		        // Nik (2007-01-14) Check, have already all currency installed
		        $sql = "SELECT COUNT(*) FROM #__ad_agency_currencies WHERE plugname='" . $pluginame . "'";
		        $db->setQuery($sql);
				$count = $db->loadColumn();
		        if ( $count["0"] == 0 ) {
			        foreach ($currency as $i => $v) {
			                $query = "INSERT INTO `#__ad_agency_currencies` ( `id` , `plugname`, `currency_name` , `currency_full` ) VALUES ( '', '".$pluginame."','".$i."', '".$v."' )";
			                $db->setQuery($query);
		                	$db->query();
				}   	    
	        }
			
			$sql = "insert into #__ad_agency_plugin_settings(`pluginid`, `setting`, `description`, `value`) values ";
			$sets = parse_ini_file($path."install", true);
			$sets = $sets['settings'];
			foreach ($sets as $set => $descr) {
				$sqltmp[] = "('".$pluginid."' ,'".$set."' ,'".$descr."', '')";
			}		
			$sql .= implode(",", $sqltmp);
			$db->setQuery($sql);
			$db->query();

		}
         //publish and set paypal plugin as default
	     // Ad Agency Special Preference: to set paypal as default - BEGIN 
   		if ($install['name'] == 'paypal') {
	            //Kiril: removed default value of PayPal e-mail
        	    $query = "update #__ad_agency_plugins set published=1, def='default' where filename='".$install['filename']."';";
	            $db->setQuery($query);
        	    $db->query();
        
	    	}
	// Ad Agency Special Preference: to set paypal as default - END 
	    	return JText::_("MODPLUGSUCCINSALLED");
	}

	function upload() {
		$table_entry = $this->getTable ("adagencyPlugin");
		jimport('joomla.filesystem.file');
		$file = JRequest::getVar('pluginfile', array(), 'files');	
		$install_path = JPATH_ROOT.DS."tmp".DS."adagencyplugin".DS;
		Jfolder::create ($install_path);
		if (JFile::copy($file['tmp_name'], $install_path.$file['name'], '')) {
			$res = $this->installPlugin($install_path, $file['name']);
			JFolder::delete ($install_path);
		} else {
			$res = JText::_('MODPLUGCOPYERR');
		}
		return $res;
	}

    function BEPluginHandler ($param = 0) {
        $result = array();
        if (!is_object($this->_plugin->instance) ) return;
        $i = 0;
        $plugin = $this->_plugin;
            $sbx = $plugin->sandbox;
            $pluginform = array(
                "header" => $plugin->name . ' ' . (JText::_("MODPLUGCONFIG")),
                "header1" => $plugin->config->headers,
            "descriptions" => $plugin->config->descrs,
                "value" => $plugin->config->values,
                "isdef"=> $plugin->def,
                "sandbox" => $plugin->name."_sandbox",
                "sbx" => $sbx
            );
        $result[$i] = $pluginform;
        $result[$i]["type"] = $plugin->type;
        $result[$i]["pluginname"] = $plugin->name;
        if ($plugin->type != 'encoding') {
            $result[$i]["reqhttps"] = $plugin->reqhttps;
            $result[$i]["reqhttps_name"] = $plugin->name."_reqhttps";
        }
        $result[$i]["published"] =$plugin->published;
        return $result;
    }

	function delete () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('adagencyPlugin');
		jimport('joomla.filesystem.file');
		foreach ($cids as $cid) {
			$sql = "select name from #__ad_agency_plugins where id = '".intval($cid)."'";
			$db->setQuery($sql);
			$result = $db->loadColumn();
			$plugname = $result["0"];
			
			
			$sql = "delete from #__ad_agency_plugin_settings where pluginid = '".intval($cid)."'";
			$db->setQuery($sql);
			$db->query();

			$sql = "delete from #__ad_agency_currencies where plugname = '".stripslashes($plugname)."'";
			$db->setQuery($sql);
			$db->query();
			
			$item->load($cid);
			JFile::delete($this->_installpath.$item->filename);
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
		$item = $this->getTable('adagencyPlugin');
		if ($task == 'publish'){
			$sql = "update #__ad_agency_plugins set published='1' where id in ('".implode("','", $cids)."')";
			$res = 1;
		} else {
			$sql = "update #__ad_agency_plugins set published='0' where id in ('".implode("','", $cids)."')";
			$res = -1;

		}
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return $res;


	}

	function getPluginOptions ($selected = '') {
		$content = '<select name="payment_type">';
		if (isset($this->payment_plugins)) {
		foreach ($this->payment_plugins as $plugin) {
		    	$content .= '<option value="'.str_replace(".php","",$plugin->filename).'"';				if($plugin->def == "default"){					 $content.= 'selected';				}
				if(isset($plugin->display_name)) { $plugin->name=$plugin->display_name; } 
				else { $plugin->name=$plugin->classname; } 
		    	$content .= '>'.(JText::_($plugin->name)).'</option>';
		}
		}
		return $content;
	}

	function getListPluginCurrency() {
		$db = JFactory::getDBO();
		$sql = "select c.*, p.name as pluginame from #__ad_agency_currencies c, #__ad_agency_plugins p where p.id=c.pluginid and p.published=1";

		$db->setQuery($sql);
		$plugs = $db->loadObjectList();
		$res = array();
		foreach ($plugs as $plug) {
			$res[$plug->pluginame][$plug->currency_name] = $plug->currency_full;
		}
		return $res;

	}
    
	function getEncoder ($method) {
		$handler = -1;
		if (count ($this->encoding_plugins) < 1 ) { 
				return (-1);
		} else {
			foreach ($this->encoding_plugins as $plugin) {
				if ($method == $plugin->name ) {
					$handler = $this->_plugin[$plugin->classname]->instance; //we found handler for encoding
					break;
				}
					
			}
			
		}
		return $handler;	
	}

	function encodeZipFile ( $method, $phpversion, $srcDir, $srcFile, $tmpDir, $dstDir, $files, $mainFile, $passphrase, $trial, $now, $licenseFile = "license.txt" ) {
		jimport("joomla.filesystem.file");
		require_once (JPATH_ROOT.DS."administrator".DS."includes".DS."pcl".DS."pclzip.lib.php");
		@unlink ($dstDir.$srcFile);
		@unlink ($dstDir.$mainFile);
	    @unlink ($tmpDir.$mainFile);
		$srcZip = new PclZip($srcDir.$srcFile);
		$x = $srcZip->extract(PCLZIP_OPT_BY_NAME, $mainFile, PCLZIP_OPT_PATH, $tmpDir);
		if ($x == 0) {
			die ($srcZip->errorInfo(true));
		}
	    $mainZip = new PclZip($tmpDir.$mainFile); 
	
		if ($mainZip->extract(PCLZIP_OPT_PATH, $tmpDir."1/") == 0 ) {

		}
		if ($mainZip->extract(PCLZIP_OPT_PATH, $dstDir) == 0 ) {

		}
	
		$handler = $this->getEncoder($method);
		if (!is_object($handler)) return (-2); //no handler found - internal error or hijack attempt
	
		foreach ( $files as $file ) {
			$file = trim($file);
			$handler->performEncoding ($file, $licenseFile, $passphrase, $tmpDir."1/", $dstDir, $now, $this->encoding_plugins[$method]); // plugin does all required
		}                    
	    $i = 0;
		$curdir = getcwd ();
		chdir ( $dstDir  );
	
		foreach ($files as $file){
		        $file = trim ($file);
			$mainZip->delete (PCLZIP_OPT_BY_NAME, $file);
			$r = $mainZip->add ($dstDir.$file, PCLZIP_OPT_REMOVE_PATH, $dstDir);
	        ++$i;
		}
	
		JFolder::delete($dstDir);
		JFolder::create($dstDir);
		JFile::copy ($srcDir.$srcFile, $dstDir.$srcFile);
		$srcZip = new PclZip (	$dstDir.$srcFile);
		$v_list = $srcZip->delete (PCLZIP_OPT_BY_NAME, $mainFile);
		$res = $srcZip->add ($tmpDir.$mainFile, PCLZIP_OPT_REMOVE_PATH, $tmpDir);
		if ($res == 0) $res = $srcZip->create ($tmpDir.$mainFile, PCLZIP_OPT_REMOVE_PATH, $tmpDir);
		chdir($curdir);
		JFolder::delete($tmpDir);
	}

	/*
	 * License file generator - converted code from original adagency
	 */
	function genLicenseFile ( $method, $zipFile, $subZipFile, $domain="localhost", $devdomain="localhost", $subdomains, $licenseFile, $passphrase, $trial_period = "", $outDir ) {
	
		//gen the license file
		foreach ($subdomains as $i => $v) {
	
			if (isset($subdomains[$i]) && trim($subdomains[$i]) != '') {
				$subdomains[$i] = preg_replace ("/^www\./", "", $subdomains[$i]);
				$subdomains[$i] = str_replace (".".$domain, "", $subdomains[$i]);
				$subdomains[$i] = str_replace ($domain, "", $subdomains[$i]);
				
				if (trim($subdomains[$i]) != '') {
					$tmp = trim( $subdomains[$i] );
					
					if (strlen(trim($domain)) > 0 ){ 			
						$subdomains[$i] = trim( $subdomains[$i] ) . "." . $domain;
						$subdomains[] = "www.".trim( $subdomains[$i] );// . "." . $domain;
					} else unset ($subdomains[$i]);
					if (strlen(trim($devdomain)) > 0 ){ 			
						$subdomains[] = trim( $tmp ) . "." . $devdomain;
						$subdomains[] = "www." . trim( $tmp ) . "." . $devdomain;
					}
	
					
				} else unset ($subdomains[$i]);
			}
		}
	
		$subdomains[] = '127.0.0.1';
		if ( is_array( $subdomains ) ) {
			$subdomains_text = @implode( ",",$subdomains );
		} else {
			$subdomains_text = '';
		}
		
        $handler = $this->getEncoder($method);

		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
		$handler->genLicense($domain, $devdomain, $subdomains_text, $licenseFile, $passphrase, $trial_period = "", $outDir, $this->encoding_plugins[$method]);
		
		//common routines - lets they stay in this form for a while
		$curdir = getcwd ();
		
		//extract the sub zip file
		chdir ( $outDir );
		jimport("joomla.filesystem.file");
		require_once (JPATH_ROOT.DS."administrator".DS."includes".DS."pcl".DS."pclzip.lib.php");

	
		$srcZip = new PclZip($zipFile);
	
		if ($srcZip->extract(PCLZIP_OPT_BY_NAME, $subZipFile) == 0) {
		}
	
		$subZip = new PclZip($subZipFile); 
	
		$license_file_text = implode ('', file($licenseFile));
		$subZip->add ($licenseFile);
		
	
		$srcZip->delete(PCLZIP_OPT_BY_NAME, $subZipFile);
		$res = $srcZip->add ($subZipFile);
		if ($res == 0) $srcZip->create ($subZipFile);
	
		//remove temp files
		@unlink( $subZipFile );
		@unlink( $licenseFile );
		//done
		chdir( $curdir );
		return $license_file_text;
	
	}

	/*
	 * function from original code... lets see what it does.
	 */

	function genLoaders ( $method, $phpVersions, $platforms, $outDir ) {
		//lets select plugin that handles choosen encoding method
		$handler = $this->getEncoder($method);
		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
	
		$handler->genLoader ($phpVersions, $platforms, $outDir);
		
	}
	
	/*
	 * this function performs the same actions as one before... i wonder if there are any reasons to keep it
	 * adds some strange files to encoded package... lets ommit it for now.
	 */

	function embbedLoader( $method, $zipFile, $subZipFile, $outDir ) {
	
		$handler = $this->getEncoder($method);
		
		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
	
		$loaderFile = $handler->getLoader ();
	
		//insert the license file into the zip file
		jimport("joomla.filesystem.file");
		require_once (JPATH_ROOT.DS."administrator".DS."includes".DS."pcl".DS."pclzip.lib.php");


		$curdir = getcwd();
		chdir( $outDir );
	
	
		$srcZip = new PclZip($zipFile);
	
	
		if ($srcZip->extract(PCLZIP_OPT_BY_NAME, $subZipFile) == 0) {
			die ($srcZip->errorInfo(true));
		}
	
		$subZip = new PclZip($subZipFile); 
		$subZip->add ($loaderFile);
	
		$srcZip->delete(PCLZIP_OPT_BY_NAME, $subZipFile);
		$res = $srcZip->add ($subZipFile);
		if ($res == 0) $srcZip->create ($subZipFile);
	
		@unlink ( $subZipFile );
		@unlink ( $loaderFile );
		//done
		chdir ( $curdir );
	}
	
	function getEncPlatformsForMethod ($method) {
		$handler = -1;
		//lets select plugin that handles choosen encoding method
		if (count ($this->encoding_plugins) < 1 ) { 
				return (-1);//do not to forget place errorcode switch in calling file so user
							// may know if encoding was successful or not.
		} else {
			foreach ($this->encoding_plugins as $plugin) {
				if ($method == $plugin->name ) {
					$handler = $this->_plugins[$plugin->classname]->instance; //we found handler for encoding
					break;
				}
					
			}
			
		}
		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
	
		$platform_options = $handler->getPlatforms ();
		return ($platform_options);	
	}
	
	function BackPluginHandler ($param = 0) {
		global $digistor_plugins, $plug_list_tmp;
		
		$result = array();
		$db = JFactory::getDBO();
		$sql = "select * from #__ad_agency_plugins";
		$db->setQuery($sql);
		$this->plugins = $db->loadObjectList();
		
		if(count($this->plugins) < 1 ){
			return;
		}
		
		$i = 0;
		foreach ($this->plugins as $plugin) {
			if($param == 0){
				$database = JFactory::getDBO();
		        $query = "select value from #__ad_agency_plugins where name='".stripslashes($plugin->name)."'";
		        $database->setQuery($query);
				$temp_result = $database->loadColumn();
		        $val = $temp_result["0"];
		        
				$query = "select id from #__ad_agency_plugins where name='".stripslashes($plugin->name)."'";
		        $database->setQuery($query);
				$temp_result = $database->loadColumn();
		        $this_plugin_id = $temp_result["0"];
				
				$query = "select sandbox from #__ad_agency_plugins where name='".stripslashes($plugin->name)."'";
		        $database->setQuery($query);
		        $temp_result = $database->loadColumn();
				$sbx = $temp_result["0"];
				
		        $pluginnname = strtoupper($plugin->name);
		        $textplugin = $pluginnname;
		        $plugin_form = array(
		            "header" => $textplugin,
					"id" => $this_plugin_id,
		            "header1" => array(''),
		            "name" => array($plugin->name."_account[]"),
		            "value" => explode(" ",$val),
		            "isdef"=> $plugin->name."_def",
		            "sandbox" => $plugin->name."_sandbox",
		            "sbx" => $sbx
		        );
		        $result[$i] = $plugin_form;
        		$result[$i]["type"] = $plugin->type;
				$result[$i]["pluginname"] = $plugin->name;
				if ($plugin->type != 'encoding') {
					$result[$i]["reqhttps"] = $plugin->reqhttps;
					$result[$i]["reqhttps_name"] = $plugin->name."_reqhttps";
				}
				if ($plugin->published == 1) {
					$result[$i]["published"] = 1;
				} else {
	                $result[$i]["published"] = 0;
				}
				
				$i++;
			}
	 	}
		return $result;		
	}

};
?>
