<script setup>
import { Codemirror } from 'vue-codemirror';
import { javascript } from '@codemirror/lang-javascript';
import { html } from '@codemirror/lang-html';
import { css } from '@codemirror/lang-css';
import { onMounted, ref } from 'vue';
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const code = ref('');
onMounted(()=>{
    code.value = props.modelValue;
})
</script>
<template>
    <codemirror v-if="props.field.input.code === 'html'"
    v-model="code"
    :placeholder="props.field.input.hint"
    :style="{ height: '200px' }"
    :autofocus="true"
    :indent-with-tab="true"
    :tab-size="2"
    :extensions="[html()]"
    @update:model-value="emit('update:modelValue', code)"
    />
    <codemirror v-else-if="props.field.input.code === 'javascript'"
    v-model="code"
    :placeholder="props.field.input.hint"
    :style="{ height: '200px' }"
    :autofocus="true"
    :indent-with-tab="true"
    :tab-size="2"
    :extensions="[javascript()]"
    @update:model-value="emit('update:modelValue', code)"
    />
    <codemirror v-else-if="props.field.input.code === 'css'"
    v-model="code"
    :placeholder="props.field.input.hint"
    :style="{ height: '200px' }"
    :autofocus="true"
    :indent-with-tab="true"
    :tab-size="2"
    :extensions="[css()]"
    @update:model-value="emit('update:modelValue', code)"
    />
    <textarea v-else class="form-control" :id="props.field.input.id" :name="props.field.input.name" :rows="props.field.input.rows" :placeholder="props.field.input.hint" v-text="modelValue" @input="emit('update:modelValue', $event.target.value)"></textarea>
    <textarea v-if="['html', 'javascript', 'css'].includes(props.field.input.code)" class="d-none" :id="props.field.input.id" :name="props.field.input.name" v-text="modelValue"></textarea>
</template>