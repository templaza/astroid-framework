<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

// The class name must always be the same as the filename (in camel case)
class JFormFieldAstroidsocialprofiles extends JFormField {

//The field class must know its own type through the variable $type.
   protected $type = 'astroidsocialprofiles';

   public function getLabel() {
      return false;
   }

   public function getInput() {
       $value = \json_decode($this->value, true);
       $profile_icons = [];
       foreach(Astroid\Helper\Constants::$social_profiles as $profile){
           $profile_icons[$profile['title']] = $profile['icons'];
       }

       foreach($value as &$item){
           if(isset($profile_icons[$item['title']])){
               $item['icons'] = $profile_icons[$item['title']];
           }
       }

       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  $value,
           'options' =>  Astroid\Helper\Constants::$social_profiles,
           'type'    =>  strtolower($this->type),
           'lang'   => [
               'social_brands'  => JText::_('TPL_ASTROID_SOCIAL_BRANDS'),
               'social_search'  => JText::_('TPL_ASTROID_SOCIAL_SEARCH_LABEL'),
               'add_profile'  => JText::_('TPL_ASTROID_ADD_PROFILE'),
               'add_custom_social_label'  => JText::_('TPL_ASTROID_ADD_CUSTOM_SOCIAL_LABEL'),
               'astroid_color'  => JText::_('TPL_ASTROID_COLOR'),
               'astroid_icon'  => JText::_('TPL_ASTROID_ICON'),
               'astroid_title'  => JText::_('TPL_ASTROID_TITLE'),
               'astroid_icon_class'  => JText::_('TPL_ASTROID_ICON_CLASS'),
               'astroid_link'  => JText::_('TPL_ASTROID_LINK'),
               'astroid_mobile_number'  => JText::_('TPL_ASTROID_MOBILE_NUMBER'),
               'astroid_skype_id'  => JText::_('TPL_ASTROID_SKYPE_ID'),
               'astroid_social_link_placeholder'  => JText::_('TPL_ASTROID_SOCIAL_LINK_PLACEHOLDER'),
               'astroid_social_whatsapp_placeholder'  => JText::_('TPL_ASTROID_SOCIAL_WHATSAPP_PLACEHOLDER'),
               'astroid_social_telegram_placeholder'  => JText::_('TPL_ASTROID_SOCIAL_TELEGRAM_PLACEHOLDER'),
               'astroid_social_skype_placeholder'  => JText::_('TPL_ASTROID_SOCIAL_SKYPE_PLACEHOLDER'),
           ]
       ];

       return json_encode($json);
//      $renderer = new JLayoutFile('fields.astroidsocialprofiles', JPATH_LIBRARIES . '/astroid/framework/layouts');
//      return $renderer->render($this->getLayoutData());
   }

}
