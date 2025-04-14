<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAstroidList extends FormField {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'AstroidList';
    protected $ordering;

   /**
    * Method to get the field input markup for a generic list.
    * Use the multiple attribute to enable multiselect.
    *
    * @return  string  The field input markup.
    *
    * @since   3.7.0
    */
   protected function getInput() {
//      $html = array();
      $attr = '';

      // Initialize some field attributes.
      $attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
      $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
      $attr .= $this->multiple ? ' multiple' : '';
      $attr .= $this->required ? ' required aria-required="true"' : '';
      $attr .= $this->autofocus ? ' autofocus' : '';
      $attr .= ' ng-model="' . $this->fieldname . '"';
      $attr .= ' data-fieldname="' . $this->fieldname . '"';
      $attr .= $this->element['ngRequired'] ? ' ng-required="' . Astroid\Helper::replaceRelationshipOperators($this->element['ngRequired']) . '"' : '';
      if (isset($this->element['astroid-content-layout']) && !empty($this->element['astroid-content-layout'])) {
         $attr .= ' data-astroid-content-layout="' . $this->element['astroid-content-layout'] . '"';
      }

      if (isset($this->element['astroid-content-layout-load']) && !empty($this->element['astroid-content-layout-load'])) {
         $attr .= ' data-astroid-content-layout-load="' . $this->element['astroid-content-layout-load'] . '"';
      }

      // To avoid user's confusion, readonly="true" should imply disabled="true".
      if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1' || (string) $this->disabled == 'true') {
         $attr .= ' disabled="disabled"';
      }

      // Initialize JavaScript field attributes.
      $attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

      if (isset($this->element['astroid-animation-selector']) && $this->element['astroid-animation-selector'] == true) {
         $attr .= ' animation-selector';
      } else {
         $attr .= ' select-ui';
      }

      // Get the field options.
      $options = (array) $this->getOptions();

      $json =   [
          'id'      =>  $this->id,
          'name'    =>  $this->name,
          'value'   =>  htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
          'options' =>  $options,
          'astroid_content_layout'    =>  (string) $this->element['astroid-content-layout'],
          'astroid_content_layout_load'    =>  (string) $this->element['astroid-content-layout-load'],
          'dynamic' => isset($this->element['dynamic']) && (bool)$this->element['dynamic'],
          'attr'    =>  trim($attr),
          'type'    =>  strtolower($this->type),
      ];
      return json_encode($json);
   }

   /**
    * Method to get the field options.
    *
    * @return  array  The field option objects.
    *
    * @since   3.7.0
    */
   protected function getOptions() {

      if (!empty($this->element['bootstrap-color'])) {
         $options = array();
         $variables = Astroid\Helper\Constants::$bootstrap_colors;
         foreach ($variables as $value => $label) {
            $object = new stdClass();
            $object->value = $value;
            $object->text = Text::_($label);
            $options[] = $object;
         }
         return $options;
      }

      $fieldname = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);
      $options = array();

      foreach ($this->element->xpath('option') as $option) {
         // Filter requirements
         if ($requires = explode(',', (string) $option['requires'])) {
            // Requires multilanguage
            if (in_array('multilanguage', $requires) && !Multilanguage::isEnabled()) {
               continue;
            }

            // Requires associations
            if (in_array('associations', $requires) && !Associations::isEnabled()) {
               continue;
            }

            // Requires adminlanguage
            if (in_array('adminlanguage', $requires) && !ModuleHelper::isAdminMultilang()) {
               continue;
            }

            // Requires vote plugin
            if (in_array('vote', $requires) && !PluginHelper::isEnabled('content', 'vote')) {
               continue;
            }
         }

         $value = (string) $option['value'];
         $text = trim((string) $option) != '' ? trim((string) $option) : $value;

         $disabled = (string) $option['disabled'];
         $disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');
         $disabled = $disabled || ($this->readonly && $value != $this->value);

         $checked = (string) $option['checked'];
         $checked = ($checked == 'true' || $checked == 'checked' || $checked == '1');

         $label = (string) $option['label'];

         $selected = (string) $option['selected'];
         $selected = ($selected == 'true' || $selected == 'selected' || $selected == '1');

         $tmp = array(
             'value' => $value,
             'text' => Text::alt($text, $fieldname),
             'disable' => $disabled,
             'class' => (string) $option['class'],
             'selected' => ($checked || $selected),
             'checked' => ($checked || $selected),
             'label' => Text::_($label),
         );

         // Set some event handler attributes. But really, should be using unobtrusive js.
         $tmp['onclick'] = (string) $option['onclick'];
         $tmp['onchange'] = (string) $option['onchange'];

         // Add the option object to the result set.
         $options[] = (object) $tmp;
      }

      if ($this->element['useglobal']) {
         $tmp = new stdClass;
         $tmp->value = '';
         $tmp->text = Text::_('JGLOBAL_USE_GLOBAL');
         $component = Factory::getApplication()->input->getCmd('option');

         // Get correct component for menu items
         if ($component == 'com_menus') {
            $link = $this->form->getData()->get('link');
            $uri = new Uri($link);
            $component = $uri->getVar('option', 'com_menus');
         }

         $params = ComponentHelper::getParams($component);
         $value = $params->get($this->fieldname);

         // Try with global configuration
         if (is_null($value)) {
            $value = Factory::getConfig()->get($this->fieldname);
         }

         // Try with menu configuration
         if (is_null($value) && Factory::getApplication()->input->getCmd('option') == 'com_menus') {
            $value = ComponentHelper::getParams('com_menus')->get($this->fieldname);
         }

         if (!is_null($value)) {
            $value = (string) $value;

            foreach ($options as $option) {
               if ($option->value === $value) {
                  $value = $option->text;

                  break;
               }
            }

            $tmp->text = Text::sprintf('JGLOBAL_USE_GLOBAL_VALUE', $value);
         }

         array_unshift($options, $tmp);
      }

      reset($options);

      return $options;
   }

   /**
    * Method to add an option to the list field.
    *
    * @param   string  $text        Text/Language variable of the option.
    * @param   array   $attributes  Array of attributes ('name' => 'value' format)
    *
    * @return  JFormFieldList  For chaining.
    *
    * @since   3.7.0
    */
   public function addOption($text, $attributes = array()) {
      if ($text && $this->element instanceof SimpleXMLElement) {
         $child = $this->element->addChild('option', $text);

         foreach ($attributes as $name => $value) {
            $child->addAttribute($name, $value);
         }
      }

      return $this;
   }

   /**
    * Method to get certain otherwise inaccessible properties from the form field object.
    *
    * @param   string  $name  The property name for which to get the value.
    *
    * @return  mixed  The property value or null.
    *
    * @since   3.7.0
    */
   public function __get($name) {
      if ($name == 'options') {
         return $this->getOptions();
      }

      return parent::__get($name);
   }

}
