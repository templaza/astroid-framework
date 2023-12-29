<script setup>
import { onMounted, ref } from 'vue';

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const data = ref('');

onMounted(()=>{
    data.value  =   props.modelValue;
})
</script>
<template>
</template>