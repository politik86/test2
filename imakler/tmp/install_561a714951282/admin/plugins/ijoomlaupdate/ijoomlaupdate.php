<?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class  plgSystemiJoomlaUpdate extends JPlugin{

	public function plgSystemiJoomlaUpdate(&$subject, $config){
		parent::__construct($subject, $config);
		$this->mainframe = JFactory::getApplication();	
		// Load javascript
		$this->loadPlugin();
	}

	function onAfterDispatch(){
		if($this->loadPlugin()){			
			JHTML::_('behavior.modal');
		}
	}

	public function onAfterRender(){
		if($this->loadPlugin()){
			$this->renderStatus();
		}	
	}
	
	public function loadPlugin(){
		// Load only for backend
		if($this->mainframe->isAdmin()){			
			if($this->isCurlInstalled() == true){
				return true;
			}
			return false;
		}
		return false;
	} 
	
	function isCurlInstalled() {
	    $array = get_loaded_extensions();
		if(in_array("curl", $array)){
			return true;
		}
		else{
			return false;
		}
	}
		
	function setParamsLastDate(){
		$db = JFactory::getDBO();
		$sql = "select `params` from #__extensions  where element='ijoomlaupdate'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		$result = $result["0"];
		
		$date = date('Y-m-d');
		if(isset($result) && $result != "" && $result != "{}"){
			$result = json_decode($result);
			$result->lastcheck = $date;
		}
		else{
			$result = array();
		}
		$sql = "update #__extensions set params='".json_encode($result)."' where element='ijoomlaupdate'";	
		$db->setQuery($sql);
		$db->query();
	}
	
	public function renderStatus(){
	
		$button	= $this->getButton();
		$this->setParamsLastDate();
		$html	= JResponse::getBody();
		$replace_string = '<ul class="nav pull-right">';
		
		$new_text = '<div id="alertijoomla" class="modal fade in" style="display: none; width: 650px;" style="z-index:10000;">
						<div class="modal-header">
						</div>
						<div class="modal-body"></div>
						<div class="modal-footer">
							<a href="#" class="btn" data-dismiss="modal" id="close-modal-btn">Close</a>
						</div>
					</div>';
		
		$html = str_replace($replace_string, $replace_string."<li style=\"padding-top:5px\">".$button."</li>", $html);
		
		$html = str_replace('</body>', $new_text."</body>", $html);
		
		
		JResponse::setBody($html);
		
	}
	
	function existComponent($component){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__extensions where element = '".$component."'";			
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		$result = $result["0"];
		
		if($result > 0){
			return true;
		}
		return false;
	}
	
	public function getButton(){		
		$button		= '';
		$updateText	= 'iJoomla Components are updated';
		$list_all_components = array("com_adagency"=>"adagency.xml", 
									 "com_magazine"=>"magazine.xml", 
									 "com_surveys"=>"surveys.xml", 
									 "com_sidebars"=>"sidebars.xml", 
									 "com_ijoomla_seo"=>"ijoomla_seo.xml", 
									 "com_ijoomla_rss"=>"ijoomla_rss.xml", 
									 "com_news_portal"=>"news_portal.xml", 
									 "com_ijoomla_archive"=>"ijoomla_archive.xml", 
									 "com_digistore"=>"digistore.xml");
		$list_installed_components = array();
		
		$show_button = false;
		foreach($list_all_components as $key=>$value){
			if($this->existComponent($key)){
				$list_installed_components[$key] = $value;
				$show_button = true;					
			}
		}
		
		if(count($list_installed_components) > 0 && $show_button){
			foreach($list_installed_components as $key=>$value){
				$latest_version	 = $this->getCurrentVersionData($key);
				$current_version = $this->getLocalVersionString($key, $value);				
				if($show_button === true && trim($latest_version) != "" && trim($current_version) != ""){					
					if(trim($current_version) != trim($latest_version)){						
						$updateText	= 'iJoomla Upgrade Alert';	                                                             
						//$button	= '<span class="ijoommlaupdate" style="padding-left:25px; background: url(\'../plugins/system/ijoomlaupdate/ijoomlaupdate/ijoomla.gif\') no-repeat scroll 0px 0px;"><a style="color:red !important;" rel="{handler: \'iframe\', size: {x: 850, y: 290}}"  class="modal"  href="'.JURI::root()."plugins/system/ijoomlaupdate/ijoomlaupdate/editversions.php".'">'.JText::_($updateText).'</a></span>';						
						$button = '<span class="ijoommlaupdate" style="padding-left:25px; background: url(\'../plugins/system/ijoomlaupdate/ijoomlaupdate/ijoomla.gif\') no-repeat scroll 0px 0px;"><a style="color:red !important;" data-toggle="modal" data-target="#alertijoomla" href="'.JURI::root()."plugins/system/ijoomlaupdate/ijoomlaupdate/editversions.php".'">'.JText::_($updateText).'</a></span>';
						break;
					}
				}
				$latest_version = "";
				$current_version = "";
			}
		}		
		return $button;
	}
	
	public function getCurrentVersionData($component){
		$version = "";
		$data = 'www.ijoomla.com/ijoomla_latest_version.txt';
		$ch = @curl_init($data);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($ch, CURLOPT_TIMEOUT, 10); 							
		
		$version = @curl_exec($ch);
		if(isset($version) && trim($version) != ""){
			$pattern = "/3.0_".$component."=(.*);/msU";
			preg_match($pattern, $version, $result);
			if(is_array($result) && count($result) > 0){
				$version = trim($result["1"]);
			}
			return $version;
		}
		return false;
	}
	
	public function getLocalVersionString($component, $xml_file){			
		$version = '';
		$path = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$component.DS.$xml_file;
		if(file_exists($path)){
			$data = implode("", file($path));
			$pos1 = strpos($data,"<version>");
			$pos2 = strpos($data,"</version>");
			$version = substr($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
			return $version;
		}
		else{
			return "";
		}
	}
}

?>