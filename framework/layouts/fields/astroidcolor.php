<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 * @var   array    $inputType       Options available for this field.
 * @var   string   $accept          File types that are accepted.
 */

$list = '';

$autocomplete   =   !$autocomplete ? ' autocomplete="off"' : ' autocomplete="' . $autocomplete . '"';
$autocomplete   =   $autocomplete === ' autocomplete="on"' ? '' : $autocomplete;
$plugin_params  =   Astroid\Helper::getPluginParams();
$color_mode     =   $plugin_params->get('astroid_color_mode_enable', 0);

$attributes = array(
    !empty($class) ? 'class="' . $class . '"' : '',
    !empty($size) ? 'size="' . $size . '"' : '',
    $disabled ? 'disabled' : '',
    $readonly ? 'readonly' : '',
    $list,
    strlen($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : '',
    $onchange ? ' onchange="' . $onchange . '"' : '',
    !empty($maxLength) ? $maxLength : '',
    $required ? 'required aria-required="true"' : '',
    $autocomplete,
    $autofocus ? ' autofocus' : '',
    $spellcheck ? '' : 'spellcheck="false"',
    !empty($inputmode) ? 'inputmode="' . $inputmode . '"' : '',
    !empty($pattern) ? 'pattern="' . $pattern . '"' : '',
    !empty($ngRequired) ? 'ng-required="' . $ngRequired . '"' : '',
);
$id_dark    =   $id_light   =   '';
if (!empty($id)) {
    $id_light   =   ' id="' . $id . '_light" data-id="'.$id.'" data-type="light"';
    $id_dark    =   ' id="' . $id . '_dark" data-id="'.$id.'" data-type="dark"';
}
$id = (empty($id) ? '' : ' id="' . $id . '"');
$value_light    =   $value_dark =   $value;
if (!empty($value)) {
    $result = json_decode($value);
    if (json_last_error() === JSON_ERROR_NONE) {
        $value_light    =   $result->light;
        $value_dark     =   $result->dark;
        if (!$color_mode) {
            $value      =   $value_light;
        }
    } else {
        if ($color_mode) {
            $value          =   json_encode(['light'=>$value_light, 'dark'=>$value_dark]);
        }
    }
}
?>
<?php if ($color_mode) : ?>
<div class="row astroid-color" style="max-width: 210px;">
    <div class="col-4">
        <input type="text"
               class="color-light"
            <?php echo $id_light; ?>
               color-selector
            <?php echo implode(' ', $attributes); ?> />
        <label class="form-label"><?php echo JText::_('TPL_ASTROID_BASIC_COLOR_MODE_OPTION_LIGHT')?></label>
    </div>
    <div class="col text-center py-3">
        <i class="fa-solid fa-arrows-left-right"></i>
    </div>
    <div class="col-4">
        <input type="text"
               class="color-dark"
            <?php echo $id_dark; ?>
               color-selector
            <?php echo implode(' ', $attributes); ?> />
        <label class="form-label"><?php echo JText::_('TPL_ASTROID_BASIC_COLOR_MODE_OPTION_DARK')?></label>
    </div>
    <textarea class="d-none" astroidcolor ng-model="<?php echo $fieldname; ?>" name="<?php echo $name; ?>"><?php echo $value; ?></textarea>
</div>
<?php else : ?>
    <input type="text" name="<?php echo $name; ?>"<?php echo $id; ?>
           value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>"
           color-picker
           ng-model="<?php echo $fieldname; ?>"
        <?php echo implode(' ', $attributes); ?> />
<?php endif; ?>
