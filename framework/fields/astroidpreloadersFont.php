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
class JFormFieldAstroidpreloadersFont extends JFormField {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidpreloadersFont';

   /**
    * Method to get the field input markup for a generic list.
    * Use the multiple attribute to enable multiselect.
    *
    * @return  string  The field input markup.
    *
    * @since   3.7.0
    */
   protected function getInput() {
      $selected = Astroid\Helper\Constants::$preloadersFont['spinner'];
      if (empty($this->value)) {
         $this->value = $selected['name'];
      }
      $html = '<div class="astroid-preloader-field d-inline-block">';
      $html .= '<span class="astroid-preloader-field-select"></span>';
      $html .= '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';

      $html .= '<div class="astroid-preloaders-selector">';
      $html .= '<div class="overlay"></div>';
      $html .= '<div class="head">Select Preloader Style<span class="astroid-preloaders-selector-exit-fs"><i class="fas fa-times"></i></span></div>';
      $html .= '<div class="body">';
      $html .= '<div class="">';

      foreach (Astroid\Helper\Constants::$preloadersFont as $preloader) {
         $html .= '<div class="astroid-preloader-select" data-value="' . $preloader['name'] . '"><div class="astroid-preloader-select-inner">';
         $html .= $preloader['code'];
         $html .= '</div></div>';
         if ($this->value == $preloader['name']) {
            $selected = $preloader;
         }
      }
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '<div class="select-preloader">';
      $html .= $selected['code'];
      $html .= '</div>';
      $html .= '<div class="clearfix"></div></div>';

      return $html;
   }

}
