<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
extract($displayData);

if (empty($value)) {
   $value = json_encode([]);
}else{
   $value = \json_decode($value, true);
   $profile_icons = [];
   foreach(Astroid\Helper\Constants::$social_profiles as $profile){
      $profile_icons[$profile['title']] = $profile['icons'];
   }

   foreach($value as &$item){
      if(isset($profile_icons[$item['title']])){
         $item['icons'] = $profile_icons[$item['title']];
      }
   }

   $value = \json_encode($value);
}

?>
<script>
   var AstroidSocialProfiles = <?php echo json_encode(Astroid\Helper\Constants::$social_profiles); ?>;
   var AstroidSocialProfilesSelected = <?php echo $value; ?>;
   var TPL_ASTROID_SOCIAL_LINK_PLACEHOLDER = '<?php echo JText::_('TPL_ASTROID_SOCIAL_LINK_PLACEHOLDER'); ?>';
   var TPL_ASTROID_SOCIAL_WHATSAPP_PLACEHOLDER = '<?php echo JText::_('TPL_ASTROID_SOCIAL_WHATSAPP_PLACEHOLDER'); ?>';
   var TPL_ASTROID_SOCIAL_TELEGRAM_PLACEHOLDER = '<?php echo JText::_('TPL_ASTROID_SOCIAL_TELEGRAM_PLACEHOLDER'); ?>';
   var TPL_ASTROID_SOCIAL_SKYPE_PLACEHOLDER = '<?php echo JText::_('TPL_ASTROID_SOCIAL_SKYPE_PLACEHOLDER'); ?>';
</script>
<div class="astroidsocialprofiles" astroidsocialprofiles>
   <textarea class="d-none" name="<?php echo $name; ?>">{{ profiles}}</textarea>
   <div class="row">
      <div class="col-sm-9">
         <h2 ng-hide="profiles.length" class="text-center my-5"><?php echo JText::_('TPL_ASTROID_NO_PROFILE_SELECTED'); ?></h2>

         <div ng-show="profiles.length" ng-sortable="{draggable: '.social-profile-item',animation: 100, handle:'.card-header'}">
            <div ng-repeat="profile in profiles track by $index" class="card mb-2 social-profile-item" ng-init="profileIndex = $index">
               <div class="card-header">
                  <span style="font-size: 18px;"><i ng-style="{'color':profile.color}" class="{{ profile.icon}}"></i> {{ profile.title}}</span>
                  <span ng-click="removeSocialProfile(profileIndex)" class="text-danger float-right" style="cursor: pointer"><i class="fas fa-trash-alt"></i></span>
                  <div class="clearfix"></div>
               </div>
               <div class="card-body">
                  <div class="row" ng-class="{'mb-2':profile.icons.length > 1}">
                     <div class="col-sm-4">
                        <label class="astroid-label" ng-show="profile.id != 'whatsapp' && profile.id != 'skype' && profile.id != 'telegram'"><?php echo JText::_('TPL_ASTROID_LINK'); ?></label>
                        <label class="astroid-label" ng-show="profile.id == 'whatsapp'"><?php echo JText::_('TPL_ASTROID_MOBILE_NUMBER'); ?></label>
                        <label class="astroid-label" ng-show="profile.id == 'telegram'"><?php echo JText::_('TPL_ASTROID_MOBILE_USERNAME'); ?></label>
                        <label class="astroid-label" ng-show="profile.id == 'skype'"><?php echo JText::_('TPL_ASTROID_SKYPE_ID'); ?></label>
                     </div>
                     <div class="col-sm-8">
                        <input type="text" placeholder="{{ getPlaceholder(profile.id) }}" ng-model="profile.link" class="form-control" autocomplete="off">
                     </div>
                  </div>
                  <div ng-if="profile.id == 'custom'" class="row mt-2">
                     <div class="col-sm-4">
                        <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_ICON_CLASS'); ?></label>
                     </div>
                     <div class="col-sm-8">
                        <input type="text" ng-model="profile.icon" class="form-control" autocomplete="off" placeholder="fab fa-youtube">
                     </div>
                  </div>
                  <div ng-if="profile.id == 'custom'" class="row mt-2">
                     <div class="col-sm-4">
                        <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_TITLE'); ?></label>
                     </div>
                     <div class="col-sm-8">
                        <input type="text" ng-model="profile.title" class="form-control" autocomplete="off" placeholder="Youtube">
                     </div>
                  </div>
                  <div ng-if="profile.id != 'custom'" class="row mt-2" ng-show="profile.icons.length > 1">
                     <div class="col-sm-4">
                        <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_ICON'); ?></label>
                     </div>
                     <div class="col-sm-8">
                        <ul class="list-inline m-0">
                           <li class="select-icon" ng-click="profile.icon = icon" ng-class="{'active':icon == profile.icon}" ng-repeat="icon in profile.icons"><i class="{{ icon}}"></i></li>
                        </ul>
                     </div>
                  </div>
                  <div ng-if="profile.id == 'custom'" class="mt-2 row">
                     <div class="col-sm-4">
                        <label class="astroid-label"><?php echo JText::_('TPL_ASTROID_COLOR'); ?></label>
                     </div>
                     <div class="col-sm-8">
                        <input type="text" color-picker ng-model="profile.color" class="form-control" autocomplete="off">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="mt-4 text-center">
            <button ng-click="addCustomProfile()" type="button" class="btn btn-round btn-wide btn-lg btn-astroid"><?php echo JText::_('TPL_ASTROID_ADD_CUSTOM_SOCIAL_LABEL'); ?></button>
         </div>
      </div>
      <div class="col-sm-3">
         <h3><?php echo JText::_('TPL_ASTROID_SOCIAL_BRANDS'); ?></h3>
         <input type="text" ng-model="searchSocialProfile" placeholder="<?php echo JText::_('TPL_ASTROID_SOCIAL_SEARCH_LABEL'); ?>" class="form-control mb-3" />
         <small><em class="mb-3 d-block text-center text-info"><?php echo JText::_('TPL_ASTROID_ADD_PROFILE'); ?></em></small>
         <div ng-click="selectSocialProfile(profile)" ng-repeat="profile in astroidsocialprofiles| filter:searchSocialProfile track by $index" class="card mb-2 social-profile-item" style="cursor: pointer">
            <div class="border radius p-2"><i class="{{ profile.icon}}"></i> {{ profile.title}}</div>
         </div>
      </div>
   </div>
</div>