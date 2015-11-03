<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

?>				<div class="control-group span6">
					<label class="ad-fields-lable "><?php echo JText::_("GRP_TYPE");?> </label>
					<div class="controls">
				<?php
				if($vars[0] != "")
				{
					foreach($vars[0] as $result)
					{
						$options[] = JHtml::_('select.option',$result->id,$result->name,'value','text');
					}
				}
					echo JHtml::_('select.genericlist', $options, 'plgdata[][group,select]', 'class="ad-fields-inputbox inputbox input-medium chzn-done" onchange=" calculatereach() " size="5" multiple="multiple" ','value', 'text',	$vars[1]);

					$options= array();
				?>
				</div>
				</div>
