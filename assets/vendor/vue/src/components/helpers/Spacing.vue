<script setup>
import { ref } from 'vue';

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const devices = ['desktop', 'tablet', 'mobile'];
const unitOptions = ['px', 'em', 'rem', 'pt', '%'];
const currentDevice = ref('desktop')
</script>
<template>
    Spacing
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>