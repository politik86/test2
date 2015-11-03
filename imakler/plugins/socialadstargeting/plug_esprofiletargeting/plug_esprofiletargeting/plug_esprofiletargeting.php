<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

?>			<table><tr>
 				<td class="ad-fields-lable"><?php echo JText::_("PROFILE_TYPE");?> </td>
		 		<td>
				<?php
				if($vars[0] != "")
				{
					foreach($vars[0] as $result)
					{
						$options[] = JHtml::_('select.option',$result->id,$result->title,'value','text');
					}

				}
					echo JHtml::_('select.genericlist', $options, 'plgdata[][esprofile,select]', 'class="ad-fields-inputbox input-medium" onchange=" calculatereach() " size="3" multiple="true" ', 'value', 'text', $vars[1]);
					$options= array();
				?>
			</td></tr></table>
