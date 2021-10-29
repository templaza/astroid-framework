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
class JFormFieldAstroidpresets extends JFormField
{

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidpresets';

   /**
    * Method to get the field input markup for a generic list.
    * Use the multiple attribute to enable multiselect.
    *
    * @return  string  The field input markup.
    *
    * @since   3.7.0
    */
   public function __get($name)
   {
      return parent::__get($name);
   }

   public function __set($name, $value)
   {
      parent::__set($name, $value);
   }

   protected function getInput()
   {
      $temp = Astroid\Framework::getTemplate();
      $presets = $temp->getPresets();
      if (!count($presets)) {
         return false;
      }
      $html = [];
      $html[] = '<div astroidpresets class="astroid-presets">';
      foreach ($presets as $preset) {
         $html[] = '<div class="astroid-presets-option astroid-presets-option-' . $preset['name'] . '" ng-click="chosePreset(\'' . $preset['name'] . '\')">';
         if (empty($preset['thumbnail'])) {
            $html[] = '<div>';
            foreach ($preset['colors'] as $color) {
               $html[] = '<span style="background-color: ' . $color . '"></span>';
            }
            $html[] = '</div>';
         } else {
            $html[] = '<img src="' . $preset['thumbnail'] . '" width="100%" />';
         }
         $html[] = '<span>' . $preset['title'] . '</span></div>';
      }
      $html[] = '</div>';
      return implode('', $html);
   }
}
