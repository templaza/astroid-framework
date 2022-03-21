<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;

extract($displayData);

$astDocument = Astroid\Framework::getDocument();
$astDocument->loadFontAwesome();


$document = \JFactory::getDocument();
$document->addCustomTag($astDocument->getStylesheets());
$document->addStyleSheet('https://fonts.googleapis.com/css?family=Nunito:300,400,600');
$document->addStyleSheet(JURI::root(false) . 'media/astroid/assets/css/astroid-menu-options.css' . '?v=' . $document->getMediaVersion());
$document->addScriptDeclaration('var astroidSearchUrl = "' . JURI::root() . 'administrator/index.php?option=com_ajax&astroid=search&format=html";');

$semanticComponents = ['icon', 'api', 'transition', 'dropdown'];

foreach ($semanticComponents as $semanticComponent) {
    $semanticComponentPath = 'vendor' . '/' . 'semantic-ui' . '/' . 'components' . '/' . $semanticComponent . '.min.css';
    if (file_exists(JPATH_SITE . '/' . 'media' . '/' . 'astroid' . '/' . 'assets' . '/' . $semanticComponentPath)) {
        $document->addStyleSheet(JURI::root(false) . 'media/astroid/assets/' . $semanticComponentPath . '?v=' . $document->getMediaVersion());
    }
}


$megamenu = (bool) @$value['megamenu'];
$megamenu = empty($megamenu) ? 0 : 1;
$showtitle = (bool) @$value['showtitle'];
$showtitle = empty($showtitle) ? 0 : 1;
$icon = (string) @$value['icon'];
$customclass = (string) @$value['customclass'];
$subtitle = (string) @$value['subtitle'];
$width = (string) @$value['width'];
$megamenu_width = (string) @$value['megamenu_width'];
$rows = (string) @$value['rows'];
$alignment = (string) @$value['alignment'];
$alignment = !empty($alignment) ? $alignment : 'left';
$megamenu_direction = (string) @$value['megamenu_direction'];
$megamenu_direction = !empty($megamenu_direction) ? $megamenu_direction : 'left';
$rows = !empty($rows) ? json_decode($rows) : [];
// For badge
$badge = (bool) @$value['badge'];
$badge = empty($badge) ? 0 : 1;
$badge_text = (string) @$value['badge_text'];
$badge_color = (string) @$value['badge_color'];
$badge_bgcolor = (string) @$value['badge_bgcolor'];
$app = JFactory::getApplication('site');
$menu = $app->getMenu('site');

$itemId = $app->input->get('id');
$menuItem = $menu->getItem($itemId);
if (isset($menuItem->language) && $menuItem->language == '*') {
    $items = $menu->getItems(['menutype'], $menu_type);
} else {
    $items = $menu->getItems(['menutype', 'language'], $menu_type);
}

$children = [];

foreach ($items as $i => $item) {
    if ($item->parent_id != $menu_item_id) {
        continue;
    }
    $children[] = $item;
}
?>
<style>
    #astroid-menu-options .form-control.astroid-module-search::placeholder {
        color: #fff;
    }
</style>
<div id="astroid-menu-options" ng-app="astroid-framework">
    <div id="astroidMenuController" ng-controller="astroidMenuController">
        <div class="ezlb-pop" ng-class="{'open': chooseRowColumns.open}">
            <div class="ezlb-pop-overlay"></div>
            <div class="ezlb-pop-body">
                <div class="ezlb-pop-header">
                    <span class="title"><?php echo JText::_('TPL_ASTROID_MENU_SELECT_A_GRID_LAYOUT'); ?></span>
                    <span class="dismiss" ng-click="chooseRowColumns.open = 0; chooseRowColumns.row = null;"><i class="fas fa-times"></i></span>
                </div>
                <div class="ezlb-grid-items">
                    <div class="row m-0">
                        <div ng-click="updateRow(chooseRowColumns.row, grid);" ng-repeat="grid in grids track by $index" class="col-3 ezlb-grid-item">
                            <div class="row m-0 p-0">
                                <div ng-repeat="gridsize in grid track by $index" style="padding: 0 3px;" class="ezlb-grid-item-col col-{{ gridsize}}"><span>{{ gridsize}}</span></div>
                            </div>
                        </div>
                        <div ng-click="updateRow(chooseRowColumns.row, 'custom');" class="col-3 ezlb-grid-item">
                            <div class="row m-0 p-0">
                                <div style="padding: 0 3px;" class="ezlb-grid-item-col col-12"><span>Custom</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ezlb-pop" ng-class="{'open': chooseModule.open}">
            <div class="ezlb-pop-overlay"></div>
            <div class="ezlb-pop-body">
                <div class="ezlb-pop-header">
                    <span class="title"><?php echo JText::_('TPL_ASTROID_MENU_SELECT_AN_ITEM'); ?></span>
                    <span class="dismiss" ng-click="chooseModule.open = 0; chooseModule.row = null; chooseModule.column = null;"><i class="fas fa-times"></i></span>
                    <input style="float: right;line-height: 60px;" type="text" class="form-control astroid-module-search" ng-model="searchModule" placeholder="<?php echo JText::_('TPL_ASTROID_MENU_SEARCH_MODULE'); ?>" />
                </div>
                <div class="ezlb-grid-items">
                    <div class="row m-0">
                        <div class="col-8">
                            <div class="row">
                                <div class="col-12">
                                    <h3><?php echo JText::_('TPL_ASTROID_MENU_MODULES'); ?></h3>
                                </div>
                                <div ng-click="addElement(module)" ng-repeat="module in joomla_modules| filter:searchModule track by $index" class="col-6 ezlb-grid-item">
                                    <div class="row m-0 p-0">
                                        <div class="ezlb-grid-item-col col-12"><span class="title"><span style="border: none; display: inline;" ng-class="{'published':module.published == 1,'unpublished':module.published == 0}"><i class="fas fa-circle"></i></span> {{ module.title}} <span style="border: none; display: inline;line-height:30px;" class="info" popover data-html="true" data-placement="bottom" data-content="Status: <strong>{{ module.published==1 ? 'Published' : 'Unpublished' }}</strong><br/>Show Title: <strong>{{ module.showtitle ? 'Enabled' : 'Disabled' }}</strong><br/>Access: <strong>{{ module.access_title}}</strong><br/>Position: <strong>{{ module.position}}</strong>" data-trigger="hover" data-title="{{ module.title}}"><i class="fas fa-info-circle"></i></span></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <h3><?php echo JText::_('TPL_ASTROID_MENU_SUB_MENUS'); ?></h3>
                            <div class="row">
                                <?php foreach ($children as $child) { ?>
                                    <div class="col-12 ezlb-grid-item">
                                        <div class="row m-0 p-0">
                                            <div ng-click="addElement({'type':'submenu', 'title':'<?php echo addslashes(htmlspecialchars($child->title)); ?> (Child Item)', 'id':'<?php echo $child->id; ?>'})" class="ezlb-grid-item-col col-12">
                                                <span class="title"><?php echo $child->title; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <h2><?php echo JText::_('TPL_ASTROID_MENU_OPTIONS'); ?></h2>
        <p><?php echo JText::_('TPL_ASTROID_MENU_TEXT'); ?></p>
        <div class="astroid-form-fieldset-section">
            <div class="row">
                <div class="col-<?php echo $menu_item_level != 1 ? 'auto' : '3'; ?>">
                    <div class="row">
                        <div class="col-6" style="<?php echo $menu_item_level != 1 ? 'display:none' : ''; ?>">
                            <label class="astroid-label" id="<?php echo $id; ?>_megamenu-lbl" for="<?php echo $id; ?>_megamenu"><?php echo JText::_('TPL_ASTROID_MEGA_MENU'); ?></label>
                            <input value="<?php echo $megamenu; ?>" type="hidden" ng-model="<?php echo $id; ?>_megamenu" astroid-switch name="<?php echo $name; ?>[megamenu]" id="<?php echo $id; ?>_megamenu" />
                        </div>
                        <div class="col-<?php echo $menu_item_level != 1 ? '12' : '6'; ?>">
                            <label class="astroid-label" id="<?php echo $id; ?>_showtitle-lbl" for="<?php echo $id; ?>_showtitle">
                                <?php echo JText::_('TPL_ASTROID_SHOW_TITLE'); ?></label>
                            <input value="<?php echo $showtitle; ?>" type="hidden" ng-model="<?php echo $id; ?>_showtitle" astroid-switch name="<?php echo $name; ?>[showtitle]" id="<?php echo $id; ?>_showtitle" />
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="row">
                        <div class="col-3">
                            <label class="astroid-label" id="<?php echo $id; ?>_subtitle-lbl" for="<?php echo $id; ?>_subtitle"><?php echo JText::_('TPL_ASTROID_SUBTITLE'); ?></label>
                            <input type="text" name="<?php echo $name; ?>[subtitle]" id="<?php echo $id; ?>_subtitle" class="form-control" value="<?php echo $subtitle; ?>" />
                        </div>
                        <div class="col-6">
                            <label class="astroid-label" id="<?php echo $id; ?>_icon-lbl" for="<?php echo $id; ?>_icon"><?php echo JText::_('TPL_ASTROID_ICON'); ?></label>
                            <div>
                                <div class="ui fluid search selection dropdown astroid-icon-selector">
                                    <input type="hidden" value="<?php echo $icon; ?>" name="<?php echo $name; ?>[icon]">
                                    <i class="dropdown icon"></i>
                                    <div class="default text"><?php echo JText::_('TPL_ASTROID_SELECT_ICON'); ?></div>
                                    <div class="menu"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <label class="astroid-label" id="<?php echo $id; ?>_customclass-lbl" for="<?php echo $id; ?>_customclass"><?php echo JText::_('ASTROID_CUSTOM_CLASS'); ?></label>
                            <input type="text" name="<?php echo $name; ?>[customclass]" id="<?php echo $id; ?>_customclass" class="form-control" value="<?php echo $customclass; ?>" />
                        </div>
                    </div>
                </div>
                <div style="<?php echo $menu_item_level != 1 ? 'display:none' : ''; ?>" class="col-3">
                    <div class="row">
                        <div ng-hide="<?php echo $id; ?>_megamenu" class="col-4">
                            <label class="astroid-label" id="<?php echo $id; ?>_width-lbl" for="<?php echo $id; ?>_width"><?php echo JText::_('TPL_ASTROID_MENU_OPTIONS_WIDTH'); ?></label>
                            <input type="text" autocomplete="off" name="<?php echo $name; ?>[width]" placeholder="280px" value="<?php echo $width; ?>" id="<?php echo $id; ?>_width" class="form-control" />
                        </div>
                        <div ng-show="<?php echo $id; ?>_megamenu" class="col-4">
                            <label class="astroid-label" id="<?php echo $id; ?>_megamenu_width-lbl" for="<?php echo $id; ?>_megamenu_width"><?php echo JText::_('TPL_ASTROID_MENU_OPTIONS_MEGAMENU_WIDTH'); ?></label>
                            <input type="text" autocomplete="off" name="<?php echo $name; ?>[megamenu_width]" placeholder="980px" value="<?php echo $megamenu_width; ?>" id="<?php echo $id; ?>_megamenu_width" class="form-control" />
                        </div>
                        <div ng-hide="<?php echo $id; ?>_megamenu" class="col-8">
                            <label class="astroid-label" id="<?php echo $id; ?>_alignment-lbl" for="<?php echo $id; ?>_alignment"><?php echo JText::_('TPL_ASTROID_MENU_OPTIONS_DROPDOWN_ALIGNMENT'); ?></label>
                            <div>
                                <div class="ui fluid search selection dropdown astroid-direction-selector">
                                    <input type="hidden" value="<?php echo $alignment; ?>" name="<?php echo $name; ?>[alignment]" id="<?php echo $id; ?>_alignment">
                                    <i class="dropdown icon"></i>
                                    <div class="default text"><?php echo JText::_('TPL_ASTROID_MENU_OPTIONS_SELECT_ALIGNMENT'); ?></div>
                                    <div class="menu">
                                        <div class="item" data-value="left"><i class="fas fa-align-left"></i> <?php echo JText::_('JGLOBAL_LEFT'); ?></div>
                                        <div class="item" data-value="right"><i class="fas fa-align-right"></i> <?php echo JText::_('JGLOBAL_RIGHT'); ?></div>
                                        <div class="item" data-value="center"><i class="fas fa-align-center"></i> <?php echo JText::_('JGLOBAL_CENTER'); ?></div>
                                        <div class="item" data-value="full"><i class="fas fa-window-maximize"></i> <?php echo JText::_('TPL_ASTROID_CONTAINER'); ?></div>
                                        <div class="item" data-value="edge"><i class="fas fa-bars"></i> <?php echo JText::_('TPL_ASTROID_FULL'); ?></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div ng-show="<?php echo $id; ?>_megamenu" class="col-8">
                            <label class="astroid-label" id="<?php echo $id; ?>_megamenu_direction-lbl" for="<?php echo $id; ?>_megamenu_direction"><?php echo JText::_('TPL_ASTROID_MENU_OPTIONS_DROPDOWN_ALIGNMENT'); ?></label>

                            <div>
                                <div class="ui fluid search selection dropdown astroid-direction-selector">
                                    <input type="hidden" value="<?php echo $megamenu_direction; ?>" name="<?php echo $name; ?>[megamenu_direction]" id="<?php echo $id; ?>_megamenu_direction">
                                    <i class="dropdown icon"></i>
                                    <div class="default text"><?php echo JText::_('TPL_ASTROID_MENU_OPTIONS_SELECT_DIRECTION'); ?></div>
                                    <div class="menu">
                                        <div class="item" data-value="left"><i class="fas fa-align-left"></i> <?php echo JText::_('JGLOBAL_LEFT'); ?></div>
                                        <div class="item" data-value="right"><i class="fas fa-align-right"></i> <?php echo JText::_('JGLOBAL_RIGHT'); ?></div>
                                        <div class="item" data-value="center"><i class="fas fa-align-center"></i> <?php echo JText::_('JGLOBAL_CENTER'); ?></div>
                                        <div class="item" data-value="full"><i class="fas fa-window-maximize"></i> <?php echo JText::_('TPL_ASTROID_CONTAINER'); ?></div>
                                        <div class="item" data-value="edge"><i class="fas fa-bars"></i> <?php echo JText::_('TPL_ASTROID_FULL'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div ng-show="<?php echo $id; ?>_megamenu" class="astroid-form-fieldset-section">
            <br />
            <h3><?php echo JText::_('TPL_ASTROID_MEGA_MENU_OPTIONS'); ?></h3>
            <p><?php echo JText::_('TPL_ASTROID_MEGA_MENU_TEXT'); ?></p>
            <textarea style="display: none" name="<?php echo $name; ?>[rows]">{{ rows}}</textarea>
            <div class="row">
                <div class="col-12" style="padding: 0 60px;">
                    <div class="ezlb">
                        <div class="ezlb-section row my-3">
                            <div class="col-12 ezlb-content" ng-sortable="{draggable:'.ezlb-row', animation: 100, handle: '.ezlb-row-handle'}">
                                <div ng-repeat="row in rows track by $index" class="ezlb-row row ezlb-row-{{ rowIndex}}" ng-init="rowIndex = $index">
                           <span class="ezlb-toolbar">
                              <span data-astroid-tooltip="<?php echo JText::_('TPL_ASTROID_EDIT_GRID_ROW'); ?>" class="ezlb-action" ng-click="chooseRowColumns.open = 1; chooseRowColumns.row = rowIndex;"><i class="fas fa-columns"></i></span>
                              <span data-astroid-tooltip="<?php echo JText::_('TPL_ASTROID_REMOVE_ROW'); ?>" ng-click="removeRow(rowIndex)" class="ezlb-action text-danger"><i class="fas fa-trash-alt"></i></span>
                           </span>
                                    <span class="ezlb-toolbar toolbar-left">
                              <span class="ezlb-action ezlb-row-handle set-align" data-astroid-tooltip="<?php echo JText::_('TPL_ASTROID_MOVE_ROW'); ?>" ng-show="rows.length > 1"><i class="fas fa-arrows-alt"></i></span>
                           </span>
                                    <div class="col-12">
                                        <div class="row" ng-sortable="{draggable: '.ezlb-col',animation: 100}">
                                            <div ng-repeat="column in row.cols track by $index" class="ezlb-col col-{{ column.size}} ezlb-col-{{ rowIndex}}-{{ columnIndex}} {{ column.elements.length == 0 ? 'ezlb-col-empty' : '' }}" ng-init="columnIndex = $index">
                                                <div ng-if="column.elements.length != 0" class="ezlb-col-overlay"></div>
                                                <div ng-if="column.elements.length == 0" ng-click="chooseModule.open = 1;chooseModule.row = rowIndex; chooseModule.column = columnIndex;" class="ezlb-add-element">
                                                </div>

                                                <div ng-show="column.elements.length != 0" class="ezlb-elements" ng-sortable="{draggable: '.ezlb-element',animation: 100, 'handle': '.ezlb-element-handle'}">
                                                    <div ng-repeat="element in column.elements track by $index" ng-init="elementIndex = $index" class="ezlb-element">

                                          <span class="ezlb-toolbar">
                                             <span ng-show="column.elements.length > 1" class="ezlb-action ezlb-element-handle" data-astroid-tooltip="<?php echo JText::_('TPL_ASTROID_MOVE_ELEMENT'); ?>"><i class="fas fa-arrows-alt"></i></span>
                                             <span data-astroid-tooltip="<?php echo JText::_('TPL_ASTROID_REMOVE_ELEMENT'); ?>" class="ezlb-action text-danger" ng-click="removeElement(elementIndex, columnIndex, rowIndex);"><i class="fas fa-trash-alt"></i></span>
                                          </span>

                                                        <span class="element-title">{{ element.title}}</span>
                                                        <span data-astroid-tooltip="<?php echo JText::_('TPL_ASTROID_ADD_ELEMENT_COLUMN'); ?>" ng-show="elementIndex == column.elements.length - 1" ng-click="chooseModule.open = 1;chooseModule.row = rowIndex; chooseModule.column = columnIndex;" class="ezlb-add-element"><i class="fas fa-plus"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="w-100" />
                        </div>
                    </div>
                    <br />
                    <br />
                    <div class="text-center">
                        <span ng-click="addRow()" class="d-inline ezlb-btn"><i class="fas fa-plus"></i> <?php echo JText::_('TPL_ASTROID_MENU_OPTIONS_ADD_ROW'); ?></span>
                    </div>
                    <br />
                    <br />
                </div>
            </div>
        </div>
        <hr>
        <div class="astroid-badge" id="astroid-badge">
            <h2><?php echo JTEXT::_('TPL_ASTROID_MEGA_BADGE_OPTIONS'); ?></h2>
            <p><?php echo JTEXT::_('TPL_ASTROID_MEGA_BADGE_OPTIONS_TEXT'); ?></p>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-3">
                            <label class="astroid-label" id="<?php echo $id; ?>_badge-lbl" for="<?php echo $id; ?>_badge"><?php echo JText::_('TPL_ASTROID_MENU_BADGE'); ?></label>
                            <input value="<?php echo $badge; ?>" type="hidden" ng-model="<?php echo $id; ?>_badge" astroid-switch name="<?php echo $name; ?>[badge]" id="<?php echo $id; ?>_badge" />
                        </div>
                        <div class="col-3" ng-show="<?php echo $id; ?>_badge">
                            <label class="astroid-label" id="<?php echo $id; ?>_badge_text-lbl" for="<?php echo $id; ?>_badge-text"><?php echo JText::_('TPL_ASTROID_MENU_BADGE_TEXT'); ?></label>
                            <input type="text" name="<?php echo $name; ?>[badge_text]" id="<?php echo $id; ?>_badge_text" class="form-control" value="<?php echo $badge_text; ?>" />
                        </div>
                        <div class="col-3" ng-show="<?php echo $id; ?>_badge">
                            <label class="astroid-label" id="<?php echo $id; ?>_badge_color-lbl" for="<?php echo $id; ?>_badge-color"><?php echo JText::_('TPL_ASTROID_MENU_BADGE_COLOR'); ?></label>
                            <input type="text" name="<?php echo $name; ?>[badge_color]" id="<?php echo $id; ?>_badge_color" class="form-control" value="<?php echo $badge_color; ?>" />
                        </div>
                        <div class="col-3" ng-show="<?php echo $id; ?>_badge">
                            <label class="astroid-label" id="<?php echo $id; ?>_badge_bgcolor-lbl" for="<?php echo $id; ?>_badge-bgcolor"><?php echo JText::_('TPL_ASTROID_MENU_BADGE_BGCOLOR'); ?></label>
                            <input type="text" name="<?php echo $name; ?>[badge_bgcolor]" id="<?php echo $id; ?>_badge_bgcolor" class="form-control" value="<?php echo $badge_bgcolor; ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var spectrumConfig = {
        showInput: true,
        showInitial: false,
        allowEmpty: true,
        showAlpha: true,
        disabled: false,
        showPalette: true,
        showPaletteOnly: false,
        showSelectionPalette: true,
        showButtons: false,
        localStorageKey: "astroid.colors",
        preferredFormat: "rgb",
        palette: [
            ["#fff", "#f8f9fa", "#dee2e6", "#adb5bd", "#495057", "#343a40", "#212529", "#000"],
            ["#007bff", "#8445f7", "#ff4169", "#c4183c", "#fb7906", "#ffb400", "#17c671", "#00b8d8"]
        ],
    };
</script>
<script>

</script>
<?php
$semanticComponents = ['icon', 'transition', 'api', 'dropdown'];
$document = \JFactory::getDocument();
$assets = JURI::root() . 'media' . '/' . 'astroid' . '/' . 'assets' . '/';
$astroid_dir = 'media' . '/' . 'astroid';
$scripts = [];
$scripts[] = $assets . 'vendor' . '/' . 'spectrum' . '/' . 'spectrum.js?v=' . $document->getMediaVersion();

foreach ($semanticComponents as $semanticComponent) {
    $semanticComponentPath = 'vendor' . '/' . 'semantic-ui' . '/' . 'components' . '/' . $semanticComponent . '.min.js';
    if (file_exists(JPATH_SITE . '/' . 'media' . '/' . 'astroid' . '/' . 'assets' . '/' . $semanticComponentPath)) {
        $scripts[] = $assets . $semanticComponentPath . '?v=' . $document->getMediaVersion();
    }
}

$scripts[] = $assets . 'vendor' . '/' . 'angular' . '/' . 'angular.min.js?v=' . $document->getMediaVersion();
$scripts[] = $assets . 'vendor' . '/' . 'angular' . '/' . 'angular-animate.min.js?v=' . $document->getMediaVersion();
$scripts[] = $assets . 'vendor' . '/' . 'angular' . '/' . 'sortable.min.js?v=' . $document->getMediaVersion();
$scripts[] = $assets . 'vendor' . '/' . 'angular' . '/' . 'angular-legacy-sortable.js?v=' . $document->getMediaVersion();
$scripts[] = $assets . 'js' . '/' . 'astroid-framework.js?v=' . $document->getMediaVersion();
echo "<script src='" . JURI::root() . "/media/astroid/assets/vendor/jquery/jquery-3.5.1.min.js'></script>";
foreach ($scripts as $script) {
    echo "<script src='" . $script . "'></script>";
}
?>
<script>
    (function($) {
        $(function() {
            $('.astroid-icon-selector').addClass('ui fluid search selection dropdown').dropdown({
                placeholder: false,
                clearable: true,
                apiSettings: {
                    url: astroidSearchUrl + '&search=icon&query={query}'
                },
                fullTextSearch: true,
                filterRemoteData: true,
                saveRemoteData: true
            });
            $('.astroid-direction-selector').addClass('ui fluid search selection dropdown').dropdown({
                placeholder: false,
                clearable: true,
                fullTextSearch: true
            });
        });
        $("#<?php echo $id; ?>_badge_color,#<?php echo $id; ?>_badge_bgcolor").spectrum(spectrumConfig);


        var initDropdownWidth = function() {
            var _alignment = $('#<?php echo $id; ?>_alignment').val();
            if (_alignment == 'full' || _alignment == 'edge') {
                $('#<?php echo $id; ?>_width').prop('disabled', true);
            } else {
                $('#<?php echo $id; ?>_width').prop('disabled', false);
            }
        }

        $('#<?php echo $id; ?>_alignment').on('change', function() {
            initDropdownWidth();
        });
        initDropdownWidth();

        var initMegamenuWidth = function() {
            var _alignment = $('#<?php echo $id; ?>_megamenu_direction').val();
            if (_alignment == 'full' || _alignment == 'edge') {
                $('#<?php echo $id; ?>_megamenu_width').prop('disabled', true);
            } else {
                $('#<?php echo $id; ?>_megamenu_width').prop('disabled', false);
            }
        }

        $('#<?php echo $id; ?>_megamenu_direction').on('change', function() {
            initMegamenuWidth();
        });
        initMegamenuWidth();
        if ($('#astroid-menu-options').parent().prev().is(':empty')) {
            $('#astroid-menu-options').parent().prev().remove();
        }
    })(jQuery);
</script>
<script>
    class AstroidRow {
        constructor() {
            this.cols = [];
            this.type = 'row';
        }
    }

    class AstroidColumn {
        constructor() {
            this.elements = [];
            this.size = 12;
            this.type = 'column';
        }
    }

    astroidFramework.controller('astroidMenuController', function($scope) {

        // Global Variables
        // All Types of Grid
        $scope.grids = <?php echo \json_encode(Astroid\Helper\Constants::$layout_grids); ?>;

        $scope.<?php echo $id; ?>_megamenu = <?php echo $megamenu; ?>;
        $scope.<?php echo $id; ?>_badge = <?php echo $badge; ?>;
        $scope.<?php echo $id; ?>_showtitle = <?php echo $showtitle; ?>;
        $scope.joomla_modules = <?php echo \json_encode(Astroid\Helper::getModules()); ?>;
        $scope.rows = <?php echo json_encode($rows); ?>;

        $scope.chooseRowColumns = {
            open: 0,
            row: null
        };
        $scope.chooseModule = {
            open: 0,
            row: null,
            column: null
        };

        $scope.addingRow = function(_index) {
            $scope.chooseRow = {
                open: 1,
                section: _index
            };
        };

        $scope.addRow = function() {
            var _rows = $scope.rows;
            var _row = new AstroidRow();
            var _col = new AstroidColumn();
            _col.size = 12;
            _row.cols.push(_col);
            _rows.push(_row);
            $scope.rows = _rows;

            $scope.chooseRowColumns.open = 1;
            $scope.chooseRowColumns.row = (_rows.length - 1);
        };

        $scope.isValidGrid = function(_grid) {
            var _total = 0;
            _grid.forEach(function(_g) {
                _total += parseInt(_g);
            });
            if (_total == 12) {
                return true;
            } else {
                return false;
            }
        };

        $scope.getCustomGrid = function() {
            var grid = prompt("Please enter custom grid size (eg. 2+3+6+1)", "");
            if (grid != null) {
                grid = grid.split('+');
                if ($scope.isValidGrid(grid)) {
                    return grid;
                } else {
                    alert("Invalid grid size.");
                    return false;
                }
            }
            return false;
        };

        $scope.updateRow = function(_rowIndex, _grid) {

            if (_grid == 'custom') {
                var _grid = $scope.getCustomGrid();
                if (_grid === false) {
                    return false;
                }
            }

            var _columns = $scope.rows[_rowIndex].cols;
            var _updatedColumns = [];

            if (_grid.length < _columns.length) {
                // decresing columns
                _columns.forEach(function(_column, _i) {
                    if (typeof _grid[_i] != 'undefined') {
                        _column.size = _grid[_i];
                        _updatedColumns.push(_column);
                    } else {
                        var _elements = _updatedColumns[_grid.length - 1].elements;
                        _column.elements.forEach(function(_el) {
                            _elements.push(_el);
                        });
                        _updatedColumns[_grid.length - 1].elements = _elements;
                    }
                });
            } else {
                // incresing or same columns
                _grid.forEach(function(_size, _i) {
                    if (typeof _columns[_i] != 'undefined') {
                        var _c = _columns[_i];
                        _c.size = _size;
                        _updatedColumns.push(_c);
                    } else {
                        var _col = new AstroidColumn();
                        _col.size = _size;
                        _updatedColumns.push(_col);
                    }
                });
            }

            $scope.rows[_rowIndex].cols = _updatedColumns;
            $scope.chooseRowColumns = {
                open: 0,
                row: null
            };
        };

        $scope.removeRow = function(_rowIndex) {
            var c = confirm('Are you sure?');
            if (c) {
                var _rows = $scope.rows;
                _rows.splice(_rowIndex, 1);
                $scope.rows = _rows;
            }
        };

        $scope.addElement = function(module) {
            var elements = angular.copy($scope.rows[$scope.chooseModule.row].cols[$scope.chooseModule.column].elements);
            var _element = angular.copy(module);
            elements.push(_element);
            $scope.rows[$scope.chooseModule.row].cols[$scope.chooseModule.column].elements = elements;
            $scope.chooseModule.row = null;
            $scope.chooseModule.column = null;
            $scope.chooseModule.open = 0;
        };

        $scope.removeElement = function(_elementIndex, _colIndex, _rowIndex) {
            var c = confirm('Are you sure?');
            if (c) {
                var _elements = $scope.rows[_rowIndex].cols[_colIndex].elements;
                _elements.splice(_elementIndex, 1);
                $scope.rows[_rowIndex].cols[_colIndex].elements = _elements;
            }
        };

    });
</script>
<script>
    jQuery.noConflict(true);
</script>