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

//get information about style switcher
$styleswitcher = $this->params->get('styleSwitcher', '1');

//get information about 'back to top' button
$backtotop = $this->params->get('backToTop', '1');

?>

<footer id="jm-footer">
    <div id="jm-footer-in" class="container<?php echo $fluid; ?> clearfix">
        <div id="jm-footer-space">
            <?php if ($this->countModules('footer-mod')) : ?>
            <div id="jm-footer-mod" class="jm-footer">
                <?php echo DJModuleHelper::renderModules('footer-mod','jmmodule', $fluid); ?>
            </div>     
            <?php endif; ?>
            <div id="jm-footer-wrapper" class="clearfix">  
                <?php if($this->countModules('copyrights')) : ?>
                <div id="jm-footer-left" class="pull-left">
                    <div id="jm-copyrights">
                        <jdoc:include type="modules" name="copyrights" style="raw"/>
                    </div>
                </div>
                <?php endif; ?>
                <div id="jm-footer-center" class="pull-left">               
                    <?php if($styleswitcher == '1') : ?>
                        <div id="jm-styleswitcher">
                            <a href="#" id="style_icon-1"><span>&nbsp;</span></a>
                            <a href="#" id="style_icon-2"><span>&nbsp;</span></a>
                            <a href="#" id="style_icon-3"><span>&nbsp;</span></a>
                            <a href="#" id="style_icon-4"><span>&nbsp;</span></a>
                        </div>
                    <?php endif; ?>  
                </div>
                <div id="jm-footer-right" class="pull-right">
                    <div id="jm-style-power">
                        <div id="jm-poweredby">
                            <a href="http://ubs-webdesign.com.ua/" onfocus="blur()" target="_blank" title="Заказать сайт">Заказать сайт</a> ubs-webdesign
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php if($backtotop == '1') : ?>
    <p id="jm-back-top"><a id="backtotop" href="javascript:void(0)"><span>&nbsp;</span></a></p>
<?php endif; ?>
