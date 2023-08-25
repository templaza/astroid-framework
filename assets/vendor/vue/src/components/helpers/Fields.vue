<script setup>
import { onBeforeMount, onMounted, onUpdated, reactive, ref } from 'vue';
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCircle, faArrowsLeftRight } from "@fortawesome/free-solid-svg-icons";
import BackToTopIcon from './BackToTopIcon.vue';
import MediaManager from './MediaManager.vue';
import Preloader from './Preloader.vue';
import Typography from './Typography.vue';
import TextArea from './TextArea.vue';
import SocialProfiles from './SocialProfiles.vue';
import LayoutBuilder from "./LayoutBuilder.vue";
import Spacing from './Spacing.vue';
library.add(faCircle, faArrowsLeftRight);
const emit = defineEmits(['update:contentlayout']);
const props = defineProps({
  field: { type: Object, default: null },
  scope: { type: Object, default: null },
  constant: { type: Object, default: null }
});

onBeforeMount(()=>{
    if (props.field.input.type === `astroidtypography`) {
        if (props.scope[props.field.name] === '') {
            props.scope[props.field.name] = new Object();
        }
        Object.keys(props.field.input.value).forEach(key => {
            if (typeof props.scope[props.field.name][key] === 'undefined') {
                props.scope[props.field.name][key] = props.field.input.value[key];
            }
        });
    }
    if (props.field.input.type === `layout`) {
        layout.value    =   props.field.input.value;
    }
    updateContentLayout();
})

onUpdated(()=>{
    updateContentLayout();
})

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
    if (props.field.input.type === `astroidradio`) {
        if (props.field.input.role === `switch`) {
            if (props.scope[props.field.name] === '1') {
                props.scope[props.field.name] = true;
            }
        }
    }
})

// Update state for Astroid Content Layout
function updateContentLayout() {
    if (props.field.input.type === `astroidmodulesposition`) {
        if (typeof props.field.input.astroid_content_layout !== 'undefined' && props.field.input.astroid_content_layout !== '') {
            emit('update:contentlayout', props.field.name, {'astroid_content_layout': props.field.input.astroid_content_layout, 'module_position': props.scope[props.field.name]});
        }
    }
    if (props.field.input.type === `astroidlist`) {
        if (typeof props.field.input.astroid_content_layout_load !== 'undefined' && props.field.input.astroid_content_layout_load !=='') {
            emit('update:contentlayout', props.field.input.astroid_content_layout_load, {'position' : props.scope[props.field.name]});
        }
    }
}

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
            let tmp = JSON.parse(props.scope[props.field.name]);
            tmp[_currentColorMode.value] = color;
            props.scope[props.field.name] = JSON.stringify(tmp);
        } else {
            let tmp = {'light': '', 'dark': ''};
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

// Layout Builder
const layout = ref([]);
</script>
<template>
    <input v-if="props.field.input.type === `astroidtext`" v-model="props.scope[props.field.name]" type="text" :id="props.field.input.id" :name="props.field.input.name" class="astroid-text form-control" :aria-label="props.field.label" :placeholder="props.field.input.hint">
    <select v-else-if="props.field.input.type === `astroidlist`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="astroid-list form-select" :aria-label="props.field.label">
        <option v-for="option in props.field.input.options" :key="option.value" :value="option.value">{{ option.text }}</option>
    </select>
    <select v-else-if="props.field.input.type === `astroidmodulesposition`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="astroid-module-select form-select" :aria-label="props.field.label">
        <option v-for="(option, key) in props.field.input.options" :key="key" :value="key">{{ option }}</option>
    </select>
    <select v-else-if="props.field.input.type === `astroidanimations`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="astroid-list form-select" :aria-label="props.field.label">
        <option v-for="option in props.field.input.options" :key="option.value" :value="option.value">{{ option.text }}</option>
    </select>
    <div v-else-if="props.field.input.type === `astroidradio`" class="astroid-radio">
        <div v-if="props.field.input.role === `default`" class="astroid-btn-group" role="group" :aria-label="props.field.label">
            <span v-for="(option, idx) in props.field.input.options" :key="idx">
                <input type="radio" class="btn-check" v-model="props.scope[props.field.name]" :name="props.field.input.name" :id="props.field.input.id+idx" :value="option.value" autocomplete="off">
                <label class="btn btn-sm btn-as btn-outline-primary btn-as-outline-primary" :for="props.field.input.id+idx">{{ option.text }}</label>
            </span>
        </div>
        <div v-else-if="props.field.input.role === `switch`" class="form-check form-switch">
            <input v-model="props.scope[props.field.name]" :name="props.field.input.name" class="form-check-input" type="checkbox" role="switch" :id="props.field.input.id">
        </div>
        <div v-else-if="props.field.input.role === `image`" class="radio-image row g-2">
            <div v-for="(option, idx) in props.field.input.options" :key="idx" class="col col-auto">
                <input type="radio" class="btn-check" v-model="props.scope[props.field.name]" :name="props.field.input.name" :id="props.field.input.id+idx" :value="option.value" autocomplete="off">
                <label class="btn btn-outline-light btn-outline-image" :for="props.field.input.id+idx"><img :src="props.constant.site_url+option.text" width="150" /></label>
            </div>
        </div>
    </div>
    <div v-else-if="props.field.input.type === `astroidcolor`" class="astroid-color">
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
    <div v-else-if="props.field.input.type === `astroidrange`">
        <label :for="props.field.input.id" class="form-label">{{ props.scope[props.field.name] }}px</label>
        <input type="range" class="form-range" v-model="props.scope[props.field.name]" :min="props.field.input.min" :max="props.field.input.max" :step="props.field.input.step" :id="props.field.input.id">
    </div>
    <div v-else-if="props.field.input.type === `astroidicon`">
        <BackToTopIcon v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidmedia`" class="astroid-media">
        <MediaManager v-model="props.scope[props.field.name]" :field="props.field" :constant="props.constant" />
    </div>
    <div v-else-if="props.field.input.type === `astroidpreloaders`" class="astroid-preloader">
        <Preloader v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidtypography`" class="astroid-typography">
        <Typography v-model="props.scope[props.field.name]" :field="props.field" :constant="props.constant" />
    </div>
    <div v-else-if="props.field.input.type === `astroidtextarea`" class="astroid-textarea">
        <TextArea v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidsocialprofiles`" class="astroid-socialprofiles">
        <SocialProfiles v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `layout`" class="astroid-layout px-2">
        <LayoutBuilder :field="props.field" :list="layout" group="root" :constant="props.constant" />
    </div>
    <div v-else-if="props.field.input.type === `astroidspacing`" class="astroid-astroidspacing">
        <Spacing v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidhidden`" class="astroid-hidden">
        <input type="hidden" :id="props.field.input.id" :name="props.field.input.name" v-model="props.scope[props.field.name]">
    </div>
</template>