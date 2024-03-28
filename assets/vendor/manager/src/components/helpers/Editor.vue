<script setup>
import { onMounted, onUpdated, ref } from 'vue';
import Quill from 'quill';

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const quill = ref(Quill);
let content = props.modelValue;

const toolbarOptions = [
    [{ 'header': 1 }, { 'header': 2 }],               // custom button values
    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
    ['blockquote', 'code-block'],
    ['link', 'image', 'video'],

    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
    [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent

    [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
    [{ 'align': [] }],

    ['clean']                                         // remove formatting button
];

onMounted(()=>{
    quill.value = new Quill('#' + props.field.input.id + '_editor', {
        modules: {
            toolbar: toolbarOptions
        },
        theme: 'snow',
        readOnly: false
    });
    quill.value.on('text-change', (delta, oldDelta, source) => {
        if (source == 'api') {
            console.log('An API call triggered this change.');
        } else if (source == 'user') {
            emit('update:modelValue', quill.value.getSemanticHTML());
        }
    });
})

onUpdated(()=>{
    if (quill.value.getSemanticHTML() !== props.modelValue) {
        quill.value.setContents(htmlToDelta(props.modelValue));
    }
})

function htmlToDelta(html) {
    const div = document.createElement('div');
    div.setAttribute('id', 'htmlToDelta');
    div.innerHTML = `<div id="quillEditor" style="display:none">${html}</div>`;
    document.body.appendChild(div);
    const quill = new Quill('#quillEditor', {
        theme: 'snow',
    });
    const delta = quill.getContents();
    document.getElementById('htmlToDelta').remove();
    return delta;
}
</script>
<template>
    <div :id="props.field.input.id + `_editor`" v-html="`\n`+content+`\n`"></div>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>