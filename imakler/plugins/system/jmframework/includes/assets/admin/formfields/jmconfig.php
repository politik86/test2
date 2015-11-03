<?php
/**
 * @version $Id: jmconfig.php 19 2013-11-05 10:07:26Z michal $
 * @package JMFramework
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * JMFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JMFramework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JMFramework. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JFormFieldJmconfig extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'Jmconfig';
    protected static $loaded = false;
    protected $template_name = null;

    
    protected function getInput()
    {
        if (!self::$loaded && defined('JMF_TPL_PATH') && defined('JMF_TPL_URL')) {
            $app = JFactory::getApplication();
            self::$loaded = true;
            
            $formControl = $this->formControl;
            if ($this->group) {
                $formControl .= '_'.$this->group;
            }
            
            $action = $app->input->get('jmconfig_action', null, 'cmd');
            $file = $app->input->get('jmconfig_file', null, 'cmd');
            $styleId = $app->input->get('id', null, 'int');
            
            if ((int)$styleId > 0) {
            	$db = JFactory::getDbo();
            	$db->setQuery('SELECT template FROM #__template_styles WHERE id='.(int)$styleId);
            	$this->template_name = $db->loadResult();
            }
            
            $msg = $this->performActions($action, $styleId, $file);
            if ($msg) {
                $uri = JURI::getInstance();
                $query = $uri->getQuery(true);
                unset($query['jmconfig_action']);
                unset($query['jmconfig_file']);
                $uri->setQuery($query);
                
                $app->redirect($uri->toString(), JText::_($msg));
                return;
                
                /*$html[] = '<div id="jm_config_message">';
                $html[] = '<span class="spacer"><span class="before"></span>';
                $html[] = '<span class="jmdesc"><label>'.JText::_($msg).'</label></span>';
                $html[] = '<span class="after"></span></span>';
                $html[] = '<div class="clear"></div>';*/
            }
            
            $options = $this->getOptions();
            $loadOptions = JHtml::_('select.genericlist', $options, 'name', '', 'value', 'text', 'default', 'jm_config_load_input');
            
            $html = array();
            
            // close control-group, etc. divs.
            $html[] = '</div></div>';
            
            $html[] = '<div id="jm_config">';
            
            $html[] = '<div id="jm_config_message" class="control-group">';
                $html[] = '<div class="control-label">';
                    $html[] = '<label id="'.$this->id.'-lbl">'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_SAVE_INFO').'</label>';
                $html[] = '</div>';
            $html[] = '</div>';
            
            $html[] = '<div id="jm_config_load" class="control-group">'; 
                $html[] = '<div class="control-label">';
                    $html[] = '<label class="hasTip" title="'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_LOAD_LABEL').'::'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_LOAD_DESC').'">'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_LOAD_LABEL').'</label>';
                $html[] = '</div>';
                $html[] = '<div class="controls">';
                    $html[] = $loadOptions.'<a href="#" class="button btn" id="jm_config_load_button"><i class="icon-upload"></i>&nbsp;&nbsp;'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_LOAD_BUTTON').'</a><a class="button btn" href="#" target="_blank" id="jm_config_download_button"><span class="hasTip" title="'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_DOWNLOAD_BUTTON').'::'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_DOWNLOAD_DESC').'"><i class="icon-download"></i>&nbsp;&nbsp;'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_DOWNLOAD_BUTTON').'</span></a>';
                $html[] = '</div>';
            $html[] = '</div>';
            
            $html[] = '<div id="jm_config_uploadload" class="control-group">'; 
                $html[] = '<div class="control-label">';
                    $html[] = '<label class="hasTip" title="'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_UPLOAD_SHORT_LABEL').'::'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_UPLOAD_DESC').'">'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_UPLOAD_LABEL').'</label>';
                $html[] = '</div>';
                $html[] = '<div class="controls">';
                    $html[] = '<input type="file" name="jm_config_upload_input" id="jm_config_upload_input" class="inputbox" /><a class="button btn" href="#" id="jm_config_upload_button"><i class="icon-upload"></i>&nbsp;&nbsp;'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_UPLOAD_BUTTON').'</a>';
                $html[] = '</div>';
            $html[] = '</div>';
            
            $html[] = '<div class="control-group">';
                $html[] = '<div class="control-label">';
                    $html[] = '<div id="jm_config_save" class="input-append">'; 
                        $html[] = '<label class="hasTip" title="'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_SAVE_LABEL').'::'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_SAVE_DESC').'">'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_SAVE_LABEL').'</label>';
                    $html[] = '</div>';
                $html[] = '</div>';
                $html[] = '<div class="controls">';
                    $html[] = '<div id="jm_config_save" class="input-append">'; 
                        $html[] = '<input value="'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_FILE_NAME').'" onfocus="if(this.value==\''.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_FILE_NAME').'\') {this.value=\'\';}" onblur="if(this.value==\'\') {this.value=\''.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_FILE_NAME').'\';}" type="text" id="jm_config_save_input" /><span class="add-on">.cfg.json</span><a class="button btn"  href="#" id="jm_config_save_button"><i class="icon-save"></i>&nbsp;&nbsp;'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_SAVE_BUTTON').'</a>';
                    $html[] = '</div>';
                $html[] = '</div>';
            $html[] = '</div>';
            
            $html[] = '<div class="control-group">';
            	$html[] = '<div class="control-label">';
            		$html[] = '<label class="hasTip" title="'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_STORAGE_LOCATION').'::'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_STORAGE_LOCATION_DESC').'">'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_STORAGE_LOCATION').'</label>';
            	$html[] = '</div>';
            	$html[] = '<div class="controls">';
            		$html[] = '<code class="jm_code">'.JMF_TPL_PATH.DS.'assets'.DS.'config'.'</code>';
            	$html[] = '</div>';
            $html[] = '</div>';
            
            $html[] = '</div>';
            
            // re-open control-group
            $html[] ='<div><div>';
            
            $uri = JURI::getInstance();
            $url = $uri->current();
            $query = $uri->getQuery(true);
            unset($query['jmconfig_action']);
            unset($query['jmconfig_file']);
            
            $script = array();
            $script[] = '<script type="text/javascript">';
            $script[] = '//<![CDATA['; 
            
            $script[] = 'window.addEvent("domready", function(){
                var jmCfgLoadButton = document.id("jm_config_load_button");
                var jmCfgDownloadButton = document.id("jm_config_download_button");
                var jmCfgLoadFile = document.id("jm_config_load_input");
                var jmCfgUploadFile = document.id("jm_config_upload_input");
                var jmCfgUploadButton = document.id("jm_config_upload_button");
                var jmCfgSaveButton = document.id("jm_config_save_button");
                var jmCfgSaveFile = document.id("jm_config_save_input");
                
                var jmCfgURL = "'.$url.'";
                var jmCfgURLParams = '.json_encode($query).';
                var jmCfgURLQuery = [];
                
                var i = 0;
                for (var key in jmCfgURLParams) {
                    jmCfgURLQuery[i] = key + "=" + jmCfgURLParams[key];
                    i++;
                }
                 
                jmCfgLoadButton.addEvent("click",function(evt){
                    evt.stop();
                    if (!jmCfgLoadFile || jmCfgLoadFile.value == "" || !jmCfgLoadFile.value) {
                        alert("'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_NO_FILE_TO_LOAD').'");
                        return false;
                    } 
                    jmCfgURLQuery.push("jmconfig_action=load");
                    jmCfgURLQuery.push("jmconfig_file=" + encodeURI(jmCfgLoadFile.value));
                    var url = jmCfgURL + "?" + jmCfgURLQuery.join("&");
                    window.location = url;
                });
                
                jmCfgSaveButton.addEvent("click",function(evt){
                    evt.stop();
                    jmCfgSaveFile.focus();
                    jmCfgURLQuery.push("jmconfig_action=save");
                    jmCfgURLQuery.push("jmconfig_file=" + encodeURI(jmCfgSaveFile.value));
                    var url = jmCfgURL + "?" + jmCfgURLQuery.join("&");
                    window.location = url;
                });
                
                jmCfgUploadButton.addEvent("click",function(evt){
                    evt.stop();
                    if (!jmCfgUploadFile || jmCfgUploadFile.value == "" || !jmCfgUploadFile.value) {
                        alert("'.JText::_('PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_NO_FILE_TO_LOAD').'");
                        return false;
                    } 
                    
                    
                    var reqURL = "'.JURI::root(false).'plugins/system/jmframework/includes/assets/admin/formfields/jmconfig/uploader.php";
                    
                    try {
                        var formdata = new FormData();
                    } catch (err) {
                        alert("Your browser does not support this feature. Use FTP to upload your config file manually");
                        return;
                    }
                    
                    formdata.append("jmconfig_file", jmCfgUploadFile.files[0]);
                    formdata.append("jmconfig_template", "'.$this->template_name.'");
                    
                    var filename = jmCfgUploadFile.value.replace(/.*(\/|\\\)/, "");
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", reqURL);
                    xhr.onreadystatechange = (function(){
                        if (xhr.readyState == 4) {
                            jmCfgURLQuery.push("jmconfig_action=load");
                            jmCfgURLQuery.push("jmconfig_file=" + encodeURI(filename));
                            var url = jmCfgURL + "?" + jmCfgURLQuery.join("&");
                            window.location = url;
                        }
                    });
                      
                    xhr.send(formdata);
                });
                
                jmCfgLoadFile.addEvent("change",function(evt){
                    jmCfgDownloadButton.setAttribute("href", "'.JMF_TPL_URL.'/assets/config/" +jmCfgLoadFile.value);
                    if (jmCfgLoadFile.value) {
                        jmCfgDownloadButton.setAttribute("href", "'.JMF_TPL_URL.'/assets/config/" +jmCfgLoadFile.value);
                        jmCfgDownloadButton.setStyle("display", "");
                    } else {
                        jmCfgDownloadButton.setStyle("display", "none");
                    }
                });
                
                if (jmCfgLoadFile.value) {
                    jmCfgDownloadButton.setAttribute("href", "'.JMF_TPL_URL.'/assets/config/" +jmCfgLoadFile.value);
                    jmCfgDownloadButton.setStyle("display", "");
                } else {
                    jmCfgDownloadButton.setStyle("display", "none");
                }
                
            });';
            
            $script[] = '//]]>';
            $script[] = '</script>';
            
            $script = implode(PHP_EOL, $script);
            $html = implode(PHP_EOL, $html);
            
            return $html.PHP_EOL.$script;
        }
    }
    
    /*protected function getLabel() {
        return '';
    }*/
    
    protected function getTitle()
    {
        return $this->getLabel();
    }
    
    protected function getOptions() {
        $options = array();
        if (defined('JMF_TPL_PATH')) {
	        $path = JMF_TPL_PATH.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'config';
	
	        $files = JFolder::files($path, '.json');
	        
	        $options[] = JHtml::_('select.option', '', '');
	        if (is_array($files)) {
	            foreach($files as $file) {
	                $options[] = JHtml::_('select.option', $file, $file);
	            }
	        }
        }

        return $options;
    }
    
    protected function performActions($action, $styleId, $file) {
        $db = JFactory::getDbo();
        if (!defined('JMF_TPL_PATH')) {
        	return false;
        }
         $path = JMF_TPL_PATH . DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'config';

         if ($action == 'save' || $action == 'load') {
            if ($action == 'load' && strlen($file) > 0) {
                if (JFile::exists($path.DIRECTORY_SEPARATOR.$file)) {
                    $settings = JFile::read($path.DIRECTORY_SEPARATOR.$file);
                    if ($settings) {
                        $db->setQuery('UPDATE #__template_styles SET params='.$db->quote($settings).' WHERE id='.(int)$styleId.'');
                        $result = $db->query();
                        if($result) {
                            return 'PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_LOAD_OK';
                        } else {
                            return 'PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_LOAD_FAIL';
                        }
                    }
                    else {
                        return 'PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_LOAD_FILE_FAIL';
                    }
                } else {
                    return 'PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_LOAD_MISSING_FILE';
                }
            } else if ($action == 'save') {
                if (!$file) {
                    $datenow = JFactory::getDate();
                    $file = $datenow->format('Y-m-d-H-i-s');
                }
                $file .= '.cfg.json';
                
                $lang = JFactory::getLanguage();

                $file = $lang->transliterate($file);
                $file = strtolower($file);
                $file = JFile::makeSafe($file);
                
                if(JFile::exists($path.DIRECTORY_SEPARATOR.$file)) {
                    return 'PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_SAVE_FILE_EXISTS';
                }
                
                $db->setQuery('SELECT params FROM #__template_styles WHERE id='.(int)$styleId.' LIMIT 1');
                $params = $db->loadResult();
                
                if(JFile::write($path.DIRECTORY_SEPARATOR.$file, $params)) {
                    return 'PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_SAVE_OK';
                } else {
                    return 'PLG_SYSTEM_JMFRAMEWORK_CONFIG_STORAGE_SAVE_FAIL';
                }
            }
        }

        return false;
    }
}
?>