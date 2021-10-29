<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Provides radio button inputs
 *
 * @link   http://www.w3.org/TR/html-markup/command.radio.html#command.radio
 * @since  11.1
 */
class JFormFieldAstroidRadio extends JFormFieldList {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'AstroidRadio';

   /**
    * Name of the layout being used to render the field
    *
    * @var    string
    * @since  3.5
    */
   protected $layout = 'fields.astroidradio';

   /**
    * Method to get the radio button field input markup.
    *
    * @return  string  The field input markup.
    *
    * @since   11.1
    */
   protected function getInput() {
      if (empty($this->layout)) {
         throw new UnexpectedValueException(sprintf('%s has no layout assigned.', $this->name));
      }

      $renderer = new JLayoutFile($this->layout, JPATH_LIBRARIES . '/astroid/framework/layouts');

      return $renderer->render($this->getLayoutData());
   }

   /**
    * Method to get the data to be passed to the layout for rendering.
    *
    * @return  array
    *
    * @since   3.5
    */
   protected function getLayoutData() {
      $data = parent::getLayoutData();

      $extraData = array(
          'options' => $this->getOptions(),
          'value' => (string) $this->value,
          'fieldname' => $this->fieldname,
          'ngShow' => Astroid\Helper::replaceRelationshipOperators($this->element['ngShow']),
          'ngHide' => Astroid\Helper::replaceRelationshipOperators($this->element['ngHide']),
          'ngRequired' => Astroid\Helper::replaceRelationshipOperators($this->element['ngRequired']),
          'images' => $this->element['radio-image'],
          'imageWidth' => $this->element['image-width'],
      );

      $data = array_merge($data, $extraData);
      return $data;
   }

   protected function getOptions() {
      // Ordering is disabled by default for backward compatibility
      $order = false;

      // Set default order direction
      $order_dir = 'asc';

      // Set default value for case sensitive sorting
      $order_case_sensitive = false;

      if ($this->element['order'] && $this->element['order'] !== 'false') {
         $order = $this->element['order'];
      }

      if ($this->element['order_dir']) {
         $order_dir = $this->element['order_dir'];
      }

      if ($this->element['order_case_sensitive']) {
         // Override default setting when the form element value is 'true'
         if ($this->element['order_case_sensitive'] == 'true') {
            $order_case_sensitive = true;
         }
      }

      // Create a $sortOptions array in order to apply sorting
      $i = 0;
      $sortOptions = array();

      foreach ($this->element->children() as $option) {
         $name = JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname));

         $sortOptions[$i] = new stdClass;
         $sortOptions[$i]->option = $option;
         $sortOptions[$i]->value = $option['value'];
         $sortOptions[$i]->name = $name;
         $i++;
      }

      // Only order if it's set
      if ($order) {
         jimport('joomla.utilities.arrayhelper');
         FOFUtilsArray::sortObjects($sortOptions, $order, $order_dir == 'asc' ? 1 : -1, $order_case_sensitive, false);
      }

      // Initialise the options
      $options = array();

      // Get the field $options
      foreach ($sortOptions as $sortOption) {
         $option = $sortOption->option;
         $name = $sortOption->name;

         // Only add <option /> elements.
         if ($option->getName() != 'option') {
            continue;
         }

         $tmp = JHtml::_('select.option', (string) $option['value'], $name, 'value', 'text', ((string) $option['disabled'] == 'true'));

         // Set some option attributes.
         $tmp->class = (string) $option['class'];
         $label = (string) $option['label'];
         $tmp->label = JText::_($label);
         // Set some JavaScript option attributes.
         $tmp->onclick = (string) $option['onclick'];

         // Add the option object to the result set.
         $options[] = $tmp;
      }

      // Do we have a class and method source for our options?
      $source_file = empty($this->element['source_file']) ? '' : (string) $this->element['source_file'];
      $source_class = empty($this->element['source_class']) ? '' : (string) $this->element['source_class'];
      $source_method = empty($this->element['source_method']) ? '' : (string) $this->element['source_method'];
      $source_key = empty($this->element['source_key']) ? '*' : (string) $this->element['source_key'];
      $source_value = empty($this->element['source_value']) ? '*' : (string) $this->element['source_value'];
      $source_translate = empty($this->element['source_translate']) ? 'true' : (string) $this->element['source_translate'];
      $source_translate = in_array(strtolower($source_translate), array('true', 'yes', '1', 'on')) ? true : false;
      $source_format = empty($this->element['source_format']) ? '' : (string) $this->element['source_format'];

      if ($source_class && $source_method) {
         // Maybe we have to load a file?
         if (!empty($source_file)) {
            $source_file = FOFTemplateUtils::parsePath($source_file, true);

            if (FOFPlatform::getInstance()->getIntegrationObject('filesystem')->fileExists($source_file)) {
               include_once $source_file;
            }
         }

         // Make sure the class exists
         if (class_exists($source_class, true)) {
            // ...and so does the option
            if (in_array($source_method, get_class_methods($source_class))) {
               // Get the data from the class
               if ($source_format == 'optionsobject') {
                  $options = array_merge($options, $source_class::$source_method());
               } else {
                  // Get the data from the class
                  $source_data = $source_class::$source_method();

                  // Loop through the data and prime the $options array
                  foreach ($source_data as $k => $v) {
                     $key = (empty($source_key) || ($source_key == '*')) ? $k : $v[$source_key];
                     $value = (empty($source_value) || ($source_value == '*')) ? $v : $v[$source_value];

                     if ($source_translate) {
                        $value = JText::_($value);
                     }

                     $options[] = JHtml::_('select.option', $key, $value, 'value', 'text');
                  }
               }
            }
         }
      }

      reset($options);

      return $options;
   }

}
