<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;

/**
 * Color Form Field class for the Joomla Platform.
 * This implementation is designed to be compatible with HTML5's `<input type="color">`
 *
 * @link   https://www.w3.org/TR/html-markup/input.color.html
 * @since  11.3
 */
class JFormFieldAstroidSpacing extends JFormField
{

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.3
     */
    protected $type = 'AstroidSpacing';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.3
     */
    protected function getInput()
    {
        $units = isset($this->element['units']) ? (string) $this->element['units'] : [];
        $this->units = !empty($units) ? explode(',', $units) : [];

        $this->unit = isset($this->element['unit']) ? (string) $this->element['unit'] : '';

        $renderer = new JLayoutFile('fields.astroidspacing', JPATH_LIBRARIES . '/astroid/framework/layouts');

        $data = $this->getLayoutData();
        $data['fieldname'] = $this->fieldname;
        $data['name'] = $this->name;
        return $renderer->render($data);
    }
}
