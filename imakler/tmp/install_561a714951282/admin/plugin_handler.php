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

function my_in_array ($needle, $haystack) {
	foreach ($haystack as $i => $v) {
		if (get_class($v) == get_class($needle)) return true;
    }
	return false;
}

class HandleAdAgencyPlugins {
	var $allowed_types = array('payment', 'encoding');
	var $plugin_instances = array();
	var $plugins = array();
	var $payment_plugins = array();
	var $encoding_plugins = array();
	var $req_methods = array ('getBEData');
	var $plugins_loaded = 0;
	var $default_payment = -1;
	
	function HandleAdAgencyPlugins() {
		$this->loadPlugins();
	}	
	
	function getlistPlugins () {
		if (empty ($this->_plugins)) {
			$database = JFactory::getDBO();	
			$sql = "select * from #__ad_agency_plugins";
			$database->setQuery($sql);
			$this->_plugins = $database->loadObjectList();
			
		}
		return $this->_plugins;

	}	
	/*
	 * Reads plugin directory and compiles list of plugins.
	 * 
	 * 11/08/2006 Function now checks if plugin file is in the database and removes it in case it is absent.
	 */
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

	
	/*
	 * Includes plugins code.
	 */
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
	
	 function registerPlugin ( $filename, $classname) { 
		global $mosConfig_absolute_path;
		$mosConfig_absolute_path = JPATH_SITE;
		$database = JFactory::getDBO();	
		if (!file_exists($mosConfig_absolute_path."/administrator/components/com_adagency/plugins/".$filename)) {
			return 0;
		}
		
		require_once ($mosConfig_absolute_path."/administrator/components/com_adagency/plugins/".$filename);
		
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
		$this->plugin_instances[$classname] = $plugin;
		return 1;		
	}
	
	/*
	 * Generates form fields for be-settings panel so admin may provide
	 * payment information for each payment system. 
	 * 
	 * 11/08/2006 Added workaround for case if no plugins installed.
	 */
	function BEPluginHandler ($param = 0) {
		global $digistor_plugins, $plug_list_tmp;
		
		$result = array();
		if (count($this->plugins) < 1 ) return;
		$i = 0;
		foreach ($this->plugins as $plugin) {
		
			if ($param == 0) {
				$result[$i] = $this->plugin_instances[$plugin->name]->getBEData();
				$result[$i]["type"] = $plugin->type;
				$result[$i]["pluginname"] = $plugin->name;
				if ($plugin->type != 'encoding') {
					$result[$i]["reqhttps"] = $plugin->reqhttps;
					$result[$i]["reqhttps_name"] = $plugin->name."_reqhttps";
				}
				
				if ($plugin->publishing == 1) {
					$result[$i]["published"] = 1;
				} else {
	                $result[$i]["published"] = 0;
				}
			}
				++$i;
	
	 		
		}
		return $result;	
		
	}
	
	
	function goToHTTPS($sid) {
		global $configs, $my;
		if (getenv('HTTPS') != 'on'){
			global $mosConfig_live_site;
			$url = str_replace("http://", "https://", $mosConfig_live_site);
			$mosConfig_live_site = $url;
			$result = '
				<form name="dataform" method="post" action="'.$url.'/index.php?option=com_ijoomla_ad_agency&task=checkout&Itemid='.$configs['itid'].'">
					<input type="hidden" name="userid" value="'.$my->id.'" />
					<input type="hidden" name="sid" value="'.$sid.'" />				
				</form>
			';
			$result .= "<script language='javascript'>
							document.dataform.submit();
						</script>";
			return $result;
			
		} else {
			return;
		}
	}
	
	/*
	 * Generates fe html-output, so fe-user may make payment via chooses system.
	 */
	function FEPluginHandler ($pay_type,  $items, $tax, $redir = false, $profile) {
		global $digistor_plugins, $database, $my, $configs;
		$result = array();
		$total = $tax['taxed'];
		if (count($this->payment_plugins) < 1 ) return -1;
		$plugin_exists = 0;
	
		if (!isset($this->payment_plugins[$pay_type])) {//if selected payment method is not available
			if (is_object($this->default_payment)) {//try default payment gateway
				$plugin = $this->default_payment;	
			} elseif (!is_object($this->default_payment)) {//no default available
				foreach ($this->payment_plugins as $plug) {//select first gateway available
					$plugin = $plug;
					break;	
				}	
			} else {
				die (_CANTPROCESSPAYMENT);	
			}
		} else { //all os ok - use normal gateway
			$plugin = $this->payment_plugins[$pay_type];
		}
		
		if ($plugin->reqhttps != 0 && getenv('HTTPS') != 'on') {//plugin requires https connection to perform checkout
			return $this->goToHTTPS ($sid);	
			
		}
	
		//now we're ready to call plugin	
		$result = $this->plugin_instances[$plugin->name]->getFEData($items, $tax, $redir, $profile, $plugin->sandbox);
			
	 	
		if (strlen(trim($result)) < 1) {
				echo "<script language='javascript'>alert ('"._INTERNAL_ERROR."'); self.history.go(-1);</script>";
				return;
		}
		return $result;			
	}
	
	function record_transactiondata($plugin, $result) {
		$database = JFactory::getDBO();	
		$database->setQuery("SELECT `value` FROM `#__ad_agency_plugins` WHERE `classname`='".$plugin->classname."'"); 
		$myaccount = $database->loadResult(); 	
		$oid = intval($_POST["custom"]);
		if($_POST['payment_status']=="Completed" && $myaccount==$_POST['receiver_email']) { 
			$database->setQuery("UPDATE `#__ad_agency_order` SET `status`='paid' WHERE `oid`='".$oid."'"); 
			$database->query(); 
		}		
	}
	
	/*
	 * After user returns from payment system we should receive responce on if transaction 
	 * was successfull, tell result to user and store info into db. Has to have
	 * real ip.
	 */
	function payment_notify ($plugin) {
	   
		$result = $this->plugin_instances[$plugin->classname]->notify($plugin->sandbox);
		return $result;
	
	}
	
	
	function payment_return ($plugin) {
		$_SESSION['in_trans'] = 1;
		$this->record_transactiondata($plugin, "success");
		$result = $this->plugin_instances[$plugin->classname]->return1();
		return $result;
	}
	
	function payment_fail ($plugin) {
		$_SESSION['in_trans'] = 1;
		$this->record_transactiondata($plugin, "fail");
		$result = $this->plugin_instances[$plugin->classname]->return2();
		return $result;
	}
	
	/*
	 * Helper function, possibly will be extended in future.
	 */
	function selectPlugins () {
		global $database, $plug_list_tmp;
		if (!$this->plugins_loaded) $this->loadPlugins();
		
		if (count($this->plugins) > 0) {
			foreach ($this->plugins as $name => $plugin) {
				$this->plugins[$name]->info = $this->plugin_instances[$name]->get_info();
			}	
		}
		
		return $this->plugins;
	
	}
	
	/*
	 * Following functions are supposed to handle encoding plugins.
	 * To perform encoding routins one have to provide following data:
	 *  - filename to be encoded
	 *  - encoding engine
	 *  - encoding options
	 *  - password options
	 *  - trial data
	 * encoding handling functions would do following: 
	 *  - (class calls for pluign routine with params specified)
	 *  - 
	 *  - ???
	 *  In most parts encoder plugin behaviour is the same as the payment one.
	 *  But the difference is in the dynamic: payment routines require little actions but 
	 * redirecting/accepting returned user. While encoder needs to generate license file
	 * for every download, encode with possibly changing params, provide extended 
	 * feedback so customer may decode his product.
	 * 
	 * Hooks are to be placed in both BE and FE to handle plugin configuration and encoding. 
	 */
	
	function getEncoder ($method) {
		$handler = -1;
		if (count ($this->encoding_plugins) < 1 ) { 
				return (-1);//do not to forget place errorcode switch in calling file so user
							// may know if encoding was successful or not.
		} else {
			foreach ($this->encoding_plugins as $plugin) {
				if ($method == $plugin->name ) {
					$handler = $this->plugin_instances[$plugin->name]; //we found handler for encoding
					break;
				}
					
			}
			
		}
		return $handler;	
	}
	
	/*
	 * Function from orgignal encoding routines.
	 */
	
	function encodeZipFile ( $method, $phpversion, $srcDir, $srcFile, $tmpDir, $dstDir, $files, $mainFile, $passphrase, $trial, $now, $licenseFile = "license.txt" ) {
		global $my, $digistor_enc_plugins, $mainframe, $dir, $r, $mosConfig_absolute_path;  
	
		include_once ($mosConfig_absolute_path."/administrator/includes/pcl/pclzip.lib.php");
	
		@unlink ($dstDir.$srcFile);
		@unlink ($dstDir.$mainFile);
        @unlink ($tmpDir, $mainFile);
	
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
		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
	
		foreach ( $files as $file ) {
			$file = trim($file);
			$handler->performEncoding ($file, $licenseFile, $passphrase, $tmpDir."1/", $dstDir, $now); // plugin does all required
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
	
		$this->recursive_remove_directory($dstDir, TRUE);
		copy ($srcDir.$srcFile, $dstDir.$srcFile);
		$srcZip = new PclZip (	$dstDir.$srcFile);
		$v_list = $srcZip->delete (PCLZIP_OPT_BY_NAME, $mainFile);
		$res = $srcZip->add ($tmpDir.$mainFile, PCLZIP_OPT_REMOVE_PATH, $tmpDir);
		if ($res == 0) $res = $srcZip->create ($tmpDir.$mainFile, PCLZIP_OPT_REMOVE_PATH, $tmpDir);
		chdir($curdir);
		$this->recursive_remove_directory($tmpDir, TRUE);
	}
	
	/*
	 * License file generator - converted code from original digistore
	 */
	function genLicenseFile ( $method, $zipFile, $subZipFile, $domain="localhost", $devdomain="localhost", $subdomains, $licenseFile, $passphrase, $trial_period = "", $outDir ) {
		global $my, $digistor_enc_plugins, $mainframe, $mosConfig_absolute_path;
	
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
		$handler->genLicense($domain, $devdomain, $subdomains_text, $licenseFile, $passphrase, $trial_period = "", $outDir);
		//common routines - lets they stay in this form for a while
		$curdir = getcwd ();
		//extract the sub zip file
		chdir ( $outDir );
		include_once ($mosConfig_absolute_path."/administrator/includes/pcl/pclzip.lib.php");
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
		global $platform_options, $my, $mainframe, $mosConfig_absolute_path, $digistor_enc_plugins;
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
		global $my, $mainframe, $digistor_enc_plugins, $mosConfig_absolute_path;
	
		$handler = $this->getEncoder($method);
		
		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
	
		$loaderFile = $handler->getLoader ();
	
		//insert the license file into the zip file
		include_once ($mosConfig_absolute_path."/administrator/includes/pcl/pclzip.lib.php");
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
					$handler = $this->plugin_instances[$plugin->name]; //we found handler for encoding
					break;
				}
					
			}
			
		}
		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
	
		$platform_options = $handler->getPlatforms ();
		return ($platform_options);	
	}
	
		function dispatchMail($orderid, $amount, $licenses, $timestamp, $items, $sid) {
		}     
	
		//integrate with idev_affiliate
		function affiliate ($total) {    	
	    }    
	
		//function addOrder ($ordid) {
		function addOrder ($items, $cust_info, $now, $paymethod, $status = "Active") {	
			
		}
		
		function addLicenses ($items, $orderid, $now ) {
		}
	
	function recursive_remove_directory($directory, $empty=FALSE)
	{
	    // if the path has a slash at the end we remove it here
	    if(substr($directory,-1) == '/')
	    {
	        $directory = substr($directory,0,-1);
	    }
	 
	    // if the path is not valid or is not a directory ...
	    if(!file_exists($directory) || !is_dir($directory))
	    {
	        // ... we return false and exit the function
	        return FALSE;
	 
	    // ... if the path is not readable
	    }elseif(!is_readable($directory))
	    {
	        // ... we return false and exit the function
	        return FALSE;
	 
	    // ... else if the path is readable
	    }else{
	 
	        // we open the directory
	        $handle = opendir($directory);
	 
	        // and scan through the items inside
	        while (FALSE !== ($item = readdir($handle)))
	        {
	            // if the filepointer is not the current directory
	            // or the parent directory
	            if($item != '.' && $item != '..')
	            {
	                // we build the new path to delete
	                $path = $directory.'/'.$item;
	 
	                // if the new path is a directory
	                if(is_dir($path)) 
	                {
	                    // we call this function with the new path
	                    HandleAdAgencyPlugins::recursive_remove_directory($path);
	 
	                // if the new path is a file
	                }else{
	                    // we remove the file
	                    unlink($path);
	                }
	            }
	        }
	        // close the directory
	        closedir($handle);
	 
	        // if the option to empty is not set to true
	        if($empty == FALSE)
	        {
	            // try to delete the now empty directory
	            if(!rmdir($directory))
	            {
	                // return false if not possible
	                return FALSE;
	            }
	        }
	        // return success
	        return TRUE;
	    }
	}
	
	
	function setAccess ($directory, $mode, $setself = true) {
		   if(substr($directory,-1) == '/')
	    {
	        $directory = substr($directory,0,-1);
	    }
	 
	    // if the path is not valid or is not a directory ...
	    if(!file_exists($directory) || !is_dir($directory))
	    {
	        // ... we return false and exit the function
	        return FALSE;
	 
	    // ... if the path is not readable
	    }elseif(!is_readable($directory))
	    {
	        // ... we return false and exit the function
	        return FALSE;
	 
	    // ... else if the path is readable
	    }else{
	 
	        // we open the directory
	        $handle = opendir($directory);
	 
	        // and scan through the items inside
	        while (FALSE !== ($item = readdir($handle)))
	        {
	            // if the filepointer is not the current directory
	            // or the parent directory
	            if($item != '.' && $item != '..')
	            {
	                // we build the new path to delete
	                $path = $directory.'/'.$item;
	 
	                // if the new path is a directory
	                if(is_dir($path)) 
	                {
	                	chmod($path, $mode);
	                    // we call this function with the new path
	                    HandleAdAgencyPlugins::setAccess($path, $mode);
	 
	                // if the new path is a file
	                }else{
	                    // we remove the file
	                    chmod($path, $mode);
	                }
	            }
	        }
	        // close the directory
	        closedir($handle);
	 
	        // if the option to empty is not set to true
	        if($setself == TRUE)
	        {
	            // try to delete the now empty directory
	            if(!chmod($directory, $mode))
	            {
	                // return false if not possible
	                return FALSE;
	            }
	        }
	        // return success
	        return TRUE;
	    }
	}

	function installPlugin($path, $plugin_file = '') {
		
	}

	function publishPlugins($publishing = true) { 
		global $database, $allow;
		foreach ($this->plugins as $plugin) {
			if ($plugin->type == 'encoding' && !file_exists($plugin->value)){
				//configuration value of the encoding plugin stores full path to the encoder executable
				//if it does not exist - plugin won't work 
		 		$tmp = mosGetParam($_REQUEST, $plugin->name );     
				if ($tmp == 1) {
 	   		            $msg = _JAS_PLG_ERROR;
				}
		        $query = "update #__ad_agency_plugins set publishing=0 where name='".$plugin->name."'";
		        $database->setQuery($query);
		        $database->query();         
		        continue;
		    }   
		
		    $param[$plugin->name] = mosGetParam($_REQUEST, $plugin->name );     
		    
		}
		foreach ($param as $i => $v) {
		    if ($v == 1 && $publishing) {
		    	$query = "select value from #__ad_agency_plugins where name='".$i."'";
			    $database->setQuery($query);
			    $allow = $database->loadResult();
			    if ($allow) {	
		            $query = "update #__ad_agency_plugins set publishing=1 where name='".$i."'";
				    $database->setQuery($query);
				    $database->query();
				} else if (!$allow) { mosErrorAlert( _JAS_PLG_UNCOMPLETED );
										exit();}
		} elseif ($v == 1) {
            $query = "update #__ad_agency_plugins set publishing=0 where name='".$i."'";
		        $database->setQuery($query);
		        $database->query();
		    }
		}
		return $msg;
	}
	
	
	function deletePlugins() {
	    global $mosConfig_absolute_path, $database;
		$param = array();	
		$plugins = $this->plugins;
		foreach ( $plugins as $i => $v ) {
		    $param[$v->filename] = mosGetParam($_REQUEST, $v->name );
		}
		
		foreach ($param as $i => $v) {
		    if ( $v == 1 ) {
			    $query = "select name from #__ad_agency_plugins where filename='".$i."'";
		        $database->setQuery($query);
		        $name = $database->loadResult();
		
		   
		        $query = "delete from #__ad_agency_currencies where plugname='".$name."'";
		    	$database->setQuery($query);
		    	$database->query();
		
		    	@unlink($mosConfig_absolute_path."/administrator/components/com_ijoomla_ad_agency/plugins/".$i);
			    $query = "delete from #__ad_agency_plugins where filename='".$i."'";
			    $database->setQuery($query);
			    $database->query();

		        
		    }
		
		}
	
	}
	
	function purgePlugins() {
		global $database, $mosConfig_absolute_path;
		$sql = "truncate table #__ad_agency_plugins";
		$database->setQuery($sql);
		$database->query();
		$path = $mosConfig_absolute_path."/administrator/components/com_ijoomla_ad_agency/plugins/";
		$this->recursive_remove_directory ($path);
		$fp = fopen ($path."index.html", "w");
		fwrite ($fp, "");
		fclose ($fp);
		return;	
	}
		
	function getPluginOptions (&$valid) {
		$defcon = get_defined_constants();
		$content = '';
		foreach ($this->payment_plugins as $plugin) {		
	    		$content .= '<option value="'.$plugin->classname.'"';
	    		if ($plugin->def=="default") $content .= ' selected="selected"';
		    	$content .= '>'.$defcon['_JAS_'.strtoupper($plugin->classname)].'</option>';
		}
		$valid = 1;			
		return $content;
	}
	
	function interceptPaymentResponse($task) {
		$flag = 0;
		$tasks = JRequest::getVar('tasks', '', 'get');
		
		if ( count ($this->payment_plugins  ) >0 ){ 
			foreach ($this->payment_plugins as $plugin) {
	    		if ($tasks == $plugin->classname."_notify") {
			        $content = $this->payment_notify($plugin);
			        echo ($content);
			        $flag = 1;
			        break;
	    		}
			}

			foreach ($this->payment_plugins as $plugin) {
			    if ($tasks == $plugin->classname."_return") {
			        $content = $this->payment_return($plugin);
			        echo ($content);
			        $flag = 1;
			        break;		
			    }
			}
	
			foreach ($this->payment_plugins as $plugin) {
			    if ($tasks == $plugin->classname."_fail") {
			        $content = $this->payment_fail($plugin);
			        echo ($content);
			        $flag = 1;
			        break;
			    }
			}
		}
		return $flag;	
	}
	
	function fillMy ($id) {
		global $my, $database;;	
		$query = "SELECT id, name, email, block, sendEmail, registerDate, lastvisitDate, activation, params"
						. "\n FROM #__users"
						. "\n WHERE id = ". intval( $id )
						;
		$database->setQuery( $query );
		$database->loadObject( $my );
		
	}
	
	function checkProfileCompletion () {
	}

	function storeTransactionData ($items, $orderid, $tax, $sid) {
	}
	
	function goToSuccessURL () {
		$page_itemid = JRequest::getInt('Itemid','0');
		if($page_itemid != '0'){
			$Itemid = "&Itemid=".$page_itemid;
		} else {
			$Itemid = NULL;
		}
			
		$mosConfig_live_site = JURI::base();
		$success_url = $mosConfig_live_site."/index.php?option=com_adagency&controller=adagencyCampaigns&task2=complete".$Itemid;

		echo ("<script language='javascript'>window.location='".$success_url."';</script>");
		
	}
	
	function goToFailedURL () {
		$page_itemid = JRequest::getInt('Itemid','0');
		if($page_itemid != '0'){
			$Itemid = "&Itemid=".$page_itemid;
		} else {
			$Itemid = NULL;
		}
	
		$mosConfig_live_site = JURI::base();
		$pending_url = $mosConfig_live_site."/index.php?option=com_adagency&controller=adagencyCampaigns&task2=failed".$Itemid;

        echo ("<script>window.location='".$pending_url."'</script>" );
	}
	
	function addFreeProduct($items, $customer_info, $sid) {
		global $database, $configs, $my;
  		$tax = calc_price($items, $customer_info, $sid);
        $now = time();
        $non_taxed = $tax['total'];//$total;
        $total = $tax['taxed'];
        $currency = $tax['currency'];
        $licenses = $tax['licenses'];
        $taxa = $tax['value'];
        $shipping = $tax['shipping'];
        $orderid = $this->addOrder($items, $customer_info, $now, 'free', $sid);
	    $this->addLicenses($items, $orderid, $now);
       	$this->dispatchMail($orderid,$total,$licenses, $now, $items, $sid);
    	emptyCart($sid );
    	$_SESSION['in_trans'] = 1;
    	$this->storeTransactionData ($items, $orderid, $tax, $sid);
    	
    	$this->goToSuccessURL ($sid);
			return;   
		
	}
	
	function performCheckout($order_row = null, $total = 0, $profile = null) {
    }
};
?>