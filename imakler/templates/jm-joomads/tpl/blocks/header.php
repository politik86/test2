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

?>

<?php if($this->countModules('header-mod') OR $this->countModules('header')) : ?>
<section id="jm-header-wrapper">
    <div id="jm-header-wrapper-in" class="container<?php echo $fluid ?>">
        <div id="jm-header-wrapper-space" class="clearfix">
            <?php if($this->countModules('header')) : ?>
            <div id="jm-header">
                <div id="jm-header-in">
                    <jdoc:include type="modules" name="header" style="jmmodule"/>
                </div>
            </div>
            <?php endif; ?>               
            <?php if($this->countModules('header-mod')) : ?>
            <div id="jm-header-mod">
                <div id="jm-header-mod-in">
                    <jdoc:include type="modules" name="header-mod" style="jmmodule"/>
					
                </div>
            </div>
            <?php endif; ?> 
        </div>
    </div>
</section>
<?php endif; ?> 
