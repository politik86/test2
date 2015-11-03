<?php
/**
 * @version $Id: jmoptiongroups.php 21 2013-11-05 10:43:23Z michal $
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

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldJmoptiongroups extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Jmoptiongroups';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$app = JFactory::getApplication();
        
        $formControl = $this->formControl;
        if ($this->group) {
            $formControl .= (empty($formControl)) ? $this->group : '_'.$this->group;
        }
        
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('
			window.addEvent("domready",function(){
				var optionGroups_'.$this->id.' = new JMOptionGroups("'.$this->id.'", "'.$formControl.'", "'.$this->value.'");
			});
		');
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';


		// Get the field options.
		$options = (array) $this->getOptions();

		$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option)
		{

			// Only add <option /> elements.
			if ($option->getName() != 'group')
			{
				continue;
			}
            
            //$optionValue = array((string)$option['value']);
			$optionValue = (string)$option['value'];
            
            /*$optionChildren = $option->children();
            foreach($optionChildren as $child) {
                $optionValue[] = preg_replace('/[^a-zA-Z0-9_]/', '_',(string)$child['name']);
            }*/
            
			// Create a new option object based on the <option /> element.
			// $tmp = JHtml::_(
				// 'select.option', (string) $option['value'],
				// JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text',
				// ((string) $option['disabled'] == 'true')
			// );
			$tmp = JHtml::_(
                'select.option', $optionValue,
                JText::_((string)($option['label'])), 'value', 'text',
                ((string) $option['disabled'] == 'true')
            );

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
