<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JHtml::_('behavior.tooltip');
$advertiser = $this->advertiser;
$configs = $this->configs;
$lists = $this->lists;
$user = $this->user;
if ($advertiser->aid==0) {
    if(isset($_SESSION['ad_company'])) $advertiser->company = $_SESSION['ad_company'];
    if(isset($_SESSION['ad_description'])) $advertiser->description = $_SESSION['ad_description'];
    if(isset($_SESSION['ad_approved'])) $advertiser->approved = $_SESSION['ad_approved'];
    if(isset($_SESSION['ad_enabled'])) $user->block = $_SESSION['ad_enabled'];
    if(isset($_SESSION['ad_username'])) $user->username = $_SESSION['ad_username'];
    if(isset($_SESSION['ad_email'])) $user->email = $_SESSION['ad_email'];
    if(isset($_SESSION['ad_name'])) $user->name = $_SESSION['ad_name'];
    if(isset($_SESSION['ad_website'])) $advertiser->website = $_SESSION['ad_website'];
    if(isset($_SESSION['ad_address'])) $advertiser->address = $_SESSION['ad_address'];
    if(isset($_SESSION['ad_city'])) $advertiser->city = $_SESSION['ad_city'];
    if(isset($_SESSION['ad_zip'])) $advertiser->zip = $_SESSION['ad_zip'];
    if(isset($_SESSION['ad_telephone'])) $advertiser->telephone = $_SESSION['ad_telephone'];
}
$get_data = JRequest::get('get');
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");

?>

<?php include(JPATH_BASE."/components/com_adagency/includes/js/advertisers.php"); ?>

<?php
	$tmp = JRequest::getVar("tmpl", "");
	if($tmp == "component"){
?>
		<style>
			.page-content{
				margin:20px;
			}
		</style>
<?php
	}
?>

<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	  <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWADVERTISERINFO'); ?>
				</h2>
            </div>
      </div>
      
	<div class="well"><?php echo JText::_('ADAG_BASIC_INFO');?></div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWADVERTISERCONTACT'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" name="name" size="40" maxlength="50" value="<?php echo $user->name; ?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERCONTACT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWADVERTISEREMAIL'); ?> </label>
			<div class="controls">
				<input class="inputbox" type="text" name="email" size="40" maxlength="100" <?php #if ($advertiser->aid!=0) echo ' readonly disabled '; ?> value="<?php echo $user->email; ?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISEREMAIL_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<?php if(isset($configs->show)&&(in_array('phone',$configs->show))) { ?>
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWADVERTISERPHONE'); ?> <?php if(isset($configs->mandatory)&&(in_array('phone',$configs->mandatory))) { ?>
                    <font color="#ff0000">*</font>
                <?php } ?>
            </label>
			<div class="controls">
				<input class="inputbox" type="text" name="telephone" size="40" maxlength="20" value="<?php echo $advertiser->telephone; ?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERPHONE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
	 <?php } ?>
	<?php if(isset($configs->show)&&(in_array('url',$configs->show))) { ?>
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWADVERTISERURL'); ?> <?php if(isset($configs->mandatory)&&(in_array('phone',$configs->mandatory))) { ?>
                    <font color="#ff0000">*</font>
                <?php } ?>
            </label>
			<div class="controls">
				<input class="inputbox" type="text" name="website" size="40" maxlength="255" value="<?php echo $advertiser->website?$advertiser->website:'http://'; ?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERURL_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
	   <?php } ?>
	   
	   <div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_TFORMAT'); ?> <?php if(isset($configs->mandatory)&&(in_array('phone',$configs->mandatory))) { ?>
                    <font color="#ff0000">*</font>
                <?php } ?>
            </label>
			<div class="controls">
				<select name="fax">
					<option value="10" <?php if(isset($advertiser->fax)&&($advertiser->fax==10)) { echo 'selected="selected"';}?>>mm/dd/yyyy</option>
					<option value="7" <?php if(isset($advertiser->fax)&&($advertiser->fax==7)) { echo 'selected="selected"';}?>>dd-mm-yyyy</option>
					<option value="11" <?php if(isset($advertiser->fax)&&($advertiser->fax==11)) { echo 'selected="selected"';}?>>yyyy-mm-dd</option>
					<option value="12" <?php if(isset($advertiser->fax)&&($advertiser->fax==12)) { echo 'selected="selected"';}?>>yyyy/mm/dd</option>
					<option value="1" <?php if(isset($advertiser->fax)&&($advertiser->fax==1)) { echo 'selected="selected"';}?>>dd-mm-yyyy hh:mm:ss</option>
					<option value="4" <?php if(isset($advertiser->fax)&&($advertiser->fax==4)) { echo 'selected="selected"';}?>>mm/dd/yyyy hh:mm:ss</option>
					<option value="5" <?php if(isset($advertiser->fax)&&($advertiser->fax==5)) { echo 'selected="selected"';}?>>yyyy-mm-dd hh:mm:ss</option>
					<option value="6" <?php if(isset($advertiser->fax)&&($advertiser->fax==6)) { echo 'selected="selected"';}?>>yyyy/mm/dd hh:mm:ss</option>
				</select>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_TFORMAT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
	<div class="well"><?php echo JText::_('ADAD_LOGIN_INFO');?></div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWADVERTISERLOGIN'); ?></label>
			<div class="controls">
				<input class="inputbox" type="text" name="username" size="40" maxlength="25" <?php if($advertiser->aid>0) { echo 'readonly="" style="color:#ff0000; font-weight: bold; background-color: transparent; border: 0px; "'; } ?> value="<?php echo $user->username; ?>" />
          	<?php if(!isset($_GET['cid'][0])){ ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERLOGIN_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			<?php } ?>
			</div>
	</div>
	<?php if($advertiser->aid<1) {?>
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWADVERTISERPASS'); ?></label>
			<div class="controls">
				<input class="inputbox" type="password" name="password" size="40" maxlength="100" value="" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERPASS_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_CNF_PSW'); ?></label>
			<div class="controls">
				<input class="inputbox" type="password" name="password2" size="40" maxlength="100" value="" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_CNF_PSW_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<?php } ?>
	
	<div class="well"><?php echo JText::_('ADAG_ADMOPT');?></div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('AD_STATUS'); ?></label>
			<div class="controls">
				 <?php echo $lists['approved'] ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('AD_STATUS_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('Enabled'); ?></label>
			<div class="controls">
				 <fieldset class="radio btn-group" id="enabled">
					<?php
						$no_checked = "";
						$yes_cheched = "";
						 if ($user->block =='0'){
							$yes_cheched = 'checked="checked"';
						}
						else{
							$no_checked = 'checked="checked"';
						}
					?>
					<input type="hidden" name="enabled" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="enabled">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ENABLED_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<?php if(isset($configs->show)&&(in_array('company',$configs->show))) { ?>
	<div class="well"><?php echo JText::_('ADAG_COMP');?></div>
	
	<div class="control-group">
			<label class="control-label"><?php echo JText::_('VIEWADVERTISERCOMPNAME'); ?>:<?php if(isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?></label>
			<div class="controls">
				<input class="inputbox" type="text" name="company" size="40" maxlength="100" valign="top" value="<?php echo $advertiser->company; ?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERCOMPNAME_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"><?php echo JText::_('VIEWADVERTISERDESC');?><?php if(isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) { ?>:<font color="#ff0000">*</font><?php } ?></label>
			<div class="controls">
				<TEXTAREA NAME="description" ROWS="6" COLS="41"><?php echo $advertiser->description;?></TEXTAREA>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERDESC_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<?php } ?>
	<?php if(isset($configs->show)&&(in_array('address',$configs->show))) { ?>
	<div class="well"><?php echo JText::_('VIEWADVERTISERADDRESS');?></div>	
	<?php include(JPATH_BASE."/components/com_adagency/includes/js/advertisers_country.php"); ?>

	<div class="control-group">
			<label class="control-label"><?php echo JText::_("VIEWADVERTISERCOUNTRY");?>:<?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?></label>
			<div class="controls">
				<?php echo $this->lists['country_option']; ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERCOUNTRY_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"><?php echo JText::_("VIEWADVERTISERPROV");?>:<?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?></label>
			<div class="controls">
				<?php
				  	echo str_replace('id="province"','id="province" style="float:left;"',$this->lists['customerlocation']);
				  ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERPROV_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"><?php echo JText::_("ADAG_CITY");?>:<?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?></label>
			<div class="controls">
				<input class="inputbox" type="text" name="city" size="40" maxlength="100" value="<?php echo $advertiser->city; ?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_CITY_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>

	<div class="control-group">
			<label class="control-label"><?php echo JText::_("ADAG_STREET");?>:<?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?></label>
			<div class="controls">
				<input class="inputbox" type="text" name="address" size="40" maxlength="100" value="<?php echo $advertiser->address; ?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_STREET_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"><?php echo JText::_("VIEWADVERTISERZIP");?>:<?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?></label>
			<div class="controls">
				<input class="inputbox" type="text" name="zip" size="40" maxlength="12" value="<?php echo $advertiser->zip; ?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISERZIP_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<?php } ?>
	<?php if(isset($configs->show)&&(in_array('email',$configs->show))) { ?>
	<div class="well"><?php echo JText::_('VIEWADVERTISEREMAILOPT');?></div>
<div class="control-group">
	<div class="control-group">
			<label class="control-label"><?php echo JText::_("VIEWADVERTISERDAY");?></label>
			<div class="controls">
				<INPUT  TYPE="checkbox" <?php if ($advertiser->email_daily_report=='Y') echo 'checked' ?> NAME="email_daily_report" value="Y" >
				<span class="lbl"></span>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISEREMAILOPT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"><?php echo JText::_("VIEWADVERTISERWEEK");?></label>
			<div class="controls">
				<INPUT  TYPE="checkbox" <?php if ($advertiser->email_weekly_report=='Y') echo 'checked' ?> NAME="email_weekly_report" value="Y" >
				<span class="lbl"></span>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISEREMAILOPT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"><?php echo JText::_("VIEWADVERTISERMONTH");?></label>
			<div class="controls">
				<INPUT TYPE="checkbox" <?php if ($advertiser->email_month_report=='Y') echo 'checked' ?> NAME="email_month_report" value="Y" >
				<span class="lbl"></span>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISEREMAILOPT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<div class="control-group">
			<label class="control-label"><?php echo JText::_("VIEWADVERTISEREXP");?></label>
			<div class="controls">
				<INPUT  TYPE="checkbox" <?php if ($advertiser->email_campaign_expiration=='Y') echo 'checked' ?> NAME="email_campaign_expiration" value="Y">
				<span class="lbl"></span>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWADVERTISEREMAILOPT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>

</div>	
	
	<?php } ?>
	<?php 
			// Approvals [begin]
			if(isset($get_data['tmpl'])&&($get_data['tmpl'] == 'component')){
				$advertiser->apr_ads = "N";
				$advertiser->apr_cmp = 'N';
			}
		?>
	<div class="well"><?php echo JText::_('ADAG_APPROVALS');?></div>
	<div class="control-group">
			<label class="control-label"><?php echo JText::_("ADAG_AA_ADS");?></label>
			<div class="controls">
				<select name="apr_ads">
					<option value="G" <?php if($advertiser->apr_ads=='G') {echo 'selected="selected"';}?>><?php echo JText::_('ADAG_USE_GLB'); ?></option>
					<option value="Y" <?php if($advertiser->apr_ads=='Y') {echo 'selected="selected"';}?>><?php echo JText::_('JAS_YES'); ?></option>
					<option value="N" <?php if($advertiser->apr_ads=='N') {echo 'selected="selected"';}?>><?php echo JText::_('JAS_NO'); ?></option>
				</select>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_AA_ADS_TIP2'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
				<?php echo JText::_('ADAG_AA_INFO');?>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"><?php echo JText::_("ADAG_AA_CMP");?></label>
			<div class="controls">
				<select name="apr_cmp">
					<option value="G" <?php if($advertiser->apr_cmp=='G') {echo 'selected="selected"';}?>><?php echo JText::_('ADAG_USE_GLB'); ?></option>
					<option value="Y" <?php if($advertiser->apr_cmp=='Y') {echo 'selected="selected"';}?>><?php echo JText::_('JAS_YES'); ?></option>
					<option value="N" <?php if($advertiser->apr_cmp=='N') {echo 'selected="selected"';}?>><?php echo JText::_('JAS_NO'); ?></option>
				</select>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_AA_ADS_TIP2'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
				<?php echo JText::_('ADAG_AA_INFO');?>
			</div>
	</div>


	<input type="hidden" name="images" value="" />
	<input type="hidden" id="sendmail" name="sendmail" value="1" />
	<input type="hidden" id="initvalcamp" value="" />
	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="aid" value="<?php echo $advertiser->aid; ?>" />
	<input type="hidden" name="user_id" value="<?php echo $advertiser->user_id; ?>" />
	<input type="hidden" name="lastreport" value="<?php if (isset($advertiser->lastreport)) echo $advertiser->lastreport; else echo time(); ?>" />
	<input type="hidden" name="weekreport" value="<?php if (isset($advertiser->weekreport)) echo $advertiser->weekreport; else echo time(); ?>" />
	<input type="hidden" name="monthreport" value="<?php if (isset($advertiser->monthreport)) echo $advertiser->monthreport; else echo time(); ?>" />
    <input type="hidden" name="task" value="save_graybox" />
	<input type="hidden" name="controller" value="adagencyAdvertisers" />
	<?php
		// the graybox code
		if(isset($get_data['tmpl'])&&($get_data['tmpl'] == 'component')) {
			echo "<input type='hidden' name='tmpl' value='component' />";
			echo "<input type='submit' value='".JText::_('ADAG_SAVE')."' class='btn btn-primary' onclick='submitbutton(\"save_graybox\");' />";
		}
	?>
</form>
