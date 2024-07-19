<script setup>
import { computed, onBeforeMount, ref, watch } from 'vue';

const props = defineProps(['sublayout_id']);
const data = JSON.parse(document.getElementById(props.sublayout_id+'_json').innerHTML);
const tpl_selected = ref(data.value.template);
const layouts = ref([{title: 'Default', name: ''}]);
const layout_selected = ref(data.value.layout);

const templates = ref({});

onBeforeMount(()=>{
    if (typeof data.value.template !== 'undefined') {
        tpl_selected.value = data.value.template;
    }
    if (typeof data.value.layout !== 'undefined') {
        layout_selected.value = data.value.layout;
    }
    templates.value = data.templates;
    if (typeof data.templates[tpl_selected.value] !== 'undefined') {
        layouts.value = data.templates[tpl_selected.value];
    } else {
        layouts.value = [];
    }
})

watch(tpl_selected, (newValue) => {
    if (typeof data.templates[newValue] !== 'undefined') {
        layouts.value = data.templates[newValue];
    } else {
        layouts.value = [];
    }
    layout_selected.value = '';
});

const value = computed(() => {
    return JSON.stringify({
        template: tpl_selected.value,
        layout: layout_selected.value
    })
})
</script>

<template>
    <div class="mb-3">
        <label :for="props.sublayout_id+ `_select_template`" class="form-label">{{ data.language.select_template }}</label>
        <select :id="props.sublayout_id+ `_select_template`" class="form-select" v-model="tpl_selected">
            <option v-if="data.inherit === true" value="inherit">{{ data.language.inherit }}</option>
            <option value="">{{ data.language.default }} - {{ data.language.system }}</option>
            <option v-for="(template, idx) in data['templates']" :value="idx">{{ idx }}</option>
        </select>
    </div>
    <div v-if="tpl_selected !== `inherit` && tpl_selected !== ``">
        <label :for="props.sublayout_id+ `_select_layout`" class="form-label">{{ data.language.select_layout }}</label>
        <select :id="props.sublayout_id+ `_select_layout`" class="form-select" v-model="layout_selected">
            <option value="">{{ data.language.default }} - {{ data.language.system }}</option>
            <option v-for="layout in layouts" :value="layout.name">{{ layout.title }}</option>
        </select>
    </div>
    <input type="hidden" :name="data['name']" :value="value">
</template>