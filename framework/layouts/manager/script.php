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

        $scope.selectPreset = function(_idx, _name) {
            var _preset = null;

            var _astroidtypography = function (key, data) {
                $('#params_' + key + '_font_face').dropdown('set selected', [data['font_face']]);
                $scope['params_' + key + '_alt_font_face']      =   data['alt_font_face'];
                $scope['params_' + key + '_font_weight']        =   data['font_weight'];

                $scope['params_' + key + '_font_size_desktop']  =   parseFloat(data['font_size']['desktop']);
                $scope['params_' + key + '_font_size_tablet']   =   parseFloat(data['font_size']['tablet']);
                $scope['params_' + key + '_font_size_mobile']   =   parseFloat(data['font_size']['mobile']);

                $scope['params_' + key + '_letter_spacing_desktop']  =   parseFloat(data['letter_spacing']['desktop']);
                $scope['params_' + key + '_letter_spacing_tablet']   =   parseFloat(data['letter_spacing']['tablet']);
                $scope['params_' + key + '_letter_spacing_mobile']   =   parseFloat(data['letter_spacing']['mobile']);

                $scope['params_' + key + '_line_height_desktop']  =   parseFloat(data['line_height']['desktop']);
                $scope['params_' + key + '_line_height_tablet']   =   parseFloat(data['line_height']['tablet']);
                $scope['params_' + key + '_line_height_mobile']   =   parseFloat(data['line_height']['mobile']);

                $scope['params_' + key + '_text_transform']     =   data['text_transform'];
                $scope['params_' + key + '_font_color']         =   data['font_color'];
            }

            var _astroidsassoverrides = function (key, data) {
                var _data = JSON.parse(data);
                $scope.overrides = AstroidSassOverrideVariables;
                var _overrides = $scope.overrides;
                var _availabled = false;
                for (let j = 0; j < _data.length; j++) {
                    _availabled = false;
                    for (let i = 0; i < _overrides.length; i++) {
                        if (_overrides[i]['variable'] === _data[j]['variable']) {
                            _availabled = true;
                            _overrides[i] = _data[j];
                            if (_overrides[i]['color'] === true) {
                                $(".sass-variable-" + i + "-value").spectrum("set", _overrides[i]['value']);
                            }
                        }
                    }
                    if (_availabled === false) {
                        _overrides.push(_data[j]);
                        $scope.overrides = _overrides;
                        if (_data[j]['color'] === true) {
                            setTimeout(function () {
                                $(".sass-variable-" + (_overrides.length - 1) + "-value").spectrum(spectrumConfig)
                            }, 50)
                        }
                    }
                }
                $scope.overrides = _overrides;
            }

            var _astroidlayout = function (key, data) {
                var _data = JSON.parse(data);
                var scope = angular.element(document.getElementById("layoutController")).scope();
                scope.layout = _data;
                setTimeout(function () {
                    scope.$apply();
                }, 50)
            }

            if(_idx in TPL_PRESETS){
                _preset = TPL_PRESETS[_idx];
            }
            if (_name != null) {
                for (var key in _name) {
                    if (typeof _preset[_name[key]] == 'object') {
                        for (var subkey in _preset[_name[key]]) {
                            if (_preset[_name[key]].hasOwnProperty(subkey)) {
                                $scope['params_' + key + '_' + subkey] = _preset[_name[key]][subkey];
                            }
                        }
                    } else {
                        if (_name[key].indexOf(":")>0) {
                            const _param_name   =   _name[key].split(":")[0],
                                _param_type     =   _name[key].split(":")[1];
                            switch (_param_type) {
                                case 'astroidtypography':
                                    _astroidtypography(_param_name, _preset[_param_name]);
                                    break;
                                case 'astroidsassoverrides':
                                    _astroidsassoverrides(_param_name, _preset[_param_name]);
                                    break;
                                case 'layout':
                                    _astroidlayout(_param_name, _preset[_param_name]);
                                    break;
                            }
                        } else {
                            $scope[_name[key]]  =   _preset[_name[key]];
                            $('[name="params[' + _name[key] + ']"]').val(_preset[_name[key]]);
                        }
                    }
                }
            }
            Admin.notify('<?php echo \JText::_('TPL_ASTROID_SYSTEM_MESSAGES_PRESET'); ?>', 'success');
        }

        // $scope.exportPreset = function() {
        //     var title = prompt("Please enter your desired name", "My Preset");
        //     if (title == "") {
        //         return false;
        //     }
        //
        //     var _colors = {};
        //     presetProps.forEach(function(prop) {
        //         if (prop.split('.').length > 1) {
        //             var param = prop.split('.');
        //             _colors[param[0]] = {};
        //             _colors[param[0]][param[1]] = $scope['params_' + param[0] + '_' + param[1]];
        //         } else {
        //             _colors[prop] = $scope[prop];
        //         }
        //     });
        //
        //     var _preset = {
        //         'title': title,
        //         'thumbnail': '',
        //         colors: _colors
        //     };
        //     var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(_preset));
        //     var dlAnchorElem = document.getElementById('downloadAnchorElem');
        //     dlAnchorElem.setAttribute("href", dataStr);
        //     dlAnchorElem.setAttribute("download", Admin.slugify(title) + ".json");
        //     dlAnchorElem.click();
        // }
    });
</script>