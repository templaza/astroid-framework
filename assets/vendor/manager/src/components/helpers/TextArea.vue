<script setup>
import { onMounted, onUpdated, ref, inject } from 'vue';
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const theme = inject('theme', 'light');
const MONACO_EDITOR_OPTIONS = {
    automaticLayout: true,
    formatOnType: true,
    formatOnPaste: true,
    height: '200px',
}
const code = ref('');
function handleChange(newValue) {
    emit('update:modelValue', newValue);
}
onMounted(()=>{
    code.value = props.modelValue;
})
onUpdated(()=>{
    if (code.value !== props.modelValue) {
        code.value = props.modelValue;
    }
})
</script>
<template>
    <vue-monaco-editor v-if="typeof props.field.input.code !== 'undefined' && props.field.input.code !== ''"
        v-model:value="code"
        :theme="(theme === 'light' ? 'light' : 'vs-dark')"
        :language="props.field.input.code"
        height="200px"
        className="border"
        :options="MONACO_EDITOR_OPTIONS"
        @change="handleChange"
    />
    <textarea v-else class="form-control" :id="props.field.input.id" :name="props.field.input.name" :rows="props.field.input.rows" :placeholder="props.field.input.hint" v-text="modelValue" @input="emit('update:modelValue', $event.target.value)"></textarea>
    <textarea v-if="typeof props.field.input.code !== 'undefined' && props.field.input.code !== ''" class="d-none" :id="props.field.input.id" :name="props.field.input.name" v-text="modelValue"></textarea>
</template>