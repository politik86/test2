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

	$advertiser = $this->advertiser;
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
?>

<script type="text/javascript" src="<?php echo JURI::base().'components/com_adagency/helpers/prototype-1.6.0.2.js' ?>"></script>
		
<?php include(JPATH_BASE."/components/com_adagency/includes/js/advertisers_ajax.php"); ?>
<div style="float:right">
<?php 
	$link = "ajax_adduser(); ";
	?>
	<!-- <a onclick="<?php echo $link;?>" href="#">SAVE</a> -->
	<input type="button" onclick="<?php echo $link;?>" value="<?php echo JText::_("SAVE_BUTTON_GRBX");?>" class="button" name="save_this"/>
</div>		
<br /><br />		
			
 <form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo JText::_('VIEWADVERTISERINFO');?></legend>
                <table class="admintable">
                
    <table class="adminform">
		<tr>
			<th colspan="2">
			<?php echo JText::_('VIEWADVERTISERINFO'); ?>
			</th>
		</tr>
		<tr>
			<td width="20%">
			<?php echo JText::_('VIEWADVERTISERCOMPNAME'); ?>:<font color="#ff0000">*</font>
			</td>
			<td>
			<input class="inputbox" type="text" name="company" size="40" maxlength="100" valign="top" value="<?php echo $advertiser->company; ?>" />
			</td>
		</tr>
		<tr>
			<td width="20%">
			<?php echo JText::_('VIEWADVERTISERDESC');?>:
			</td>
			<td>
		<TEXTAREA NAME="description" ROWS="3" COLS="50"><?php echo $advertiser->description;?></TEXTAREA>
			<br>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JText::_('ADV_APPROVED'); ?>:
			</td>
			<td>
			 <?php echo $lists['approved'] ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JText::_('Enabled'); ?>:
			</td>
			<td>
			 <?php echo $lists['enabled'] ?>
			</td>
		</tr>
		<tr>
		<tr>
			<th colspan="2">
			 <?php echo JText::_('VIEWADVERTISERINFO2') ?>
			</th>
		</tr>
		
		<?php if ($advertiser->aid==0) { ?>
		<tr>
			<td width="10%">
			<?php echo JText::_('VIEWADVERTISERLOGIN');?>:<font color="#ff0000">*</font>  
			</td>
			<td>
			<input class="inputbox" type="text" name="username" size="40" maxlength="25" value="<?php echo $user->username; ?>" />
			</td>
		</tr>
		<tr>
			<td width="10%">
			 <?php echo JText::_('VIEWADVERTISERPASS'); ?>:<font color="#ff0000">*</font>
			</td>
			<td>
			<input class="inputbox" type="password" name="password" size="40" maxlength="100" value="" />
			</td>
		</tr>
		<?php	} else { ?>
		<tr>
			<td width="10%">
			 <?php echo JText::_('VIEWADVERTISERLOGIN');?>:<font color="#ff0000">*</font> 
			</td>
			<td>
			<input class="inputbox" type="text" name="username" readonly="" style="color:#FF0000; font-weight:bold; background-color:transparent;border:0px solid white" size="40" maxlength="25" value="<?php echo $user->username; ?>" />
			</td>
		</tr>
		<?php	} ?>
		<tr>
			<td width="10%">
			 <?php echo JText::_('VIEWADVERTISEREMAIL');?>:<font color="#ff0000">*</font> 
			</td>
			<td>
				<input class="inputbox" type="text" name="email" size="40" maxlength="100" 
					<?php #if ($advertiser->aid!=0) echo ' readonly disabled '; ?>
					value="<?php echo $user->email; ?>" />
			</td>
		</tr>
		<tr>
			<td width="10%">
			  <?php echo JText::_('VIEWADVERTISEREMAILOPT')?>:
			</td>
			<td>
				<table align="left" border="0" cellspacing="0" width="350">
				<tr>
					<td width="20">
						<INPUT  TYPE="checkbox" <?php if ($advertiser->email_daily_report=='Y') echo 'checked' ?> NAME="email_daily_report" value="Y" >
					</td>
					<td width="120">
						<?php echo JText::_('VIEWADVERTISERDAY'); ?>
					</td>
					<td width="20">
						<INPUT  TYPE="checkbox" <?php if ($advertiser->email_weekly_report=='Y') echo 'checked' ?> NAME="email_weekly_report" value="Y" >
					</td>
					<td width="180">
						<?php echo JText::_('VIEWADVERTISERWEEK'); ?> 
					</td>
				</tr>
				<tr>
					<td>
						<INPUT  TYPE="checkbox" <?php if ($advertiser->email_month_report=='Y') echo 'checked' ?> NAME="email_month_report" value="Y" >
					</td>
					<td>
						<?php echo JText::_('VIEWADVERTISERMONTH'); ?> 
					</td>
					<td>
						<INPUT  TYPE="checkbox" <?php if ($advertiser->email_campaign_expiration=='Y') echo 'checked' ?> NAME="email_campaign_expiration" value="Y">
					</td>
					<td >
      				<?php echo JText::_('VIEWADVERTISEREXP'); ?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th colspan="2">
   			<?php echo JText::_('VIEWADVERTISERINFO3');?>
			</th>
		</tr>
		<tr>
			<td width="10%">
			 <?php echo JText::_('VIEWADVERTISERCONTACT'); ?>:<font color="#ff0000">*</font>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" size="40" maxlength="50" value="<?php echo $user->name; ?>" />
			</td>
		</tr>
		<tr>
			<td width="10%">
   			<?php echo JText::_('VIEWADVERTISERURL'); ?>:
			</td>

			<td>
			<input class="inputbox" type="text" name="website" size="40" maxlength="255" value="<?php echo $advertiser->website?$advertiser->website:'http://'; ?>" />
			</td>
		</tr>
		<tr>
			<td width="10%">
     <?php echo JText::_('VIEWADVERTISERADDRESS'); ?>:
			</td>
			<td>
			<input class="inputbox" type="text" name="address" size="40" maxlength="100" value="<?php echo $advertiser->address; ?>" />
			</td>
		</tr>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/advertisers_ajax_prov.php"); ?>
    <tr>
      <td><?php echo JText::_("VIEWADVERTISERCOUNTRY");?>:<font color="#ff0000">*</font></td>
      <td><?php echo $this->lists['country_option']; ?></td>
    </tr>
    <tr>
      <td><?php echo JText::_("VIEWADVERTISERPROV");?>:<font color="#ff0000">*</font></td>
      <td id="to_be_replaced_prov"> <?php 
		echo $this->lists['customerlocation'];
?>
      </td>
    </tr>
		<tr>
			<td width="10%">
     <?php echo JText::_('VIEWADVERTISERCITY'); ?>:
			</td>
			<td>
			<input class="inputbox" type="text" name="city" size="40" maxlength="100" value="<?php echo $advertiser->city; ?>" />
			</td>
		</tr>
		<tr>
			<td width="10%">
     <?php echo JText::_('VIEWADVERTISERZIP'); ?>:
			</td>
			<td>
			<input class="inputbox" type="text" name="zip" size="40" maxlength="12" value="<?php echo $advertiser->zip; ?>" />
			</td>
		</tr>
		<tr>
			<td width="10%">
     <?php echo JText::_('VIEWADVERTISERPHONE'); ?>:
			</td>
			<td>
			<input class="inputbox" type="text" name="telephone" size="40" maxlength="20" value="<?php echo $advertiser->telephone; ?>" />
			</td>
		</tr>
		
		<tr>
			<td colspan="3">
			</td>
		</tr>
	</table>
	<input type="button" onclick="<?php echo $link;?>" value="<?php echo JText::_("SAVE_BUTTON_GRBX");?>" class="button" name="save_this"/>
	</fieldset>
   	    	 
		<input type="hidden" name="images" value="" />                
	        <input type="hidden" name="option" value="com_adagency" />
	        <input type="hidden" name="aid" value="<?php echo $advertiser->aid; ?>" />
	        <input type="hidden" name="user_id" value="<?php echo $advertiser->user_id; ?>" />
	        <input type="hidden" name="lastreport" value="<?php if (isset($advertiser->lastreport)) echo $advertiser->lastreport; else echo time(); ?>" />	        
	       <input type="hidden" name="weekreport" value="<?php if (isset($advertiser->weekreport)) echo $advertiser->weekreport; else echo time(); ?>" />	        
	       <input type="hidden" name="monthreport" value="<?php if (isset($advertiser->monthreport)) echo $advertiser->monthreport; else echo time(); ?>" />	        
	        <input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="adagencyAdvertisers" />
        </form>