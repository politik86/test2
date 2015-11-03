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

/**
 * General Controller of Adagency component
 */
class adagencyAdminController extends JControllerLegacy{

	function __construct() {			
		parent::__construct();
		$ajax_req = JRequest::getVar("no_html", 0, "request");
		$squeeze2 = JRequest::getVar("tmpl", 0, "request");
		$task = JRequest::getVar("task", '', "get");
		$task2 = JRequest::getVar("task2", '', "get");
		$controller_request  = NULL;
		$controller = JRequest::getVar("controller",'');
		
		$db =  JFactory::getDBO();	
		$sql = "select `params` from #__ad_agency_settings";
        $db->setQuery( $sql );
		$configs = $db->loadColumn();
	 	$configs = $configs['0'];
		@$configs = @unserialize($configs);

		if(($task != 'provinces')&&($controller != 'adagencyUpgrade')) {
		
			$controller_request  = $controller;
			if (!$ajax_req && !$squeeze2){
				$document = JFactory::getDocument();
				$document->addStyleSheet('components/com_adagency/css/bootstrap.min.css');
				$document->addStyleSheet('components/com_adagency/css/font-awesome.min.css');
				
				if($controller_request == "pages" && $task == "edit_page"){
					// do nothing
				}
				elseif($controller_request == "about" && ($task == "vimeo" || $task == "youtube")){
					// do nothing
				}
				elseif($controller_request == "adagencyPackages" && $task == "preview" ){
					// do nothing
				}
				elseif($controller_request == "adagencyReports" && $task == "overview_csv" ){
					// do nothing
				}
				else{
					$document->addStyleSheet("components/com_adagency/css/tmploverride.css");
					$document->addStyleSheet('components/com_adagency/css/ace-fonts.css' );
					$document->addStyleSheet('components/com_adagency/css/ace.min.css' );
				}
				if($controller_request != ""){

					if(!isset($configs['jquery_back']) || @$configs['jquery_back'] == "0"){
						$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.js" );
					}

					$jq = false;
					$keys = array_keys($document->_scripts);
					foreach($keys as $val)  {
						if(preg_match("/jquery.js/",$val)) {$jq = true; }
					}

					if(!$jq){
							$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.js" );
					}
					
					$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js" );
					$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.adagency.js" );
					
					
				}
				
				$document->addStyleSheet('components/com_adagency/css/fullcalendar.css' );
				$document->addStyleSheet("components/com_adagency/css/ij30.css");
				$view = $this->getView('adagencyDtree', 'html');
			
				$view->showDtree();
				?>	
			
				
					<script type="text/javascript">
					<?php
						$controller = JRequest::getVar("controller", "");
						if($controller != "" && $controller != "adagencyReports"){
					?>		
						document.write("<script src='components/com_adagency/js/jquery-1.9.1.min.js'>"+"<"+"/script>");
					<?php
						}
					?>
					document.write("<script src='http://code.jquery.com/ui/1.10.3/jquery-ui.js'>"+"<"+"/script>");
				</script>
				<script src="components/com_adagency/js/ace-elements.min.js"></script>
				<script src="components/com_adagency/js/ace.min.js"></script>
				
				<?php
				
			}
		}
				

	}


	function display ($cachable = false, $urlparams = Array()) {
		parent::display(false, null);	
	}

	function debugStop($msg = '') {
        global $mainframe;
	  	echo $msg;
		$mainframe->close();
	}
}
?>