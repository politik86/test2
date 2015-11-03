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

//get logo and site description
$logo = htmlspecialchars($this->params->get('logo'));
$logotext = htmlspecialchars($this->params->get('logoText'));
$sitedescription = htmlspecialchars($this->params->get('siteDescription'));
$app = JFactory::getApplication();
$sitename = $app->getCfg('sitename');

?>
<div id="top-banner"><jdoc:include type="modules" name="top-banner" style="xhtml" /></div>
<?php if ($this->countModules('top-menu-nav') or $this->countModules('topbar') or ($logo != '') or ($logotext != '') or ($sitedescription != '')) : ?>
<section id="jm-bar">  
    <div id="jm-bar-in" class="container<?php echo $fluid ?>">
        <div id="jm-bar-space" class="clearfix">      
            <?php if($this->countModules('topbar') or $this->countModules('top-menu-nav')) : ?>
            <div id="jm-bar-right" class="pull-right">
                <?php if($this->countModules('topbar')) : ?>
                <div id="jm-topbar" class="clearfix">
                    <jdoc:include type="modules" name="topbar" style="jmmoduleraw"/>
                </div>
                <?php endif; ?>
                <?php if($this->countModules('top-menu-nav')) : ?>
                <div id="jm-djmenu" class="clearfix">
                    <jdoc:include type="modules" name="top-menu-nav" style="raw"/>
                </div>
                <?php endif; ?>
            </div> 
            <?php endif; ?> 
            <?php if (($logo != '') or ($logotext != '') or ($sitedescription != '')) : ?>
            <div id="jm-bar-left" class="pull-left">
                <div id="jm-logo-sitedesc">
                    <?php if (($logo != '') or ($logotext != '')) : ?>
                    <h1 id="jm-logo">
                        <a href="<?php echo JURI::base(); ?>" onfocus="blur()" >
                            <?php if ($logo != '') : ?>
                            <img src="<?php echo JURI::base(), $logo; ?>" alt="<?php if(!$logotext) { echo $sitename; } else { echo $logotext; }; ?>" border="0" />
                            <?php else : ?>
                            <?php echo '<span>'.$logotext.'</span>';?>
                            <?php endif; ?>
                        </a>
                    </h1>
                    <?php endif; ?>
                    <?php if ($sitedescription != '') : ?>
                    <div id="jm-sitedesc">
                        <?php echo $sitedescription; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>                
        </div>
    </div>
</section>
<?php endif; ?>
