<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

/**
 * Form Field class for the Joomla Platform.
 *
 * Provides a pop up date picker linked to a button.
 * Optionally may be filtered to use user's or server's time zone.
 *
 * @since  11.1
 */
class JFormFieldAstroidCalendar extends FormField {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidcalendar';

   /**
    * The allowable maxlength of calendar field.
    *
    * @var    integer
    * @since  3.2
    */
   protected $maxlength;

   /**
    * The format of date and time.
    *
    * @var    integer
    * @since  3.2
    */
   protected $format;

   /**
    * The filter.
    *
    * @var    integer
    * @since  3.2
    */
   protected $filter;

   /**
    * The minimum year number to subtract/add from the current year
    *
    * @var    integer
    * @since  3.7.0
    */
   protected $minyear;

   /**
    * The maximum year number to subtract/add from the current year
    *
    * @var    integer
    * @since  3.7.0
    */
   protected $maxyear;
   protected $todaybutton;
   protected $weeknumbers;
   protected $showtime;
   protected $filltable;
   protected $timeformat;
   protected $singleheader;

   /**
    * Name of the layout being used to render the field
    *
    * @var    string
    * @since  3.7.0
    */
   protected $layout = 'fields.astroidcalendar';

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
         case 'maxlength':
         case 'format':
         case 'filter':
         case 'timeformat':
         case 'todaybutton':
         case 'singleheader':
         case 'weeknumbers':
         case 'showtime':
         case 'filltable':
         case 'minyear':
         case 'maxyear':
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
         case 'maxlength':
         case 'timeformat':
            $this->$name = (int) $value;
            break;
         case 'todaybutton':
         case 'singleheader':
         case 'weeknumbers':
         case 'showtime':
         case 'filltable':
         case 'format':
         case 'filter':
         case 'minyear':
         case 'maxyear':
            $this->$name = (string) $value;
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
      $return = parent::setup($element, $value, $group);

      if ($return) {
         $this->maxlength = (int) $this->element['maxlength'] ? (int) $this->element['maxlength'] : 45;
         $this->format = (string) $this->element['format'] ? (string) $this->element['format'] : '%Y-%m-%d';
         $this->filter = (string) $this->element['filter'] ? (string) $this->element['filter'] : 'USER_UTC';
         $this->todaybutton = (string) $this->element['todaybutton'] ? (string) $this->element['todaybutton'] : 'true';
         $this->weeknumbers = (string) $this->element['weeknumbers'] ? (string) $this->element['weeknumbers'] : 'true';
         $this->showtime = (string) $this->element['showtime'] ? (string) $this->element['showtime'] : 'false';
         $this->filltable = (string) $this->element['filltable'] ? (string) $this->element['filltable'] : 'true';
         $this->timeformat = (int) $this->element['timeformat'] ? (int) $this->element['timeformat'] : 24;
         $this->singleheader = (string) $this->element['singleheader'] ? (string) $this->element['singleheader'] : 'false';
         $this->minyear = (string) $this->element['minyear'] ? (string) $this->element['minyear'] : null;
         $this->maxyear = (string) $this->element['maxyear'] ? (string) $this->element['maxyear'] : null;

         if ($this->maxyear < 0 || $this->minyear > 0) {
            $this->todaybutton = 'false';
         }
      }

      return $return;
   }

   /**
    * Method to get the field input markup.
    *
    * @return  string  The field input markup.
    *
    * @since   11.1
    */
   protected function getInput() {
      return $this->getLayoutData();
   }

   /**
    * Method to get the data to be passed to the layout for rendering.
    *
    * @return  array
    *
    * @since  3.7.0
    */
   protected function getLayoutData() {
      $data = parent::getLayoutData();
      $direction = strtolower(Factory::getDocument()->getDirection());

      $extraData = array(
          'value' => $this->value,
          'type'    =>  strtolower($this->type),
          'maxLength' => $this->maxlength,
          'format' => $this->format,
          'filter' => $this->filter,
          'todaybutton' => ($this->todaybutton === 'true') ? 1 : 0,
          'weeknumbers' => ($this->weeknumbers === 'true') ? 1 : 0,
          'showtime' => ($this->showtime === 'true') ? 1 : 0,
          'filltable' => ($this->filltable === 'true') ? 1 : 0,
          'timeformat' => $this->timeformat,
          'singleheader' => ($this->singleheader === 'true') ? 1 : 0,
          'minYear' => $this->minyear,
          'maxYear' => $this->maxyear,
          'direction' => $direction,
          'fieldname' => $this->fieldname,
          'ngShow' => $this->element['ngShow'],
          'ngHide' => $this->element['ngHide'],
          'ngRequired' => $this->element['ngRequired'],
      );

      return json_encode(array_merge($data, $extraData));
   }

}