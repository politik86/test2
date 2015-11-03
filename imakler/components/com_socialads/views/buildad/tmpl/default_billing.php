<?php
/**
 *  @package    Social Ads
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined('_JEXEC') or die( 'Restricted access' );
//JHtml::_('behavior.formvalidation');
//jimport( 'joomla.form.formvalidator' );
JHtml::_('behavior.tooltip');
JHtmlBehavior::framework();
//$params = JComponentHelper::getParams( 'com_socialads' );
$baseurl=JRoute::_ (JUri::root().'index.php');
?>

<fieldset class="sa_fieldset">
	<legend class="hidden-desktop"><?php echo JText::_('COM_SOCIALADS_CKOUT_BILL_DETAILS'); ?></legend>
	<div id="ads_mainwrapper" class="row-fluid form-horizontal">
	<?php
		$socialadshelper = new socialadshelper();
		$buildformPath = $socialadshelper->getViewpath('buildad','billform');

		ob_start();
			include($buildformPath);
			$billhtml = ob_get_contents();
		ob_end_clean();

		echo $billhtml;
		?>
	</div>

</fieldset>

