<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAstroidHeading extends JFormField
{

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidheading';

   /**
    * Method to get the field input markup for a generic list.
    * Use the multiple attribute to enable multiselect.
    *
    * @return  string  The field input markup.
    *
    * @since   3.7.0
    */
   protected function getInput()
   {

      $attrs = [];
      $ngShow = Astroid\Helper::replaceRelationshipOperators($this->element['ngShow']);
      if (!empty($ngShow)) {
         $attrs[] = 'ng-show="' . $ngShow . '"';
      }
      $ngHide = Astroid\Helper::replaceRelationshipOperators($this->element['ngHide']);
      if (!empty($ngHide)) {
         $attrs[] = 'ng-hide="' . $ngHide . '"';
      }
      $ngRequired = Astroid\Helper::replaceRelationshipOperators($this->element['ngRequired']);
      if (!empty($ngRequired)) {
         $attrs[] = 'ng-hide="' . $ngRequired . '"';
      }

      $helpLink = '';
      if (!empty($this->element['help'])) {
         $helpLink = ' <a target="_blank" href="' . $this->element['help'] . '"><span class="far fa-question-circle"></span></a>';
      }

      return "<div " . implode(' ', $attrs) . " class='form-group form-group-heading'><h3 class='mb-0'>" . ((!empty($this->element['icon']) ? "<i class='" . $this->element['icon'] . "'></i> " : "")) . JText::_($this->element['title']) . $helpLink . "</h3><p class='mb-0'>" . JText::_($this->description) . "</p></div>";
   }
}
