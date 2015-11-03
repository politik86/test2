<?php
/**
 * @version $Id: license.php 7 2013-02-06 11:23:52Z szymon $
 * @package DJ-MediaTools
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 * DJ-MediaTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-MediaTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-MediaTools. If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport( 'joomla.database.table' );


class DJMediatoolsControllerLicense extends JControllerLegacy {

	public function edit(){
		$ext = JRequest::getString('option', '');
		$config = JFactory::getConfig();
		$secret_file = JFile::makeSafe('license_'.$config->get('secret').'.txt');
		$license_file = JPATH_BASE.DS."components".DS.JRequest::getVar('option').DS.$secret_file;
		if(JFile::exists($license_file)){
			$fh = fopen($license_file, 'r');
			$key = fgets($fh);
			fclose($fh);
		}else{
			$key='';
		}


		if($key){
			$license = DJLicense::checkSubscription($key);
		}else{
			$license = '';
		}

		$product = DJLicense::getProductName();
		$u = JFactory::getURI();
			
		echo '<div class="lic_manager">';
			echo '<form action="index.php" method="post" name="adminForm" id="adminForm" >'
				.'<h3>'.JText::_('COM_DJCLASSIFIEDS_DJLIC_LICENSE_MANAGER').'</h3>'
				.'<div class="lic_details">'
				.'<div class="lic_drow"><span class="lic_drow_label">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_PRODUCT').'</span><code>'.$product.'</code></div>'
				.'<div class="lic_drow"><span class="lic_drow_label">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_DOMAIN').'</span><code>'.$u->getHost().'</code></div>';
		
			
			echo '<div class="lic_drow"><span class="lic_drow_label">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_LICENSE').'</span>';	
				if(!$key){
					echo '<input class="inputbox" type="text" value="" name="license" size="30" />';
				}else if(strstr(@$this->license[0], 'E')){
					echo '<input class="inputbox" type="text" value="'.$key.'" name="license" size="30" />';
				}else{
					echo $key;
					echo '<input type="hidden" value="'.$key.'" name="license" size="30" />';
				}
			echo '</div>';
	
			$cl='';
			if(!$key){
				$cl=' last';	
			}
			echo '<div class="lic_drow lic_status_row'.$cl.'"><span class="lic_drow_label">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_STATUS').'</span>';
				
					if(!$key){
						echo '<div class="lic_status_box lic_invalid">';
							echo '<div class="lic_srow1">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_ENTER_LICENSE').'</div>';
							echo '<div class="lic_srow2">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_YOU_CAN_GET').' <a href="http://www.dj-extensions.com">DJ-EXTENSIONS.COM</a></div>';
						echo '</div>';
					}else if(strstr(@$license[0], 'E')){
						echo '<div class="lic_status_box lic_invalid">';
							echo '<div class="lic_srow1">'.end($license).'</div>';
						echo '</div>';
					}else{
						echo '<div class="lic_status_box lic_valid">';
							echo  '<div class="lic_srow1">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_YOUR_LIC_IS_VALID').'</div>';											
							echo  '<div class="lic_srow2">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_EXP_DATE').' '.date("d.m.y", strtotime($license[2])).'</div>';	
						echo '</div>';
					}
				
				echo '<div style="clear:both"></div>';
			echo '</div>';
	
				
			if($key){
				echo '<div class="lic_drow last"><span class="lic_drow_label">&nbsp;</span><input type="checkbox" name="release" value="1" /> '.JText::_('COM_DJCLASSIFIEDS_DJLIC_RELEASE_DOMAIN').'</div>';
			}
				
				echo '<div class="lic_buttons">'
					.'<input type="submit" class="button" value="'.JText::_('COM_DJCLASSIFIEDS_DJLIC_SUBMIT').'"  />'
					.'<input type="button" class="button" value="Close" onclick="SqueezeBox.close(); window.parent.location.reload();" />'
					.'</div>'
					.'<input type="hidden" name="option" value="'.$ext.'" />'
					.'<input type="hidden" name="task" value="license.save" />'
					.'</form>';
		
		echo '</div>';

	}


	public function save(){
		$app	= JFactory::getApplication();
		$config = JFactory::getConfig();
		
		$ch = curl_init();
		$ext = JRequest::getString('option', '');
		$license = JRequest::getVar('license');
		$r = JRequest::getString('release', '0');

		curl_setopt($ch, CURLOPT_URL,'http://dj-extensions.com/index.php?option=com_djsubscriptions&view=registerLicense&license='.$license.'&ext='.$ext.'&r='.$r);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$u = JFactory::getURI();
		curl_setopt ($ch, CURLOPT_REFERER, $u->getHost());

		if(!curl_errno($ch))
		{
			$contents = curl_exec ($ch);
		}

		curl_close ($ch);
		$res= explode(';', $contents);

		if(strstr($res[0], 'E')){
			$app->enqueueMessage(end($res),'Error');
			$app->redirect('index.php?option='.$ext.'&task=license.edit&tmpl=component');
		}else if(strstr($res[0], 'R')){
			$secret_file = JFile::makeSafe('license_'.$config->get('secret').'.txt');
			$license_file = JPATH_BASE.DS."components".DS.JRequest::getVar('option').DS.$secret_file;
			$fh = fopen($license_file, 'w');
			fwrite($fh, '');
			fclose($fh);
			$key = fgets($fh);
			$app->redirect('index.php?option='.$ext.'&task=license.edit&tmpl=component',end($res));
		}else{
			$secret_file = JFile::makeSafe('license_'.$config->get('secret').'.txt');		
			$license_file = JPATH_BASE.DS."components".DS.JRequest::getVar('option').DS.$secret_file;
			$fh = fopen($license_file, 'w');
			fwrite($fh, $license);
			fclose($fh);
			$key = fgets($fh);
			$app->redirect('index.php?option='.$ext.'&task=license.edit&tmpl=component',end($res));
		}
	}

	public function update_list(){
		$ext_name = 'djmediatools';
		//$ext_name2 = 'djcf';
		$db =  JFactory::getDBO();
		$query = "SELECT name, type, element, manifest_cache "
		."FROM #__extensions WHERE element LIKE '%".$ext_name."%' OR folder='djmediatools' "
		."ORDER BY type ";
		$db->setQuery($query);
		$ext_list = $db->loadObjectList();
		//echo "<pre>".print_r($ext_list,true)."</pre> ";
		JHTML::_('behavior.framework');
		$config = JFactory::getConfig();
		$secret_file = JFile::makeSafe('license_'.$config->get('secret').'.txt');		
		$license_file = JPATH_BASE.DS."components".DS.JRequest::getVar('option').DS.$secret_file;
		
		$fh = fopen($license_file, 'r');
		$license = fgets($fh);
		fclose($fh);

		$ext_versions= explode(';', DJLicense::checktVersions());
		//echo "<pre>".print_r($ext_versions,true)."</pre> ";
		jimport( 'joomla.utilities.utility' );
		
		$js = "		
		function make_update3(ext,license,version){
		
		
			$('update_link_'+ext+'').set('html','".JText::_('COM_DJCLASSIFIEDS_DJLIC_PLEASE_WAIT')." <img src=\"".JURI::base()."components/".JRequest::getString('option')."/images/loading.gif\" />');
		
			// The elements used.
			var myForm = document.id('frm_'+ext+'');
			var myElement = document.id('myResult2');
				
			var req_url='http://dj-extensions.com/index.php?option=com_djsubscriptions&view=getUpdate&license='+license+'&ext='+ext+'&v='+version;
			var myRequest = new Request({
				url: 'index.php',
				method: 'post',
				data: {
					'option': 'com_installer',
					'view': 'install',
					'task': 'install.install',
					'installtype': 'url',
					'".JSession::getFormToken()."': 1,
					'install_url': req_url
				},
				onRequest: function(){
					myElement.set('html', '<div style=\"text-align:center;\"><img style=\"margin-top:10px;\" src=\"".JURI::base()."components/".JRequest::getString('option')."/images/long_loader.gif\" /><br />".JText::_('COM_DJCLASSIFIEDS_DJLIC_LOADING')."</div>');
				},
				onSuccess: function(responseText){
					myElement.set('html', '');
					
					if (/\bMSIE\b/.test(navigator.appVersion)) { window.location.reload(); }
					
					var response = new Element('div');
					response.set('html',responseText);
					var res = response.getElement('#system-message, .alert').getParent();
					if(res.getElement('.error, .alert-error')){
						$('update_link_'+ext+'').set('html','".JText::_('COM_DJCLASSIFIEDS_DJLIC_FAILD')."');
					} else {
						$('update_link_'+ext+'').set('html','".JText::_('COM_DJCLASSIFIEDS_DJLIC_DONE')." <img src=\"".JURI::base()."templates/hathor/images/admin/tick.png\" />');
					}
												
					myElement.set('html', res.get('html'));
				},
				onFailure: function(){
					myElement.set('html', 'Sorry, your request failed, please contact to contact@design-joomla.eu');
				}
			});
			myRequest.send();		
		}
		";
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		
		?>
		
		<div class="lic_extensions_box">	
			<div id="toolbar-box">
				<div class="pagetitle icon-32-export"
					style="height: 30px; padding-left: 50px; width: auto;">
					<h3 style="margin: 5px 0px;"><?php echo JText::_('COM_DJCLASSIFIEDS_DJLIC_EXT_LIST')?></h3>
				</div>
			</div>
			<table class="adminlist table table-striped">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_DJCLASSIFIEDS_DJLIC_NAME')?></th>
						<th><?php echo JText::_('COM_DJCLASSIFIEDS_DJLIC_TYPE')?></th>
						<th><?php echo JText::_('COM_DJCLASSIFIEDS_DJLIC_ELEMENT')?></th>
						<th><?php echo JText::_('COM_DJCLASSIFIEDS_DJLIC_CURRENT_VERSION')?></th>
						<th><?php echo JText::_('COM_DJCLASSIFIEDS_DJLIC_LATEST_VERSION')?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php
				
				$style = '';
				$version = new JVersion;
				if (version_compare($version->getShortVersion(), '3.0.0', '<')) {
					$style = ' style="cursor:pointer; background: none repeat scroll 0 0 #F0F0F0;border: 1px solid silver;color: #000000;font-size: 10px;padding: 1px 5px;" ';
				}				
				
				foreach($ext_versions as $r){
					$e= explode(',', $r);
					$exist = '';
					foreach($ext_list as $ext){
						//if($ext->element==$e[0]){
						if( strtolower($ext->element) == strtolower($e[0]) || ( $ext->type=='plugin' && strtolower($ext->name) == strtolower($e[0]))){
							$l_version = $e[1];
								echo '<tr><td>'.JText::_($ext->name).'</td>';
									echo '<td>'.$ext->type.'</td>';
									echo '<td>'.$ext->element.'</td>';
									$mc = json_decode($ext->manifest_cache);
									$c_version = $mc->version;
									echo '<td>'.$c_version.'</td>';
									echo '<td>'.$l_version.'</td>';
									echo '<td width="100px">';
									if($e[2]){
										if(version_compare($c_version,$l_version,'<')){
											echo '<div id="update_link_'.$e[0].'" style="width:100px;"><span class="btn btn-mini btn-danger" '.$style.' onclick="make_update3(\''.$e[0].'\',\''.$license.'\',\''.$c_version.'\');">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_UPDATE').'</span></div>';
										}else{
											echo '<span>'.JText::_('COM_DJCLASSIFIEDS_DJLIC_LATEST').' <img src="'.JURI::base().'templates/hathor/images/admin/tick.png" style="vertical-align: middle;" /></span>';
										}		
									}else{
										if($e[3]){
											echo $e[3].JText::_('COM_DJCLASSIFIEDS_DJLIC_RENEW').'</a>';											
										}else{
											echo '<a href="http://www.dj-extensions.com" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_RENEW').'</a>';
										}																	
									}
													
									echo '</td>';
								echo '</tr>';			
							$exist = 1;
							break;
						}
					}
					if(!$exist){
						echo '<tr><td>'.$e[0].'</td>';
						echo '<td>';
						if(strstr($e[0],'com_')){
							echo 'component';
						}else if(strstr($e[0],'mod_')){
							echo 'module';
						}else if(strstr($e[0],'plg_')){
							echo 'plugin';
						}
						echo '</td>';			
						echo '<td>'.$e[0].'</td><td>---</td><td>'.$e[1].'</td><td>';
							if($e[2]){
								echo '<div id="update_link_'.$e[0].'" style="width:100px;"><span class="btn btn-mini btn-success" '.$style.' onclick="make_update3(\''.$e[0].'\',\''.$license.'\',\'0\');">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_INSTALL').'</span></div>';
							}else{
								if($e[4]){
									echo $e[4].JText::_('COM_DJCLASSIFIEDS_DJLIC_BUY').'</a>';	
								}else{
									echo '<a href="http://www.dj-extensions.com" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_DJLIC_BUY').'</a>';
								}
							}
						echo '</td></tr>';
					}
				}
			?>
				</tbody>
			</table>
			
			<div id="myResult2"></div>
		</div>
	<?php
	}
}
