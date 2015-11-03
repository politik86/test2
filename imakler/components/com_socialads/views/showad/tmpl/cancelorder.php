<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
JHtml::_('behavior.modal', 'a.modal');
$document = JFactory::getDocument();
//$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css');
$document->addStyleSheet(JUri::base().'components/com_socialads/css/helper.css');
?>
<div class="techjoomla-bootstrap " >
	<div class="well" >
		<div class="alert alert-error">
			<span ><?php echo JText::_('ORDER_CANCELLED'); ?> </span>
		</div>

	</div>
</div>

<?php
return false;

