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
$form = Astroid\Framework::getForm();
?>
<script type="text/javascript">
    astroidFramework.controller('astroidController', function($scope) {
        <?php foreach ($form->getFieldsets() as $key => $fieldset) { ?>
            <?php $fields = $form->getFields($key); ?>
            <?php
            foreach ($fields as $key => $field) {
                if (strtolower($field->type) == "astroidtextarea" || $field->type == "astroidheading") {
                    continue;
                }
                if (is_string($field->value)) {
                    $value = "'" . addslashes($field->value) . "'";
                } elseif (is_array($field->value)) {
                    $value = \json_encode($value);
                } elseif (is_object($field->value)) {
                    $value = \json_encode($field->value);
                } else {
                    $value = $field->value;
                }
                echo '$scope.' . $field->fieldname . ' = ' . $value . ';';
                if ($field->type == "layout") {
                    echo '$scope.layoutfield = "' . $field->fieldname . '";';
                }
            }
            ?>
        <?php } ?>
        $scope.chosePreset = function(_name) {
            var _preset = null;
            if(_name in TEMPLATE_PRESETS){
                _preset = TEMPLATE_PRESETS[_name];
            }
            if (_preset != null) {
                for (var key in _preset.preset) {
                    if (_preset.preset.hasOwnProperty(key)) {
                        if (typeof _preset.preset[key] == 'object') {
                            for (var subkey in _preset.preset[key]) {
                                if (_preset.preset[key].hasOwnProperty(subkey)) {
                                    $scope['params_' + key + '_' + subkey] = _preset.preset[key][subkey];
                                }
                            }
                        } else {
                            $scope[key] = _preset.preset[key];
                        }
                    }
                }
            }
            $('.astroid-presets-option').removeClass('active');
            $('.astroid-presets-option-' + _name).addClass('active');
            Admin.notify('<?php echo \JText::_('TPL_ASTROID_SYSTEM_MESSAGES_PRESET'); ?>', 'success');
        }

        $scope.exportPreset = function() {
            var title = prompt("Please enter your desired name", "My Preset");
            if (title == "") {
                return false;
            }

            var _colors = {};
            presetProps.forEach(function(prop) {
                if (prop.split('.').length > 1) {
                    var param = prop.split('.');
                    _colors[param[0]] = {};
                    _colors[param[0]][param[1]] = $scope['params_' + param[0] + '_' + param[1]];
                } else {
                    _colors[prop] = $scope[prop];
                }
            });

            var _preset = {
                'title': title,
                'thumbnail': '',
                colors: _colors
            };
            var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(_preset));
            var dlAnchorElem = document.getElementById('downloadAnchorElem');
            dlAnchorElem.setAttribute("href", dataStr);
            dlAnchorElem.setAttribute("download", Admin.slugify(title) + ".json");
            dlAnchorElem.click();
        }

    });
</script>