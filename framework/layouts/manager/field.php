<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 * 	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/header/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

if ($field->type == 'astroidgroup') {
    return;
}
if ($field->type == 'layout' || $field->type == 'astroidheading' || $field->type == 'Hidden') {
    echo $field->input;
    return;
}
?>
<?php
$ngHide = Astroid\Helper::replaceRelationshipOperators($field->getAttribute('ngHide'));
$ngShow = Astroid\Helper::replaceRelationshipOperators($field->getAttribute('ngShow'));
$gclass = (string) $field->getAttribute('groupClass');

$input = trim(str_replace('ng-media-class', 'ng-class', $field->input));
if (empty($input)) {
    return;
}
?>
<div<?php echo !empty($ngHide) ? ' ng-hide="' . $ngHide . '"' : ''; ?><?php echo !empty($ngShow) ? ' ng-show="' . $ngShow . '"' : ''; ?> class="form-group<?php echo !empty($gclass) ? ' ' . $gclass : ''; ?>">
    <div class="row">
        <?php
        $label = $field->getAttribute('label');
        if ($label != 'false' && !empty($label)) { ?>
            <div class="col-sm-5">
                <label for="<?php echo $field->id; ?>" class="astroid-label"><?php echo strip_tags($field->label); ?></label>
                <?php if (!empty($field->getAttribute('description'))) { ?>
                    <div class="help-block">
                        <?php echo JText::_($field->getAttribute('description')); ?>
                    </div>
                <?php } ?>
            </div>
            <div class="col-sm-7" data-fieldset="astroid-tab-<?php echo $fieldset->name; ?>">
                <?php echo $input; ?>
            </div>
        <?php } else { ?>
            <div class="col-sm-12" data-fieldset="astroid-tab-<?php echo $fieldset->name; ?>">
                <?php echo $input; ?>
            </div>
            <div class="col-sm-12">
                <?php if (!empty($field->getAttribute('description'))) { ?>
                    <div class="help-block">
                        <?php echo JText::_($field->getAttribute('description')); ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    </div>