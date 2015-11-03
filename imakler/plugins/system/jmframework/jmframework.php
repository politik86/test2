<?php
/**
 * @version $Id: jmframework.php 25 2013-12-10 12:42:15Z michal $
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

defined('_JEXEC') or die('Restricted access');

class plgSystemJMFramework extends JPlugin
{
    private $template;
    
    public function __construct(&$subject, $config = array()) {
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        parent::__construct($subject, $config);
    }
    
    /**
     * 
     * Enter description here ...
     * @param JForm $form
     * @param unknown $data
     */
    
    function onContentPrepareForm($form, $data)
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $this->template = $this->getTemplateName();
        
        if ($this->template && ( ($app->isAdmin() && $form->getName() == 'com_templates.style') || ($app->isSite() && ($form->getName() == 'com_config.templates' || $form->getName() == 'com_templates.style')) )) {
            jimport('joomla.filesystem.path');
            //JForm::addFormPath( dirname(__FILE__) . DS. 'includes' . DS .'assets' . DS . 'admin' . DS . 'params');
            $plg_file = JPath::find(dirname(__FILE__) . DS. 'includes' . DS .'assets' . DS . 'admin' . DS . 'params', 'template.xml');
            $tpl_file = JPath::find(JPATH_ROOT . DS. 'templates' . DS . $this->template, 'templateDetails.xml');
            
            if (!$plg_file) {
                return false;
            }
            if ($tpl_file) {
                $form->loadFile($plg_file, false, '//form');
                $form->loadFile($tpl_file, false, '//config');
            } else {
                $form->loadFile($plg_file, false, '//form');
            }
            
            if ($app->isSite()) {
                $jmstorage_fields = $form->getFieldset('jmstorage');
                foreach ($jmstorage_fields as $name => $field){
                    $form->removeField($name, 'params');
                }
                $form->removeField('config', 'params');
            }
            
            if ($app->isAdmin()) {
            	$doc->addStyleDeclaration('#jm-ef3plugin-info, .jm-row > .jm-notice {display: none !important;}');
            }
            
        }
    }
    
    function onAfterRoute(){
        $app = JFactory::getApplication();
        
        $template = $this->getTemplateName();
        if ($template) {
            define('JMF_FRAMEWORK_PATH', dirname(__FILE__));
            define('JMF_FRAMEWORK_URL', JURI::root(false).'plugins/system/jmframework');
            
            define('JMF_TPL', $template);
            define('JMF_TPL_PATH', JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template);
            define('JMF_TPL_URL', JURI::root(false). 'templates/' . $template);
            
            $this->loadLanguage();
            
            if ($app->isSite()) {
                require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'template.php';   
            } else {
                require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'admin.php';  
            }
            define('JMF_EXEC', 'JMF');
            $this->template = $template;    
        }
    }
    
    function onAfterRender() {
        $app = JFactory::getApplication();
        if ($app->isAdmin() && $this->template) {
            $this->loadLanguage('tpl_'.$this->template, JPATH_ROOT);
        }
    }
        
    function onBeforeRender(){
        $app = JFactory::getApplication();
        $template = $this->getTemplateName();
        if ($template && ($app->isAdmin() || ($app->input->get('option') == 'com_config' && $app->input->get('view') == 'templates' ) )) {
            define('JMF_TPL_ASSETS', JURI::root(false).'plugins/system/jmframework/includes/assets/admin/');
            
            $document = JFactory::getDocument();
            
            if ($app->isAdmin()) {
                $document->addStyleSheet(JMF_TPL_ASSETS . 'css/admin.css');
            }
            $document->addScript(JMF_TPL_ASSETS . 'js/jmoptiongroups.js');
            $document->addScript(JMF_TPL_ASSETS . 'js/jmspacer.js');
            //$document->addScript(JMF_TPL_ASSETS . 'js/jmconfig.js');
            $document->addScript(JMF_TPL_ASSETS . 'js/jscolor.js');
            $document->addScript(JMF_TPL_ASSETS . 'js/misc.js');
            
            //$document->addScript('http://code.jquery.com/jquery-latest.js');
        }
    }
    function getTemplateName() {
        $app = JFactory::getApplication();
        $template = false;
        if ($app->isSite()) {
            $template = $app->getTemplate(null);
        } else {
            $option = $app->input->get('option', null, 'string');
            $view = $app->input->get('view', null, 'string');
            $task = $app->input->get('task', '', 'string');
            $controller = current(explode('.',$task));
            $id = $app->input->get('id', null, 'int');
            if ($option == 'com_templates' && ($view == 'style' || $controller == 'style' || $task == 'apply' || $task == 'save' || $task == 'save2copy') && $id > 0) {
                $db = JFactory::getDbo();
                
                $query = $db->getQuery(true);
                
                $query->select('template');
                $query->from('#__template_styles');
                $query->where('id='.$id);
                
                $db->setQuery($query);
                $template = $db->loadResult();
            }
        }
        
        if ($template) {
            jimport('joomla.filesystem.file');
            $path = JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'templateDetails.xml';
            if (JFile::exists($path)) {
                $xml = JInstaller::parseXMLInstallFile($path);
                if (array_key_exists('group', $xml)){
                    if ($xml['group'] == 'jmf') {
                        return $template;
                    }   
                }
            }
        }
        
        return false;
    }
}
