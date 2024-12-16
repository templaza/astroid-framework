<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Language\Text;
FormHelper::loadFieldClass('list');

/**
 * Modules Position field.
 *
 * @since  3.4.2
 */
class JFormFieldAstroidModulesList extends ListField
{

    /**
     * The form field type.
     *
     * @var    string
     * @since  3.4.2
     */
    protected $type = 'AstroidModulesList';
    protected $ordering;

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   3.4.2
     */
    public function getOptions()
    {
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $extension   =   (string)$this->element['extension'];
        if ($extension) {
            $query->select('m.id, m.title')
                ->from('#__modules AS m')
                ->where('m.module' . ' = ' . $db->quote($extension))
                ->where('m.published' . ' = 1')
                ->where('m.client_id' . ' = 0');
        } else {
            $query->select('m.id, m.title')
                ->from('#__modules AS m')
                ->where('m.published' . ' = 1')
                ->where('m.client_id' . ' = 0');
        }

        return $db->setQuery($query)->loadObjectList();
    }

    protected function getInput()
    {
        // Get the field options.
        $options = (array) $this->getOptions();

        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
            'options' =>  $options,
            'type'    =>  strtolower($this->type),
        ];
        return json_encode($json);
    }
}
