<script setup>
import {onBeforeMount, onUpdated, ref, watch, computed} from 'vue';

const emit = defineEmits(['update:modelValue', 'update:Preset']);
const props = defineProps(['modelValue', 'field', 'update']);
const devices = [
    'xs', 'sm', 'md', 'lg', 'xl', 'xxl'
]
const active = ref('lg');
const data = ref({
    xs: '',
    sm: '',
    md: '',
    lg: '',
    xl: '',
    xxl: ''
})
const icons = {
    xxl: 'fa-solid fa-tv',
    xl: 'fa-solid fa-desktop',
    lg: 'fa-solid fa-computer',
    md: 'fa-solid fa-laptop',
    sm: 'fa-solid fa-tablet',
    xs: 'fa-solid fa-mobile'
}
function init() {
    if (props.modelValue !== '') {
        try {
            const tmp = JSON.parse(props.modelValue);
            if (typeof tmp === 'object' && !Array.isArray(tmp) && tmp !== null) {
                data.value    = JSON.parse(props.modelValue);
            } else {
                data.value.xs = data.value.sm = data.value.md = data.value.lg = props.modelValue;
            }
        } catch (e) {
            data.value.xs = data.value.sm = data.value.md = data.value.lg = props.modelValue;
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
                <option v-for="device in devices" :value="device">{{device}}</option>
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