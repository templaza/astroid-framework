<script setup>
import { onMounted, onUpdated, ref, watch } from 'vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const content = ref('');

onMounted(()=>{
    content.value = props.modelValue;
})

onUpdated(()=>{
    if (content.value !== props.modelValue) {
        content.value = props.modelValue;
    }
})

watch(content, (newText) => {
    if (newText !== props.modelValue) {
        emit('update:modelValue', newText);
    }
})
</script>
<template>
    <QuillEditor v-model:content="content" content-type="html" toolbar="full"
    :options="{
        placeholder: 'Your content here ...'
    }">
    </QuillEditor>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>