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
class JFormFieldRadio extends JFormFieldList {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'Radio';

   /**
    * Name of the layout being used to render the field
    *
    * @var    string
    * @since  3.5
    */
   protected $layout = 'joomla.form.field.radio';

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

      return $this->getRenderer($this->layout)->render($this->getLayoutData());
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
          'ngShow' => $this->element['ngShow'],
          'ngHide' => $this->element['ngHide'],
          'ngModel' => $this->element['ngModel'],
          'ngRequired' => $this->element['ngRequired'],
      );

      return array_merge($data, $extraData);
   }

}
