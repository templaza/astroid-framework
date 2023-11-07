<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Joomla\Plugin\System\Astroid\Extension;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Astroid\Framework;
use Astroid\Helper;
use Astroid\Helper\Template;

/**
 * Joomla! System Logging Plugin.
 *
 * @since  1.5
 */
final class AstroidPlugin extends CMSPlugin
{
    public function onAfterRoute()
    {
        if (!file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
            return false;
        }
        Framework::init();
        $option = $this->getApplication()->input->get('option', '');
        $astroid = $this->getApplication()->input->get('astroid', '');
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

    public function onContentBeforeSave($context, $table, $isNew, $data = null)
    {
        if (!file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
            return false;
        }

        return Framework::getClient()->onContentBeforeSave($context, $table, $isNew, $data);
    }

    public function onBeforeRender()
    {
        if (!file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
            return false;
        }
        if (Framework::isSite()) {
            Framework::getClient()->onBeforeRender();
        }
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
            return false;
        }

        $cache = PluginHelper::getPlugin('system', 'cache');
        if (Framework::isSite() && !empty($cache)) {
            return false;
        }

        // Capture output.
        $contents = ob_get_contents();

        if ($contents) {
            ob_end_clean();
        }
        echo Helper::str_lreplace('</body>', Helper::debug() . '</body>', $contents);
    }

    public function onInstallerAfterInstaller($installmodel, $package, $installer, $result)
    {
        if (!$result || Framework::isSite()) {
            return false;
        }
        Framework::getClient()->onInstallerAfterInstaller($package);
    }

    public function onExtensionAfterSave($context, $table, $isNew)
    {
        if (!file_exists(JPATH_LIBRARIES . '/astroid/framework/library/astroid')) {
            return false;
        }
        if (Framework::isAdmin() && $context == "com_templates.style" && $isNew && Template::isAstroidTemplate(JPATH_SITE . "/templates/{$table->template}/templateDetails.xml")) {
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
}
