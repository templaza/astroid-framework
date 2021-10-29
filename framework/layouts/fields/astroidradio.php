<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
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
 * @var   array    $options         Options available for this field.
 */

$template = Astroid\Framework::getTemplate();
$imageRadio = false;
if (isset($images) && $images == 'true') {
   $imageRadio = true;
}

/**
 * The format of the input tag to be filled in using sprintf.
 *     %1 - id
 *     %2 - name
 *     %3 - value
 *     %4 = any other attributes
 */
$format = '<label class="btn-radio-astroid' . (!$imageRadio ? ' btn-round' : ' btn-image') . ' btn-wide btn" ng-class="{\'btn-white\':' . $fieldname . '!=\'%3$s\',\'%7$s\':' . $fieldname . '==\'%3$s\'}" for="%1$s" ng-class="%6$s"><input ng-model="' . $fieldname . '" ' . (!empty($ngRequired) ? ' ng-required="' . $ngRequired . '"' : '') . ' autocomplete="off" type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s />%5$s</label>';
$alt = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
?>
<div id="<?php echo $id; ?>" class="<?php echo $imageRadio ? '' : 'btn-group astroid-radio-btn'; ?> btn-group-toggle"
     <?php echo $disabled ? 'disabled' : ''; ?>
     <?php echo $required ? 'required aria-required="true"' : ''; ?>
     <?php echo $autofocus ? 'autofocus' : ''; ?> ng-radio-init="<?php echo $fieldname; ?>='<?php echo $value; ?>'">

   <?php if (!empty($options)) : ?>
      <?php foreach ($options as $i => $option) : ?>
         <?php
         // Initialize some option attributes.
         $checked = ((string) $option->value === $value) ? 'ng-checked="true"' : '';
         $optionClass = !empty($option->class) ? 'class="' . $option->class . '"' : '';
         $disabled = !empty($option->disable) || ($disabled && !$checked) ? 'disabled' : '';

         // Initialize some JavaScript option attributes.
         $onclick = !empty($option->onclick) ? 'onclick="' . $option->onclick . '"' : '';
         $onchange = !empty($option->onchange) ? 'onchange="' . $option->onchange . '"' : '';
         $oid = $id . $i;
         $ovalue = htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8');
         $attributes = array_filter(array($checked, $optionClass, $disabled, $onchange, $onclick));
         ?>

         <?php if ($required) : ?>
            <?php $attributes[] = 'required aria-required="true"'; ?>
         <?php endif; ?>
         <?php $ngClass = "{'active':$fieldname==" . (is_numeric($ovalue) ? $ovalue : "'$ovalue'") . "}"; ?>

         <?php
         $optiontext = $option->text;
         $optionclass = $option->class;
         $optionclass = empty($optionclass) ? 'btn-success' : $optionclass;
         if ($imageRadio) {
            $optionclass = 'btn-light';
            $optiontext = '<img ' . (!empty($imageWidth) ? 'width="' . $imageWidth . '"' : '') . ' src="' . JURI::root() . str_replace('TEMPLATE_NAME', $template->template, $optiontext) . '" />' . (!empty($option->label) ? '<span>' . $option->label . '</span>' : '');
         }
         ?>
         <?php echo sprintf($format, $oid, $name, $ovalue, implode(' ', $attributes), $optiontext, $ngClass, $optionclass); ?>
      <?php endforeach; ?>
   <?php endif; ?>
</div>