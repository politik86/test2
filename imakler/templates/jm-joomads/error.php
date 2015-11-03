<?php

/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

/**
 * @package     Joomla.Site
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
if (!isset($this->error)) {
	$this->error = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$this->debug = false;
}

//get language and direction
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<title><?php echo $this->error->getCode(); ?> - <?php echo $this->title; ?></title>
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css" type="text/css" />
</head> 
<body>
	<div class="jm-error">
		<div class="jm-error-title">
			<div class="jm-error-code">
				<h1><?php echo $this->error->getCode(); ?></h1>
			</div>
			<div class="jm-error-message">
				<h2><?php echo $this->error->getMessage(); ?></h2>
			</div>
		</div>
		<div class="jm-error-desc">
			<?php echo JText::_('JERROR_JM-JOOMADS_PAGE_DOESNT_EXIST'); ?><br/>
			<?php echo JText::_('JERROR_JM-JOOMADS_GO_BACK_OR_HEAD_OVER'); ?><br />
			<div class="jm-error-buttons">
				<a class="jm-error-left" href="javascript:history.go(-1)"><?php echo JText::_('JERROR_JM-JOOMADS_BACK'); ?></a> <a class="jm-error-right" href="<?php echo JURI::base(); ?>" title="<?php echo JText::_('JERROR_JM-JOOMADS_HOME_PAGE'); ?>"><?php echo JText::_('JERROR_JM-JOOMADS_HOME_PAGE'); ?></a>
			</div>
		</div>
	</div>	
</body>
</html>