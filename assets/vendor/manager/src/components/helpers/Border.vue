<script setup>
import {onBeforeMount, ref, computed, onMounted, inject, watch, onUnmounted} from 'vue';
import { ColorPicker } from 'vue-color-kit'

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field', 'colorMode']);
const constant = inject('constant', {});
const theme = inject('theme', 'light');
const data = ref({
    'border_width' : '1',
    'border_style' : 'none',
    'border_color' : {
        light: '',
        dark: ''
    }
})

onBeforeMount(()=>{
    if (typeof props.modelValue !== 'undefined' && props.modelValue !== '') {
        data.value = {
            ...data.value,
            ...JSON.parse(props.modelValue)
        };
    }
})

onMounted(()=>{
    _currentColorMode.value = props.colorMode === 2 ? 'dark' : 'light';
    document.addEventListener('click', handleClickOutside);
})
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
const handleClickOutside = function(event) {
    const elem = document.getElementById(props.field.input.id + '-colorpicker');
    const circle_light = document.getElementById(props.field.input.id + '-colorcircle-light');
    const circle_dark = document.getElementById(props.field.input.id + '-colorcircle-dark');
    if (_showColorPicker.value === true) {
        if (
            elem &&
            !elem.contains(event.target) &&
            (
                (circle_light && !circle_light.contains(event.target) && _currentColorMode.value === 'light') ||
                (circle_dark && !circle_dark.contains(event.target) && _currentColorMode.value === 'dark')
            )
        ) {
            _showColorPicker.value = false;
        }
    }
};
const _showColorPicker = ref(false);
const _currentColor = ref('');
const _currentColorMode = ref('light');
function showColorPicker(colorMode) {
    _currentColor.value     = data.value.border_color[colorMode];
    _currentColorMode.value = colorMode;
    _showColorPicker.value  = true;
}

function changeColor(color) {
    const { r, g, b, a } = color.rgba;
    if (r === 0, g === 0, b === 0, a === 0) {
        data.value.border_color[_currentColorMode.value] = '';
    } else {
        data.value.border_color[_currentColorMode.value] = `rgba(${r}, ${g}, ${b}, ${a})`;
    }
}
function copyColor(from) {
    if (from === 'light') {
        data.value.border_color.dark = data.value.border_color.light;
    } else {
        data.value.border_color.light = data.value.border_color.dark;
    }
}
const border_text = computed(() => {
  return JSON.stringify(data.value);
})
watch(border_text, (newText) => {
    if (newText !== props.modelValue) {
        emit('update:modelValue', newText);
    }
})
</script>
<template>
    <div class="row g-3">
        <div v-if="data.border_style !== `none`" class="col-12">
            <label :for="props.field.input.id + `_border_width`" class="form-label">{{ data.border_width }}px</label>
            <input v-model="data.border_width" type="range" class="form-range" min="1" max="50" step="1" :id="props.field.input.id + `_border_width`">
        </div>
        <div :class="{'col-6' : data.border_style !== `none`, 'col-12' : data.border_style === `none`}">
            <label class="form-label" :for="props.field.input.id + `_border_style`">Border Style</label>
            <select class="form-select" v-model="data.border_style" :id="props.field.input.id + `_border_style`">
                <option value="solid">Solid</option>
                <option value="dotted">Dotted</option>
                <option value="dashed">Dashed</option>
                <option value="double">Double</option>
                <option value="none">None</option>
            </select>
        </div>
        <div v-if="data.border_style !== `none`" class="col-6">
            <div class="row">
                <div v-if="props.colorMode === 1 || props.colorMode === 0" :class="{
                    'col-4 text-center' : (props.colorMode === 1),
                    'col-12': (props.colorMode === 0)
                }">
                    <i class="border astroid-color-picker fas fa-circle fa-3x" :id="props.field.input.id+`-colorcircle-light`" :style="{'color': data.border_color.light}" @click="showColorPicker('light')"></i>
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
                    <i class="border astroid-color-picker fas fa-circle fa-3x" :id="props.field.input.id+`-colorcircle-dark`" :style="{'color': data.border_color.dark}" @click="showColorPicker('dark')"></i>
                    <div v-if="props.colorMode === 1">Dark</div>
                </div>
            </div>
            <input type="hidden" :name="props.field.input.name+`[font_color]`" :id="props.field.input.id+`_font_color`" v-model="props.modelValue['font_color']" />
            <ColorPicker v-if="_showColorPicker"
                :theme="theme"
                :color="_currentColor"
                :sucker-hide="true"
                :sucker-canvas="null"
                :sucker-area="[]"
                :id="props.field.input.id+`-colorpicker`"
                @changeColor="changeColor"
            />
        </div>
    </div>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>