<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2015 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
  $item_id = $this->itemid;
  if($item_id != 0){ 
    $Itemid = "&Itemid=".intval($item_id);  
  } 
  else{ 
    $Itemid = NULL; 
  }
?>

<script type="text/javascript">
  function redirect(){
    document.location.href="<?php echo "index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=0".$Itemid; ?>";
  }
</script>

<!-- Login Container -->
<div class="ada-login">
  <div class="ada-login-heading">
    <h2 class="ada-login-title"><?php echo JText::_('AD_REGISTER_OR_LOGIN');?></h2>
    <h3 class="ada-login-subtitle"><?php echo JText::_('ADAG_LOGACC'); ?></h3>
  </div>
  <form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=login'.$Itemid); ?>" method="post" name="adaglogin" class="uk-form" id="adg-form-login">
    <ul class="uk-grid uk-grid-medium uk-grid-width-medium-1-2">
      <li>
        <!-- Login form -->
        <div class="uk-panel uk-panel-box ada-login-box">
          <fieldset>
            <legend><?php echo JText::_('ADAG_LOG_BLW'); ?></legend>
            <!-- Login input -->
            <div class="uk-form-row">
              <label for="adag_username">
                <?php echo JText::_('ADAG_USER');?>: 
                <span class="uk-text-danger">*</span>
              </label>
              <input type="text" id="adag_username" name="adag_username" placeholder="username.." />
            </div>
            <!-- Register input -->
            <div class="uk-form-row">
              <label for="adag_passwd">
                <?php echo JText::_('ADAG_PSW');?>: 
                <span class="uk-text-danger">*</span>
              </label>
              <input type="password" id="adag_passwd" name="adag_password" placeholder="password.." />
            </div>
            <!-- Remember me checkbox -->
            <div class="uk-form-row">
              <label><input type="checkbox" name="remember_me" value="1" checked="checked" /> <?php echo JText::_('ADAG_REMME');?></label>
            </div>
            <!-- Login button -->
            <div class="uk-form-row">
              <?php
                $class = "";
                $log_label = JText::_('ADAG_LOGCONT').">>";
                $reg_label = JText::_('ADAG_CONTREG');
              ?>
              
              <button type="submit" class="uk-button uk-button-success"> <?php echo $log_label; ?></button>
            </div>
          </fieldset>
        </div>
      </li>
      <li>
        <!-- Register message -->
        <div class="uk-panel uk-panel-box ada-register-box uk-panel-box-primary">
          <fieldset>
            <legend><?php echo JText::_('ADAG_NOTMB');?></legend>
            <!-- Register description -->
            <div class="uk-form-row">
              <p><?php echo JText::_('ADAG_REG_NOW'); ?>! <?php echo JText::_('ADAG_REGPREV'); ?></p>
            </div>
            <!-- Register button -->
            <div class="uk-form-row">
              <button class="uk-button uk-button-primary" type="button" onClick="javascript:redirect()"> <?php echo $reg_label; ?></button>
            </div>
          </fieldset>
        </div>
      </li>
    </ul>
    <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />         
    <input type="hidden" name="returnpage" value="<?php echo JRequest::getVar("returnpage", ""); ?>" />
    <input type="hidden" name="pid" value="<?php echo JRequest::getVar("pid", ""); ?>" />
  </form>
</div>
