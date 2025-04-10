<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Astroid\Element\Layout;
use Astroid\Helper;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAstroidLayoutModule extends FormField {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'AstroidLayoutModule';
    protected $ordering;

    protected function getInput() {
        $app    =   Factory::getApplication();
        $id     =   $app->input->get('id');
        $layout =   Layout::loadModuleLayout($id);
        $constant   =   Helper\Constants::manager_configs('joomla_module');
        // Get Language
        $language = array();
        foreach (Helper\Constants::$translationStrings as $string) {
            $language[strtoupper($string)] = $app->getLanguage()->_($string);
        }
        $json = [
            'id'      =>  $this->id,
            'name' => $this->name,
            'layout' => $layout,
            'constant'   => $constant,
            'language'  =>  $language
        ];
        $html   =   '<script type="application/json" id="astroid_layout_module_json">'.json_encode($json).'</script>';
        $html   .=  '<div id="astroid-layout-module" class="astroid-layout border rounded p-4"></div>';
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle('astroid.manager', "media/astroid/assets/vendor/manager/dist/index.css");
        $wa->registerAndUseStyle('astroid.icons', "media/astroid/assets/vendor/linearicons/font.min.css");
        if (version_compare(JVERSION, '5.0.0', '<')) {
            $wa->registerAndUseStyle('astroid.manager.fontawesome', "media/astroid/assets/vendor/fontawesome/css/all.min.css");
        }
        $wa->useScript('bootstrap.tab');
        $wa->useScript('bootstrap.modal');
        $wa->registerAndUseScript('astroid.manager.tinymce', 'media/astroid/assets/vendor/tinymce/tinymce.min.js', ['relative' => true, 'version' => 'auto']);
        $wa->registerAndUseScript('astroid.manager', 'media/astroid/assets/vendor/manager/dist/index.js', ['relative' => true, 'version' => 'auto'], ['type' => 'module']);
        $wa->addInlineScript("(function ($) { $(document).ready(function () { $('#astroid-layout-module').parent().prev().remove(); }); })(jQuery)");
        return $html;
    }
}
