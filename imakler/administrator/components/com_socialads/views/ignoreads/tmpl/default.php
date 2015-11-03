<?php
defined( '_JEXEC' ) or die( ';)' );
JHTML::_('behavior.modal', 'a.modal');

$document = JFactory::getDocument();
$input=JFactory::getApplication()->input;

?>
<div class="techjoomla-bootstrap">
<form action="index.php" name="adminForm" method="post">
<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search sa-text-filter btn-group">
				<input type="text" name="ignore_search" id="ignore_search" value="<?php echo $input->get('ignore_search'); ?>" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();" class="btn hasTooltip"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" onclick="document.getElementById('ignore_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
</div>

<table class="adminlist table table-striped">
<tr>
		<th><?php echo JText::_('IGNOREDBY');?></th>
		<th><?php echo JText::_('DATE');?></th>
		<th><?php echo JText::_('REASON');?></th>
</tr>
<?php
	foreach($this->ignoredata as $ignoredata){
?>
<tr>
		<td><?php echo $ignoredata->name;?></td>
		<td><?php echo date('Y-m-d', strtotime($ignoredata->idate));?></td>
		<td><?php echo $ignoredata->ad_feedback;?></td>
</tr>
<?php } ?>
<tr>
      	<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
</tr>
</table>

	<input type="hidden" name="option" value="com_socialads" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="ignoreads" />
	<input type="hidden" name="controller" value="ignoreads" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="adid" value="<?php echo $input->get('adid',0,'INT');?>" />
</form>
</div>
