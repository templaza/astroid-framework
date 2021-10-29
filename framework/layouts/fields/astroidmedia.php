<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 *
 * @var   string   $preview         The preview image relative path
 * @var   integer  $previewHeight   The image preview height
 * @var   integer  $previewWidth    The image preview width
 * @var   string   $asset           The asset text
 * @var   string   $authorField     The label text
 * @var   string   $folder          The folder text
 * @var   string   $link            The link text
 */
extract($displayData);

$mediaType = 'IMAGE';
if($media=='videos'){
   $mediaType = 'VIDEO';
}
$params = JComponentHelper::getParams('com_media');
?>
<div astroidmediagallery  ng-model="<?php echo $fieldname; ?>" ng-init="selectMedia = false;Imgpath='<?php echo $params->get('image_path', 'images'); ?>'" >
   <input type="hidden" class="image-value" name="<?php echo $name; ?>" ng-value="<?php echo $fieldname; ?>" />
   <input type="hidden" id="dropzone_folder_<?php echo $id; ?>" ng-value='gallery.current_folder' />
   
   <div class="astroid-media-preview astroid-zoom-animation" ng-show="<?php echo $fieldname; ?> != ''">
      <?php if($media=='images') { ?>
      <img ng-src="{{ getImageUrl()}}" />
      <?php } ?>
      <?php if($media=='videos') { ?>
      <a class="text-center" target="_blank" ng-href="{{ getImageUrl()}}">
         <span class="d-block"><i class="fas fa-video"></i></span>
         <span class="d-block">{{ getFileName() }}</span>
      </a>
      <?php } ?>
   </div>
   <div class="clearfix"></div>
   <ul class="list-inline astroid-media-selctor">
      <li class="list-inline-item">
         <button ng-show="<?php echo $fieldname; ?> == ''" ng-click="selectMedia = true; getLibrary('','astroid-media-tab-library-<?php echo $id; ?>');" type="button" class="btn astroid-fade-fast-animation btn-white float-left btn-round"><?php echo JText::_('TPL_ASTROID_SELECT_'.$mediaType); ?></button>
         <button ng-show="<?php echo $fieldname; ?> != ''" ng-click="selectMedia = true; getLibrary('','astroid-media-tab-library-<?php echo $id; ?>');" type="button" class="btn btn-white float-left btn-round astroid-fade-fast-animation"><?php echo JText::_('TPL_ASTROID_CHANGE_'.$mediaType); ?></button>
      </li>
      <li ng-show="<?php echo $fieldname; ?> != ''" class="list-inline-item astroid-fade-fast-animation">
         <button ng-click="clearImage('<?php echo $id; ?>');" type="button" class="btn btn-link float-left text-danger"><?php echo JText::_('ASTROID_CLEAR'); ?></button>
      </li>
   </ul>
   
   <!-- media pop up -->
   <div class="ezlb-pop" ng-media-class="{'open':selectMedia}">
      <div class="ezlb-pop-overlay"></div>
      <div class="ezlb-pop-body">
         <div class="astroid-pop-loading astroid-fade-animation" ng-init="loading = false" ng-show='loading'><span><span class="fas fa-circle-notch fa-spin"></span></span></div>
         <div class="ezlb-pop-header">
            <ul class="nav nav-tabs" role="tablist">
               <li class="nav-item">
                  <a href="javascript:void(0);" class="active nav-link" id="astroid-media-tab-upload-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-media-upload-<?php echo $id; ?>" role="tab" aria-controls="astroid-media-upload-<?php echo $id; ?>" aria-selected="true"><?php echo JText::_('TPL_ASTROID_UPLOAD_FILES'); ?></a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="javascript:void(0);" id="astroid-media-tab-library-<?php echo $id; ?>" data-toggle="tab" data-target="#astroid-media-library-<?php echo $id; ?>" role="tab"><?php echo JText::_('TPL_ASTROID_MEDIA_LIB'); ?></a>
               </li>
            </ul>
            <span class="dismiss" ng-click="selectMedia = false"><i class="fas fa-times"></i></span>
         </div>
         <div class="">
            <div class="tab-content" id="astroid-media-tab-content">
               <div class="tab-pane show active" id="astroid-media-upload-<?php echo $id; ?>" role="tabpanel" aria-labelledby="astroid-media-tab-upload-<?php echo $id; ?>">
                  <div data-media="<?php echo $media; ?>" dropzone data-dropzone-dir="false" data-dropzone-id="<?php echo $id; ?>" class="dropzone astroid-dropzone">
                     <div class="dz-message">
                        <div class="dz-message-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="dz-message-text"><?php echo JText::_('TPL_ASTROID_DROP_FILES'); ?></div>
                        <div class="dz-message-button"><span class="btn btn-dark btn-round"><?php echo JText::_('TPL_ASTROID_SELECT_FILES'); ?></span></div>
                     </div>
                     <div class="dz-message-hover">
                        <div class="dz-message-text text-white"><?php echo JText::_('TPL_ASTROID_DROP_FILES'); ?></div>
                     </div>
                  </div>
               </div>
               <div class="tab-pane" id="astroid-media-library-<?php echo $id; ?>" role="tabpanel" aria-labelledby="astroid-media-tab-library-<?php echo $id; ?>">
                  <div class="row mt-2">
                     <div class="col-12">
                        <div class="row">
                           <div class="col">
                              <nav aria-label="breadcrumb" ng-if="bradcrumb.length >= 2 && folder != ''">
                                 <ol class="breadcrumb">
                                    <li ng-repeat="item in bradcrumb" class="breadcrumb-item" ng-media-class="{'active':$last}"><a ng-if="!$last" ng-click="getLibrary(item.url,'astroid-media-tab-library-<?php echo $id; ?>')" href="javascript:void(0);">{{ item.name}}</a><span ng-if="$last">{{ item.name}}</span></li>
                                 </ol>
                              </nav>
                              <nav aria-label="breadcrumb" ng-if="folder == ''">
                                 <ol class="breadcrumb">
                                    <li class="breadcrumb-item active"><span><?php echo JText::_('TPL_ASTROID_LIBRARY'); ?></span></li>
                                 </ol>
                              </nav>
                           </div>
                           <div class="col-auto py-1">
                              <div class="btn-toolbar" role="toolbar">
                                 <div class="btn-group mr-1" role="group">
                                    <a ng-click="newFolder('astroid-media-tab-library-<?php echo $id; ?>')" href="javascript:void(0);" class="btn btn-secondary"><i class="fas fa-plus"></i> <?php echo JText::_('TPL_ASTROID_NEW_FOLDER'); ?></a>
                                 </div>
                                 <div data-media="<?php echo $media; ?>" data-dropzone-dir="true" dropzone data-dropzone-id="<?php echo $id; ?>" class="dropzone btn-group p-0 border-0 mr-1" role="group">
                                    <div class="dz-message m-0">
                                    <button type="button" class="dz-message-button btn btn-dark"><i class="fas fa-cloud-upload-alt"></i> <?php echo JText::_('TPL_ASTROID_UPLOAD_BTN_LBL');?></button>
                                    </div>
                                 </div>
                                 <div class="btn-group" role="group">
                                    <a ng-click="getLibrary(folder, 'astroid-media-tab-library-<?php echo $id; ?>')" href="javascript:void(0);" class="btn btn-white"><i class="fas fa-sync-alt"></i> <?php echo JText::_('TPL_ASTROID_REFRESH'); ?></a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="file-manager mb-5">
                           <div class="row">
                              <div ng-click="getLibrary(folder.path_relative,'astroid-media-tab-library-<?php echo $id; ?>')" class="col-2 file-manager-item file-manager-folder" ng-repeat="folder in gallery.folders">
                                 <span class="icon d-flex fas fa-folder align-items-center justify-content-center"></span>
                                 <span class="name">{{ folder.name}}</span>
                              </div>
                              <div ng-click="selectImage('<?php echo $id; ?>', image.path_relative)" class="col-2 file-manager-item file-manager-image" ng-repeat="image in gallery.<?php echo $media; ?>">
                                 <span class="icon d-flex align-items-center justify-content-center">
                                    <?php if($media=='images'){ ?>
                                    <img ng-src="{{ getImgUrl(image.path_relative)}}" />
                                    <?php } ?>
                                    <?php if($media=='videos'){ ?>
                                    <span class="icon d-flex fas fa-video align-items-center justify-content-center"></span>
                                    <?php } ?>
                                 </span>
                                 <span class="name">{{ image.name}}</span>
                              </div>
                              <div class="col-12 no-files" ng-if="gallery.folders.length == 0 && gallery.<?php echo $media; ?>.length == 0">
                                 <span><?php echo JText::_('TPL_ASTROID_NO_FILES'); ?></span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <!--<div class="col-3">
                        <div style="min-height:400px;" data-dropzone-dir="true" dropzone data-dropzone-id="<?php echo $id; ?>" class="dropzone astroid-dropzone">
                           <div class="dz-message">
                              <div class="dz-message-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                              <div class="dz-message-text"><?php echo JText::_('TPL_ASTROID_DROP_FILES'); ?></div>
                              <div class="dz-message-button"><span class="btn btn-dark btn-round"><?php echo JText::_('TPL_ASTROID_SELECT_FILES'); ?></span></div>
                           </div>
                           <div class="dz-message-hover">
                              <div class="dz-message-text text-white"><?php echo JText::_('TPL_ASTROID_DROP_FILES'); ?></div>
                           </div>
                        </div>
                        <div ng-click="newFolder('astroid-media-tab-library-<?php echo $id; ?>')" class="w-100 my-3 text-center"><button type="button" class="btn btn-dark btn-round"><?php echo JText::_('TPL_ASTROID_NEW_FOLDER'); ?></button></div>
                     </div>-->
                  </div>
                  <div class="clearfix"></div>
               </div>
             </div>
         </div>
      </div>
   </div>
</div>