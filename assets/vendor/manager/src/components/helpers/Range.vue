<script setup>
import ResponsiveToggle from "./ResponsiveToggle.vue";
import {onBeforeMount, onUpdated, ref, watch, computed, inject} from 'vue';

const emit = defineEmits(['update:modelValue', 'update:Preset']);
const props = defineProps(['modelValue', 'field', 'presetUpdated']);
const language  =   inject('language', []);
const active = ref('mobile');
const data = ref({
    mobile: '',
    landscape_mobile: '',
    tablet: '',
    desktop: '',
    large_desktop: '',
    larger_desktop: ''
})
function init() {
    if (props.modelValue !== '') {
        try {
            const tmp = JSON.parse(props.modelValue);
            if (typeof tmp === 'object' && !Array.isArray(tmp) && tmp !== null) {
                data.value    = JSON.parse(props.modelValue);
            } else {
                data.value.mobile = props.modelValue;
            }
        } catch (e) {
            data.value.mobile = props.modelValue;
        }
    }
}
onBeforeMount(()=>{
    init();
})

onUpdated(()=>{
    if (props.presetUpdated === true) {
        emit('update:Preset', false);
        init();
    }
})

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
        <div class="col"><input type="number" :id="props.field.input.id" class="form-control form-control-sm" aria-label="Range Number" v-model="data[active]"></div>
        <div class="col-auto"><label :for="props.field.input.id+'_'+active" class="form-label">{{ props.field.input.postfix }}</label></div>
        <div v-if="props.field.input.responsive" class="col-auto d-flex align-items-center"><i class="fa-solid fa-ellipsis-vertical fa-sm opacity-50"></i></div>
        <div v-if="props.field.input.responsive" class="col-auto"><ResponsiveToggle v-model="active" /></div>
    </div>
    <input type="range" class="form-range" v-model="data[active]" :min="props.field.input.min" :max="props.field.input.max" :step="props.field.input.step" :id="props.field.input.id+'_'+active">
    <input
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>