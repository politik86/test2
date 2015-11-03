<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_SocialAds
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die(';)');
$doc = JFactory::getDocument();
$input = JFactory::getApplication()->input;
$user = JFactory::getUser();

if (!$user->id)
{
?>
<div class="techjoomla-bootstrap">
	<div class="alert alert-block">
	<?php echo JText::_('BUILD_LOGIN'); ?>
	</div>
</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	return false;
}

require_once JPATH_SITE . DS . 'components' . DS . 'com_socialads' . DS . 'adshelper.php';
$doc->addStyleSheet(JUri::base() . 'components/com_socialads/css/helper.css');
$doc->addScript(JUri::root() . 'components/com_socialads/js/flowplayer-3.2.9.min.js'); // Added by manoj stable 2.7.5

$adid = $input->get('id', 0, 'INT');
$adRetriever = new adRetriever;
echo $adRetriever->getAdHTML($adid, 1);
