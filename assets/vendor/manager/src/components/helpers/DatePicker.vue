<script setup>
import { ref, onBeforeMount, watch } from 'vue';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: { type: String, default: '' },
    field: { type: Object, default: null },
});

</script>
<template>
    <Datepicker :model-value="props.modelValue" @update:model-value="(value) => {emit('update:modelValue', value)}" model-type="yyyy/MM/dd HH:mm"/>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>