<script setup>
import { onBeforeMount, ref, computed, onMounted, inject, watch } from 'vue';
import { ColorPicker } from 'vue-color-kit'

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const constant = inject('constant', {});
const theme = inject('theme', 'light');
const data = ref({
    'border_width' : '1',
    'border_style' : 'solid',
    'border_color' : {
        light: '#ccc',
        dark: '#ccc'
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
    document.addEventListener('click', function(event) {
        const elem          = document.getElementById(props.field.input.id+'-colorpicker');
        const circle_light  = document.getElementById(props.field.input.id+'-colorcircle-light');
        const circle_dark   = document.getElementById(props.field.input.id+'-colorcircle-dark');
        if (_showColorPicker.value === true) {
            if (
                elem 
                && circle_light 
                && !circle_light.contains(event.target) 
                && !elem.contains(event.target)
                && (
                    (
                        circle_dark
                        && !circle_dark.contains(event.target)
                    )
                    || parseInt(constant.color_mode) === 0 
                )
            ) {
                _showColorPicker.value = false;
            }
        }    
    });
})
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
        <div class="col-12">
            <label :for="props.field.input.id + `_border_width`" class="form-label">{{ data.border_width }}px</label>
            <input v-model="data.border_width" type="range" class="form-range" min="1" max="50" step="1" :id="props.field.input.id + `_border_width`">
        </div>
        <div class="col-6">
            <label class="form-label" :for="props.field.input.id + `_border_style`">Border Style</label>
            <select class="form-select" v-model="data.border_style" :id="props.field.input.id + `_border_style`">
                <option value="solid">Solid</option>
                <option value="dotted">Dotted</option>
                <option value="dashed">Dashed</option>
                <option value="double">Double</option>
                <option value="none">None</option>
            </select>
        </div>
        <div class="col-6">
            <div class="row">
                <div :class="{
                    'col-4 text-center' : (constant.color_mode === '1'),
                    'col-12': (constant.color_mode !== '1')
                }">
                    <font-awesome-icon :id="props.field.input.id+`-colorcircle-light`" :icon="['fas', 'circle']" size="3x" class="border astroid-color-picker" :style="{'color': data.border_color.light}" @click="showColorPicker('light')" />
                    <div v-if="constant.color_mode === '1'">Light</div>
                </div>
                <div v-if="constant.color_mode === '1'" class="col text-center py-3">
                    <font-awesome-icon :icon="['fas', 'arrows-left-right']" />
                </div>
                <div v-if="constant.color_mode === '1'" class="col-4 text-center">
                    <font-awesome-icon :id="props.field.input.id+`-colorcircle-dark`" :icon="['fas', 'circle']" size="3x" class="border astroid-color-picker" :style="{'color': data.border_color.dark}" @click="showColorPicker('dark')" />
                    <div>Dark</div>
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