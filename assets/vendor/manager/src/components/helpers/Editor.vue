<script setup>
import { onMounted, onUpdated, ref, watch } from 'vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const editor = ref(ClassicEditor);
const editorData = ref('');

onMounted(()=>{
    editorData.value = props.modelValue;
})
onUpdated(()=>{
    if (content.value !== props.modelValue) {
        content.value = props.modelValue;
    }
})
watch(editorData, (newText) => {
    if (newText !== props.modelValue) {
        emit('update:modelValue', newText);
    }
})
</script>
<template>
    <ckeditor :editor="editor" v-model="editorData"></ckeditor>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>