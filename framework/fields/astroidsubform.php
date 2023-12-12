<?php
use Joomla\CMS\Form\FormField;
use Joomla\Filesystem\Path;
use Astroid\Element;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class JFormFieldAstroidSubForm extends FormField
{
    /**
     * The form field type.
     * @var    string
     */
    protected $type = 'AstroidSubform';

    /**
     * Form source
     * @var string
     */
    protected $formsource;

    protected $formtype = '';

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param   string  $name  The property name for which to get the value.
     *
     * @return  mixed  The property value or null.
     *
     * @since   3.6
     */
    public function __get($name)
    {
        switch ($name) {
            case 'formsource':
                return $this->$name;
        }

        return parent::__get($name);
    }

    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @param   string  $name   The property name for which to set the value.
     * @param   mixed   $value  The value of the property.
     *
     * @return  void
     *
     * @since   3.6
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'formsource':
                $this->formsource = (string) $value;

                // Add root path if we have a path to XML file
                if (strrpos($this->formsource, '.xml') === \strlen($this->formsource) - 4) {
                    $this->formsource = Path::clean(JPATH_ROOT . '/' . $this->formsource);
                }

                break;

            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Method to attach a Form object to the field.
     *
     * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed              $value    The form field value to validate.
     * @param   string             $group    The field name group control value.
     *
     * @return  boolean  True on success.
     *
     * @since   3.6
     */
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        if (!parent::setup($element, $value, $group)) {
            return false;
        }

        foreach (['formsource'] as $attributeName) {
            $this->__set($attributeName, $element[$attributeName]);
        }

        $this->formtype = 'file';

        if ($this->value && \is_string($this->value)) {
            // Guess here is the JSON string from 'default' attribute
            $this->value = json_decode($this->value, true);
        }

        if (!$this->formsource && $element->form) {
            // Set the formsource parameter from the content of the node
            $this->formsource = $element->form->saveXML();
            $this->formtype = 'string';
        }

        return true;
    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   3.6
     */
    protected function getInput()
    {
        $subform_data   = new Element('subform', ['formsource' => $this->formsource, 'formtype' => $this->formtype]);
        $form_template  = $subform_data->renderJson('subform');
        $value = $this->value;
        if (empty($value)) {
            $options = [];
        } else {
            $options = \json_decode($value, true);
        }
        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  $options,
            'type'    =>  strtolower($this->type),
            'form'    =>  $form_template
        ];
        return json_encode($json);
    }
}