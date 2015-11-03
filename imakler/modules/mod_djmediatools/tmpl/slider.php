<?php
/**
 * @version $Id: slider.php 10 2013-05-20 14:47:45Z szymon $
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

// no direct access
defined('_JEXEC') or die ('Restricted access'); ?>

<div style="border: 0px !important;">
	<div id="djslider-loader<?php echo $mid; ?>" class="djslider-loader">
	
	<?php if($params->get('show_custom_nav') && $params->get('custom_nav_pos')=='above') { ?>
		<div id="cust-navigation<?php echo $mid; ?>" class="navigation-container-custom">
			<div class="cust-navigation-in">
			<?php $i = 0; foreach ($slides as $slide) { 
				?><span class="load-button<?php if ($i == 0) echo ' load-button-active'; ?>"><?php //echo ($i+1) ?></span><?php 
				if(count($slides) == $i + $params->get('visible_images')) break; else $i++; } ?>
			</div>
        </div>
    <?php } ?>
	
    <div id="djslider<?php echo $mid; ?>" class="djslider">
        <div id="slider-container<?php echo $mid; ?>" class="slider-container">
        	<ul id="slider<?php echo $mid; ?>">
          		<?php foreach ($slides as $slide) { ?>
          			<li>
          				<?php $image = '<img src="'.$slide->resized_image.'" alt="'.$slide->alt.'" class="dj-image" />'; ?>
            			<?php if (($slide->link && $params->get('link_image',1)==1) || $params->get('link_image',1) > 1) { ?>
							<?php require JModuleHelper::getLayoutPath('mod_djmediatools', 'slideshow_imagelink'); ?>
						<?php } else { ?>
							<?php echo $image; ?>
						<?php } ?>
						
						<div class="dj-slide-desc">
							<?php require JModuleHelper::getLayoutPath('mod_djmediatools', 'slideshow_description'); ?>
						</div>
						
					</li>
                <?php } ?>
        	</ul>
        </div>
        <?php if($params->get('show_arrows') || $params->get('show_buttons')) { ?>
        <div id="navigation<?php echo $mid; ?>" class="navigation-container">
        	<?php if($params->get('show_arrows')) { ?>
        		<img id="prev<?php echo $mid; ?>" class="prev-button" src="<?php echo $navigation->prev; ?>" alt="<?php echo JText::_('Previous'); ?>" />
				<img id="next<?php echo $mid; ?>" class="next-button" src="<?php echo $navigation->next; ?>" alt="<?php echo JText::_('Next'); ?>" />
			<?php } ?>
			<?php if($params->get('show_buttons')) { ?>
				<img id="play<?php echo $mid; ?>" class="play-button" src="<?php echo $navigation->play; ?>" alt="<?php echo JText::_('Play'); ?>" />
				<img id="pause<?php echo $mid; ?>" class="pause-button" src="<?php echo $navigation->pause; ?>" alt="<?php echo JText::_('Pause'); ?>" />
			<?php } ?>
        </div>
        <?php } ?>
        <?php if($params->get('show_custom_nav') && ($params->get('custom_nav_pos')=='topin' || $params->get('custom_nav_pos')=='bottomin')) { ?>
		<div id="cust-navigation<?php echo $mid; ?>" class="navigation-container-custom">
			<div class="cust-navigation-in">
			<?php $i = 0; foreach ($slides as $slide) { 
				?><span class="load-button<?php if ($i == 0) echo ' load-button-active'; ?>"><?php //echo ($i+1) ?></span><?php 
				if(count($slides) == $i + $params->get('visible_images')) break; else $i++; } ?>
			</div>
        </div>
        <?php } ?>
    </div>
    
    <?php if($params->get('show_custom_nav') && $params->get('custom_nav_pos')=='below') { ?>
    <div id="cust-navigation<?php echo $mid; ?>" class="navigation-container-custom">
			<div class="cust-navigation-in">
			<?php $i = 0; foreach ($slides as $slide) { 
				?><span class="load-button<?php if ($i == 0) echo ' load-button-active'; ?>"><?php //echo ($i+1) ?></span><?php 
				if(count($slides) == $i + $params->get('visible_images')) break; else $i++; } ?>
			</div>
        </div>
    <?php } ?>
    
	</div>
</div>
<div style="clear: both"></div>