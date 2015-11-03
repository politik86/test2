<?php
/**
 *  @package    Social Ads
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.form.formvalidator' );
JHtml::_('behavior.tooltip');
JHtmlBehavior::framework();
$root_url=JUri::root();

$document=JFactory::getDocument();
$params = JComponentHelper::getParams( 'com_socialads' );

$document->addScript($root_url.'components/com_socialads/js/fuelux2.3loader.min.js');
$document->addStyleSheet($root_url.'components/com_socialads/css/fuelux2.3.1.css');
$document->addScript($root_url.'components/com_socialads/js/steps.js');

/*
 * @Amol Check that without this files, tab is steps running on Joomla 2.5 site ? if yes then remove commented out files
 * $document->addStyleSheet('https://fuelcdn.com/fuelux/2.3/css/fuelux-responsive.css');
 * $document->addScript('https://fuelcdn.com/fuelux/2.3.1/loader.min.js');
*/

?>

<div class="techjoomla-bootstrap">
<form action="" method="post" enctype="multipart/form-data" id="adminForm" name="adminForm" >
<div id="ads_ckoutsteps">
	<h1 class=""><?php echo JText::_('COM_SOCIALADS_CKOUT_PGHEADING')?></h1>
	<div class="row-fluid">
		<div class="span12">
			<div class="fuelux wizard-example">
				<div id="MyWizard" class="wizard">
					<ul class="steps nav ">
						<li data-target="#adsstep1" class="active">
							<span class="badge badge-info">1</span><?php echo JText::_('COM_SOCIALADS_CKOUT_BILL_DETAILS')?>
							<span class="chevron"></span>
						</li>
						<li data-target="#adsstep2">
							<span class="badge">2</span><?php echo JText::_('COM_SOCIALADS_CKOUT_ADS_SUMMERY')?>
							<span class="chevron"></span>
						</li>
						<li data-target="#adsstep3">
							<span class="badge">3</span><?php echo JText::_('COM_SOCIALADS_CKOUT_ORDER_THANX_PG')?>
							<span class="chevron"></span>
						</li>
					</ul>
					<div class="actions">
						<button class="btn btn-mini btn-prev"> <i class="icon-arrow-left"></i>Prev</button>
						<button class="btn btn-mini btn-next" data-last="Finish">Next<i class="icon-arrow-right"></i></button>
					</div>
				</div>
				<div class="tab-content step-content">
					<div class="tab-pane step-pane active" id="adsstep1">
						<?php
						$socialadshelper = new socialadshelper();
						 	$billpath = $socialadshelper->getViewpath('checkout','billing');
						ob_start();
							include($billpath);
							$html = ob_get_contents();
						ob_end_clean();
						echo $html;
						?>
						<br>
					</div>
					<div class="tab-pane step-pane" id="adsstep2">
						<h2>adsstep 2]</h2>
						<?php
						$socialadshelper = new socialadshelper();
						 $billpath = $socialadshelper->getViewpath('checkout','adsummary');
						ob_start();
							include($billpath);
							$html = ob_get_contents();
						ob_end_clean();
						echo $html;
						?>
						<br>
					</div>
					<div class="tab-pane step-pane" id="adsstep3">
						<h2>Okay</h2>
						Now you are at the 3rd step of this wizard example.<br>
					</div>
				</div>

				<br>

				<div class=" pull-right">
					<div class="actions">
						<button id="btnWizardPrev" class="btn btn-prev"> <i class="icon-arrow-left"></i>Prev</button>
						<button id="btnWizardNext" class="btn btn-next btn-primary" data-last="Finish">Next<i class="icon-arrow-right"></i></button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
</form>
</div>
