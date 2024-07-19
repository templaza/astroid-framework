<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\Folder;
use Astroid\Element\Layout;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAstroidLayoutList extends FormField {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'AstroidLayoutList';
    protected $directory;
    protected $ordering;

    /**
     * Method to get the field input markup for a generic list.
     * Use the multiple attribute to enable multiselect.
     *
     * @return  string  The field input markup.
     *
     * @since   3.7.0
     */
    protected function getInput() {
        $path = (string) $this->element['directory'];
        $inherit = (bool) $this->element['inherit'];
        $folders = Folder::folders(Path::clean(JPATH_ROOT . '/templates'));
        $data = array();
        $templates = array();
        for ($i = 0; $i < count($folders); $i++ ) {
            $layout_folder = Path::clean(JPATH_ROOT. '/media/templates/site/' . $folders[$i] . '/astroid/' . $path);
            if (file_exists($layout_folder)) {
                $layouts = Folder::files($layout_folder, '.json');
                if (count($layouts)) {
                    $tmp_layouts = array();
                    foreach ($layouts as $layout) {
                        $layout_name = basename($layout, ".json");
                        if ($layout_name !== 'default') {
                            $tmp = Layout::getDataLayout($layout_name, $folders[$i], $path);
                            $tmp['name'] = $layout_name;
                            unset($tmp['data']);
                            $tmp_layouts[] = $tmp;
                        }
                    }
                    $templates[$folders[$i]] = $tmp_layouts;
                }
            }
        }
        $data['templates'] = $templates;
        $value = !empty($this->value) ? $this->value : '{"template":"","layout":""}';
        $data['value'] = json_decode($value);
        $data['name'] = $this->name;
        $data['language'] = [
            'default' => Text::_('JDEFAULT'),
            'select_template' => Text::_('ASTROID_SELECT_TEMPLATE'),
            'inherit' => Text::_('JGLOBAL_INHERIT'),
            'system' => Text::_('ASTROID_ELEMENT_CATEGORY_SYSTEM')
        ];
        $data['inherit'] = $inherit;
        if ($path == 'layouts') {
            $data['language']['select_layout'] = Text::_('ASTROID_SELECT_SUB_LAYOUT');
        } else {
            $data['language']['select_layout'] = Text::_('ASTROID_SELECT_ARTICLE_LAYOUT');
        }
        $html   =   '<script type="application/json" id="'.$this->id.'_json">'.json_encode($data).'</script>';
        $html   .=  '<div class="as-select-sublayout" id="'.$this->id.'"></div>';
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseScript('astroid.selectsublayout.js', 'media/astroid/assets/vendor/selectsublayout/dist/index.js', ['relative' => true, 'version' => 'auto'], ['type' => 'module']);
        return $html;
    }
}
