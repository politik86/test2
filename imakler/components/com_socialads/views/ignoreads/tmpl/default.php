<?php
defined( '_JEXEC' ) or die( ';)' );
JHtml::_('behavior.modal', 'a.modal');

$document = JFactory::getDocument();
$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
//$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css'); 
//$this->setLayout('ignore');
?>
<div class="techjoomla-bootstrap">
<?php
$user = JFactory::getUser();
if (!$user->id)
{
	?>
	<div class="alert alert-block">
	<?php echo JText::_('BUILD_LOGIN'); ?>
	</div>
	</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	return false;
}
?>
<form action="index.php" name="adminForm" method="post">
<table>
		<tr>
			<td width="100%">
				<?php JHtml::_('behavior.tooltip'); echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="ignore_search" id="ignore_search" value="<?php echo $input->get('ignore_search'); ?>" onchange="document.adminForm.submit();" />
				<button class="btn btn-success" onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button class="btn btn-warning" onclick="document.getElementById('ignore_search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
		</tr>
	</table>
	
<table class="ignoretable table table-striped">
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
</div><!--techjoomla-bootstarp ends-->
