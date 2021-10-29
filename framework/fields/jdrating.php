<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

// The class name must always be the same as the filename (in camel case)
class JFormFieldJDRating extends JFormField
{

   protected $type = 'JDRating';

   public function getInput()
   {
      $document = JFactory::getDocument();
      $document->addStylesheet(JURI::root() . 'media/astroid/jd/fields/rating.min.css');

      $clear = $this->element['clear'];
      if (!empty($clear) && $clear) {
         $clear = true;
      } else {
         $clear = false;
      }


      $document->addScript(JURI::root() . 'media/astroid/jd/fields/rating.min.js');
      $script = '(function($){'
         . '$(function(){'
         . '$(".ui.rating").rating({'
         . 'onRate:function(value){'
         . '$(this).siblings("input[type=hidden]").val(value);'
         . '}'
         . ($clear ? ', clearable: true' : '')
         . '});';
      if (ASTROID_JOOMLA_VERSION == 4) {
         $script .= '$(document).on("subform-row-add", function(event){'
            . '$(event.detail.row).find(".ui.rating").rating({'
            . 'onRate:function(value){'
            . '$(this).siblings("input[type=hidden]").val(value);'
            . '}'
            . '});';
      } else {
         $script .= '$(document).on("subform-row-add", function(event, row){'
            . '$(row).find(".ui.rating").rating({'
            . 'onRate:function(value){'
            . '$(this).siblings("input[type=hidden]").val(value);'
            . '}'
            . '});';
      }
      $script .= '});'
         . '$(".btn-rating-clear").click(function(){'
         . '$(this).siblings(".ui.rating").rating("clear rating");'
         . '});'
         . '})'
         . '})(jQuery);';
      $document->addScriptDeclaration($script);

      $rating_type = $this->element['rating-type'];
      $rating_size = $this->element['rating-size'];

      $max = $this->element['max'];

      $max = !empty($max) ? $max : 10;
      $classes = ['ui', 'rating'];
      if (!empty($rating_type)) {
         $classes[] = $rating_type;
      }
      if (!empty($rating_size)) {
         $classes[] = $rating_size;
      }
      if (!empty($this->class)) {
         $classes[] = $this->class;
      }


      return '<div id="' . $this->id . '" style="line-height: 30px;"><div class="' . implode(' ', $classes) . '" data-rating="' . $this->value . '" data-max-rating="' . $max . '"></div><input name="' . $this->name . '" type="hidden" value="' . $this->value . '" />' . ($clear ? '&nbsp;&nbsp;&nbsp;&nbsp; <a style="margin-top: -8px;" class="btn btn-outline-dark btn=sm btn-rating-clear" href="javascript:void(0);">' . JText::_('ASTROID_CLEAR') . '</a>' : '') . '</div>';
   }
}
