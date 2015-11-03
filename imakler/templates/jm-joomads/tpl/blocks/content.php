<?php
/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;

//get template width type
$templatewidthtype = $this->params->get('templateWidthType', '0');
$fluid = ($templatewidthtype != '0') ? '-fluid' : '';

//get column's width
$columnleft = $this->params->get('columnLeftWidth', '3');
$columnright = $this->params->get('columnRightWidth', '3');
$columncontent = $this->params->get('columnContentWidth');

//get information about font size switcher
$fontswitcher = $this->params->get('fontSizeSwitcher', '0');

?>

<section id="jm-main">
    <div id="jm-main-in" class="container<?php echo $fluid; ?>">
        <div id="jm-main-space" class="clearfix">
            <?php if($this->countModules('breadcrumbs')) : ?>
            <div id="jm-breadcrumbs" class="clearfix">
                <div id="jm-breadcrumbs-in">
                    <jdoc:include type="modules" name="breadcrumbs" style="raw" />
                </div>
            </div>
            <?php endif; ?>   
        	<div id="jm-content-wrapper">
        	    <div id="jm-content-wrapper-in">
        	        <div id="jm-content-wrapper-bg" class="clearfix ">
            	        <div id="jm-middle-page">
                		    <div id="jm-content">
                            <?php if($this->countModules('content-top')) : ?>
                            <div id="jm-content-top" class="jm-grid">
                                <?php echo DJModuleHelper::renderModules('content-top','jmmodule', $fluid); ?>
                            </div>
                            <?php endif; ?>                    
                            
                            <?php if($fontswitcher) : ?>
                            <div id="jm-font-switcher">
                                <a href="javascript:void(0);" class="texttoggler small" rel="smallview" title="small size">A</a>
                                <a href="javascript:void(0);" class="texttoggler normal" rel="normalview" title="normal size">A</a>
                                <a href="javascript:void(0);" class="texttoggler large" rel="largeview" title="large size">A</a>
                                <script type="text/javascript">
                                //documenttextsizer.setup("shared_css_class_of_toggler_controls")
                                documenttextsizer.setup("texttoggler")
                                </script>
                            </div>
                            <?php endif; ?>                
                            <?php if (!in_array(JRequest::getVar('Itemid'),(array)$this->params->get('DisableComponentDisplay', array()))) { ?> 
                            <div id="jm-maincontent">
                                <jdoc:include type="message" />
                                <jdoc:include type="component" />
                            </div>
                            <?php } ?>
                            <?php if($this->countModules('content-bottom')) : ?>
                            <div id="jm-content-bottom" class="jm-grid">
                                <?php echo DJModuleHelper::renderModules('content-bottom','jmmodule', $fluid); ?>
                            </div>
                            <?php endif; ?>
                            </div>
            	        </div>	        
            	        <?php if($this->countModules('right-column')) : ?>
                        <aside id="jm-right" class="span<?php echo $columnright; ?>">
                            <?php echo DJModuleHelper::renderModules('right-column','jmmodule', $fluid); ?>
                        </aside>
                        <?php endif; ?>         
                    </div>           
        	    </div>    
        	</div>
        	<?php if($this->countModules('left-column')) : ?>
        	<aside id="jm-left" class="span<?php echo $columnleft; ?>">  	     	    
        		<?php echo DJModuleHelper::renderModules('left-column','jmmodule', $fluid); ?>
        	</aside>
        	<?php endif; ?>
    	</div>
	</div>
</section>