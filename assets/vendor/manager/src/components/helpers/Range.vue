<script setup>
import ResponsiveToggle from "./ResponsiveToggle.vue";
import {onBeforeMount, onMounted, onUpdated, ref, watch, computed, inject} from 'vue';

const emit = defineEmits(['update:modelValue', 'update:Preset']);
const props = defineProps(['modelValue', 'field', 'presetUpdated']);
const language  =   inject('language', []);
const devices = ['mobile', 'landscape_mobile', 'tablet', 'desktop', 'large_desktop', 'larger_desktop'];
const active = ref('mobile');
const data = ref({
    larger_desktop: '',
    large_desktop: '',
    desktop: '',
    tablet: '',
    landscape_mobile: '',
    mobile: '',
    postfix: {
        'larger_desktop' : '',
        'large_desktop' : '',
        'desktop' : '',
        'tablet'  : '',
        'landscape_mobile'  : '',
        'mobile'  : ''
    }
})
const placeholder = ref({
    'larger_desktop' : '',
    'large_desktop' : '',
    'desktop' : '',
    'tablet'  : '',
    'landscape_mobile'  : '',
    'mobile'  : ''
});
function init() {
    if (props.modelValue !== '') {
        try {
            const tmp = JSON.parse(props.modelValue);
            if (typeof tmp === 'object' && !Array.isArray(tmp) && tmp !== null) {
                data.value    = JSON.parse(props.modelValue);
            } else {
                data.value.mobile = props.modelValue;
            }
            if (typeof data.value.postfix === 'undefined') {
                data.value.postfix = {
                    'larger_desktop' : '',
                    'large_desktop' : '',
                    'desktop' : '',
                    'tablet'  : '',
                    'landscape_mobile'  : '',
                    'mobile'  : ''
                }
            }
        } catch (e) {
            data.value.mobile = props.modelValue;
            if (typeof data.value.postfix === 'undefined') {
                data.value.postfix = {
                    'larger_desktop' : '',
                    'large_desktop' : '',
                    'desktop' : '',
                    'tablet'  : '',
                    'landscape_mobile'  : '',
                    'mobile'  : ''
                }
            }
        }
    }
}
const postfix = ref([])
onBeforeMount(()=>{
    postfix.value = props.field.input.postfix.split('|');
    init();
})
onMounted(()=>{
    if (typeof data.value.postfix === 'undefined' || !data.value.postfix[active.value]) {
        Object.keys(data.value.postfix).forEach(key => {
            data.value.postfix[key] = postfix.value[0];
        });
    }
    updatePlaceholder();
    devices.forEach(device => {
        if (data.value[device] && active.value !== device) {
            fieldChanged.value = true;
            active.value = device;
        }
    })
})
onUpdated(()=>{
    if (props.presetUpdated === true) {
        emit('update:Preset', false);
        init();
    }
    updatePlaceholder();
})
const fieldChanged = ref(false);
function updatePlaceholder() {
    let lastDevice = '';
    devices.forEach(device => {
        placeholder.value[device] = lastDevice;
        if (data.value[device]) {
            lastDevice = 'Inherit value ' + data.value[device] + data.value.postfix[device] + ' from ' + language['ASTROID_' + device.toUpperCase()];
        }
    })
}

function updatePostfix(postfix) {
    data.value.postfix[active.value] = postfix;
}

const data_text = computed(() => {
    return props.field.input.responsive ? JSON.stringify(data.value) : data.value[active.value];
})
watch(data_text, (newText) => {
    if (newText !== props.modelValue) {
        emit('update:modelValue', newText);
    }
})
</script>
<template>
    <div class="row gx-3">
        <div class="col">
            <div class="input-group input-group-sm">
                <input type="number" :id="props.field.input.id" class="form-control" aria-label="Range Number" v-model="data[active]" :placeholder="placeholder[active]">
                <button v-if="props.field.input.responsive" class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ data.postfix[active] }}</button>
                <ul v-if="props.field.input.responsive" class="dropdown-menu dropdown-menu-end">
                    <li v-for="unit in postfix"><a class="dropdown-item" href="#" @click.prevent="updatePostfix(unit)">{{ unit }}</a></li>
                </ul>
                <span v-else class="input-group-text">{{ postfix[0] }}</span>
            </div>
        </div>
        <div v-if="props.field.input.responsive" class="col-auto d-flex align-items-center"><i class="fa-solid fa-ellipsis-vertical fa-sm opacity-50"></i></div>
        <div v-if="props.field.input.responsive" class="col-auto"><ResponsiveToggle v-model="active" :fieldChanged="fieldChanged" @update:statusField="status => (fieldChanged = status)" /></div>
    </div>
    <input type="range" class="form-range" v-model="data[active]" :min="props.field.input.min" :max="props.field.input.max" :step="props.field.input.step" :id="props.field.input.id+'_'+active">
    <input
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>