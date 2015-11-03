<?php

/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.joomla-monster.com/license.html Joomla-Monster Proprietary Use License
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

/**
 * @version		$Id: modules.php 10822 2008-08-27 17:16:00Z tcp $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die;

function modChrome_jmmodule($module, &$params, &$attribs) {
	$moduleTag      = $params->get('module_tag', 'div');
	$headerTag      = htmlspecialchars($params->get('header_tag', 'h3'));
	$bootstrapSize  = (int) $params->get('bootstrap_size', '0');
	$moduleClass    = $bootstrapSize != '0' ? $bootstrapSize : '';
    if($module->showtitle == 0) { $notitle='notitle'; } else $notitle='';
    $title = $module->title;
    $title = preg_split('#\s#', $title);
    $title[0] = '<span>'.$title[0].'</span>';
    $title= implode(' ', $title);
    
	if (!empty ($module->content)) : ?>
	<<?php echo $moduleTag; ?> class="jm-module <?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
		<<?php echo $moduleTag; ?>  class="jm-module-in">
			<?php if ((bool) $module->showtitle) :?>
		   		<<?php echo $headerTag; ?> class="jm-title <?php echo $params->get('header_class'); ?>"><?php echo $title; ?></<?php echo $headerTag; ?>>
		   	<?php endif;?>
		    <<?php echo $moduleTag; ?> class="jm-module-content clearfix <?php echo $notitle; ?>">
		    	<?php echo $module->content; ?>	      
		    </<?php echo $moduleTag; ?>>
		</<?php echo $moduleTag; ?>>
	</<?php echo $moduleTag; ?>>
	<?php endif; ?>
<?php } ?>

<?php
function modChrome_jmmoduleraw($module, &$params, &$attribs) {
    $moduleTag      = $params->get('module_tag', 'div');    
    if ($module->content != '') {
?>
    <<?php echo $moduleTag; ?> class="jm-module-raw <?php echo $params->get('moduleclass_sfx'); ?>">
        <?php echo $module->content; ?>
    </<?php echo $moduleTag; ?>>
<?php 
    }
} 
?>