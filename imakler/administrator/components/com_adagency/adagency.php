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

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once('components/com_adagency/helpers/legacy.php');

// Access check.

if (!JFactory::getUser()->authorise('core.manage', 'com_adagency')) {

	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));

}



require_once (JPATH_COMPONENT.DS.'controller.php');

require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

$controller_request = NULL;

$controller = JRequest::getWord('controller');



global $mainframe;

$mainframe = JFactory::getApplication();



if ($controller) {

	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';

	if (file_exists($path)) {

		require_once($path);

	} else {

	 	$controller = '';

	}



}



$classname = "adagencyAdminController".$controller;



$ajax_req = JRequest::getVar("no_html", 0, "request");

if(!$ajax_req){

}

$task = JRequest::getWord('task');
$tmpl = JRequest::getWord('tmpl');

if($task == "overview_csv" || $task == "overview_pdf" || $task == "advertisers_csv" || $task == "advertisers_pdf" || $task == "campaigns_csv" || $task == "campaigns_pdf" || $task == "target" || $task == "add_package_from_modal" || ($task == "upload" && $controller == "adagencyJomsocial") || $task == "uploadImageContent" || $task == "phpchangeProvince"){
	JRequest::setVar("tmpl", "component");
	$controller = new $classname() ;
	$controller->execute($task);
	$controller->redirect();
}
else{
?>

	<div id="js-cpanel">

     <?php         



$controller = new $classname() ;       



$doc = JFactory::getDocument();

$doc->addScript('components/com_adagency/js/jquery-1.9.1.min.js');

?>

 <div id="admin_content_wrapper">

            <?php

				if($task != "vimeo" && $task != "youtube" && $task !="preview" && $task != "details" && $task != "overview_csv" && $task != "advertisers_csv" && $task != "campaigns_csv" && $tmpl !="component"){

					require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'sidebar.php');

				}

            ?>	

            <div class="main-content">
                <?php
					$cont_req = JRequest::getVar("controller", "");
					$task2 = JRequest::getVar("task2", "");
					
					$form_button = '<form id="prform" name="prform" target="_blank" style="margin:0px; float:left;" method="post" action="http://www.ijoomla.com/index.php?option=com_digistore&controller=digistoreCart&task=add&pid[0]=81&cid[0]=81" onsubmit="return prodformsubmit4a60cb04c1341();">
										<input name="qty" value="1" type="hidden" />
										<input name="pid" id="product_id" value="81" type="hidden" />
										<input name="Button" type="submit" class="btn btn-warning" value="Buy Pro" />
									</form>';
					
                	if($cont_req == "adagencyConfigs" && $task2 == "payments"){
				?>
                		<div class="msg-content">
                			<div class="alert alert-error light-msg">
                            	<span class="pull-left" style="line-height:35px;"><?php echo JText::_("COM_ADAGENCY_PAYMENTS_MSG"); ?></span>
	                            <?php
                                	echo $form_button;
								?>
    	                        &nbsp;
        	                    <span class="pull-left" style="line-height:35px;">
                                	<a class="pull-left" href="http://adagency.ijoomla.com/pricing/" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
								</span>
                                <div class="clearfix"></div>
							</div>
                		</div>
                <?php
					}
					elseif($cont_req == "adagencyConfigs" && $task2 == "email"){
				?>
                		<div class="msg-content">
                			<div class="alert alert-error light-msg">
                            	<span class="pull-left" style="line-height:35px;"><?php echo JText::_("COM_ADAGENCY_EMAIL_MSG"); ?></span>
                                <?php
                                	echo $form_button;
								?>
    	                        &nbsp;
        	                    <span class="pull-left" style="line-height:35px;">
                                	<a class="pull-left" href="http://adagency.ijoomla.com/pricing/" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
                                </span>
                                <div class="clearfix"></div>
							</div>
                		</div>
                <?php
					}
					elseif($cont_req == "adagencyConfigs" && $task2 == "overview"){
				?>
                		<div class="msg-content">
                			<div class="alert alert-error light-msg">
                            	<span class="pull-left" style="line-height:35px;"><?php echo JText::_("COM_ADAGENCY_OVERVIEW_MSG"); ?></span>
                                <?php
                                	echo $form_button;
								?>
    	                        &nbsp;
        	                    <span class="pull-left" style="line-height:35px;">
                                	<a class="pull-left" href="http://adagency.ijoomla.com/pricing/" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
                                </span>
                                <div class="clearfix"></div>
							</div>
                		</div>
                <?php
					}
					elseif($cont_req == "adagencyConfigs" && $task2 == "registration"){
				?>
                		<div class="msg-content">
                			<div class="alert alert-error light-msg">
                            	<span class="pull-left" style="line-height:35px;"><?php echo JText::_("COM_ADAGENCY_REGISTRATION_MSG"); ?></span>
                                <?php
                                	echo $form_button;
								?>
    	                        &nbsp;
        	                    <span class="pull-left" style="line-height:35px;">
                                	<a class="pull-left" href="http://adagency.ijoomla.com/pricing/" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
                                </span>
                                <div class="clearfix"></div>
							</div>
                		</div>
                <?php
					}
					elseif($cont_req == "adagencyConfigs" && $task2 == "approvals"){
				?>
                		<div class="msg-content">
                			<div class="alert alert-error light-msg">
                            	<span class="pull-left" style="line-height:35px;"><?php echo JText::_("COM_ADAGENCY_APPROVALS_MSG"); ?></span>
                                <?php
                                	echo $form_button;
								?>
    	                        &nbsp;
        	                    <span class="pull-left" style="line-height:35px;">
                                	<a class="pull-left" href="http://adagency.ijoomla.com/pricing/" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
                                </span>
                                <div class="clearfix"></div>
							</div>
                		</div>
                <?php
					}
					elseif($cont_req == "adagencyConfigs" && $task2 == "jomsocial"){
				?>
                		<div class="msg-content">
                			<div class="alert alert-error light-msg">
                            	<span class="pull-left" style="line-height:35px;"><?php echo JText::_("COM_ADAGENCY_JOMSOCIAL_MSG"); ?></span>
                                <?php
                                	echo $form_button;
								?>
    	                        &nbsp;
        	                    <span class="pull-left" style="line-height:35px;">
                                	<a class="pull-left" href="http://adagency.ijoomla.com/pricing/" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
                                </span>
                                <div class="clearfix"></div>
							</div>
                		</div>
                <?php
					}
					elseif($cont_req == "adagencyAds" && $task == "add"){
				?>
                		<div class="msg-content">
                			<div class="alert alert-error light-msg">
                            	<span class="pull-left" style="line-height:35px;"><?php echo JText::_("COM_ADAGENCY_JS_STREAM_MSG"); ?></span>
                                <?php
                                	echo $form_button;
								?>
    	                        &nbsp;
        	                    <span class="pull-left" style="line-height:35px;">
                                	<a class="pull-left" href="http://adagency.ijoomla.com/pricing/" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
                                </span>
                                <div class="clearfix"></div>
							</div>
                		</div>
                <?php
					}
					elseif($cont_req == "adagencyJomsocial" && $task == "edit"){
				?>
                		<div class="msg-content">
                			<div class="alert alert-error light-msg">
                            	<span class="pull-left" style="line-height:35px;"><?php echo JText::_("COM_ADAGENCY_JS_STREAM_MSG"); ?></span>
                                <?php
                                	echo $form_button;
								?>
    	                        &nbsp;
        	                    <span class="pull-left" style="line-height:35px;">
                                	<a class="pull-left" href="http://adagency.ijoomla.com/pricing/" target="_blank"><?php echo JText::_("COM_ADAGENCY_COMPARE"); ?></a>
                                </span>
                                <div class="clearfix"></div>
							</div>
                		</div>
                <?php
					}
					
				?>
                <div class="page-content">
                    <?php
                    	if($controller_request != "pages" && $task != "edit_page"){
					?>
                            <div class="page-header clearfix no-padding">
                                <?php
                                    $pageTitle = "";
                                    $image_pub_top = '<a href="http://www.ijoomla.com" target="_blank"><img src="components/com_adagency/images/ijoomla-logo.png"></a>';
                                    $controller_request = JRequest::getVar("controller", "");
                                    $layout = JRequest::getVar("task2", "");
									$layout2 = JRequest::getVar("task", "");
									
									if($controller_request == "adagencyConfigs"){
									 	if($layout == "general") $pageTitle = JText::_("VIEWTREESETTINGMANAGER")." > ".JText::_("VIEWTREEGENERAL");
										elseif($layout == "payments") $pageTitle = JText::_("VIEWTREESETTINGMANAGER")." > ".JText::_("VIEWTREEPAYMENTS");
										elseif($layout == "email") $pageTitle = JText::_("VIEWTREESETTINGMANAGER")." > ".JText::_("VIEWTREEEMAILS");
										elseif($layout == "content") $pageTitle = JText::_("VIEWTREESETTINGMANAGER")." > ".JText::_("VIEWTREELANGUAGE");
										elseif($layout == "overview") $pageTitle = JText::_("VIEWTREESETTINGMANAGER")." > ".JText::_("VIEWTREEOVERVIEW");
										elseif($layout == "registration") $pageTitle = JText::_("VIEWTREESETTINGMANAGER")." > ".JText::_("ADAG_REGISTRATION");
										elseif($layout == "approvals") $pageTitle = JText::_("VIEWTREESETTINGMANAGER")." > ".JText::_("ADAG_APPROVALS");
										elseif($layout == "jomsocial") $pageTitle = JText::_("VIEWTREESETTINGMANAGER")." > ".JText::_("ADAG_JOMSOC_TARGETING");
                                        $image_pub_top = '';
									}
									elseif($controller_request == "adagencyGeo" && $layout2 == "settings" ){
										$pageTitle = JText::_("VIEWTREESETTINGMANAGER")." > ".JText::_("ADAG_GEOT");
                                        $image_pub_top = "";
                                    }
									elseif($controller_request == "adagencyReports"){
										$pageTitle = JText::_("VIEWTREEREPORTS");
                                        $image_pub_top = "";
                                    }
									elseif($controller_request == "adagencyBlacklist"){
										$pageTitle = JText::_("AD_BLCKLIST");
                                        $image_pub_top = "";
                                    }
									elseif($controller_request != ""){
                                        $image_pub_top = "";
                                    }
									elseif($controller_request == ""){
                                        $pageTitle = JText::_("PAGE_DASHBOARD_HEAD");
                                    }
                                ?>
                                <h2 class="pull-left"><?php echo $pageTitle; ?></h2>
                                <div class="pull-right"><?php echo $image_pub_top; ?></div>
                            </div>
					<?php
                    	}

						$controller->execute($task);
						$controller->redirect();
					?>
  			</div>
                <script>
					jQuery("#toolbar").addClass("pull-right no-margin").prependTo(".page-header");										
					// move the page title
                    jQuery(".pub-page-title").addClass("pull-left no-margin").prependTo(".page-header");
                </script>
            </div>
        </div>
    </div>
<?php
}
?>