<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;

JLoader::register('ModulesHelper', JPATH_ADMINISTRATOR . '/components/com_modules/helpers/modules.php');
JFormHelper::loadFieldClass('list');

/**
 * Modules Position field.
 *
 * @since  3.4.2
 */
class JFormFieldAstroidModulesPosition extends JFormFieldList
{

   /**
    * The form field type.
    *
    * @var    string
    * @since  3.4.2
    */
   protected $type = 'AstroidModulesPosition';

   /**
    * Method to get the field options.
    *
    * @return  array  The field option objects.
    *
    * @since   3.4.2
    */
   public function getOptions()
   {
      $clientId = JFactory::getApplication()->input->get('client_id', 0, 'int');
      $options = ModulesHelper::getPositions($clientId);
      $positions = Astroid\Helper::getPositions();
      //$options = array();
      $options = array_merge(parent::getOptions(), $options);

      $more_positions = [];
      foreach ($options as $option) {
         $more_positions[$option->value] = JText::_($option->text);
      }

      $positions = array_merge($more_positions, $positions);
      return array_unique($positions);
   }

   protected function getLayoutData()
   {
      $data = parent::getLayoutData();
      $extraData = array(
         'ngShow' => $this->element['ngShow'],
         'ngHide' => $this->element['ngHide'],
         'ngModel' => $this->element['ngModel'],
         'ngRequired' => $this->element['ngRequired'],
      );
      return array_merge($data, $extraData);
   }

   protected function getInput()
   {
      $html = array();
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
         $attr .= ' select-ui-addable';
      }

      // Get the field options.
      $options = (array) $this->getOptions();

      // Create a read-only list (no name) with hidden input(s) to store the value(s).
      if ((string) $this->readonly == '1' || (string) $this->readonly == 'true') {
         $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);

         // E.g. form field type tag sends $this->value as array
         if ($this->multiple && is_array($this->value)) {
            if (!count($this->value)) {
               $this->value[] = '';
            }

            foreach ($this->value as $value) {
               $html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
            }
         } else {
            $html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
         }
      } else {
         // Create a regular list.
         $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
      }

      return implode($html);
   }
}
