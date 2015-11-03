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

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<h2 class="pub-page-title" ><?php echo JText::_("VIEWTREEABOUT"); ?></h2>
<div id = "about-content">
        <div class="well well-minimized">
            <strong><?php echo JText::_("AD_ABOUTCOMPONENT"); ?> </strong>
        </div>
        <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('AD_ABOUTINSTALLED');?></h5></div>
			<div class="widget-body">
    			<div class="widget-main clearfix">
    				 <div class="clearfix"></div>
                    <div class="span12">
                        <div class="span3">
                            <?php echo $this->component['installed'] ?
                            '<font color="green"><strong>'.JText::_("AD_ABOUTINSTALLED").'</strong></font>' :
                            '<font color="red"><strong><nowrap>'.JText::_("AD_ABOUTNOTINSTALLED").'</nowrap></strong></font>';
                            ?>
                        </div>
                        <div class="span2">
                            + <?php echo $this->component['name'];?>
                        </div>
                        <div class="span4">
                            <?php echo $this->component['version'];?>
                        </div>
                    </div>
                  </div>
               </div>   
       <div class="clearfix"></div>
       
	  <div class="well well-minimized">
            <strong><?php echo JText::_("AD_ABOUTMODULES"); ?> </strong>
        </div>
	  <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('AD_ABOUTINSTALLED');?></h5></div>
			<div class="widget-body">
    			<div class="widget-main clearfix">
    				 <div class="clearfix"></div>
                    <div class="span12">
                        <div class="span3">
                            <?php echo $this->modulezone['installed'] ?
                            '<font color="green"><strong>'.JText::_("AD_ABOUTINSTALLED").'</strong></font>' :
                            '<font color="red"><strong><nowrap>'.JText::_("AD_ABOUTNOTINSTALLED").'</nowrap></strong></font>';
                            ?>
                        </div>
                        <div class="span2">
                            + <?php echo $this->modulezone['name'];?>
                        </div>
                        <div class="span4">
                            <?php echo $this->modulezone['version'];?>
                        </div>
                    </div>
                     <div class="clearfix"></div>
                    <div class="span12">
                        <div class="span3">
                            <?php echo $this->modulemenu['installed'] ?
                            '<font color="green"><strong>'.JText::_("AD_ABOUTINSTALLED").'</strong></font>' :
                            '<font color="red"><strong><nowrap>'.JText::_("AD_ABOUTNOTINSTALLED").'</nowrap></strong></font>';
                            ?>
                        </div>
                        <div class="span2">
                            + <?php echo $this->modulemenu['name'];?>
                        </div>
                        <div class="span4">
                            <?php echo $this->modulemenu['version'];?>
                        </div>
                    </div>
                     <div class="clearfix"></div>
                    <div class="span12">
                        <div class="span3">
                            <?php echo $this->modulecpanel['installed'] ?
                            '<font color="green"><strong>'.JText::_("AD_ABOUTINSTALLED").'</strong></font>' :
                            '<font color="red"><strong><nowrap>'.JText::_("AD_ABOUTNOTINSTALLED").'</nowrap></strong></font>';
                            ?>
                        </div>
                        <div class="span2">
                            + <?php echo $this->modulecpanel['name'];?>
                        </div>
                        <div class="span4">
                            <?php echo $this->modulecpanel['version'];?>
                        </div>
                    </div>
                     <div class="clearfix"></div>
                    <div class="span12">
                        <div class="span3">
                            <?php echo $this->modulegeo['installed'] ?
                            '<font color="green"><strong>'.JText::_("AD_ABOUTINSTALLED").'</strong></font>' :
                            '<font color="red"><strong><nowrap>'.JText::_("AD_ABOUTNOTINSTALLED").'</nowrap></strong></font>';
                            ?>
                        </div>
                        <div class="span2">
                            + <?php echo $this->modulegeo['name'];?>
                        </div>
                        <div class="span4">
                            <?php echo $this->modulegeo['version'];?>
                        </div>
                    </div>
                     <div class="clearfix"></div>
                     <div class="span12">
                        <div class="span3">
                            <?php echo $this->moduleremote['installed'] ?
                            '<font color="green"><strong>'.JText::_("AD_ABOUTINSTALLED").'</strong></font>' :
                            '<font color="red"><strong><nowrap>'.JText::_("AD_ABOUTNOTINSTALLED").'</nowrap></strong></font>';
                            ?>
                        </div>
                        <div class="span2">
                            + <?php echo $this->moduleremote['name'];?>
                        </div>
                        <div class="span4">
                            <?php echo $this->moduleremote['version'];?>
                        </div>
                    </div>
                    
                  </div>
               </div>   
       <div class="clearfix"></div>
	
</div>
	  	<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="adagencyAbout" />
</form>



