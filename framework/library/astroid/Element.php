<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace Astroid;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;

defined('_JEXEC') or die;

class Element
{
    public mixed $id = null;
    protected $app = null;
    public mixed $type = '';
    public string $title = '';
    public string $icon = '';
    public string $color = '';
    public mixed $category = null;
    public bool $multiple = true;
    public string $classname = '';
    public string $description = '';
    public string $element_type = 'layout';
    protected string $xml_file = '';
    protected string $default_xml_file = '';
    protected string $default_pro_xml_file = '';
    protected string $layout = '';
    public array $params = [];
    public mixed $data = [];
    protected mixed $template = null;
    protected $xml = null;
    protected mixed $form = null;
    protected mixed $raw_data = null;
    protected array $subform = [];
    protected string $mode = '';

    public function __construct($type = '', $data = [], $template = null, $mode = '')
    {
        $this->type = $type;
        $this->mode = $mode;
        if (!empty($data)) {
            if ($type == 'subform') {
                $this->subform = $data;
            } else {
                $this->id = $data['id'];
                $this->data = isset($data['params']) ? $data['params'] : [];
                $this->raw_data = $data;
            }
        }
        $this->app = Factory::getApplication();

        if ($template === null) {
            $this->template = Framework::getTemplate();
        } else {
            $this->template = $template;
        }

        $template_name = $this->template->isAstroid ? $this->template->template : '';
        if (empty($template_name) && defined('ASTROID_TEMPLATE_NAME')) {
            $template_name = ASTROID_TEMPLATE_NAME;
        }

        $this->setClassName();
        $this->template->setLog("Initiated Element : " . $type, "success");
        if ($type !== 'subform' && $type !=='sublayout') {
            $library_elements_directory = Path::clean(JPATH_LIBRARIES . '/astroid/framework/elements/');
            $plugin_elements_directory = Path::clean(JPATH_SITE . '/plugins/astroid/');
            $template_elements_directory = Path::clean(JPATH_SITE . '/media/templates/site/' . $template_name . '/astroid/elements/');

            if (file_exists($library_elements_directory . $this->type . '/default.xml')) {
                $this->default_xml_file = $library_elements_directory . $this->type . '/default.xml';
            } elseif ($this->mode === 'article_data') {
                $this->default_xml_file = $library_elements_directory . 'article.xml';
            } else {
                $this->default_xml_file = $library_elements_directory . 'default.xml';
            }

            switch ($this->type) {
                case 'section':
                    $this->xml_file = $library_elements_directory . 'section.xml';
                    break;
                case 'column':
                    $this->xml_file = $library_elements_directory . 'column.xml';
                    break;
                case 'row':
                    $this->xml_file = $library_elements_directory . 'row.xml';
                    break;
                default:
                    if (file_exists(Path::clean($library_elements_directory . $this->type . '/' . $this->type . '.xml'))) {
                        $this->xml_file = Path::clean($library_elements_directory . $this->type . '/' . $this->type . '.xml');
                        $this->layout = Path::clean($library_elements_directory . $this->type . '/' . $this->type . '.php');
                    }
                    break;
            }

            if (file_exists($plugin_elements_directory)) {
                $plugin_folders = Folder::folders($plugin_elements_directory);
                if (count($plugin_folders)) {
                    foreach ($plugin_folders as $plugin_folder) {
                        if (PluginHelper::isEnabled('astroid', $plugin_folder) && file_exists(Path::clean($plugin_elements_directory . '/' . $plugin_folder . '/elements/' . $this->type . '/' . $this->type . '.xml'))) {
                            $this->xml_file = Path::clean($plugin_elements_directory . '/' . $plugin_folder . '/elements/' . $this->type . '/' . $this->type . '.xml');
                            $this->layout = Path::clean($plugin_elements_directory . '/' . $plugin_folder . '/elements/' . $this->type . '/' . $this->type . '.php');
                        }
                    }
                }
            }

            if (file_exists(Path::clean($template_elements_directory . $this->type . '/' . $this->type . '.xml'))) {
                $this->xml_file = Path::clean($template_elements_directory . $this->type . '/' . $this->type . '.xml');
                $this->layout = Path::clean($template_elements_directory . $this->type . '/' . $this->type . '.php');
            }
        }

        if (!defined('ASTROID_FRONTEND') && $this->type !='sublayout') {
            if ($this->xml_file) {
                $this->loadXML();
            }
            $this->loadForm();
        }
    }

    protected function setClassName(): void
    {
        $type = $this->type;
        $type = str_replace('-', ' ', $type);
        $type = str_replace('_', ' ', $type);
        $type = ucwords(strtolower($type));
        $classname = 'AstroidElement' . str_replace(' ', '', $type);
        $this->classname = $classname;
    }

    protected function loadXML(): void
    {
        $xml = simplexml_load_file($this->xml_file);
        $this->xml = $xml;
        $title = (string) @$xml->title;
        $icon = (string) @$xml->icon;
        $description = (string) @$xml->description;
        $color = (string) @$xml->color;
        $multiple = (string) @$xml->multiple;
        $category = (string) @$xml->category;

        $this->title = $title;
        $this->element_type = (string) @$xml->element_type;
        $this->icon = $icon;
        $this->category = explode(',', $category);
        for ($i = 0 ; $i < count($this->category); $i++) {
            $this->category[$i] = Text::_($this->category[$i]);
        }
        $this->description = $description;
        $this->color = $color;
        $this->multiple = $multiple == "false" ? false : true;
    }

    public function loadForm(): void
    {
        $this->form = new Form($this->type);
        if ($this->type !== 'subform') {
            $defaultXml = simplexml_load_file($this->default_xml_file);
            $this->form->load($defaultXml->form, false);
            if ($this->mode !== 'article_data' && Helper::isPro()) {
                $defaultXml = simplexml_load_file(ASTROID_PRO_PATH . DIRECTORY_SEPARATOR . 'elements' . DIRECTORY_SEPARATOR . 'default.xml');
                $this->form->load($defaultXml->form, false);
            }
        } else {
            if ($this->subform['formtype'] == 'string') {
                $defaultXml = simplexml_load_string($this->subform['formsource']);
                $this->form->load($defaultXml, false);
            } else {
                $defaultXml = simplexml_load_file($this->subform['formsource']);
                $this->form->load($defaultXml->form, false);
            }
        }

        if (!empty($this->xml_file)) {
            $xml = $this->xml->form;
            $this->form->load($xml, false);
        }

        $formData = [];
        $fieldsets = $this->form->getFieldsets();
        foreach ($fieldsets as $key => $fieldset) {
            $fields = $this->form->getFieldset($key);
            foreach ($fields as $field) {
                $formData[] = ['name' => $field->name, 'value' => $field->value];
            }
        }

        $this->params = $formData;
    }

    public function getInfo(): array
    {
        return [
            'type' => $this->type,
            'title' => Text::_($this->title),
            'icon' => $this->icon,
            'category' => $this->category,
            'element_type' => $this->element_type,
            'description' => Text::_($this->description),
            'color' => $this->color,
            'multiple' => $this->multiple,
            'params' => $this->params,
        ];
    }

    public function renderJson($type = 'system'): array
    {
        switch ($type) {
            case 'sublayout':
                return array('info' => [
                    'type' => 'sublayout',
                    'title' => 'Sublayouts',
                    'icon' => 'as-icon as-icon-layers',
                ], 'type' => $type);
                break;
            default:
                $form = $this->getForm();
                $fieldsets = $form->getFieldsets();
                $form_content = array();
                $model_form = [];
                foreach ($fieldsets as $key => $fieldset) {
                    if ($this->mode === 'article_data' && isset($fieldset->articleData) && $fieldset->articleData === 'false') {
                        continue;
                    }
                    $fields = $form->getFieldset($key);
                    $groups = [];
                    foreach ($fields as $key => $field) {
                        if ($field->type == 'astroidgroup') {
                            $groups[$field->fieldname] = ['title' => Text::_($field->getAttribute('title', '')), 'icon' => $field->getAttribute('icon', ''), 'description' => Text::_($field->getAttribute('description', '')), 'fields' => []];
                        }
                    }

                    foreach ($fields as $key => $field) {
                        if ($field->type == 'astroidgroup') {
                            continue;
                        }
                        if ($this->mode === 'article_data' && $field->getAttribute('articleData', 'true') === 'false') {
                            continue;
                        }

                        $model_form[$field->fieldname] = $field->value;
                        $field_group = $field->getAttribute('astroidgroup', 'none');
                        $js_input   =   json_decode($field->input);

                        $field_tmp  =   [
                            'id'            =>  $field->id,
                            'name'          =>  $field->fieldname,
                            'value'         =>  $field->value,
                            'label'         =>  Text::_($field->getAttribute('label')),
                            'description'   =>  Text::_($field->getAttribute('description')),
                            'input'         =>  $field->input,
                            'type'          =>  'string',
                            'group'         =>  $fieldset->name,
                            'ngShow'        =>  Helper::replaceRelationshipOperators($field->getAttribute('ngShow'))
                        ];
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $field_tmp['input']     =   $js_input;
                            $field_tmp['type']      =   'json';
                        }
                        $groups[$field_group]['fields'][] = $field_tmp;
                    }
                    $fieldset->label    = Text::_($fieldset->label);
                    $fieldset->childs   = $groups;
                    $form_content[] = $fieldset;
                }
                return array('content' => $form_content, 'info' => $this->getInfo(), 'type' => $type);
                break;
        }
    }

    public function getForm()
    {
        return $this->form;
    }

    protected function getParams()
    {
        $formData = [];
        foreach ($this->data as $data) {
            $data = (array) $data;
            $formData[$data['name']] = $data['value'];
        }
        /* $params = [];
        foreach ($this->params as $param) {
           $param = (array) $param;
           if (isset($formData[$param['name']])) {
              $params[$param['name']] = $formData[$param['name']];
           } else {
              $params[$param['name']] = $param['value'];
           }
        } */

        return new Helper\AstroidParams($formData);
    }
}
