<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: { type: String, default: '' },
    field: { type: Object, default: null },
});

onMounted(() => {
    if (props.field.input.value.trim() !== '') {
        const tmp = JSON.parse(props.field.input.value)
        _color.light    = tmp.light;
        _color.dark     = tmp.dark;
    }
        
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
                    || props.field.input.colormode === '0' 
                )
            ) {
                _showColorPicker.value = false;
            }
        }    
    });
})
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
    _color[_currentColorMode.value] = `rgba(${r}, ${g}, ${b}, ${a})`;
    updateColor(_color[_currentColorMode.value]);
}
</script>
<template>
    <div class="row">
        <div :class="{
                'col-4 text-center' : (props.field.input.colormode === '1'),
                'col-12': (props.field.input.colormode !== '1')
        }">
            <font-awesome-icon :id="props.field.input.id+`-colorcircle-light`" :icon="['fas', 'circle']" size="3x" class="border astroid-color-picker" :style="{'color': _color.light}" @click="showColorPicker('light')" />
            <div v-if="props.field.input.colormode === '1'">Light</div>
        </div>
        <div v-if="props.field.input.colormode === '1'" class="col text-center py-3">
            <font-awesome-icon :icon="['fas', 'arrows-left-right']" />
        </div>
        <div v-if="props.field.input.colormode === '1'" class="col-4 text-center">
            <font-awesome-icon :id="props.field.input.id+`-colorcircle-dark`" :icon="['fas', 'circle']" size="3x" class="border astroid-color-picker" :style="{'color': _color.dark}" @click="showColorPicker('dark')" />
            <div>Dark</div>
        </div>
    </div>
    <input type="hidden" :name="props.field.input.name" :id="props.field.input.id" :value="modelValue" />
    <ColorPicker v-if="_showColorPicker"
        theme="light"
        :color="_currentColor"
        :sucker-hide="true"
        :sucker-canvas="null"
        :sucker-area="[]"
        :id="props.field.input.id+`-colorpicker`"
        @changeColor="changeColor"
    />
</template>