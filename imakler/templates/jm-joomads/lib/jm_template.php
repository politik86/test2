<?php

/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;

class JMTemplateBlank extends JMFTemplate {
    public function postSetUp() {
    	
        // get columns width
        $columnleft = $this->params->get('columnLeftWidth', '3');
        $columnright = $this->params->get('columnRightWidth', '3');

        $columncontent = null;
        if ((!$this->countModules('left-column')) && (!$this->countModules('right-column'))) {
            $columncontent = '12';
			$currentscheme = 'scheme1 nocolumn';
        } else if (($this->countModules('left-column')) && (!$this->countModules('right-column'))) {
            $columncontent = 12 - $columnleft;
			$currentscheme = 'scheme2 noright';
        } else if ((!$this->countModules('left-column')) && ($this->countModules('right-column'))) {
            $columncontent = 12 - $columnright;
			$currentscheme = 'scheme2 noleft';
        } else {
            $columncontent = 12 - $columnright - $columnleft;
			$currentscheme = 'scheme3';
        } 
		
        $this->params->set('columnContentWidth', $columncontent);
		$this->params->set('currentScheme', $currentscheme);
        
		// get grid sizes to calculate margins for left-content-right option
		
		$span_left = $this->params->get('columnLeftWidth', '3');
		$span_right = $this->params->get('columnRightWidth', '3');
		$span_content = $columncontent;
		
		/* grid layout */
        $columns = 12;
        
        /* fixed */
        $span_width = 52.25;
        $gutter = 30;
        $row_width = ($columns * $span_width) + ($gutter * ($columns - 1));
        
        /* 768px - 979px */
        $span_width_768 = 34.5;
        $gutter_768 = 30;
        $row_width_768 = ($columns * $span_width_768) + ($gutter_768 * ($columns - 1));
        
        /* 1200+ */
        $span_width_1200 = 74.25;
        $gutter_1200 = 30;
        $row_width_1200 = ($columns * $span_width_1200) + ($gutter_1200 * ($columns - 1));
        
        /* fluid */
        $span_width_fluid = $span_width / $row_width;
        $gutter_fluid = $gutter / $row_width;
        
        /* fluid 768px - 949px */
        $span_width_fluid_768 = $span_width_768 / $row_width_768;
        $gutter_fluid_768 = $gutter_768 / $row_width_768;
        
        /* fluid 1200+ */
        $span_width_fluid_1200 = $span_width_1200 / $row_width_1200;
        $gutter_fluid_1200 = $gutter_1200 / $row_width_1200;
        
        $grid_settings = array();
        
        $grid_settings['grid_left'] = $span_width * $span_left + ($span_left * $gutter);
        $grid_settings['grid_right'] = $span_width * $span_right + ($span_right * $gutter);
        $grid_settings['grid_content'] = $span_width * $span_content + ($span_content * $gutter);
        
        $grid_settings['grid_left_fl'] = ($span_width_fluid * $span_left + ($span_left * $gutter_fluid))*100;
        $grid_settings['grid_right_fl'] = ($span_width_fluid * $span_right + ($span_right * $gutter_fluid))*100;
        $grid_settings['grid_content_fl'] = ($span_width_fluid * $span_content + ($span_content * $gutter_fluid))*100;
        
        $grid_settings['grid_left_res'] = $span_width_768 * $span_left + ($span_left * $gutter_768);
        $grid_settings['grid_right_res'] = $span_width_768 * $span_right + ($span_right * $gutter_768);
        $grid_settings['grid_content_res'] = $span_width_768 * $span_content + ($span_content * $gutter_768);
        
        $grid_settings['grid_left_fl_res'] = ($span_width_fluid_768 * $span_left + ($span_left * $gutter_fluid_768))*100;
        $grid_settings['grid_right_fl_res'] = ($span_width_fluid_768 * $span_right + ($span_right * $gutter_fluid_768))*100;
        $grid_settings['grid_content_fl_res'] = ($span_width_fluid_768 * $span_content + ($span_content * $gutter_fluid_768))*100;
        
        $grid_settings['grid_left_1200'] = $span_width_1200 * $span_left + ($span_left * $gutter_1200);
        $grid_settings['grid_right_1200'] = $span_width_1200 * $span_right + ($span_right * $gutter_1200);
        $grid_settings['grid_content_1200'] = $span_width_1200 * $span_content + ($span_content * $gutter_1200);        
        
        $grid_settings['grid_left_fl_1200'] = ($span_width_fluid_1200 * $span_left + ($span_left * $gutter_fluid_1200))*100;
        $grid_settings['grid_right_fl_1200'] = ($span_width_fluid_1200 * $span_right + ($span_right * $gutter_fluid_1200))*100;
        $grid_settings['grid_content_fl_1200'] = ($span_width_fluid_1200 * $span_content + ($span_content * $gutter_fluid_1200))*100;
        
        $grid_settings['grid_gutter'] = $gutter;
        $grid_settings['grid_gutter_fl'] = $gutter_fluid*100;
        $grid_settings['grid_gutter_768'] = $gutter_768;
        $grid_settings['grid_gutter_1200'] = $gutter_1200;
        $grid_settings['grid_gutter_fl_res'] = $gutter_fluid_768*100;
        $grid_settings['grid_gutter_fl_1200'] = $gutter_fluid_1200*100;
        
        /* fixed column size */
        $grid_settings['grid_left_fixed'] = ($span_width * $span_left + ($span_left * $gutter)-$gutter);
        $grid_settings['grid_right_fixed'] = ($span_width * $span_right + ($span_right * $gutter)-$gutter);
        
        /* fixed column size for 768px - 979px */
        $grid_settings['grid_left_fixed_768'] = ($span_width_768 * $span_left + ($span_left * $gutter_768)-$gutter_768);
        $grid_settings['grid_right_fixed_768'] = ($span_width_768 * $span_right + ($span_right * $gutter_768)-$gutter_768);
        
        /* fixed column size for 1200px */
        $grid_settings['grid_left_fixed_1200'] = ($span_width_1200 * $span_left + ($span_left * $gutter_1200)-$gutter_1200);
        $grid_settings['grid_right_fixed_1200'] = ($span_width_1200 * $span_right + ($span_right * $gutter_1200)-$gutter_1200);
        
        foreach($grid_settings as $param => $value) {
            $this->params->set($param, $value);
        }
    }
}