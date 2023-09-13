<script setup>
import axios from "axios";
import { onBeforeMount, ref, reactive } from 'vue';

const emit = defineEmits(['update:loadPreset']);
const props = defineProps({
    field: { type: Object, default: null },
    config: { type: Object, default: null },
});
const toast_msg = reactive({
    header: '',
    body:'',
    icon: '',
    color:'darkviolet'
});
const list = ref([]);
onBeforeMount(() => {
    list.value = props.field.input.value;
})

function loadPreset(preset) {
    if (confirm('Your current configure will be lost and overwritten by new data. Are you sure?')) {
        const toastAstroidMsg = document.getElementById('loadPreset');
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastAstroidMsg);
        let url = 'index.php?t='+Math.random().toString(36).substring(7);
        if (process.env.NODE_ENV === 'development') {
            url = "preset_ajax.txt?ts="+Date.now();
        }
        const formData = new FormData(); // pass data as a form
        formData.append(props.config.astroid_admin_token, 1);
        formData.append('name', preset.name);
        formData.append('astroid', 'loadpreset');
        formData.append('option', 'com_ajax');
        formData.append('template', props.config.tpl_template_name);
        axios.post(url, formData, {
            headers: {
            "Content-Type": "multipart/form-data",
            },
        })
        .then((response) => {
            if (response.data.status === 'success') {
                emit('update:loadPreset', response.data.data);
                toast_msg.icon = 'fa-solid fa-rocket';
                toast_msg.header = 'Preset '+preset.title+' Applied.'
                toast_msg.body = 'Please click "Save" button to save your changes!'
                toast_msg.color = 'green';
                toastBootstrap.show();
            }
        })
        .catch((err) => {
            console.error(err);
        });
    }
}
</script>
<template>
    <div class="row row-cols-lg-3 g-3">
        <div v-for="preset in list">
            <div class="card card-default">
                <img v-if="preset.thumbnail !== ''" :src="preset.thumbnail" :alt="preset.title" class="card-img-top">
                <div class="card-body">
                    <h5>{{ preset.title }}</h5>
                    <p v-if="preset.desc !==''">{{ preset.desc }}</p>
                    <button class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="loadPreset(preset)">Load Preset</button>
                    <a v-if="preset.demo" class="btn btn-sm btn-as btn-as-light ms-2" :href="preset.demo">Demo</a>
                </div>
            </div>
        </div>
    </div>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="loadPreset" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="me-2" :class="toast_msg.icon" :style="{color: toast_msg.color}"></i>
                <strong class="me-auto">{{ toast_msg.header }}</strong>
                <small>1 second ago</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ toast_msg.body }}
            </div>
        </div>
    </div>
</template>