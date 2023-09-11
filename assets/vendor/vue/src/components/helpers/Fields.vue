<script setup>
import { onBeforeMount, onMounted, onUpdated, reactive, ref, watch } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCircle, faArrowsLeftRight } from "@fortawesome/free-solid-svg-icons";
import BackToTopIcon from './BackToTopIcon.vue';
import MediaManager from './MediaManager.vue';
import Preloader from './Preloader.vue';
import Typography from './Typography.vue';
import TextArea from './TextArea.vue';
import SocialProfiles from './SocialProfiles.vue';
import Layout from "./Layout.vue";
import Spacing from './Spacing.vue';
import Gradient from './Gradient.vue';
import SassOverrides from './SassOverrides.vue';
import DatePicker from './DatePicker.vue';
import Colors from './Colors.vue';
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
    updateContentLayout();
})

onUpdated(()=>{
    updateContentLayout();
})

onMounted(()=>{
    if (props.field.input.type === `astroidradio`) {
        if (props.field.input.role === `switch`) {
            if (props.scope[props.field.name] === '1') {
                switchField.value = true;
            }
        }
    }
})

// Switch Field
const switchField = ref(false);
watch(switchField, (newValue) => {
    props.scope[props.field.name] = newValue ? 1 : 0;
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
            <input v-model="switchField" @input="" class="form-check-input" type="checkbox" role="switch" :id="props.field.input.id">
            <input type="hidden" :name="props.field.input.name" :value="props.scope[props.field.name]">
        </div>
        <div v-else-if="props.field.input.role === `image`" class="radio-image row g-2">
            <div v-for="(option, idx) in props.field.input.options" :key="idx" class="col col-auto">
                <input type="radio" class="btn-check" v-model="props.scope[props.field.name]" :name="props.field.input.name" :id="props.field.input.id+idx" :value="option.value" autocomplete="off">
                <label class="btn btn-outline-light btn-outline-image" :for="props.field.input.id+idx"><img :src="props.constant.site_url+option.text" width="150" /></label>
            </div>
        </div>
    </div>
    <div v-else-if="props.field.input.type === `astroidcolor`" class="astroid-color">
        <Colors v-model="props.scope[props.field.name]" :field="props.field" />
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
        <Layout v-model="props.scope[props.field.name]" :field="props.field" :constant="props.constant" />
    </div>
    <div v-else-if="props.field.input.type === `astroidspacing`" class="astroid-spacing">
        <Spacing v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidgradient`" class="astroid-gradient">
        <Gradient v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidsassoverrides`" class="astroid-sass-overrides">
        <SassOverrides v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidcalendar`" class="astroid-calendar">
        <DatePicker v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidheading`" class="astroid-heading">
        <h3>{{ props.field.input.title }}</h3>
        <div>{{ props.field.input.description }}</div>
    </div>
    <div v-else-if="props.field.input.type === `astroidhidden`" class="astroid-hidden">
        <input type="hidden" :id="props.field.input.id" :name="props.field.input.name" v-model="props.scope[props.field.name]">
    </div>
</template>