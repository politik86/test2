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

	$document = JFactory::getDocument();
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
    $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
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
<div id="register_login">
<div class="page-title">
<h2> <?php echo JText::_('AD_REGISTER_OR_LOGIN');?></h2>
<h3> <?php echo JText::_('ADAG_LOGACC'); ?></h3>
</div>

<div class="adg_row">
	<div class="adg_cell span12">   
		<div><div> 		
          <form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=login'.$Itemid); ?>" method="post" name="adaglogin" class="clearfix" id="adg-form-login" >
           	<div class="adg_row">
             <div class="span6 adg_bordered_box adg_cell">
             	<div><div>
             	<div class="adg_mod_title">
	             	<h3> 
	             	<?php echo JText::_('ADAG_LOG_BLW'); ?></h3>
    			</div>
             	
                <div class="control-group">
                    <label  class="control-label a_cell span3" for="adag_username">
                        <?php echo JText::_('ADAG_USER');?>:
                        <span class="star">*</span>
                    </label>
                    <div class="controls">
                       <input type="text" class="inputbox" size="15" id="adag_username" name="adag_username" placeholder="Username" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label  class="control-label a_cell span3" for="adag_passwd">
                        <?php echo JText::_('ADAG_PSW');?>:
                        <span class="star">*</span>
                    </label>
                    <div class="controls">
                        <input type="password" class="inputbox" size="15" id="adag_passwd" name="adag_password" placeholder="Password" />
                    </div>
                </div>
               
                
                <div class="control-group">
                    <label class="control-label">
                    </label>
                    <div class="controls">
                       	<input type="checkbox" name="remember_me" value="1" checked="checked" />&nbsp;<?php echo JText::_('ADAG_REMME');?>
                    </div>
                </div>
                	<?php
								$class = "";
								$log_label = JText::_('ADAG_LOGCONT').">>";
								$reg_label = JText::_('ADAG_CONTREG');
							
							?>
                <div class="control-group">
                    <label class="control-label">
                   			
                    </label>
                    <div class="controls">
                    	 <button type="submit" class="btn btn-primary"> <?php echo $log_label; ?></button>
                    </div>
                </div>
       		</div></div>
    	</div>

    	<div class="span6 adg_bordered_box adg_cell">
    		<div><div>
    		
			<div class="adg_mod_title">
            <h3><?php echo JText::_('ADAG_NOTMB');?></h3>
            </div>
            <div class="notamember_text"> 
                <?php echo JText::_('ADAG_REG_NOW'); ?>
            </div>  
            	
            	 <div class="control-group">
                    <div class="controls">
                    	<button class="btn btn-success" type="button" onClick="javascript:redirect()"> <?php echo $reg_label; ?></button>
                    </div>
                </div>
             </div></div>
        </div>
        </div>
        <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />					
		<input type="hidden" name="returnpage" value="<?php echo JRequest::getVar("returnpage", ""); ?>" />
		<input type="hidden" name="pid" value="<?php echo JRequest::getVar("pid", ""); ?>" />
	</form>
	</div></div>
</div>
</div>
</div>