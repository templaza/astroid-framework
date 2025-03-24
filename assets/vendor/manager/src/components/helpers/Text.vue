<script setup>
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
</script>
<template>
    <input :value="props.modelValue" @input="emit('update:modelValue', $event.target.value)" type="text" :id="props.field.input.id" :name="props.field.input.name" class="astroid-text form-control" @keydown.enter.prevent="" :aria-label="props.field.label" :placeholder="props.field.input.hint">
</template>