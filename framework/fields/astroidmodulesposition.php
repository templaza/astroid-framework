<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Component\Modules\Administrator\Helper\ModulesHelper;
use Joomla\CMS\Language\Text;
FormHelper::loadFieldClass('list');

/**
 * Modules Position field.
 *
 * @since  3.4.2
 */
class JFormFieldAstroidModulesPosition extends ListField
{

   /**
    * The form field type.
    *
    * @var    string
    * @since  3.4.2
    */
   protected $type = 'AstroidModulesPosition';
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
      $clientId = Factory::getApplication()->input->get('client_id', 0, 'int');
      $options = ModulesHelper::getPositions($clientId);
      $positions = Astroid\Helper::getPositions();
      //$options = array();
      $options = array_merge(parent::getOptions(), $options);

      $more_positions = [];
      foreach ($options as $option) {
         $more_positions[$option->value] = Text::_($option->text);
      }

      $positions = array_merge($more_positions, $positions);
      return array_unique($positions);
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
           'astroid_content_layout'    =>  (string) $this->element['astroid-content-layout'],
           'astroid_content_layout_load'    =>  (string) $this->element['astroid-content-layout-load'],
           'type'    =>  strtolower($this->type),
       ];
       return json_encode($json);
   }
}
