<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Joomla\Plugin\System\Astroid\Extension;
use Joomla\CMS\Factory;
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
        Framework::getDocument()->waLocked(false);
    }

    public function onContentPrepareForm($form, $data)
    {
        if (defined('_ASTROID')) {
            Framework::getClient()->onContentPrepareForm($form, $data);
        }
    }

    public function onContentBeforeSave($context, $table, $isNew, $data = null)
    {
        if (defined('_ASTROID')) {
            return Framework::getClient()->onContentBeforeSave($context, $table, $isNew, $data);
        }
    }

    public function onBeforeRender()
    {
        if (defined('_ASTROID')) {
            Framework::getClient()->onBeforeRender();
        }
    }

    public function onAfterRender()
    {
        if (defined('_ASTROID')) {
            Framework::getDocument()->waLocked(true);
            Framework::getClient()->onAfterRender();
        }
    }

    public function onAfterRespond()
    {
        if (defined('_ASTROID')) {
            if (!(Helper::getPluginParams()->get('astroid_debug', 0)) || Framework::isAdmin()) {
                return;
            }

            $cache = PluginHelper::getPlugin('system', 'cache');
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
    }

    public function onContentPrepareData($context, $user)
    {
        // Fix issue Attempt to assign property "astroid_author_social0" on array of User Component
        if ($context == 'com_users.profile' && isset($user->params) && !empty($user->params)) {
            $params = new \Joomla\Registry\Registry();
            $params->loadArray($user->params);
            if (is_array($params->get('astroid_author_social')) && !count($params->get('astroid_author_social'))) {
                $params->remove('astroid_author_social');
                $db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
                $object = new \stdClass();
                $object->id = $user->id;
                $object->params = $params->toString();
                $db->updateObject('#__users', $object, 'id');
            }
        }
    }

    public function onUserAfterSave($user, $isnew, $success, $msg): void
    {
        // Fix issue Attempt to assign property "astroid_author_social0" on array of User Component
        if (isset($user['params']) && !empty($user['params'])) {
            $params = new \Joomla\Registry\Registry($user['params']);
            if (is_array($params->get('astroid_author_social')) && !count($params->get('astroid_author_social'))) {
                $params->remove('astroid_author_social');
                $db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
                $object = new \stdClass();
                $object->id = $user['id'];
                $object->params = $params->toString();
                $db->updateObject('#__users', $object, 'id');
            }
        }
    }

    public function onInstallerAfterInstaller($installmodel, $package, $installer, $result)
    {
        if (defined('_ASTROID')) {
            if (!$result || Framework::isSite()) {
                return false;
            }
            Framework::getClient()->onInstallerAfterInstaller($package);
        }
    }

    public function onExtensionAfterSave($context, $table, $isNew)
    {
        if (defined('_ASTROID')) {
            if (Framework::isAdmin()) {
                if (($context == 'com_modules.module' || $context == 'com_advancedmodules.module') && $table->module == 'mod_astroid_layout' && !empty($table->id)) {
                    $astroid_module_layout = Factory::getApplication()->input->get('astroid_module_layout', '', 'raw');
                    if (!empty($astroid_module_layout)) {
                        Helper::putContents(JPATH_SITE . '/media/mod_astroid_layout/params/' . $table->id . '.json', $astroid_module_layout);
                    }
                }
                if ($context == "com_templates.style" && $isNew && Template::isAstroidTemplate(JPATH_SITE . "/templates/{$table->template}/templateDetails.xml")) {
                    $params = \json_decode($table->params, TRUE);
                    $parent_id = $params['astroid'];
                    Template::setTemplateDefaults($table->template, $table->id, $parent_id);
                }
            }
        }
    }

    public function onAfterAstroidFormLoad($template, $form)
    {
        if ($template->isAstroid && Framework::isAdmin() && !count($template->getPresets())) {
            $form->removeField('template_preset', 'params');
            $form->removeField('presets', 'params');
        }
    }

    public function onContentAfterDelete($context, $article): void
    {
        if (defined('_ASTROID')) {
            Framework::getClient()->onContentAfterDelete($context, $article);
        }
    }
}
