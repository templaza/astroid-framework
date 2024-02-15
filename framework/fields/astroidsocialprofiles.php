<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

// The class name must always be the same as the filename (in camel case)
class JFormFieldAstroidsocialprofiles extends FormField {

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

       if (!empty($value)) {
           foreach($value as &$item){
               if(isset($profile_icons[$item['title']])){
                   $item['icons'] = $profile_icons[$item['title']];
               }
           }
       }

       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  $value,
           'options' =>  Astroid\Helper\Constants::$social_profiles,
           'type'    =>  strtolower($this->type),
           'lang'   => [
               'social_brands'  => Text::_('TPL_ASTROID_SOCIAL_BRANDS'),
               'social_search'  => Text::_('TPL_ASTROID_SOCIAL_SEARCH_LABEL'),
               'add_profile'  => Text::_('TPL_ASTROID_ADD_PROFILE'),
               'add_custom_social_label'  => Text::_('TPL_ASTROID_ADD_CUSTOM_SOCIAL_LABEL'),
               'astroid_color'  => Text::_('TPL_ASTROID_COLOR'),
               'astroid_icon'  => Text::_('TPL_ASTROID_ICON'),
               'astroid_title'  => Text::_('TPL_ASTROID_TITLE'),
               'astroid_icon_class'  => Text::_('TPL_ASTROID_ICON_CLASS'),
               'astroid_link'  => Text::_('TPL_ASTROID_LINK'),
               'astroid_mobile_number'  => Text::_('TPL_ASTROID_MOBILE_NUMBER'),
               'astroid_skype_id'  => Text::_('TPL_ASTROID_SKYPE_ID'),
               'astroid_username'  => Text::_('TPL_ASTROID_MOBILE_USERNAME'),
               'astroid_social_link_placeholder'  => Text::_('TPL_ASTROID_SOCIAL_LINK_PLACEHOLDER'),
               'astroid_social_whatsapp_placeholder'  => Text::_('TPL_ASTROID_SOCIAL_WHATSAPP_PLACEHOLDER'),
               'astroid_social_telegram_placeholder'  => Text::_('TPL_ASTROID_SOCIAL_TELEGRAM_PLACEHOLDER'),
               'astroid_social_skype_placeholder'  => Text::_('TPL_ASTROID_SOCIAL_SKYPE_PLACEHOLDER'),
           ]
       ];

       return json_encode($json);
   }
}
