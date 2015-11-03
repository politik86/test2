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

$plugin = $this->plugin;
$configs = $this->configs;
$nullDate = 0;
$test = $this->plugin_data;

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
?>
<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
    <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWPLUGSETTINGS'); ?>
				</h2>
            </div>
      </div>
      
<table class="table table-striped table-bordered">
        <thead>
	        <th width="15%"><?php echo JText::_('VIEWPLUGPLUG');?></th>
	        <th><?php echo JText::_('VIEWPLUGSETTING');?></th>
        </thead>
        <tbody>
<?php
    $content = '';
    $content .= '
        <style type="text/css">
        .plug_unpublished {
            color:gray;
        }
        .plug_published {
            color:black;
        }
    </style>
    ';
    $k = 0;
    if ( count($test) > 0)
        foreach ( $test as $i => $v) {
            if( 1 == 1) {
                $content .= '
                    <tr class="row'.$k.'">
                        <td valign="top">
                             '.$v["header"].'
                        </td>
                        <td valign="top"><div class="span12">';
                foreach ($v['header1'] as $i1 => $v1 ) {
                    $content .= '<label class="span3">'.$v["descriptions"][$i1].'</label>';
                    $content .= '<input class="span9" name="'.$v["header1"][$i1].'" type="'.($v["type"]=="payment"&&$v["pluginname"]=="authorizenet"?"password":"text").'" size="30" value="'.$v["value"][$i1].'"/>';
                }
                $content .= '</div></td>';
                $content .= "</tr>";
                $k = 1 - $k;
        } else {
        }
        }
    echo ($content);
?>
</tbody>
</table>
    <input type="hidden" name="images" value="" />
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="id" value="<?php echo $plugin->id; ?>" />
    <input type="hidden" name="plugintype" value="<?php echo $plugin->type; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="adagencyPlugins" />
</form>
