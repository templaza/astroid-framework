<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
extract($displayData);
?>
<div class="astroid-gradient">
   <textarea class="d-none" astroidgradient ng-model="<?php echo $fieldname; ?>" name="<?php echo $name; ?>"><?php echo $value; ?></textarea>
   <div class="gradient-preview-container"><div class="gradient-preview"></div></div>
   <div class="gradient-toolbar">
      <div class="gradient-type">
         <label><input type="radio" class="gradient-type" value="linear" name="<?php echo $name; ?>[gradient_type]" /><img src="<?php echo JURI::root(); ?>media/astroid/assets/images/linear-gradient.png" /></label>
         <label><input type="radio" class="gradient-type" value="radial" name="<?php echo $name; ?>[gradient_type]" /><img src="<?php echo JURI::root(); ?>media/astroid/assets/images/radial-gradient.png" /></label>
      </div>
      <div class="gradient-colors">
         <div class=""><input class="start-color" type="text" name="<?php echo $name; ?>[start_color]" color-selector /></div>
         <div class=""><input name="<?php echo $name; ?>[stop_color]" class="stop-color" type="text" color-selector /></div>
      </div>
   </div>
    <div class="gradient-toolbar">
        <input name="<?php echo $name; ?>[gradient_angle]" type="text" class="form-control gradient-angle" placeholder="<?php echo JText::_('TPL_ASTROID_GRADIENT_ANGLE') ?>">
        <select name="<?php echo $name; ?>[gradient_radial_position]" class="form-select gradient-radial-position">
            <option value="at center top">Top Center</option>
            <option value="at left top">Top Left</option>
            <option value="at right top">Top Right</option>
            <option value="at center center">Center Center</option>
            <option value="at left center">Left Center</option>
            <option value="at right center">Right Center</option>
            <option value="at center bottom">Bottom Center</option>
            <option value="at left bottom">Bottom Left</option>
            <option value="at right bottom">Bottom Right</option>
        </select>
    </div>
</div>