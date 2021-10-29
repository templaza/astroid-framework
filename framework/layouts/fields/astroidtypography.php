<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;

extract($displayData);
$value = array_merge($defaults, $value);
$font_face = (string) $value['font_face'];
$alt_font_face = (string) $value['alt_font_face'];
$font_unit = (string) $value['font_unit'];
$font_size = (object) $value['font_size'];
$font_size_unit = (object) $value['font_size_unit'];
$font_color = (string) $value['font_color'];
$letter_spacing = (object) $value['letter_spacing'];
$letter_spacing_unit = (object) $value['letter_spacing_unit'];
$line_height = (object) $value['line_height'];
$line_height_unit = (object) $value['line_height_unit'];
$font_style = (array) $value['font_style'];
$font_weight = (string) $value['font_weight'];
$text_transform = (string) $value['text_transform'];
$unit_options = ['px', 'em', 'rem', 'pt'];
$media_types = ['desktop', 'tablet', 'mobile'];
?>
<div class="row">
   <div class="col-12">
      <div class="row astroid-typography-field-group">
         <div class="col-sm-4">
            <?php if ($fontpicker) { ?>
               <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_FONT_FAMILY_LABEL'); ?></label>

               <div data-preview="<?php echo $id; ?>-astroid-typography-preview" data-value="<?php echo $font_face; ?>" class="ui selection dropdown search optgroup astroid-font-selector form-control">
                  <input type="hidden" name="<?php echo $name; ?>[font_face]" ng-model="<?php echo $id; ?>_font_face" value="<?php echo $font_face; ?>" />
                  <div class="text"><?php echo JText::_('JGLOBAL_INHERIT'); ?></div>
                  <i class="dropdown icon"></i>
                  <div class="menu"></div>
               </div>

               <div class="clearfix mb-4"></div>
               <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_ALT_FONT_FAMILY_LABEL'); ?></label>

               <select data-placeholder="<?php echo JText::_('JGLOBAL_INHERIT'); ?>" name="<?php echo $name; ?>[alt_font_face]" ng-model="<?php echo $id; ?>_alt_font_face" ng-init="<?php echo $id; ?>_alt_font_face = '<?php echo $alt_font_face; ?>'" class="form-control" select-ui>
                  <?php foreach (Astroid\Helper\Font::$system_fonts as $s_font_value => $s_font_title) { ?>
                     <option value="<?php echo $s_font_value; ?>"><?php echo $s_font_title; ?></option>
                  <?php } ?>
               </select>


               <div class="clearfix mb-4"></div>
            <?php } ?>
            <?php if ($weightpicker) { ?>
               <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_FONT_WEIGHT_LABEL'); ?></label>
               <select data-typography-field="<?php echo $id; ?>" data-typography-property="font-weight" name="<?php echo $name; ?>[font_weight]" class="form-control" select-ui-div>
                  <option <?php echo ($font_weight == '' ? ' selected' : ''); ?> value=""><?php JText::_('JDEFAULT'); ?></option>
                  <?php
                  foreach (array(100, 200, 300, 400, 500, 600, 700, 800, 900) as $weight) {
                     echo '<option ' . ($font_weight == $weight ? ' selected' : '') . ' value="' . $weight . '">' . $weight . '</option>';
                  }
                  ?>
               </select>
            <?php } ?>
         </div>
         <div class="col-sm-4">
            <?php if ($sizepicker) { ?>
               <ul class="nav tabmedia" data-typography-tab role="tablist">
                  <li> <label class="astroid-label d-inline-block"><?php echo JText::_('TPL_ASTROID_FONT_SIZE_LABEL'); ?></label> </li>
                  <li>
                     <a class="active" href="javascript:void(0);" id="astroid-font-size-desktop-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-fontsize-desktop-<?php echo $id; ?>" role="tab" aria-controls="astroid-fontsize-desktop-<?php echo $id; ?>" aria-selected="true" data-typography-field-id="<?php echo $id; ?>" data-typography-tab-device="desktop"><i class="fas fa-desktop"></i></a>
                  </li>
                  <li>
                     <a href="javascript:void(0);" id="astroid-font-size-tablet-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-fontsize-tablet-<?php echo $id; ?>" role="tab" aria-controls="astroid-fontsize-tablet-<?php echo $id; ?>" aria-selected="true" data-typography-field-id="<?php echo $id; ?>" data-typography-tab-device="tablet"><i class="fas fa-tablet-alt"></i></a>
                  </li>
                  <li>
                     <a href="javascript:void(0);" id="astroid-font-size-mobile-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-fontsize-mobile-<?php echo $id; ?>" role="tab" aria-controls="astroid-fontsize-mobile-<?php echo $id; ?>" aria-selected="true" data-typography-field-id="<?php echo $id; ?>" data-typography-tab-device="mobile"><i class="fas fa-mobile-alt"></i></a>
                  </li>
               </ul>
               <div class="tab-content" id="astroid-font-size-tab-content">
                  <?php foreach ($media_types as $k => $mtype) { ?>
                     <div class="tab-pane <?php echo (($k + 1) == 1) ? 'show active' : ''; ?>" id="astroid-fontsize-<?php echo $mtype; ?>-<?php echo $id; ?>" role="tabpanel" aria-labelledby="astroid-font-size-<?php echo $mtype; ?>-<?php echo $id; ?>">
                        <div class="mb-4 position-relative">
                           <span class="range-slider-value d-none"></span>
                           <div class="d-inline-block margin-left-75px">
                              <ul class="list-inline unit-picker mb-0">
                                 <?php foreach ($unit_options as $unit_option) { ?>
                                    <li class="list-inline-item"><label><input <?php echo (isset($font_size_unit->$mtype)  && $unit_option == $font_size_unit->$mtype) ? 'checked' : ''; ?> value="<?php echo $unit_option; ?>" type="radio" name="<?php echo $name; ?>[font_size_unit][<?php echo $mtype ?>]" data-sid="<?php echo $id; ?>_font_size_<?php echo $mtype; ?>" /><span><?php echo $unit_option; ?></span></label></li>
                                 <?php } ?>
                              </ul>
                           </div>

                           <div class="clearfix"></div>
                           <input data-typography-field="<?php echo $id; ?>" data-typography-property="font-size" data-typography-tab-device="<?php echo $mtype ?>" name="<?php echo $name; ?>[font_size][<?php echo $mtype ?>]" data-slider-min="0" data-slider-step="0.001" data-unit="<?php echo (isset($font_size_unit->$mtype)) ? $font_size_unit->$mtype : ''; ?>" data-slider-max="100" data-prefix="" data-postfix="" type="number" data-slider-value="<?php echo (isset($font_size->$mtype)) ? $font_size->$mtype : 0; ?>" id="<?php echo $id; ?>_font_size_<?php echo $mtype ?>" data-slider-id="<?php echo $id; ?>_font_size_<?php echo $mtype ?>" range-slider ng-model="<?php echo $id; ?>_font_size_<?php echo $mtype ?>">
                        </div>
                     </div>
                  <?php } ?>
               </div>
               <div class="clearfix"></div>
            <?php } ?>

            <?php if ($letterspacingpicker) { ?>
               <ul class="nav tabmedia" data-typography-tab role="tablist">
                  <li><label class="astroid-label d-inline-block"><?php echo JText::_('TPL_ASTROID_LETTER_SPACING_LABEL'); ?></label></li>
                  <li>
                     <a class="active" href="javascript:void(0);" id="astroid-letter-spacing-desktop-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-letterspacing-desktop-<?php echo $id; ?>" role="tab" aria-controls="astroid-letterspacing-desktop-<?php echo $id; ?>" aria-selected="true" data-typography-field-id="<?php echo $id; ?>" data-typography-tab-device="desktop"><i class="fas fa-desktop"></i></a>
                  </li>
                  <li>
                     <a href="javascript:void(0);" id="astroid-font-size-tablet-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-letterspacing-tablet-<?php echo $id; ?>" role="tab" aria-controls="astroid-letterspacing-tablet-<?php echo $id; ?>" aria-selected="true" data-typography-field-id="<?php echo $id; ?>" data-typography-tab-device="tablet"><i class="fas fa-tablet-alt"></i></a>
                  </li>
                  <li>
                     <a href="javascript:void(0);" id="astroid-font-size-mobile-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-letterspacing-mobile-<?php echo $id; ?>" role="tab" aria-controls="astroid-letterspacing-mobile-<?php echo $id; ?>" aria-selected="true" data-typography-field-id="<?php echo $id; ?>" data-typography-tab-device="mobile"><i class="fas fa-mobile-alt"></i></a>
                  </li>
               </ul>
               <div class="tab-content" id="astroid-font-size-tab-content">
                  <?php foreach ($media_types as $k => $mtype) { ?>
                     <div class="tab-pane <?php echo (($k + 1) == 1) ? 'show active' : ''; ?>" id="astroid-letterspacing-<?php echo $mtype; ?>-<?php echo $id; ?>" role="tabpanel" aria-labelledby="astroid-letter-spacing-<?php echo $mtype; ?>-<?php echo $id; ?>">
                        <div class="mb-4 position-relative">
                           <div class="d-inline-block margin-left-75px">
                              <ul class="list-inline unit-picker mb-0">
                                 <?php foreach ($unit_options as $unit_option) { ?>
                                    <li class="list-inline-item"><label><input <?php echo (isset($letter_spacing_unit->$mtype) && $unit_option == $letter_spacing_unit->$mtype) ? 'checked' : ''; ?> data-sid="<?php echo $id; ?>_letter_spacing_<?php echo $mtype; ?>" value="<?php echo $unit_option; ?>" type="radio" name="<?php echo $name; ?>[letter_spacing_unit][<?php echo $mtype; ?>]" /><span><?php echo $unit_option; ?></span></label></li>
                                 <?php } ?>
                              </ul>
                           </div>
                           <div class="clearfix"></div>
                           <input data-typography-field="<?php echo $id; ?>" data-typography-property="letter-spacing" data-typography-tab-device="<?php echo $mtype ?>" name="<?php echo $name; ?>[letter_spacing][<?php echo $mtype; ?>]" data-slider-min="0" data-slider-step="0.001" data-slider-max="100" data-prefix="" data-postfix="" data-unit="<?php echo (isset($letter_spacing_unit->$mtype)) ? $letter_spacing_unit->$mtype : 0; ?>" type="number" data-slider-value="<?php echo (isset($letter_spacing->$mtype)) ? $letter_spacing->$mtype : 0; ?>" id="<?php echo $id; ?>_letter_spacing_<?php echo $mtype; ?>" data-slider-id="<?php echo $id; ?>_letter_spacing_<?php echo $mtype; ?>" range-slider ng-model="<?php echo $id; ?>_letter_spacing_<?php echo $mtype; ?>">
                        </div>
                     </div>
                  <?php } ?>
               </div>
               <div class="clearfix"></div>
            <?php } ?>
            <?php if ($lineheightpicker) { ?>
               <ul class="nav tabmedia" data-typography-tab role="tablist">
                  <li><label class="astroid-label"><?php echo JText::_('TPL_ASTROID_LINE_HEIGHT_LABEL'); ?></label></li>
                  <li>
                     <a class="active" href="javascript:void(0);" id="astroid-line-height-desktop-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-lineheight-desktop-<?php echo $id; ?>" role="tab" aria-controls="astroid-lineheight-desktop-<?php echo $id; ?>" aria-selected="true" data-typography-field-id="<?php echo $id; ?>" data-typography-tab-device="desktop"><i class="fas fa-desktop"></i></a>
                  </li>
                  <li>
                     <a href="javascript:void(0);" id="astroid-line-height-tablet-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-lineheight-tablet-<?php echo $id; ?>" role="tab" aria-controls="astroid-lineheight-tablet-<?php echo $id; ?>" aria-selected="true" data-typography-field-id="<?php echo $id; ?>" data-typography-tab-device="tablet"><i class="fas fa-tablet-alt"></i></a>
                  </li>
                  <li>
                     <a href="javascript:void(0);" id="astroid-line-height-mobile-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-lineheight-mobile-<?php echo $id; ?>" role="tab" aria-controls="astroid-lineheight-mobile-<?php echo $id; ?>" aria-selected="true" data-typography-field-id="<?php echo $id; ?>" data-typography-tab-device="mobile"><i class="fas fa-mobile-alt"></i></a>
                  </li>
               </ul>
               <div class="tab-content" id="astroid-line-height-tab-content">
                  <?php foreach ($media_types as $k => $mtype) { ?>
                     <div class="tab-pane <?php echo (($k + 1) == 1) ? 'show active' : ''; ?>" id="astroid-lineheight-<?php echo $mtype; ?>-<?php echo $id; ?>" role="tabpanel" aria-labelledby="astroid-line-height-<?php echo $mtype; ?>-<?php echo $id; ?>">
                        <div class="mb-4 position-relative">
                           <div class="d-inline-block margin-left-75px">
                              <ul class="list-inline unit-picker mb-0">
                                 <?php foreach ($unit_options as $unit_option) { ?>
                                    <li class="list-inline-item"><label><input <?php echo (isset($line_height_unit->$mtype) && $unit_option == $line_height_unit->$mtype) ? 'checked' : ''; ?> value="<?php echo $unit_option; ?>" type="radio" name="<?php echo $name; ?>[line_height_unit][<?php echo $mtype; ?>]" data-sid="<?php echo $id; ?>_line_height_<?php echo $mtype; ?>" /><span><?php echo $unit_option; ?></span></label></li>
                                 <?php } ?>
                              </ul>
                           </div>
                           <div class="clearfix"></div>
                           <input data-typography-field="<?php echo $id; ?>" data-typography-property="line-height" data-typography-tab-device="<?php echo $mtype ?>" name="<?php echo $name; ?>[line_height][<?php echo $mtype; ?>]" data-slider-min="0" data-slider-step="0.001" data-slider-max="100" data-prefix="" data-postfix="" data-unit="<?php echo (isset($line_height_unit->$mtype)) ? $line_height_unit->$mtype : 0; ?>" type="number" data-slider-value="<?php echo (isset($line_height->$mtype)) ? $line_height->$mtype : 0; ?>" id="<?php echo $id; ?>_line_height_<?php echo $mtype; ?>" data-slider-id="<?php echo $id; ?>_line_height_<?php echo $mtype; ?>" range-slider ng-model="<?php echo $id; ?>_line_height_<?php echo $mtype; ?>">
                        </div>
                     </div>
                  <?php } ?>
               </div>
            <?php } ?>
         </div>
         <div class="col-sm-4">
            <?php if ($colorpicker) { ?>
               <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_FONT_COLOR_LABEL'); ?></label>
               <input color-picker data-typography-field="<?php echo $id; ?>" data-typography-property="color" type="text" name="<?php echo $name; ?>[font_color]" id="<?php echo $id; ?>_font_color" ng-init="<?php echo $id; ?>_font_color='<?php echo $font_color; ?>'" ng-model="<?php echo $id; ?>_font_color" data-value="<?php echo $font_color; ?>" value="<?php echo $font_color; ?>" class="form-control astroid-color-picker" />
               <div class="clearfix mb-1"></div>
            <?php } ?>
            <?php if ($stylepicker) { ?>
               <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_FONT_STYLE_LABEL'); ?></label>
               <fieldset class="astroid-font-style-selector checkboxes">
                  <?php foreach (array('italic', 'underline') as $style) { ?>
                     <label for="<?php echo $id; ?>_font_style_<?php echo $style; ?>" class="checkbox">
                        <input data-typography-field="<?php echo $id; ?>" data-typography-property="font-style" type="checkbox" id="<?php echo $id; ?>_font_style_<?php echo $style; ?>" name="<?php echo $name; ?>[font_style][]" value="<?php echo $style; ?>" <?php echo in_array($style, $font_style) ? ' checked' : ''; ?> /><span class="fas fa-<?php echo $style; ?>"></span></label>
                  <?php } ?>
               </fieldset>
               <div class="clearfix"></div>
            <?php } ?>
            <?php if ($transformpicker) { ?>
               <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_TEXT_TRANSFORM_LABEL'); ?></label>
               <select data-typography-field="<?php echo $id; ?>" data-typography-property="text-transform" name="<?php echo $name; ?>[text_transform]" class="form-control" select-ui-div>
                  <option <?php echo ($text_transform == '' ? ' selected="selected"' : ''); ?> value="none"><?php echo JText::_('ASTROID_NONE'); ?></option>
                  <?php
                  foreach (array('uppercase' => 'JGLOBAL_UPPERCASE', 'lowercase' => 'JGLOBAL_LOWERCASE', 'capitalize' => 'JGLOBAL_CAPITALIZE') as $transform => $transform_title) {
                     echo '<option ' . ($text_transform == $transform ? ' selected="selected"' : '') . ' value="' . $transform . '">' . JText::_($transform_title) . '</option>';
                  }
                  ?>
               </select>
            <?php } ?>
         </div>
      </div>
   </div>
</div>
<br />
<div class="row">
   <div class="col-12">
      <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_OPTIONS_PREVIEW_LABEL'); ?></label>
      <small class="library-font-warning text-danger d-none">* <?php echo JText::_('TPL_ASTROID_OPTIONS_LIBRARY_FONT_WARNING'); ?></small>
      <small class="default-font-warning text-danger d-none">* <?php echo JText::_('TPL_ASTROID_OPTIONS_DEFAULT_FONT_WARNING'); ?></small>
      <?php
      $alphas = range('A', 'Z');

      $speciman = '';
      foreach ($alphas as $alpha) {
         $speciman .= '<span>' . $alpha . strtolower($alpha) . '</span>';
      }
      $speciman .= '<div class="clearfix"></div>';
      for ($i = 0; $i <= 9; $i++) {
         $speciman .= '<span>' . $i . '</span>';
      }
      ?>
      <div class="astroid-typography-preview-container">
         <div class="astroid-typography-preview <?php echo $id; ?>-astroid-typography-preview">
            <?php echo $speciman; ?>
         </div>
         {{ <?php echo $id; ?>_font_style}}
      </div>
   </div>
</div>