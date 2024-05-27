<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Astroid\Helper\Style;
use Astroid\Helper;

extract($displayData);

$mainframe      =   Factory::getApplication();

$form_elements  = $params->get('form_elements', '');
if (empty($form_elements)) {
    return false;
}
$form_elements  = json_decode($form_elements);
if (empty($form_elements) || !count($form_elements)) {
    return false;
}

$row_column_cls     =   '';
$responsive_key     =   ['xxl', 'xl', 'lg', 'md', 'sm', 'xs'];
foreach ($responsive_key as $key) {
    if ($key !== 'xs') {
        $row_gutter         =   $params->get('row_gutter_'.$key, '');
        $column_gutter      =   $params->get('column_gutter_'. $key, '');
        if ($row_gutter) {
            $row_column_cls .=  ' gy-' . $key . '-' . $row_gutter;
        }
        if ($column_gutter) {
            $row_column_cls .=  ' gx-' . $key . '-' . $column_gutter;
        }
    } else {
        $row_gutter         =   $params->get('row_gutter', 3);
        $column_gutter      =   $params->get('column_gutter', 3);
        $row_column_cls     .=  ' gy-' . $row_gutter;
        $row_column_cls     .=  ' gx-' . $column_gutter;
    }
}

$show_label     =   $params->get('show_label', 1);

echo '<form class="as-form-builder mt-4" method="post" action="'.Uri::root().'index.php?option=com_ajax&astroid=ajax_widget">';
echo '<div class="row'.$row_column_cls.'">';
foreach ($form_elements as $key => $form_element) :
    $form_builder_item    =   Style::getSubFormParams($form_element->params);
    //Options
    $column     =   [];
    $column[]   =   (isset($form_builder_item['column_lg']) && $form_builder_item['column_lg']) ? 'col-lg-' . 12/$form_builder_item['column_lg'] : 'col-lg-6';
    $column[]   =   (isset($form_builder_item['column_md']) && $form_builder_item['column_md']) ? 'col-md-' . 12/$form_builder_item['column_md'] : 'col-md-6';
    $column[]   =   (isset($form_builder_item['column_sm']) && $form_builder_item['column_sm']) ? 'col-sm-' . 12/$form_builder_item['column_sm'] : 'col-sm-12';
    $column[]   =   (isset($form_builder_item['column']) && $form_builder_item['column']) ? 'col-' . 12/$form_builder_item['column'] : 'col-12';

    echo '<div class="'.implode(' ', $column).'">';
    if ($show_label && $form_builder_item['field_label']) {
        echo '<label class="form-label uk-form-label" for="as-form-builder-'.$form_builder_item['field_name'].'">'.$form_builder_item['field_label'].($form_builder_item['field_required'] == 1 ? ' <span class="text-danger">*</span>' : '').'</label>';
    }
    $required   =   $form_builder_item['field_required'] == 1 ? ' required' : '';
    switch ($form_builder_item['type']) {
        case 'text':
            echo '<input id="as-form-builder-'.$form_builder_item['field_name'].'" class="w-100 form-control" type="text" name="as-form-builder-['.$form_builder_item['field_name'].']" placeholder="'.$form_builder_item['field_placeholder'].'"'.$required.' />';
            break;
        case 'email':
            echo '<input id="as-form-builder-'.$form_builder_item['field_name'].'" class="w-100 form-control" type="email" name="as-form-builder-['.$form_builder_item['field_name'].']" placeholder="'.$form_builder_item['field_placeholder'].'"'.$required.' />';
            break;
        case 'phone':
            echo '<input id="as-form-builder-'.$form_builder_item['field_name'].'" class="w-100 form-control" type="phone" name="as-form-builder-['.$form_builder_item['field_name'].']" placeholder="'.$form_builder_item['field_placeholder'].'"'.$required.' />';
            break;
        case 'textarea':
            echo '<textarea id="as-form-builder-'.$form_builder_item['field_name'].'" class="w-100 form-control" name="as-form-builder-['.$form_builder_item['field_name'].']" placeholder="'.$form_builder_item['field_placeholder'].'" rows="5"'.$required.'></textarea>';
            break;
        case 'select':
            if (!empty($form_builder_item['field_options'])) {
                $field_options = json_decode($form_builder_item['field_options'], true);
                echo '<select id="as-form-builder-'.$form_builder_item['field_name'].'" class="w-100 form-select" name="as-form-builder-['.$form_builder_item['field_name'].']"'.$required.'>';
                foreach ($field_options as $key_opt => $field_option) {
                    $field_option_params  =   Helper::loadParams($field_option['params']);
                    $selected   =   $field_option_params->get('opt_selected', 0) == 1 ? ' selected' : '';
                    echo '<option value="'.$field_option_params->get('opt_value', '').'"'.$selected.'>'.$field_option_params->get('opt_text', '').'</option>';
                }
                echo '</select>';
            }
            break;
        case 'checkbox':
            if (!empty($form_builder_item['field_options'])) {
                $field_options = json_decode($form_builder_item['field_options'], true);
                foreach ($field_options as $key_opt => $field_option) {
                    $field_option_params  =   Helper::loadParams($field_option['params']);
                    $checked   =   $field_option_params->get('opt_selected', 0) == 1 ? ' checked' : '';
                    echo '<div class="form-check">';
                    echo '<input class="form-check-input" type="checkbox" value="'.$field_option_params->get('opt_value', '').'" name="as-form-builder-['.$form_builder_item['field_name'].']" id="as-form-builder-checkbox-'.$form_builder_item['field_name'].'-'.$key_opt.'"'.$checked.'>';
                    echo '<label class="form-check-label" for="as-form-builder-checkbox-'.$form_builder_item['field_name'].'-'.$key_opt.'">'.$field_option_params->get('opt_text', '').'</label>';
                    echo '</div>';
                }
            }
            break;
        case 'radio':
            if (!empty($form_builder_item['field_options'])) {
                $field_options = json_decode($form_builder_item['field_options'], true);
                foreach ($field_options as $key_opt => $field_option) {
                    $field_option_params  =   Helper::loadParams($field_option['params']);
                    $checked   =   $field_option_params->get('opt_selected', 0) == 1 ? ' checked' : '';
                    echo '<div class="form-check">';
                    echo '<input class="form-check-input" type="radio" value="'.$field_option_params->get('opt_value', '').'" name="as-form-builder-['.$form_builder_item['field_name'].']" id="as-form-builder-checkbox-'.$form_builder_item['field_name'].'-'.$key_opt.'"'.$checked.'>';
                    echo '<label class="form-check-label" for="as-form-builder-checkbox-'.$form_builder_item['field_name'].'-'.$key_opt.'">'.$field_option_params->get('opt_text', '').'</label>';
                    echo '</div>';
                }
            }
            break;
        case 'date':
            echo '<input id="as-form-builder-'.$form_builder_item['field_name'].'" class="w-100 form-control" type="date" name="as-form-builder-['.$form_builder_item['field_name'].']"'.$required.' />';
            break;
        case 'range':
            echo '<input id="as-form-builder-'.$form_builder_item['field_name'].'" class="w-100 form-range" type="range" name="as-form-builder-['.$form_builder_item['field_name'].']" min="'.$form_builder_item['field_min'].'" max="'.$form_builder_item['field_max'].'" step="'.$form_builder_item['field_step'].'"'.$required.' />';
            break;
        case 'number':
            echo '<input id="as-form-builder-'.$form_builder_item['field_name'].'" class="w-100 form-control" type="number" name="as-form-builder-['.$form_builder_item['field_name'].']" min="'.$form_builder_item['field_min'].'" max="'.$form_builder_item['field_max'].'" step="'.$form_builder_item['field_step'].'"'.$required.' />';
            break;
    }
    echo '</div>';
endforeach;
echo '</div>';

if ($params->get('enable_captcha', 0) == 1) :
    $captcha_type = $params->get('captcha_type', 'default');
    echo '<div class="mt-2">';
    if ($captcha_type == 'recaptcha') {
        PluginHelper::importPlugin('captcha', 'recaptcha');
        $recaptcha = Factory::getApplication()->triggerEvent('onDisplay', array(null, 'as_form_builder_recaptcha' , 'as-form-builder-recaptcha'));
        echo (isset($recaptcha[0])) ? $recaptcha[0] : '<p class="uk-alert-danger">' . Text::_('ASTROID_RECAPTCHA_NOT_INSTALLED') . '</p>';
    } elseif ($captcha_type == 'invisible-recaptcha') {
        PluginHelper::importPlugin('captcha', 'recaptcha_invisible');
        $recaptcha = Factory::getApplication()->triggerEvent('onDisplay', array(null, 'as_form_builder_invisible_recaptcha' , 'as-form-builder-invisible-recaptcha'));
        echo (isset($recaptcha[0])) ? $recaptcha[0] : '<p class="uk-alert-danger">' . Text::_('ASTROID_RECAPTCHA_NOT_INSTALLED') . '</p>';
    } else {
        echo Helper::loadCaptcha('as-formbuilder-captcha');
    }
    echo '<input type="hidden" name="captcha_type" value="'.$captcha_type.'">';
    echo '</div>';
endif;

echo '<input type="hidden" name="form_id" value="'.$element->unqid.'">';
echo '<input type="hidden" name="template" value="'.Astroid\Framework::getTemplate()->id.'">';
echo '<input type="hidden" name="widget" value="formbuilder">';
echo '<input type="hidden" class="token" name="'.Session::getFormToken().'" value="1">';

$button_style       =   $params->get('button_style', 'primary');
$button_outline     =   $params->get('button_outline', 0);

$button_size        =   $params->get('button_size', '');
$button_size        =   $button_size ? ' '. $button_size : '';

$button_radius      =   $params->get('border_radius', '');
$button_bd_radius   =   $button_radius ? ' ' . $button_radius : '';

$button_margin_top  =   $params->get('button_margin_top', '');
$button_margin      =   !empty($button_margin_top) ? ' mt-' . $button_margin_top : '';
$button_class   =   $button_style !== 'text' ? 'btn-' . (intval($button_outline) ? 'outline-' : '') . $button_style . $button_size. $button_bd_radius : 'as-btn-text text-uppercase text-reset';
$btn_title      =   $button_style == 'text' ? '<small>'. Text::_('JSUBMIT') . '</small>' : Text::_('JSUBMIT');
echo '<button type="submit" class="as-form-builer-submit btn ' . $button_class . $button_margin . '">'.$btn_title.'</button>';
echo '<div class="as-formbuilder-status mt-4"></div>';
echo '</form>';

Factory::getDocument()->getWebAssetManager()->registerAndUseScript('astroid.formbuilder', "astroid/formbuilder.min.js", ['relative' => true, 'version' => 'auto'], [], ['jquery']);
