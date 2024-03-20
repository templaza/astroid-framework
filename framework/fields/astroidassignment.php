<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Component\Menus\Administrator\Helper\MenusHelper;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAstroidAssignment extends FormField {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'astroidassignment';
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
        $menuTypes = MenusHelper::getMenuLinks();
        $menus  =   array();
        $value  =   \json_decode($this->value, true);
        if (!empty($menuTypes)) {
            foreach ($menuTypes as $type) {
                if (count($type->links)) {
                    foreach ($type->links as $link) {
                        if (isset($value[$link->value])) {
                            $menus[$link->value] = $value[$link->value];
                        } else {
                            $menus[$link->value] = true;
                        }
                    }
                }
            }
        }
        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  $menus,
            'menu'    =>  Astroid\Helper::getMenuLinks(),
            'multilanguage' => Multilanguage::isEnabled(),
            'type'    =>  strtolower($this->type),
        ];
        return json_encode($json);
    }
}