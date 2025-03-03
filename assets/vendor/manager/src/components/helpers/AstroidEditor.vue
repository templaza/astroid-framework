<script setup>
import {inject, onUpdated, ref} from 'vue';
import Editor from '@tinymce/tinymce-vue'

const emit = defineEmits(['update:modelValue', 'update:Preset']);
const props = defineProps(['modelValue', 'field', 'presetUpdated']);
const constant  =   inject('constant', {});
const theme = inject('theme', 'light');
const content = ref(props.modelValue);

const MONACO_EDITOR_OPTIONS = {
    automaticLayout: true,
    formatOnType: true,
    formatOnPaste: true,
    wordWrap: 'on',
    height: '200px',
}

const field_id = props.field.input.id + '-' + (Date.now() * 1000 + Math.random() * 1000).toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);

onUpdated(()=>{
    if (props.presetUpdated === true) {
        emit('update:Preset', false);
        content.value = props.modelValue;
    }
})
function handleChange() {
    emit('update:modelValue', content.value);
}
const _isloading = ref(true);
function handleInit(editor) {
    _isloading.value = false;
}
</script>
<template>
    <ul class="nav nav-tabs" :id="field_id + `-my-editor`" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" :id="field_id + `-editor-tab`" data-bs-toggle="tab" :data-bs-target="`#` + field_id + `-editor-tab-pane`" type="button" role="tab" :aria-controls="field_id + `-editor-tab-pane`" aria-selected="true">Editor</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" :id="field_id + `-html-tab`" data-bs-toggle="tab" :data-bs-target="`#` + field_id + `-html-tab-pane`" type="button" role="tab" :aria-controls="field_id + `-html-tab-pane`" aria-selected="false">View Source</button>
        </li>
    </ul>
    <div class="tab-content" :id="field_id + `-my-editor-content`">
        <div class="tab-pane fade show active" :id="field_id + `-editor-tab-pane`" role="tabpanel" :aria-labelledby="field_id + `-editor-tab`" tabindex="0">
            <div v-show="!_isloading">
                <Editor
                    v-model="content"
                    :licenseKey=constant.tiny_mce_license
                    :init="{
                    plugins: 'lists link image table media wordcount fullscreen searchreplace charmap codesample visualblocks preview',
                    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media codesample | removeformat searchreplace | fullscreen',
                }"
                    @change="handleChange"
                    @init="handleInit"
                />
            </div>
            <div v-if="_isloading" class="loading d-flex justify-content-center flex-column w-100" style="align-items: center;">
                <i class="fa-solid fa-basketball fa-bounce fa-3x" style="--fa-bounce-land-scale-x: 1.2;--fa-bounce-land-scale-y: .8;--fa-bounce-rebound: 5px;"></i>
                <div class="fa-beat-fade mt-3" style="--fa-beat-fade-opacity: 0.1; --fa-beat-fade-scale: 1.05;">Loading...</div>
            </div>
        </div>
        <div class="tab-pane fade" :id="field_id + `-html-tab-pane`" role="tabpanel" :aria-labelledby="field_id + `-html-tab`" tabindex="0">
            <vue-monaco-editor
                v-model:value="content"
                :theme="(theme === 'light' ? 'light' : 'vs-dark')"
                language="html"
                height="200px"
                className="border"
                :options="MONACO_EDITOR_OPTIONS"
                @change="handleChange"
            />
        </div>
    </div>
    <input
        :id="field_id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>