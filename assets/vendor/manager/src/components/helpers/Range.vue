<script setup>
import {onBeforeMount, onUpdated, ref, watch, computed, inject} from 'vue';

const emit = defineEmits(['update:modelValue', 'update:Preset']);
const props = defineProps(['modelValue', 'field', 'update']);
const language  =   inject('language', []);
const devices = [
    'mobile', 'landscape_mobile', 'tablet', 'desktop', 'large_desktop', 'larger_desktop'
]
const active = ref('mobile');
const data = ref({
    mobile: '',
    landscape_mobile: '',
    tablet: '',
    desktop: '',
    large_desktop: '',
    larger_desktop: ''
})
const icons = {
    larger_desktop: 'fa-solid fa-tv',
    large_desktop: 'fa-solid fa-desktop',
    desktop: 'fa-solid fa-computer',
    tablet: 'fa-solid fa-laptop',
    landscape_mobile: 'fa-solid fa-tablet',
    mobile: 'fa-solid fa-mobile'
}
const texts = {
    larger_desktop: language.ASTROID_XXL,
    large_desktop: language.ASTROID_XL,
    desktop: language.ASTROID_DESKTOP,
    tablet: language.ASTROID_LAPTOP,
    landscape_mobile: language.ASTROID_SM,
    mobile: language.JDEFAULT
}
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
    if (props.update === true) {
        emit('update:Preset', false);
        init();
    }
})

const data_text = computed(() => {
    return JSON.stringify(data.value);
})
watch(data_text, (newText) => {
    if (newText !== props.modelValue) {
        emit('update:modelValue', newText);
    }
})
</script>
<template>
    <div class="row gx-3">
        <div class="col"><input type="number" class="form-control form-control-sm" aria-label="Range Number" v-model="data[active]"></div>
        <div class="col-auto"><label :for="props.field.input.id+'_'+active" class="form-label">{{ props.field.input.postfix }}</label></div>
        <div class="col-auto d-flex align-items-center"><i class="fa-solid fa-ellipsis-vertical fa-sm opacity-50"></i></div>
        <div class="col-auto d-flex align-items-center"><i :class="icons[active]"></i></div>
        <div class="col-auto">
            <select v-model="active" class="form-select form-select-sm">
                <option v-for="device in devices" :value="device">{{ texts[device] }}</option>
            </select>
        </div>
    </div>
    <input type="range" class="form-range" v-model="data[active]" :min="props.field.input.min" :max="props.field.input.max" :step="props.field.input.step" :id="props.field.input.id+'_'+active">
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>