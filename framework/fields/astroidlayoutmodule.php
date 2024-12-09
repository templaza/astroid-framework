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
        if ($app->input->get('option', '') == 'com_modules'
            && $app->input->get('view', '') == 'module'
            && $app->input->get('layout', '') == 'edit') {
            $id     =   $app->input->get('id');
            $layout =   Layout::loadModuleLayout($id);
            $constant   =   Helper\Constants::manager_configs('joomla_module');
            $json = [
                'id'      =>  $this->id,
                'name' => $this->name,
                'layout' => $layout,
                'constant'   => $constant,
            ];
            $html   =   '<script type="application/json" id="astroid_layout_module_json">'.json_encode($json).'</script>';
            $html   .=  '<div id="astroid-layout-module" class="astroid-layout border rounded p-4"></div>';
            $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
            $wa->registerAndUseStyle('astroid.manager', "media/astroid/assets/vendor/manager/dist/index.css");
            $wa->registerAndUseStyle('astroid.icons', "media/astroid/assets/vendor/linearicons/font.min.css");
            $wa->useScript('bootstrap.tab');
            $wa->registerAndUseScript('astroid.manager', 'media/astroid/assets/vendor/manager/dist/index.js', ['relative' => true, 'version' => 'auto'], ['type' => 'module']);
            $wa->addInlineScript("(function ($) { $(document).ready(function () { $('#astroid-layout-module').parent().prev().remove(); }); })(jQuery)");
            return $html;
        }
        return 'This field is only available in module edit layout.';
    }
}
