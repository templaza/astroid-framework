<script setup>
import {onMounted, onUpdated, reactive, ref, inject, onUnmounted} from 'vue';
import { ColorPicker } from 'vue-color-kit'

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: { type: String, default: '' },
    field: { type: Object, default: null },
    colorMode: { type: Number, default: 0 }
});
const theme = inject('theme', 'light');

onMounted(() => {
    if (props.modelValue.trim() !== '') {
        try {
            const tmp = JSON.parse(props.modelValue);
            _color.light    = tmp.light;
            _color.dark     = tmp.dark;
        } catch (e) {
            _color.light = props.modelValue;
            _color.dark = props.modelValue;
        }
    }
    _currentColorMode.value = props.colorMode === 2 ? 'dark' : 'light';
    document.addEventListener('click', handleClickOutside);
})
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
onUpdated(()=>{
    try {
        if (!!props.modelValue) {
            let tmp = JSON.parse(props.modelValue);
            _color.light = tmp.light;
            _color.dark = tmp.dark;
        }
    } catch (e) {
        _color.light = props.modelValue;
        _color.dark = props.modelValue;
    }
})
const handleClickOutside = function(event) {
    const elem          = document.getElementById(props.field.input.id+'-colorpicker');
    const circle_light  = document.getElementById(props.field.input.id+'-colorcircle-light');
    const circle_dark   = document.getElementById(props.field.input.id+'-colorcircle-dark');
    if (_showColorPicker.value === true) {
        if (
            elem
            && !elem.contains(event.target)
            && (
                (circle_light && !circle_light.contains(event.target) && _currentColorMode.value === 'light')
                || (circle_dark && !circle_dark.contains(event.target) && _currentColorMode.value === 'dark')
            )
        ) {
            _showColorPicker.value = false;
        }
    }
};
const _color = reactive({
    light: '',
    dark: ''
})
const _showColorPicker = ref(false);
const _currentColor = ref('#59c7f9');
const _currentColorMode = ref('light');

function showColorPicker(colorMode) {
    _currentColor.value     = _color[colorMode];
    _currentColorMode.value = colorMode;
    _showColorPicker.value  = true;
}

function updateColor(color) {
    try {
        if (!!props.modelValue) {
            let tmp = JSON.parse(props.modelValue);
            tmp[_currentColorMode.value] = color;
            emit('update:modelValue', JSON.stringify(tmp));
        } else {
            let tmp = {'light': '', 'dark': ''};
            tmp[_currentColorMode.value] = color;
            emit('update:modelValue', JSON.stringify(tmp));
        }
    } catch (e) {
        const tmp = {'light': color, 'dark': color};
        emit('update:modelValue', JSON.stringify(tmp));
    }
}

function changeColor(color) {
    const { r, g, b, a } = color.rgba;
    if (r === 0, g === 0, b === 0, a === 0) {
        _color[_currentColorMode.value] = '';
    } else {
        _color[_currentColorMode.value] = `rgba(${r}, ${g}, ${b}, ${a})`;
    }
    updateColor(_color[_currentColorMode.value]);
}

function copyColor(from) {
    if (from === 'light') {
        _color.dark = _color.light;
    } else {
        _color.light = _color.dark;
    }
    emit('update:modelValue', JSON.stringify(_color));
}
</script>
<template>
    <div class="row">
        <div v-if="props.colorMode === 1 || props.colorMode === 0" :class="{
            'col-4 text-center' : (props.colorMode === 1),
            'col-12': (props.colorMode === 0)
        }">
            <i class="border astroid-color-picker fas fa-circle fa-3x" :id="props.field.input.id+`-colorcircle-light`" :style="{'color': _color.light}" @click="showColorPicker('light')"></i>
            <div v-if="props.colorMode === 1">Light</div>
        </div>
        <div v-if="props.colorMode === 1" class="col text-center py-3">
            <div class="btn-group" role="group" aria-label="Copy Color">
                <button class="btn btn-link p-1 link-body-emphasis" @click.prevent="copyColor('dark')"><i class="fa-solid fa-caret-left"></i></button><button class="btn btn-link p-1 link-body-emphasis" @click.prevent="copyColor('light')"><i class="fa-solid fa-caret-right"></i></button>
            </div>
        </div>
        <div v-if="props.colorMode === 1 || props.colorMode === 2" :class="{
            'col-4 text-center' : (props.colorMode === 1),
            'col-12': (props.colorMode === 2)
        }">
            <i class="border astroid-color-picker fas fa-circle fa-3x" :id="props.field.input.id+`-colorcircle-dark`" :style="{'color': _color.dark}" @click="showColorPicker('dark')"></i>
            <div v-if="props.colorMode === 1">Dark</div>
        </div>
    </div>
    <input type="hidden" :name="props.field.input.name" :id="props.field.input.id" :value="modelValue" />
    <ColorPicker v-if="_showColorPicker"
        :theme="theme"
        :color="_currentColor"
        :sucker-hide="true"
        :sucker-canvas="null"
        :sucker-area="[]"
        :id="props.field.input.id+`-colorpicker`"
        @changeColor="changeColor"
    />
</template>