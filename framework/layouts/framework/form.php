<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
extract($displayData);

jimport('joomla.filesystem.helper');

$form = $element->getForm();
$fieldsets = $form->getFieldsets();
$active = false;
?>

<div class="ezlb-pop-header">
   <ul class="nav nav-tabs" role="tablist">
      <?php foreach ($fieldsets as $key => $fieldset) { ?>
      <li class="nav-item">
        <a class="nav-link<?php echo !$active ? ' active' : ''; ?>" id="element-form-<?php echo $fieldset->name; ?>-tab" data-toggle="tab" href="#element-form-<?php echo $fieldset->name; ?>" role="tab" aria-controls="element-form-<?php echo $fieldset->name; ?>" aria-selected="<?php echo $active ? 'true' : 'false'; ?>"><?php echo JText::_($fieldset->label); ?></a>
      </li>
      <?php $active = true; } $active = false; ?>
   </ul>
   <span class="dismiss" id="element-settings-close"><i class="fas fa-times"></i></span>
   <span class="compress"><i class="fas fa-compress"></i></span>
   <span class="expand"><i class="fas fa-expand"></i></span>
</div>
<div class="row">
   <div class="col-12">
      <form id="element-form-<?php echo $element->type; ?>">
         <div class="tab-content">
            <?php foreach ($fieldsets as $key => $fieldset) { ?>
               <div class="tab-pane astroid-tab-pane fade show<?php echo !$active ? ' active' : '';
            $active = true;
               ?>" id="element-form-<?php echo $fieldset->name; ?>" role="tabpanel" aria-labelledby="element-form-<?php echo $fieldset->name; ?>-tab">
                    <?php $fields = $form->getFieldset($key); ?>
                    <?php
                    $groups = [];
                    foreach ($fields as $key => $field) {
                       if ($field->type == 'astroidgroup') {
                          $groups[$field->fieldname] = ['title' => $field->getAttribute('title', ''), 'icon' => $field->getAttribute('icon', ''), 'description' => $field->getAttribute('description', ''), 'fields' => []];
                       }
                    }
                    $groups['none'] = ['fields' => []];


                    foreach ($fields as $key => $field) {
                       if ($field->type == 'astroidgroup') {
                          continue;
                       }
                       $field_group = $field->getAttribute('astroidgroup', 'none');
                       $groups[$field_group]['fields'][] = $field;
                    }

                    foreach ($groups as $groupname => $group) {
                       if (empty($group['fields'])) {
                          continue;
                       }
                       ?>
                     <div class="pb-4">
                        <?php
                        if (!empty($group['title']) && !empty($group['fields'])) {
                           echo '<h3>' . (!empty($group['icon']) ? '<i class="' . $group['icon'] . '"></i>&nbsp;' : '') . JText::_($group['title']) . '</h3>';
                           if (!empty($group['description'])) {
                              echo '<p><small>' . JText::_($group['description']) . '</small></p>';
                           }
                        }
                        ?>
                        <div class="border-0 astroid-form-fieldset-section<?php echo!empty($group['title']) ? ' labeled' : ' non-labeled'; ?>">
                           <?php
                           foreach ($group['fields'] as $field) {
                              if ($field->type == 'astroidgroup') {
                                 continue;
                              }
                              if ($field->type == 'layout' || $field->type == 'astroidheading' || $field->type == 'Hidden') {
                                 echo $field->input;
                              } else {
                                 $ngHide = Astroid\Helper::replaceRelationshipOperators($field->getAttribute('ngHide'));
                                 $ngShow = Astroid\Helper::replaceRelationshipOperators($field->getAttribute('ngShow'));
                                 ?>
                                 <div<?php echo!empty($ngHide) ? ' ng-hide="' . $ngHide . '"' : ''; ?><?php echo!empty($ngShow) ? ' ng-show="' . $ngShow . '"' : ''; ?> class="form-group">
                                    <div class="row">
            <?php if ($field->label !== false) { ?>
                                          <div class="col-sm-5">
                                             <label for="<?php echo $field->name; ?>" class="astroid-label"><?php echo $field->label; ?></label>
                                                <?php if (!empty($field->getAttribute('description'))) { ?>
                                                <div class="help-block">
                                                <?php echo JText::_($field->getAttribute('description')); ?>
                                                </div>
               <?php } ?>
                                          </div>
                                          <div class="col-sm-7" data-fieldset="astroid-tab-<?php echo $fieldset->name; ?>">
                                          <?php echo $field->input; ?>
                                          </div>
                                          <?php } else { ?>
                                          <div class="col-sm-12" data-fieldset="astroid-tab-<?php echo $fieldset->name; ?>">
               <?php echo $field->input; ?>
                                          </div>
                                          <div class="col-sm-12">
                                             <?php if (!empty($field->getAttribute('description'))) { ?>
                                                <div class="help-block"><?php echo JText::_($field->getAttribute('description')); ?></div>
                                          <?php } ?>
                                          </div>
            <?php } ?>
                                    </div>
                                 </div>
                              <?php } ?>
      <?php } ?>
                        </div>
                     </div>
               <?php } ?>
               </div>
<?php } ?>
         </div>
      </form>
   </div>
</div>