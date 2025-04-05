<script setup>
import {onBeforeMount, onUpdated, inject, computed} from 'vue';
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
import Presets from './Presets.vue';
import MultiSelect from './MultiSelect.vue';
import SubForm from './SubForm.vue';
import Icons from './Icons.vue';
import AstroidEditor from './AstroidEditor.vue';
import Categories from './Categories.vue';
import Assignment from './Assignment.vue';
import Border from './Border.vue';
import SubLayouts from './SubLayouts.vue';
import Range from './Range.vue';
import SwitchBox from "./SwitchBox.vue";
import Text from "./Text.vue";
import DynamicContent from "./DynamicContent.vue";

const emit = defineEmits(['update:contentlayout', 'update:loadPreset', 'update:getPreset', 'update:subFormState', 'update:presetState']);
const props = defineProps({
    field: { type: Object, default: null },
    scope: { type: Object, default: null },
    presetUpdated: {type: Boolean, default: false},
    actSave: {type: Boolean, default: false}
});
const constant = inject('constant', {});

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

// Update subLayout
function updateSubLayouts() {
    props.scope['sublayout'] = 'update';
}

// Get Dynamic Fields
const dynamicFields = computed(() => {
    let fields = [];
    if (typeof props.scope['dynamic_content_settings'] === 'undefined') {
        return fields;
    }
    Object.keys(constant.dynamic_source_fields).forEach(key => {
        if (constant.dynamic_source_fields[key].filters.includes(props.scope['dynamic_content_settings']['source'])) {
            fields.push({
                type: 'heading',
                label: constant.dynamic_source_fields[key].name
            })
            Object.keys(constant.dynamic_source_fields[key].fields).forEach(field => {
                fields.push({
                    type: 'field',
                    value: field,
                    label: constant.dynamic_source_fields[key].fields[field],
                    category: {
                        value: constant.dynamic_source_fields[key].value,
                        name: constant.dynamic_source_fields[key].name
                    }
                });
            });
        }
    })
    return fields;
})
function selectDynamicField(field) {
    if (typeof props.scope['dynamic_content_settings']['dynamic_content'] === 'undefined') {
        props.scope['dynamic_content_settings']['dynamic_content'] = {};
    }
    props.scope['dynamic_content_settings']['dynamic_content'][props.field.name] = field;
}
function removeDynamicField() {
    delete props.scope['dynamic_content_settings']['dynamic_content'][props.field.name];
}
</script>
<template>
    <div v-if="constant.is_pro && typeof props.field.input.dynamic !== `undefined` && props.field.input.dynamic && dynamicFields.length" class="dynamic-select dropdown position-absolute end-0 ">
        <button type="button" class="btn btn-link dropdown-toggle link-body-emphasis px-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-database me-2"></i>Dynamic</button>
        <ul class="dropdown-menu">
            <li v-for="(field, key) in dynamicFields" :key="key">
                <hr v-if="field.type === `heading` && key !== 0" class="dropdown-divider">
                <h6 v-if="field.type === `heading`" class="dropdown-header">{{ field.label }}</h6>
                <a v-else class="dropdown-item" href="#" @click.prevent="selectDynamicField(field)">{{ field.label }}</a>
            </li>
        </ul>
    </div>
    <div v-if="constant.is_pro && typeof props.field.input.dynamic !== `undefined` && props.field.input.dynamic && dynamicFields.length && typeof props.scope.dynamic_content_settings.dynamic_content[props.field.name] !== `undefined` && Object.keys(props.scope.dynamic_content_settings.dynamic_content[props.field.name]).length !== 0" class="dynamic-content form-control">
        <div class="row g-0 align-items-center">
            <div class="dynamic-field-label col px-2"><i class="fa-solid fa-database me-2"></i>{{ props.scope.dynamic_content_settings.dynamic_content[props.field.name].category.name + ' - ' + props.scope.dynamic_content_settings.dynamic_content[props.field.name].label }}</div>
            <div class="dynamic-field-tools col-auto">
                <nav class="nav">
                    <a class="nav-link" href="#" @click.prevent="removeDynamicField"><i class="fa-solid fa-trash-can"></i></a>
                </nav>
            </div>
        </div>
    </div>
    <Text v-else-if="props.field.input.type === `astroidtext`" v-model="props.scope[props.field.name]" :field="props.field" />
    <select v-else-if="props.field.input.type === `astroidlist`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="astroid-list form-select" :aria-label="props.field.label">
        <option v-for="option in props.field.input.options" :key="option.value" :value="option.value">{{ option.text }}</option>
    </select>
    <select v-else-if="props.field.input.type === `astroidmodulesposition`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="astroid-module-position form-select" :aria-label="props.field.label">
        <option v-for="(option, key) in props.field.input.options" :key="key" :value="key">{{ option }}</option>
    </select>
    <select v-else-if="props.field.input.type === `astroidmoduleslist`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="astroid-module-list form-select" :aria-label="props.field.label">
        <option v-for="(option, key) in props.field.input.options" :key="key" :value="option.id">{{ option.title }}</option>
    </select>
    <select v-else-if="props.field.input.type === `astroidmodulesstyle`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="astroid-module-style form-select" :aria-label="props.field.label">
        <option v-for="(option, key) in props.field.input.options" :key="key" :value="option.value">{{ option.text }}</option>
    </select>
    <select v-else-if="props.field.input.type === `astroidanimations`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="astroid-list form-select" :aria-label="props.field.label">
        <option v-for="option in props.field.input.options" :key="option.value" :value="option.value">{{ option.text }}</option>
    </select>
    <div v-else-if="props.field.input.type === `astroidradio`" class="astroid-radio">
        <div v-if="props.field.input.role === `default`" class="astroid-btn-group" :class="{'full' : props.field.input.width === 'full'}" role="group" :aria-label="props.field.label">
            <span v-for="(option, idx) in props.field.input.options" :key="idx">
                <input type="radio" class="btn-check" v-model="props.scope[props.field.name]" :name="props.field.input.name" :id="props.field.input.id+idx" :value="option.value" autocomplete="off">
                <label class="btn btn-sm btn-as btn-outline-primary btn-as-outline-primary" :for="props.field.input.id+idx" v-html="option.text"></label>
            </span>
        </div>
        <SwitchBox v-else-if="props.field.input.role === `switch`" v-model="props.scope[props.field.name]" :field="props.field" :presetUpdated="props.presetUpdated" @update:Preset="state => (emit('update:presetState', state))" />
        <div v-else-if="props.field.input.role === `image`" class="radio-image row g-2">
            <div v-for="(option, idx) in props.field.input.options" :key="idx" class="col col-auto">
                <input type="radio" class="btn-check" v-model="props.scope[props.field.name]" :name="props.field.input.name" :id="props.field.input.id+idx" :value="option.value" autocomplete="off">
                <label class="btn btn-outline-light btn-outline-image" :for="props.field.input.id+idx"><img :src="constant.site_url+option.text" width="150" /></label>
            </div>
        </div>
    </div>
    <div v-else-if="props.field.input.type === `astroidcolor`" class="astroid-color">
        <Colors v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidrange`">
        <Range v-model="props.scope[props.field.name]" :field="props.field" :presetUpdated="props.presetUpdated" @update:Preset="state => (emit('update:presetState', state))" />
    </div>
    <div v-else-if="props.field.input.type === `astroidicon`">
        <BackToTopIcon v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidmedia`" class="astroid-media">
        <MediaManager v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidpreloaders`" class="astroid-preloader">
        <Preloader v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidtypography`" class="astroid-typography">
        <Typography v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroideditor`" class="astroid-editor">
        <AstroidEditor v-model="props.scope[props.field.name]" :field="props.field" :presetUpdated="props.presetUpdated" @update:Preset="state => (emit('update:presetState', state))" />
    </div>
    <div v-else-if="props.field.input.type === `astroidtextarea`" class="astroid-textarea">
        <TextArea v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidsocialprofiles`" class="astroid-socialprofiles">
        <SocialProfiles v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `layout`" class="astroid-layout px-2">
        <Layout v-model="props.scope[props.field.name]" :field="props.field" @update:subLayouts="updateSubLayouts" :presetUpdated="props.presetUpdated" @update:Preset="state => (emit('update:presetState', state))" />
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
    <div v-else-if="props.field.input.type === `astroidmultiselect`" class="astroid-multi-select">
        <MultiSelect v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidpreset`" class="astroid-preset">
        <Presets :field="props.field" @update:loadPreset="(value) => {emit('update:loadPreset', value)}" @update:getPreset="(value) => {emit('update:getPreset', value)}" />
    </div>
    <div v-else-if="props.field.input.type === `astroidsubform`" class="astroid-subform">
        <SubForm v-model="props.scope[props.field.name]" :field="props.field" :actSave="props.actSave" @update:subFormState="(value) => {emit('update:subFormState', value)}" />
    </div>
    <div v-else-if="props.field.input.type === `astroidicons`" class="astroid-icons">
        <Icons v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidcategories`" class="astroid-categories">
        <Categories v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidheading`" class="astroid-heading">
        <h5 v-if="props.field.input.title">{{ props.field.input.title }}</h5>
        <p v-if="props.field.input.description" class="form-text">{{ props.field.input.description }}</p>
    </div>
    <div v-else-if="props.field.input.type === `astroidhidden`" class="astroid-hidden">
        <input type="hidden" :id="props.field.input.id" :name="props.field.input.name" v-model="props.scope[props.field.name]">
    </div>
    <div v-else-if="props.field.input.type === `astroiddivider`" class="astroid-divider">
        <hr/>
    </div>
    <div v-else-if="props.field.input.type === `astroidassignment`" class="astroid-assignment">
        <Assignment v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidborder`" class="astroid-border">
        <Border v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `sublayouts`" class="astroid-sub-layouts">
        <SubLayouts v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `articlelayouts`" class="astroid-article-layouts">
        <SubLayouts v-model="props.scope[props.field.name]" :field="props.field" type="article_layouts" />
    </div>
    <div v-else-if="props.field.input.type === `dynamiccontent`" class="astroid-dynamic-content">
        <DynamicContent v-model="props.scope[props.field.name]" :field="props.field" />
    </div>
    <div v-else-if="props.field.input.type === `astroidgetpro`" class="astroid-get-pro card card-body">
        <h6 class="card-title">{{ props.field.input.title }}</h6>
        <div class="card-text form-text" v-html="props.field.input.desc"></div>
    </div>
</template>