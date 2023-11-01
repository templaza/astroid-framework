<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Helper;
use \Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Form\Form;
use Joomla\Filesystem\Folder;

defined('_JEXEC') or die;

class Template
{
    public static function getAstroidTemplates($full = false)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db
            ->getQuery(true)
            ->select('s.id, s.template, s.title')
            ->from('#__template_styles as s')
            ->where('s.client_id = 0')
            ->where('e.enabled = 1')
            ->leftJoin('#__extensions as e ON e.element=s.template AND e.type=' . $db->quote('template') . ' AND e.client_id=s.client_id');

        $db->setQuery($query);
        $templates = $db->loadObjectList();
        $return = [];
        foreach ($templates as $template) {
            $astroidTemplate = self::isAstroidTemplate(JPATH_SITE . "/templates/{$template->template}/templateDetails.xml");
            if ($astroidTemplate !== false) {
                self::setTemplateDefaults($template->template, $template->id);
                if (!$full) {
                    $return[] = $template->id;
                } else {
                    $return[] = $template;
                }
            }
        }
        return $return;
    }

    public static function isAstroidTemplate($template_xml_path)
    {
        if (!file_exists($template_xml_path)) {
            return false;
        }
        $xml = Helper::getXML($template_xml_path);
        $version = (string) $xml->version;
        $form = new Form('template');
        $form->loadFile($template_xml_path, false, '//config');
        $fields = $form->getFieldset('basic');
        $return = false;
        foreach ($fields as $field) {
            if (strtolower($field->type) === 'astroidmanagerlink') {
                $item['version'] = $version;
                $return = $item;
                break;
            }
        }
        return $return;
    }

    public static function setTemplateDefaults($template, $id, $parent_id = 0)
    {
        $params_path = JPATH_SITE . "/media/templates/site/{$template}/params/{$id}.json";
        if (!file_exists($params_path)) {
            if (!empty($parent_id) && file_exists(JPATH_SITE . "/media/templates/site/{$template}/params/" . $parent_id . '.json')) {
                $params = file_get_contents(JPATH_SITE . "/media/templates/site/{$template}/params" . '/' . $parent_id . '.json');
                Helper::putContents(JPATH_SITE . "/media/templates/site/{$template}/params" . '/' . $id . '.json', $params);
            } else if (file_exists(JPATH_SITE . '/media/templates/site/' . $template . '/astroid/default.json')) {
                $params = file_get_contents(JPATH_SITE . '/media/templates/site/' . $template . '/astroid/default.json');
                $params = str_replace('TEMPLATE_NAME', $template, $params);
                Helper::putContents(JPATH_SITE . "/media/templates/site/{$template}/params" . '/' . $id . '.json', $params);
            } else {
                Helper::putContents(JPATH_SITE . "/media/templates/site/{$template}/params" . '/' . $id . '.json', '');
            }
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $object = new \stdClass();
            $object->id = $id;
            $object->params = \json_encode(["astroid" => $id]);
            $db->updateObject('#__template_styles', $object, 'id');
            self::uploadTemplateDefaults($template, $id);
        }
    }

    public static function uploadTemplateDefaults($template, $id = 0)
    {
        $old_source         = JPATH_SITE . '/templates/' . $template . '/images/default';
        $source             = JPATH_SITE . '/media/templates/site/' . $template . '/images/default';
        $destination        = JPATH_SITE . '/images/' . $template;
        if (file_exists($source)) {
            Folder::copy($source, $destination, '', true);
        }
        elseif (file_exists($old_source)) {
            Folder::copy($old_source, $destination, '', true);
        }
    }

    public static function prepareChildTemplateDefaults($parent, $child) {
        $source         =   JPATH_SITE . '/media/templates/site/' . $parent;
        $destination    =   JPATH_SITE . '/media/templates/site/' . $child;
        if (file_exists($source.'/astroid')) {
            Folder::copy($source.'/astroid', $destination.'/astroid');
        }
        if (file_exists($source.'/fonts')) {
            Folder::copy($source.'/fonts', $destination.'/fonts');
        }
    }
}
