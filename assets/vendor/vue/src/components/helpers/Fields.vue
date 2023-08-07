<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCircle, faArrowsLeftRight } from "@fortawesome/free-solid-svg-icons";
import BackToTopIcon from './BackToTopIcon.vue';
import MediaManager from './MediaManager.vue';
library.add(faCircle, faArrowsLeftRight);

const props = defineProps({
  field: { type: Object, default: null },
  scope: { type: Object, default: null },
  constant: { type: Object, default: null }
});

onMounted(()=>{
    if (props.field.input.type === 'astroidcolor') {
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
    }
})

// Astroid Color Field
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
        if (!!props.scope[props.field.name]) {
            const tmp = JSON.parse(props.scope[props.field.name]);
            tmp[_currentColorMode.value] = color;
            props.scope[props.field.name] = JSON.stringify(tmp);
        }
    } catch (e) {
        const tmp = {'light': color, 'dark': color};
        props.scope[props.field.name] = JSON.stringify(tmp);
    }
}

function changeColor(color) {
    const { r, g, b, a } = color.rgba;
    _color[_currentColorMode.value] = `rgba(${r}, ${g}, ${b}, ${a})`;
    updateColor(_color[_currentColorMode.value]);
}
</script>
<template>
    <input v-if="props.field.input.type === `astroidtext`" v-model="props.scope[props.field.name]" type="text" :id="props.field.input.id" :name="props.field.input.name" class="astroid-text form-control" :aria-label="props.field.label" :placeholder="props.field.input.hint">
    <select v-if="props.field.input.type === `astroidlist`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="astroid-list form-select" :aria-label="props.field.label">
        <option v-for="option in props.field.input.options" :key="option.value" :value="option.value">{{ option.text }}</option>
    </select>
    <div v-if="props.field.input.type === `astroidradio`" class="astroid-radio">
        <div v-if="props.field.input.role === `default`" class="astroid-btn-group" role="group" :aria-label="props.field.label">
            <span v-for="(option, idx) in props.field.input.options" :key="idx">
                <input type="radio" class="btn-check" v-model="props.scope[props.field.name]" :name="props.field.input.name" :id="props.field.input.id+idx" :value="option.value" autocomplete="off">
                <label class="btn btn-outline-primary btn-as-outline-primary" :for="props.field.input.id+idx">{{ option.text }}</label>
            </span>
        </div>
        <div v-if="props.field.input.role === `switch`" class="form-check form-switch">
            <input v-model="props.scope[props.field.name]" :name="props.field.input.name" class="form-check-input" type="checkbox" role="switch" :id="props.field.input.id">
        </div>
    </div>
    <div v-if="props.field.input.type === `astroidcolor`" class="astroid-color">
        <div class="row">
            <div :class="{
                'col-4 text-center' : (props.field.input.colormode === '1'),
                'col-12': (props.field.input.colormode !== '1')
            }">
                <font-awesome-icon :id="props.field.input.id+`-colorcircle-light`" :icon="['fas', 'circle']" size="3x" class="border" :style="{'color': _color.light}" @click="showColorPicker('light')" />
                <div v-if="props.field.input.colormode === '1'">Light</div>
            </div>
            <div v-if="props.field.input.colormode === '1'" class="col text-center py-3">
                <font-awesome-icon :icon="['fas', 'arrows-left-right']" />
            </div>
            <div v-if="props.field.input.colormode === '1'" class="col-4 text-center">
                <font-awesome-icon :id="props.field.input.id+`-colorcircle-dark`" :icon="['fas', 'circle']" size="3x" class="border" :style="{'color': _color.dark}" @click="showColorPicker('dark')" />
                <div>Dark</div>
            </div>
        </div>
        <input type="hidden" :name="props.field.input.name" :id="props.field.input.id" v-model="props.scope[props.field.name]" />
        <ColorPicker v-if="_showColorPicker"
            theme="light"
            :color="_currentColor"
            :sucker-hide="true"
            :sucker-canvas="null"
            :sucker-area="[]"
            :id="props.field.input.id+`-colorpicker`"
            @changeColor="changeColor"
        />
    </div>
    <div v-if="props.field.input.type === `astroidrange`">
        <label :for="props.field.input.id" class="form-label">{{ props.scope[props.field.name] }}px</label>
        <input type="range" class="form-range" v-model="props.scope[props.field.name]" :min="props.field.input.min" :max="props.field.input.max" :step="props.field.input.step" :id="props.field.input.id">
    </div>
    <div v-if="props.field.input.type === `astroidicon`">
        <BackToTopIcon v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-if="props.field.input.type === `astroidmedia`" class="astroid-media">
        <MediaManager v-model="props.scope[props.field.name]" :field="props.field" :constant="props.constant" />
    </div>
</template>