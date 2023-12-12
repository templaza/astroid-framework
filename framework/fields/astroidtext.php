<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormField;
/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @link   http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since  11.1
 */
class JFormFieldAstroidtext extends FormField {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidtext';
    protected $ordering;

   /**
    * The allowable maxlength of the field.
    *
    * @var    integer
    * @since  3.2
    */
   protected $maxLength;

   /**
    * The mode of input associated with the field.
    *
    * @var    mixed
    * @since  3.2
    */
   protected $inputmode;

   /**
    * The name of the form field direction (ltr or rtl).
    *
    * @var    string
    * @since  3.2
    */
   protected $dirname;

   /**
    * Name of the layout being used to render the field
    *
    * @var    string
    * @since  3.7
    */
   protected $layout = 'fields.text';

   /**
    * Method to get certain otherwise inaccessible properties from the form field object.
    *
    * @param   string  $name  The property name for which to get the value.
    *
    * @return  mixed  The property value or null.
    *
    * @since   3.2
    */
   public function __get($name) {
      switch ($name) {
         case 'maxLength':
         case 'dirname':
         case 'inputmode':
            return $this->$name;
      }

      return parent::__get($name);
   }

   /**
    * Method to set certain otherwise inaccessible properties of the form field object.
    *
    * @param   string  $name   The property name for which to set the value.
    * @param   mixed   $value  The value of the property.
    *
    * @return  void
    *
    * @since   3.2
    */
   public function __set($name, $value) {
      switch ($name) {
         case 'maxLength':
            $this->maxLength = (int) $value;
            break;

         case 'dirname':
            $value = (string) $value;
            $this->dirname = ($value == $name || $value == 'true' || $value == '1');
            break;

         case 'inputmode':
            $this->inputmode = (string) $value;
            break;

         default:
            parent::__set($name, $value);
      }
   }

   /**
    * Method to attach a JForm object to the field.
    *
    * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
    * @param   mixed             $value    The form field value to validate.
    * @param   string            $group    The field name group control value. This acts as an array container for the field.
    *                                      For example if the field has name="foo" and the group value is set to "bar" then the
    *                                      full field name would end up being "bar[foo]".
    *
    * @return  boolean  True on success.
    *
    * @see     JFormField::setup()
    * @since   3.2
    */
   public function setup(SimpleXMLElement $element, $value, $group = null) {
      $result = parent::setup($element, $value, $group);

      if ($result == true) {
         $inputmode = (string) $this->element['inputmode'];
         $dirname = (string) $this->element['dirname'];

         $this->inputmode = '';
         $inputmode = preg_replace('/\s+/', ' ', trim($inputmode));
         $inputmode = explode(' ', $inputmode);

         if (!empty($inputmode)) {
            $defaultInputmode = in_array('default', $inputmode) ? JText::_('JLIB_FORM_INPUTMODE') . ' ' : '';

            foreach (array_keys($inputmode, 'default') as $key) {
               unset($inputmode[$key]);
            }

            $this->inputmode = $defaultInputmode . implode(' ', $inputmode);
         }

         // Set the dirname.
         $dirname = ((string) $dirname == 'dirname' || $dirname == 'true' || $dirname == '1');
         $this->dirname = $dirname ? $this->getName($this->fieldname . '_dir') : false;

         $this->maxLength = (int) $this->element['maxlength'];
      }

      return $result;
   }

   /**
    * Method to get the field input markup.
    *
    * @return  string  The field input markup.
    *
    * @since   11.1
    */
   protected function getInput() {
       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  $this->value,
           'hint'    =>  $this->hint,
           'type'    =>  strtolower($this->type),
       ];

       return json_encode($json);
   }

   /**
    * Method to get the field options.
    *
    * @return  array  The field option objects.
    *
    * @since   3.4
    */
   protected function getOptions() {
      $options = array();

      foreach ($this->element->children() as $option) {
         // Only add <option /> elements.
         if ($option->getName() != 'option') {
            continue;
         }

         // Create a new option object based on the <option /> element.
         $options[] = JHtml::_(
                         'select.option', (string) $option['value'], JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text'
         );
      }

      return $options;
   }

   /**
    * Method to get the field suggestions.
    *
    * @return  array  The field option objects.
    *
    * @since       3.2
    * @deprecated  4.0  Use getOptions instead
    */
   protected function getSuggestions() {
      return $this->getOptions();
   }

   /**
    * Method to get the data to be passed to the layout for rendering.
    *
    * @return  array
    *
    * @since 3.7
    */
   protected function getLayoutData() {
      $data = parent::getLayoutData();

      // Initialize some field attributes.
      $maxLength = !empty($this->maxLength) ? ' maxlength="' . $this->maxLength . '"' : '';
      $inputmode = !empty($this->inputmode) ? ' inputmode="' . $this->inputmode . '"' : '';
      $dirname = !empty($this->dirname) ? ' dirname="' . $this->dirname . '"' : '';

      /* Get the field options for the datalist.
        Note: getSuggestions() is deprecated and will be changed to getOptions() with 4.0. */
      $options = (array) $this->getSuggestions();

      $extraData = array(
          'maxLength' => $maxLength,
          'pattern' => $this->pattern,
          'inputmode' => $inputmode,
          'dirname' => $dirname,
          'options' => $options,
          'ngShow' => Astroid\Helper::replaceRelationshipOperators($this->element['ngShow']),
          'ngHide' => Astroid\Helper::replaceRelationshipOperators($this->element['ngHide']),
          'ngModel' => $this->element['ngModel'],
          'ngRequired' => Astroid\Helper::replaceRelationshipOperators($this->element['ngRequired']),
          'isSwitch' => $this->element['astroid-switch'] == true ? 1 : 0,
      );

      return array_merge($data, $extraData);
   }

}
