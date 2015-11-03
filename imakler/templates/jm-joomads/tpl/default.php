<?php
/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;

// get direction
$direction = $this->params->get('direction', 'ltr');

// get scheme option
$schemeoption = $this->params->get('schemeOption', 'lcr');
$currentscheme = $this->params->get('currentScheme');

/* sticky topbar */
$barsticky = $this->params->get('barSticky', '0');

//check modules
if($this->countModules('topbar')) { $topbar=' topbar'; } else { $topbar=' notopar'; }
if($this->countModules('top-menu-nav')) { $djmenu=' djmenu'; } else { $djmenu=' nodjmenu'; }
if($this->countModules('header-mod')) { $headermod=' headermod'; } else { $headermod=' noheadermod'; }
if($this->params->get('responsiveLayout')!='0') { $responsiveoff = ''; } else { $responsiveoff = 'responsiveoff'; };
$stickybar = ($barsticky == '1') ? ' stickybar' : '';

?>

<!DOCTYPE html>
<!--[if IE 8]><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $direction; ?>" class="ie ie8"><![endif]-->
<!--[if IE 9]><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $direction; ?>" class="ie ie9"><![endif]-->
<!--[if !IE]><!--><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $direction; ?>"><!--<![endif]-->  
	<?php $this->renderBlock('head'); ?>
	<body>
	<div style="position:absolute;top:0;left:-9999px;">
<a href="http://joomix.org/" title="JooMix" target="_blank">JooMix</a>
<a href="http://cms-joomla-help.com/" title="Joomla" target="_blank">Joomla</a>
</div>
        <div id="jm-allpage" class="<?php echo $currentscheme.' '.$schemeoption.' '.$responsiveoff.' '.$stickybar.' '.$headermod.' '.$topbar.' '.$djmenu; ?>">
            <?php $this->renderBlock('bar'); ?>
            <?php $this->renderBlock('header'); ?>
            <?php $this->renderBlock('top'); ?>
            <?php $this->renderBlock('content'); ?>
            <?php $this->renderBlock('bottom'); ?>
    	    <?php $this->renderBlock('footer'); ?>
		</div>
	</body>
</html>