<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

?>		<div class="control-group span6">
				<label class="ad-fields-lable "><?php echo JText::_("PROFILE_TYPE");?> </label>
		 		<div class="controls">
				<?php
				if($vars[0] != "")
				{
					foreach($vars[0] as $result)
					{
						$options[] = JHtml::_('select.option',$result->id,$result->name,'value','text');
					}

				}
					echo JHtml::_('select.genericlist', $options, 'plgdata[][jsprofile,select]', 'class="ad-fields-inputbox inputbox input-medium chzn-done" onchange=" calculatereach() " size="3" multiple="multiple" ', 'value', 'text', $vars[1]);
					$options= array();
				?>
				</div>
			</div>

