<script setup>
import {onUpdated, ref, watch, onMounted} from 'vue';

const emit = defineEmits(['update:modelValue', 'update:Preset']);
const props = defineProps(['modelValue', 'field', 'update']);
onMounted(()=>{
    if (parseInt(props.modelValue) === 1) {
        switchField.value = true;
    }
})
onUpdated(()=>{
    if (props.update === true) {
        emit('update:Preset', false);
        switchField.value = parseInt(props.modelValue) === 1;
    }
})
// Switch Field
const switchField = ref(false);
watch(switchField, (newValue) => {
    if (newValue) {
        emit('update:modelValue', 1)
    } else {
        emit('update:modelValue', 0)
    }
})
</script>
<template>
    <div class="form-check form-switch">
        <input v-model="switchField" class="form-check-input" type="checkbox" role="switch" :id="props.field.input.id">
        <input type="hidden" :name="props.field.input.name" :value="modelValue">
    </div>
</template>