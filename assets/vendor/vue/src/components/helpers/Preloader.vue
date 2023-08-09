<script setup>
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    field: { type: Object, default: null },
    modelValue: { type: String, default: '' },
});
</script>
<template>

</template>