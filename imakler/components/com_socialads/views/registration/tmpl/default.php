<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
global $mainframe;
$mainframe = JFactory::getApplication();
$input=JFactory::getApplication()->input;
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
JHtml::_('behavior.formvalidation');
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base().'components'.DS.'com_socialads'.DS.'css'.DS.'socialads.css');
$user	=	JFactory::getUser();
if(!$socialads_config['sa_reg_show'])
{
	if (!$user->id)
	{ ?>
	<div class="techjoomla-bootstrap">
		<div class="alert alert-block">
			<?php echo JText::_('BUILD_LOGIN'); ?>
		</div>
	</div>
<?php
	}
	return;
}
if(JVERSION >= '1.6.0'){
$js = "
	Joomla.submitbutton = function(pressbutton){";

	} else {

	$js = "function submitbutton( pressbutton ) {";

 }

	$js .="var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform(pressbutton);
			return;
		}
 {
			submitform(pressbutton);
			return;
		}
	}

	function submitform(pressbutton){
		 if (pressbutton) {
		 	document.adminForm.task.value = pressbutton;
		 }
		 if (typeof document.adminForm.onsubmit == 'function') {
		 	document.adminForm.onsubmit();
		 }
		 	document.adminForm.submit();
	}
	";
$document->addScriptDeclaration($js);
/*
$users = JFactory::getuser();
if ($users->id)
{
	$session = JFactory::getSession();
	$socialadsbackurl=$session->get('socialadsbackurl');
	$mainframe->redirect(JRoute::_($socialadsbackurl));
}*/
$session = JFactory::getSession();
$socialadsbackurl=$session->get('socialadsbackurl');

if($user->id > 0)
{
	$mainframe->redirect(JRoute::_($socialadsbackurl));
}
?>
<?php
		//newly added for JS toolbar inclusion
		if(file_exists(JPATH_SITE . DS .'components'. DS .'com_community') and $socialads_config['show_js_toolbar']==1)
		{
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'toolbar.php');
			$toolbar    = CFactory::getToolbar();
			$tool = CToolbarLibrary::getInstance();

			?>
			<style>
			<!--
				div#proimport-wrap #community-wrap { margin: 0;padding: 0; }
				div#proimport-wrap #community-wrap { min-height: 45px !important; }
			-->
			</style>
			<script src="<?php echo JUri::root().'components/com_community/assets/bootstrap/bootstrap.min.js'; ?>" type="text/javascript"></script>
			<div id="proimport-wrap">
				<div id="community-wrap">
					<?php	echo $tool->getHTML();	?>
				</div>
			</div>	<!-- end of proimport-wrap div -->
				<?php
		}
		//eoc for JS toolbar inclusion
?>
<div class="techjoomla-bootstrap">
	<div class="row-fluid sa_reg"><!--1-->
		<div class="span8"><!--2-->
			<div class="page-header">
				<h2><?php echo JText::_('USER_REG');	?> </h2>
				<?php echo JText::_('UN_REGISTER');?>
			</div>
		   <form action="" method="post" name="adminForm" class="form-validate" id="adminForm">
			<div ><!--3-->
					<!-- Username -->
				<div class="control-group">
					<label class="control-label"  for="user_name">
						<?php echo JText::_( 'USER_NAME' ); ?>
					</label>
					<div class="controls">
						<input class="inputbox required validate-name" type="text" name="user_name" id="user_name" maxlength="50" value="" />
					</div>
				</div>
					<!-- Username -->
				<!-- Password -->
				<div class="control-group">
					<label class="control-label"  for="user_email">
						<?php echo JText::_( 'USER_EMAIL' ); ?>
					</label>
					<div class="controls">
						<input class="inputbox required validate-email" type="text" name="user_email" id="user_email" maxlength="100" value="" />
					</div>
				</div>
				<!-- Password -->
				<!-- Password -->
				<div class="control-group">
					<label class="control-label"  for="confirm_user_email">
						<?php echo JText::_( 'CONFIRM_USER_EMAIL' ); ?>
					</label>
					<div class="controls">
						<input class="inputbox required validate-email" type="text" name="confirm_user_email" id="confirm_user_email"  maxlength="100" value="" />
					</div>
				</div>
				<!-- Password -->
			<?php
			/*
			if($socialads_config['article'] == 1){?>
				<div class="control-group">
					<label class="control-label" for="user_info">
						<a href="index.php?option=com_content&view=article&id=<?php echo $socialads_config['tnc']; ?>&tmpl=component" target="_blank"/><?php echo JText::_( 'TERMS_CONDITION' ); ?></a>
					</label>
					<div class="controls">
						<input class="inputbox" type="checkbox" name="user_info" id="user_info" size="30" value="" />
					</div>
				</div>
			<?php }
			*/?>
				<div class="form-actions">
						<button class="btn btn-warning" type="button" onclick="submitbutton('cancel');" name="cancel" id="cancel" ><?php echo JText::_('BUTTON_CANCEL_TEXT');?></button>
						<button class="btn btn-success validate" type="submit" onclick="submitbutton('save');"><?php echo JText::_('BUTTON_SAVE_TEXT_REG'); ?></button>
				</div>
				<input type="hidden" name="option" value="com_socialads" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="controller" value="registration" />
				<input type="hidden" name="Itemid" value="<?php echo $input->get('Itemid',0,'INT');?>" />
			</div><!--3 end-->
			<?php echo JHtml::_( 'form.token' ); ?>
			</form>
		</div><!--2 end-->
		<div class="span4">
			<div class="page-header">
				<h2><?php echo JText::_('LOGIN');	?> </h2>
				<?php echo JText::_('SA_REGISTER');?>
			</div>
			<a href='<?php
				$msg=JText::_('LOGIN');
				$uri=$socialadsbackurl;
				$url=base64_encode($uri);
				echo JRoute::_('index.php?option=com_users&view=login&return='.$url); ?>'>
				<div style="margin-left:auto;margin-right:auto;" class="control-group">
					<input id="LOGIN" class="btn btn-large btn-success validate" type="button" value="<?php echo JText::_('SIGN_UP'); ?>">
				</div>
			</a>
		</div>
	</div> <!--1 end-->
</div><!--bootstrap-->
