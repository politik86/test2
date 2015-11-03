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
	$k = 0;
	$n = count ($this->plugins);

?>
<script language="javascript" type="text/javascript" >
<!--
	function checkPluginFile () {
		var file = document.getElementById("pluginfile");
		if (file.value.length < 1) {
			alert ('<?php echo JText::_("VIEWPLUGNOPLUGFORUPL");?>');
			return false;
		}

	}

-->
</script>
<div class="row-fluid">
            <h2 class="pub-page-title"><?php echo JText::_("VIEWTREEPLUGINS"); ?></h2>
</div>
<div class="pluginlist-content">
	 <div class="row-fluid">
	      <div class="span12">
	      	<form action="index.php" name="adminForm" id="adminForm" method="post">
	      	<div id="editcell" >
		       <table class="table table-striped table-bordered" id="pluginlist-content">  
		       		<thead>
		       			<th width="5">
							 <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
							 <span class="lbl"></lbl>
						</th>
					    <th width="20">
							<?php echo JText::_('VIEWPLUGID');?>
						</th>
						<th>
							<?php echo JText::_('VIEWPLUGTITLE');?>
						</th>
						<th>
							<?php echo JText::_("Plugin Type");?>	
						</th>
						<th>
							<?php echo JText::_("VIEWPLUGPUBLISH");?>	
						</th>
		       		</thead>
		       		<tbody>
		       			<?php 
							for ($i = 0; $i < $n; $i++):
								@$plugin = $this->plugins[$i];
								if (empty($plugin)) continue;
								$id = $plugin->id;
								$checked = JHTML::_('grid.id', $i, $id);
								$link = JRoute::_("index.php?option=com_adagency&controller=adagencyPlugins&task=edit&cid[]=".intval($id));
								$published = JHTML::_('grid.published', $plugin, $i );
						?>
		       			
						<tr class="row<?php echo $k;?>"> 
						     	<td>
						     	    <?php echo $checked;?>
						     	    <span class="lbl"></lbl>
								</td>		
						     	<td>
						     	    	<?php echo $i+1;?>
								</td>		
					
						     	<td>
						     	    	<?php echo "<a href='".$link."'>".$plugin->name.'</a> ( '.$plugin->filename.' ) ';?>
								</td>		
						     	<td>
						     	    	<?php echo 'payment';?>
								</td>
								<td align="center">
						     	    	<?php echo $published;?>
								</td>		
						 </tr>
						<?php 
								$k = 1 - $k;
							endfor;
						?>
		       		</tbody>
		       	</table>
		       	</div>
		       	
				<input type="hidden" name="option" value="com_adagency" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="controller" value="adagencyPlugins" />
				
				</form>
	      </div>
	</div>
	
	
	<!-- UPLOAD -->	
	<form action="index.php" name="pluginFileForm" method="post" enctype="multipart/form-data" onsubmit="return checkPluginFile();">
		<div id="editcell" >
			<table >
				<tr>
					<td nowrap>
						<input type="file" name="pluginfile" id="pluginfile" /> 
						<input type="submit" class="btn" name="submit" value="Upload plugin" />
					</td>
				</tr>
			</table>
		</div>
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="upload" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="adagencyPlugins" />
	</form>
</div> 