<?php
/**
 * @package   Astroid Framework
 * @author    TemPlaza https://www.templaza.com
 * @copyright Copyright (C) 2011 - 2021 TemPlaza.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 */
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
define('COMPILE_SASS', 0);

if (file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
    JLoader::registerNamespace('Astroid', JPATH_LIBRARIES . '/astroid/framework/library/astroid', false, false, 'psr4');
}

use Astroid\Framework;
use Astroid\Helper;
use Astroid\Helper\Template;

/**
 * Astroid system plugin
 *
 * @since  1.0
 */
class plgSystemAstroid extends JPlugin
{

    protected $app;

    public function onAfterRoute()
    {
        if (!file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
            return false;
        }
        Framework::init();
        $option = $this->app->input->get('option', '');
        $astroid = $this->app->input->get('astroid', '');
        if ($option == 'com_ajax' && !empty($astroid)) {
            Framework::getClient()->execute($astroid);
        }
    }

    public function onContentPrepareForm($form, $data)
    {
        if (!file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
            return false;
        }
        Framework::getClient()->onContentPrepareForm($form, $data);
    }

    public function onAfterRender()
    {
        if (!file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
            return false;
        }
        Framework::getClient()->onAfterRender();
    }

    public function onAfterRespond()
    {
        if (!file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
            return false;
        }
        if (!(Helper::getPluginParams()->get('astroid_debug', 0)) || Framework::isAdmin()) {
            return;
        }

        $cache = \JPluginHelper::getPlugin('system', 'cache');
        if (Framework::isSite() && !empty($cache)) {
            return;
        }

        // Capture output.
        $contents = ob_get_contents();

        if ($contents) {
            ob_end_clean();
        }
        echo Helper::str_lreplace('</body>', Helper::debug() . '</body>', $contents);
    }

    public function onExtensionAfterSave($context, $table, $isNew)
    {
        if (!file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
            return false;
        }
        if (Framework::isAdmin() && $context == "com_templates.style" && $isNew && Template::isAstroidTemplate($table->template)) {
            $params = \json_decode($table->params, TRUE);
            $parent_id = $params['astroid'];
            Template::setTemplateDefaults($table->template, $table->id, $parent_id);
        }
    }

    public function onAfterAstroidFormLoad($template, $form)
    {
        if ($template->isAstroid && Framework::isAdmin() && !count($template->getPresets())) {
            $form->removeField('template_preset', 'params');
            $form->removeField('presets', 'params');
        }
    }

    // onBeforeAstroidRender
    // onAfterAstroidRender
}
