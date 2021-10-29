<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldAstroidMenuOptions extends JFormField {

   //The field class must know its own type through the variable $type.
   protected $type = 'AstroidMenuOptions';

   public function getLabel() {
      return false;
   }

   public function getInput() {
      $renderer = new JLayoutFile('fields.astroidmenuoptions', JPATH_LIBRARIES . '/astroid/framework/layouts');
      $data = $this->getLayoutData();
      if (!is_array($this->value) && empty($this->value)) {
         //$value = [];
         $value = [
             'megamenu' => FALSE,
             'showtitle' => FALSE,
             'icon' => '',
             'customclass' => '',
             'width' => '250px',
             'megamenu_width' => '980px',
             'alignment' => 'right',
             'megamenu_direction' => 'right',
             'subtitle' => '',
             'badge' => FALSE,
             'badge_text' => '',
             'badge_color' => '#FFF',
             'badge_bgcolor' => '#000'
         ];
      } else {
         $value = (array) $this->value;
      }

      $menu_item = $this->form->getData()->toObject();

      $extraData = array(
          'value' => $value,
          'fieldname' => $this->fieldname,
          'ngShow' => $this->element['ngShow'],
          'ngHide' => $this->element['ngHide'],
          'menu_type' => $menu_item->menutype,
          'menu_item_id' => $menu_item->id,
          'menu_item_level' => $menu_item->level,
      );

      //$extraData['defaults'] = $defaults;

      $data = array_merge($data, $extraData);

      return $renderer->render($data);
   }

}
