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
const fullscreen = ref(false);
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
    <div v-if="typeof props.field.input.code !== 'undefined' && props.field.input.code !== ''">
        <vue-monaco-editor
            v-model:value="code"
            :theme="(theme === 'light' ? 'light' : 'vs-dark')"
            :language="props.field.input.code"
            height="200px"
            className="border"
            :options="MONACO_EDITOR_OPTIONS"
            @change="handleChange"
        />
        <div class="d-grid">
            <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary rounded-0" @click.prevent="fullscreen = true">
                <i class="fas fa-expand me-1"></i> Edit in Fullscreen
            </button>
        </div>
        <div class="modal d-block" :id="props.field.input.id+`_full_editor`" tabindex="-1" v-if="fullscreen">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-html="props.field.label"></h5>
                        <button type="button" class="btn-close" aria-label="Close" @click.prevent="fullscreen = false"></button>
                    </div>
                    <div class="modal-body">
                        <vue-monaco-editor
                        v-model:value="code"
                            :theme="(theme === 'light' ? 'light' : 'vs-dark')"
                            :language="props.field.input.code"
                            height="100%"
                            className="border"
                            :options="MONACO_EDITOR_OPTIONS"
                            @change="handleChange"
                        />
                    </div>
                </div>
            </div>
        </div>
        <textarea class="d-none" :id="props.field.input.id" :name="props.field.input.name" v-text="modelValue"></textarea>
    </div>
    <textarea v-else class="form-control" :id="props.field.input.id" :name="props.field.input.name" rows="8" :placeholder="props.field.input.hint" v-text="modelValue" @input="emit('update:modelValue', $event.target.value)"></textarea>
</template>