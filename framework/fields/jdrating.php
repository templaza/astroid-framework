<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

// The class name must always be the same as the filename (in camel case)
class JFormFieldJDRating extends FormField
{
    protected $type = 'JDRating';

    public function getInput()
    {
        $app = Factory::getApplication();
        $wa = $app->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle('astroid.content_rating', 'astroid/rating.min.css');

        $clear = $this->element['clear'];
        if (!empty($clear) && $clear) {
            $clear = true;
        } else {
            $clear = false;
        }

        $wa->registerAndUseScript('astroid.content_rating', 'astroid/rating.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);

        $script = '(function($){'
            . '$(function(){'
            . '$(".ui.rating").rating({'
            . 'onRate:function(value){'
            . '$(this).siblings("input[type=hidden]").val(value);'
            . '}'
            . ($clear ? ', clearable: true' : '')
            . '});';
        $script .= '$(document).on("subform-row-add", function(event){'
            . '$(event.detail.row).find(".ui.rating").rating({'
            . 'onRate:function(value){'
            . '$(this).siblings("input[type=hidden]").val(value);'
            . '}'
            . '});';
        $script .= '});'
            . '$(".btn-rating-clear").click(function(){'
            . '$(this).siblings(".ui.rating").rating("clear rating");'
            . '});'
            . '})'
            . '})(jQuery);';
        $wa->addInlineScript($script);

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

        return '<div id="' . $this->id . '" style="line-height: 30px;"><div class="' . implode(' ', $classes) . '" data-rating="' . $this->value . '" data-max-rating="' . $max . '"></div><input name="' . $this->name . '" type="hidden" value="' . $this->value . '" />' . ($clear ? '&nbsp;&nbsp;&nbsp;&nbsp; <a style="margin-top: -8px;" class="btn btn-outline-dark btn=sm btn-rating-clear" href="javascript:void(0);">' . Text::_('ASTROID_CLEAR') . '</a>' : '') . '</div>';
    }
}