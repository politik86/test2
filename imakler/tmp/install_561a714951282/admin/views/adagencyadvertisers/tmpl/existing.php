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

$lists = $this->lists;
$configs = $this->configs;
$data = $this->datas;
$usr = $this->usr;
$get_data = JRequest::get('get');
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");

?>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/existent.php"); ?>
<script type="text/javascript">

function changeProvince() {
	var url = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=provinces&no_html=1&format=raw' + '&country=' + document.getElementById('country').value;
	new Ajax(url, {
		method: "get",
		update: $('province'),
		onComplete: function(response) {
		  // alert(this.response.text);
		}
	}).request();
}
</script>

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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo JText::_('VIEWADVERTISERINFO');?></legend>
                <table class="admintable">

    <table class="adminform">
		<?php /* - Choose adv- */ ?>
		<tr>
			<th style="text-align:left;" colspan="2">
				<?php echo JText::_('ADAG_CHS_USR'); ?>
			</th>
		</tr>

		<tr>
			<td width="10%">
				<?php echo JText::_('CONFIGLNAME'); ?>:
			</td>
			<td>
				<input class="inputbox" type="text" maxlength="255" size="40" name="username" readonly="readonly" style="color: rgb(255, 0, 0); font-weight: bold; background-color: transparent; border: 0px none;"  value="<?php if(isset($usr)) { echo $usr->username; } ?>"/>
			</td>
		</tr>

		<tr>
			<td width="10%">
				<?php echo JText::_('VIEWADVERTISERCONTACT'); ?>:
			</td>
			<td>
				<input class="inputbox" type="text" maxlength="255" size="40" name="fullname"  value="<?php if(isset($usr)) { echo $usr->name; } ?>"/>
                <span class="adag_tip">
                    <img src="components/com_adagency/images/tooltip.png" border="0" />
                    <span><?php echo JText::_('VIEWADVERTISERCONTACT_TIP'); ?></span>
                </span>
			</td>
		</tr>

		<tr>
			<td width="10%">
				<?php echo JText::_('VIEWADVERTISEREMAIL'); ?>:
			</td>
			<td>
				<input class="inputbox" type="text" maxlength="255" size="40" name="email"  value="<?php if(isset($usr)) { echo $usr->email; } ?>"/>
                <span class="adag_tip">
                    <img src="components/com_adagency/images/tooltip.png" border="0" />
                    <span><?php echo JText::_('VIEWADVERTISEREMAIL_TIP'); ?></span>
                </span>
			</td>
		</tr>

        <?php /* - Choose adv- */ ?>

		<?php /* - BASIC INFORMATION [begin]- */ ?>
		<tr>
			<th style="text-align:left;" colspan="2">
				<?php echo JText::_('ADAG_BASIC_INFO'); ?>
			</th>
		</tr>

		<?php if(isset($configs->show)&&(in_array('phone',$configs->show))) { ?>
		<tr>
			<td width="10%">
                <?php echo JText::_('VIEWADVERTISERPHONE'); ?>:
                <?php if(isset($configs->mandatory)&&(in_array('phone',$configs->mandatory))) { ?>
                    <font color="#ff0000">*</font>
                <?php } ?>
			</td>
			<td>
				<input class="inputbox" type="text" name="telephone" size="40" maxlength="20" value="<?php if(isset($data['telephone'])) { echo $data['telephone']; }?>" />
                <span class="adag_tip">
                    <img src="components/com_adagency/images/tooltip.png" border="0" />
                    <span><?php echo JText::_('VIEWADVERTISERPHONE_TIP'); ?></span>
                </span>
			</td>
		</tr>
        <?php } ?>

		<?php if(isset($configs->show)&&(in_array('url',$configs->show))) { ?>
		<tr>
			<td width="10%">
                <?php echo JText::_('VIEWADVERTISERURL'); ?>:
                <?php if(isset($configs->mandatory)&&(in_array('url',$configs->mandatory))) { ?>
                    <font color="#ff0000">*</font>
                <?php } ?>
			</td>
			<td>
				<input class="inputbox" type="text" name="website" size="40" maxlength="255" value="<?php if(isset($data['website'])) { echo $data['website']; }?>" />
                <span class="adag_tip">
                    <img src="components/com_adagency/images/tooltip.png" border="0" />
                    <span><?php echo JText::_('VIEWADVERTISERURL_TIP'); ?></span>
                </span>
			</td>
		</tr>
        <?php } ?>

		<tr style="display:none;">
			<td width="10%">
				<?php echo JText::_('ADAG_TFORMAT'); ?>:<font color="#ff0000">*</font>
			</td>
			<td>
				<select name="fax">
					<option value="10" <?php if(isset($data['fax'])&&(intval($data['fax']) == 10)) { echo $data['fax']; }?>>mm/dd/yyyy</option>
					<option value="9" <?php if(isset($data['fax'])&&(intval($data['fax']) == 9)) { echo $data['fax']; }?>>mm-dd-yyyy</option>
					<option value="7" <?php if(isset($data['fax'])&&(intval($data['fax']) == 7)) { echo $data['fax']; }?>>dd-mm-yyyy</option>
					<option value="8" <?php if(isset($data['fax'])&&(intval($data['fax']) == 8)) { echo $data['fax']; }?>>dd/mm/yyyy</option>
					<option value="11" <?php if(isset($data['fax'])&&(intval($data['fax']) == 11)) { echo $data['fax']; }?>>yyyy-mm-dd</option>
					<option value="12" <?php if(isset($data['fax'])&&(intval($data['fax']) == 12)) { echo $data['fax']; }?>>yyyy/mm/dd</option>
					<option value="1" <?php if(isset($data['fax'])&&(intval($data['fax']) == 1)) { echo $data['fax']; }?>>dd-mm-yyyy hh:mm:ss</option>
					<option value="2" <?php if(isset($data['fax'])&&(intval($data['fax']) == 2)) { echo $data['fax']; }?>>dd/mm/yyyy hh:mm:ss</option>
					<option value="3" <?php if(isset($data['fax'])&&(intval($data['fax']) == 3)) { echo $data['fax']; }?>>mm-dd-yyyy hh:mm:ss</option>
					<option value="4" <?php if(isset($data['fax'])&&(intval($data['fax']) == 4)) { echo $data['fax']; }?>>mm/dd/yyyy hh:mm:ss</option>
					<option value="5" <?php if(isset($data['fax'])&&(intval($data['fax']) == 5)) { echo $data['fax']; }?>>yyyy-mm-dd hh:mm:ss</option>
					<option value="6" <?php if(isset($data['fax'])&&(intval($data['fax']) == 6)) { echo $data['fax']; }?>>yyyy/mm/dd hh:mm:ss</option>
				</select>	&nbsp;
                <span class="adag_tip">
                    <img src="components/com_adagency/images/tooltip.png" border="0" />
                    <span><?php echo JText::_('ADAG_TFORMAT_TIP'); ?></span>
                </span>
            </td>
		</tr>

		<?php /* - BASIC INFORMATION [end]- */ ?>

		<?php /* - ADMIN OPTIONS [begin] - */ ?>
		<tr>
			<th style="text-align:left;" colspan="2">
			<?php echo JText::_('ADAG_ADMOPT'); ?>
			</th>
		</tr>
		<tr>
			<td>
			<?php echo JText::_('AD_STATUS'); ?>:
			</td>
			<td>
			 <?php echo $lists['approved'] ?>&nbsp;
             <span class="adag_tip">
                 <img src="components/com_adagency/images/tooltip.png" border="0" />
                 <span><?php echo JText::_('AD_STATUS_TIP'); ?></span>
             </span>
			</td>
		</tr>

		<?php /* - ADMIN OPTIONS [end] - */?>

		<?php /* - COMPANY [begin] - */?>
		<?php if(isset($configs->show)&&(in_array('company',$configs->show))) { ?>
		<tr>
			<th style="text-align:left;" colspan="2">
			<?php echo JText::_('ADAG_COMP'); ?>
			</th>
		</tr>
		<tr>
			<td width="20%">
			<?php echo JText::_('VIEWADVERTISERCOMPNAME'); ?>:<?php if(isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?>
			</td>
			<td>
			<input class="inputbox" type="text" name="company" size="40" maxlength="100" valign="top" value="<?php if(isset($data['company'])) { echo $data['company']; }?>" />
            <span class="adag_tip">
                <img src="components/com_adagency/images/tooltip.png" border="0" />
                <span><?php echo JText::_('VIEWADVERTISERCOMPNAME_TIP'); ?></span>
            </span>
			</td>
		</tr>
		<tr>
			<td width="20%">
			<?php echo JText::_('VIEWADVERTISERDESC');?><?php if(isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) { ?>:<font color="#ff0000">*</font><?php } ?>
			</td>
			<td>
		<TEXTAREA NAME="description" ROWS="6" COLS="41"><?php if(isset($data['description'])) { echo $data['description']; }?></TEXTAREA>
            <span class="adag_tip" style="float:right;margin-right:58%;">
                <img src="components/com_adagency/images/tooltip.png" border="0" />
                <span><?php echo JText::_('VIEWADVERTISERDESC_TIP'); ?></span>
            </span>
			<br>
			</td>
		</tr>
		<?php } ?>
		<?php /* - COMPANY [end] - */?>

		<?php /* - ADDRESS [begin] - */?>
		<?php if(isset($configs->show)&&(in_array('address',$configs->show))) { ?>
		<tr>
			<th style="text-align:left;" colspan="2">
			 <?php echo JText::_('VIEWADVERTISERADDRESS') ?>
			</th>
		</tr>
				<tr>
			<td width="10%">
    			<?php echo JText::_('ADAG_STREET'); ?>:<?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?>
			</td>
			<td>
			<input class="inputbox" type="text" name="address" size="40" maxlength="100" value="<?php if(isset($data['address'])) { echo $data['address']; }?>" />
			</td>
		</tr>
<?php //include(JPATH_BASE."/components/com_adagency/includes/js/advertisers_country.php"); ?>

		<tr>
		  <td><?php echo JText::_("VIEWADVERTISERCOUNTRY");?>:<?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?></td>
		  <td><?php echo $this->lists['country_option']; ?>
              &nbsp;
              <span class="adag_tip">
                  <img src="components/com_adagency/images/tooltip.png" border="0" />
                  <span><?php echo JText::_('VIEWADVERTISERCOUNTRY_TIP'); ?></span>
              </span>
          </td>
		</tr>
		<tr>
		  <td><?php echo JText::_("VIEWADVERTISERPROV");?>:<?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?></td>
		  <td> <?php
					echo $this->lists['customerlocation'];
				?>
                <span class="adag_tip"  style="float:right;margin-right:82%;margin-top:-15px;">
                    <img src="components/com_adagency/images/tooltip.png" border="0" />
                    <span><?php echo JText::_('VIEWADVERTISERPROV_TIP'); ?></span>
                </span>
		  </td>
		</tr>

		<tr>
			<td width="10%">
	 <?php echo JText::_('VIEWADVERTISERZIP'); ?>:<?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?><font color="#ff0000">*</font><?php } ?>
			</td>
			<td>
			<input class="inputbox" type="text" name="zip" size="40" maxlength="12" value="<?php if(isset($data['zip'])) { echo $data['zip']; }?>" />
            <span class="adag_tip">
                <img src="components/com_adagency/images/tooltip.png" border="0" />
                <span><?php echo JText::_('VIEWADVERTISERZIP_TIP'); ?></span>
            </span>
			</td>
		</tr>
		<?php } ?>
		<?php /* - ADDRESS [end] - */?>

		<?php /* - EMAIL REPORTS [begin]- */ ?>
		<?php if(isset($configs->show)&&(in_array('email',$configs->show))) { ?>
		<tr>
			<th style="text-align:left;" colspan="2">
			 <?php echo JText::_('VIEWADVERTISEREMAILOPT') ?>
			</th>
		</tr>
		<tr>
			<td width="10%">&nbsp;

			</td>
			<td>
				<table align="left" border="0" cellspacing="0" style="width: 360px;">
				<tr>
					<td width="20">
						<INPUT  TYPE="checkbox" NAME="email_daily_report" value="Y" <?php if(isset($data['email_daily_report'])) { echo 'checked = "checked"'; }?>>
					</td>
					<td width="120">
						<?php echo JText::_('VIEWADVERTISERDAY'); ?>
					</td>
					<td width="20">
						<INPUT  TYPE="checkbox" NAME="email_weekly_report" value="Y" <?php if(isset($data['email_weekly_report'])) { echo 'checked = "checked"'; }?>>
					</td>
					<td width="180">
						<?php echo JText::_('VIEWADVERTISERWEEK'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<INPUT  TYPE="checkbox" NAME="email_month_report" value="Y" <?php if(isset($data['email_month_report'])) { echo 'checked = "checked"'; }?>>
					</td>
					<td>
						<?php echo JText::_('VIEWADVERTISERMONTH'); ?>
					</td>
					<td>
						<INPUT  TYPE="checkbox" NAME="email_campaign_expiration" value="Y" <?php if(isset($data['email_campaign_expiration'])) { echo 'checked = "checked"'; }?>>
					</td>
					<td >
      				<?php echo JText::_('VIEWADVERTISEREXP'); ?>
                    <span class="adag_tip" style="float:right;margin-top:-30px;">
                        <img src="components/com_adagency/images/tooltip.png" border="0" />
                        <span><?php echo JText::_('VIEWADVERTISEREMAILOPT_TIP'); ?></span>
                    </span>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<?php } ?>
		<?php /* - EMAIL REPORTS [end]- */?>

		<?php // Approvals [begin]?>
		<tr>
			<th style="text-align:left;" colspan="2">
			 <?php echo JText::_('ADAG_APPROVALS') ?>
			</th>
		</tr>
		<tr>
			<td><?php echo JText::_('ADAG_AA_ADS');?></td>
			<td><select name="apr_ads">
				<option value="G"><?php echo JText::_('ADAG_USE_GLB'); ?></option>
				<option value="Y" <?php if(isset($data['apr_ads'])&&($data['apr_ads'] == 'Y')) { echo 'selected = "selected"'; }?>><?php echo JText::_('JAS_YES'); ?></option>
				<option value="N" <?php if(isset($data['apr_ads'])&&($data['apr_ads'] == 'N')) { echo 'selected = "selected"'; }?>><?php echo JText::_('JAS_NO'); ?></option>
			</select><?php echo JText::_('ADAG_AA_INFO');?>
            <span class="adag_tip">
                <img src="components/com_adagency/images/tooltip.png" border="0" /></span></td>
		</tr>
		<tr>
			<td><?php echo JText::_('ADAG_AA_CMP');?></td>
			<td><select name="apr_cmp">
				<option value="G"><?php echo JText::_('ADAG_USE_GLB'); ?></option>
				<option value="Y" <?php if(isset($data['apr_ads'])&&($data['apr_cmp'] == 'Y')) { echo 'selected = "selected"'; }?>><?php echo JText::_('JAS_YES'); ?></option>
				<option value="N" <?php if(isset($data['apr_ads'])&&($data['apr_cmp'] == 'N')) { echo 'selected = "selected"'; }?>><?php echo JText::_('JAS_NO'); ?></option>
			</select><?php echo JText::_('ADAG_AA_INFO');?>
            <span class="adag_tip">
                <img src="components/com_adagency/images/tooltip.png" border="0" />
                <span><?php echo JText::_('ADAG_AA_CMP_TIP2'); ?></span>
            </span>
            </td>
		</tr>
		<?php // Approvals [end]?>


	</table>
	<input type="hidden" name="images" value="" />
	<input type="hidden" id="sendmail" name="sendmail" value="1" />
	<input type="hidden" name="user_id" value="<?php if(isset($usr)) { echo $usr->id; } ?>" />
	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="lastreport" value="<?php echo time(); ?>" />
	<input type="hidden" name="weekreport" value="<?php echo time(); ?>" />
	<input type="hidden" name="monthreport" value="<?php echo time(); ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="adagencyAdvertisers" />
    <input type="hidden" name="gb_existing" value="1" />
	<?php
		// the graybox code
		if(isset($get_data['tmpl'])&&($get_data['tmpl'] == 'component')) {
			echo "<input type='hidden' name='tmpl' value='component' />";
			echo "<input type='submit' value='".JText::_('ADAG_SAVE')."' class='btn' onclick='submitbutton(\"save_graybox\");' />";
		}
	?>
    </fieldset>
</form>
