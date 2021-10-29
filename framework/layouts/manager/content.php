<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 * 	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/header/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
$template = Astroid\Framework::getTemplate();
$document = Astroid\Framework::getDocument();
$form = Astroid\Framework::getForm();
?>
<div id="astroid-content-wrapper" class="col">
    <div class="container-fluid">
        <input type="file" accept=".json" id="astroid-settings-import" class="d-none" />
        <form id="astroid-form" action="<?php echo Astroid\Helper::getAstroidUrl('save', ['template' => $template->template . '-' . $template->id]); ?>" method="POST">
            <input type="hidden" id="astroid-admin-token" name="<?php echo JSession::getFormToken(); ?>" value="1" />
            <input type="hidden" id="export-form" name="export_settings" value="0" />
            <div class="tab-content">
                <div class="live-preview-toolbar">
                    <span onclick="Admin.showOptions()" class="btn btn-round btn-wide btn-white"><i class="fas fa-chevron-left"></i> Back</span>
                </div>
                <?php $active = false; ?>
                <?php foreach ($form->getFieldsets() as $key => $fieldset) { ?>
                    <div class="astroid-tab-pane tab-pane<?php echo $active ? ' active' : ''; ?>" id="astroid-tab-<?php echo $fieldset->name; ?>" role="tabpanel" aria-labelledby="<?php echo $fieldset->name; ?>-astroid-tab" astroid-type="<?php echo isset($fieldset->astroidtype) ? $fieldset->astroidtype : ''; ?>">
                        <?php $fields = $form->getFields($key);
                        $fields = $form->getFields($key);
                        $fieldsArr = [];
                        $order = 1;
                        $orders = [];
                        $reorders = [];
                        foreach ($fields as $field) {
                            $ordering = $field->getAttribute('after', '');
                            if (empty($ordering)) {
                                $field->ordering = $order++;
                                $fieldsArr[] = $field;
                                $orders[$field->name] = $field->ordering;
                            } else {
                                if (isset($orders[$ordering])) {
                                    $field->ordering = $orders[$ordering];
                                    $fieldsArr[] = $field;
                                    $orders[$field->name] = $field->ordering;
                                } else {
                                    $reorders[] = $field;
                                }
                            }
                        }

                        foreach ($reorders as &$reorder) {
                            $ordering = $reorder->getAttribute('after', '');
                            $reorder->ordering = $orders[$ordering];
                            $fieldsArr[] = $reorder;
                        }

                        usort($fieldsArr, 'Astroid\Helper::orderingFields');

                        ?>
                        <?php
                        $groups = [];
                        foreach ($fieldsArr as $key => $field) {
                            if ($field->type == 'astroidgroup') {
                                $groups[$field->fieldname] = ['title' => $field->getAttribute('title', ''), 'icon' => $field->getAttribute('icon', ''), 'description' => $field->getAttribute('description', ''), 'fields' => [], 'help' => $field->getAttribute('help', '')];
                            }
                        }
                        $groups['none'] = ['fields' => []];


                        foreach ($fieldsArr as $key => $field) {

                            if ($field->type == 'astroidgroup') {
                                continue;
                            }

                            if (empty($field->getAttribute('name'))) {
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
                            <div style="padding-top:20px" id="astroid-form-fieldset-section-<?php echo $groupname; ?>">
                                <?php
                                if (!empty($group['title']) && !empty($group['fields'])) {
                                    echo '<h3 class="astroid-group-title ' . (!empty($group['description']) ? 'mb-0' : '') . '">' . (!empty($group['icon']) ? '<i class="' . $group['icon'] . '"></i>&nbsp;' : '') . JText::_($group['title']) . '' . (!empty($group['help']) ? ' <a target="_blank" href="' . $group['help'] . '"><span class="far fa-question-circle"></span></a>' : '') . '</h3>';
                                    if (!empty($group['description'])) {
                                        echo '<p><small>' . JText::_($group['description']) . '</small></p>';
                                    }
                                }
                                ?>
                                <div class="astroid-form-fieldset-section<?php echo !empty($group['title']) ? ' labeled' : ' non-labeled'; ?>">
                                    <?php
                                    foreach ($group['fields'] as $field) {
                                        $document->include('manager.field', ['field' => &$field, 'fieldset' => $fieldset]);
                                    } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php $active = false; ?>
                <?php } ?>
            </div>
        </form>
    </div>