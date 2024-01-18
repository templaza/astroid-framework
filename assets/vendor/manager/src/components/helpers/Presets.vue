<script setup>
import axios from "axios";
import { onBeforeMount, ref, reactive, onMounted, inject } from 'vue';

const emit = defineEmits(['update:loadPreset', 'update:getPreset']);
const props = defineProps({
    field: { type: Object, default: null }
});
const constant = inject('constant', {});
const toast_msg = reactive({
    header: '',
    body:'',
    icon: '',
    color:'darkviolet'
});
const list = ref([]);
const key_bg = ['#ffcdd2','#e1bee7','#bbdefb','#b2dfdb','#ffcc80'];
onBeforeMount(() => {
    list.value = props.field.input.value;
    emit('update:getPreset', props.field.input.value);
})

onMounted(()=>{
    const presetModal = document.getElementById('addPresetModal')
    presetModal.addEventListener('hidden.bs.modal', event => {
        modalType.value = '';
    })
})

function loadPreset(preset) {
    if (confirm('Your current configure will be lost and overwritten by new data. Are you sure?')) {
        const toastAstroidMsg = document.getElementById('loadPreset');
        const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
        let url = 'index.php?t='+Math.random().toString(36).substring(7);
        if (process.env.NODE_ENV === 'development') {
            url = "preset_ajax.txt?ts="+Date.now();
        }
        const formData = new FormData(); // pass data as a form
        formData.append(constant.astroid_admin_token, 1);
        formData.append('name', preset.name);
        formData.append('astroid', 'loadpreset');
        formData.append('option', 'com_ajax');
        formData.append('template', constant.tpl_template_name);
        axios.post(url, formData, {
            headers: {
            "Content-Type": "multipart/form-data",
            },
        })
        .then((response) => {
            if (response.data.status === 'success') {
                emit('update:loadPreset', response.data.data);
                toast_msg.icon = 'fa-solid fa-rocket';
                toast_msg.header = 'Preset '+preset.title+' Applied.';
                toast_msg.body = 'Please click "Save" button to save your changes!';
                toast_msg.color = 'green';
                toastBootstrap.show();
            } else {
                toast_msg.icon = 'fa-regular fa-face-sad-tear';
                toast_msg.header = 'Preset '+preset.title+' is not Applied.';
                toast_msg.body = response.data.message;
                toast_msg.color = 'red';
                toastBootstrap.show();
            }
        })
        .catch((err) => {
            console.error(err);
        });
    }
}

function deletePreset(index) {
    if (confirm('Are you sure?')) {
        const toastAstroidMsg = document.getElementById('loadPreset');
        const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
        let url = 'index.php?t='+Math.random().toString(36).substring(7);
        if (process.env.NODE_ENV === 'development') {
            url = "preset_ajax.txt?ts="+Date.now();
        }
        const formData = new FormData(); // pass data as a form
        formData.append(constant.astroid_admin_token, 1);
        formData.append('name', list.value[index].name);
        formData.append('astroid', 'removepreset');
        formData.append('option', 'com_ajax');
        formData.append('template', constant.tpl_template_name);
        axios.post(url, formData, {
            headers: {
            "Content-Type": "multipart/form-data",
            },
        })
        .then((response) => {
            if (response.data.status === 'success') {
                toast_msg.icon = 'fa-solid fa-trash';
                toast_msg.header = 'Preset has been removed.'
                toast_msg.body = list.value[index].title+' preset has been removed. This action cannot be undo.'
                toast_msg.color = 'red';
                toastBootstrap.show();
                list.value.splice(index, 1);
            } else {
                toast_msg.icon = 'fa-regular fa-face-sad-tear';
                toast_msg.header = 'Preset '+list.value[index].title+' is not deleted.';
                toast_msg.body = response.data.message;
                toast_msg.color = 'red';
                toastBootstrap.show();
            }
        })
        .catch((err) => {
            console.error(err);
        });
    }
}

const modalType = ref('');
function showModal() {
    const myModal = new Modal('#addPresetModal');
    myModal.show();
}

// Save Preset
const presetTitle = ref(null);
const save_disabled = ref(false);
const formInfo = reactive({
    title: '',
    description: ''
});
function initSavePreset() {
    modalType.value = 'save';
    formInfo.title = '';
    formInfo.description = '';
}
function savePreset() {
    if (!formInfo.title) {
        alert('Title cannot be empty!')
        presetTitle.value.focus();
        return false;
    }
    const action_link = constant.astroid_action.replace(/\&amp\;/g, '&');
    const form = document.getElementById('astroid-form');
    const toastAstroidMsg = document.getElementById('loadPreset');
    const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
    const formData = new FormData(form); // pass data as a form;
    formData.append('astroid-preset', 1);
    formData.append('astroid-preset-name', formInfo.title);
    formData.append('astroid-preset-desc', formInfo.description);
    save_disabled.value = true;
    axios.post(action_link, formData, {
        headers: {
        "Content-Type": "multipart/form-data",
        },
    })
    .then((response) => {
        toast_msg.icon = 'fa-solid fa-floppy-disk';
        if (response.data.status === 'success') {
            toast_msg.header= 'Preset has been saved';
            toast_msg.body = 'Preset '+formInfo.title+' has been created';
            toast_msg.color = 'green';
            list.value.push({
                title: formInfo.title,
                desc: formInfo.description,
                keyword: formInfo.title.charAt(0),
                thumbnail: '',
                demo: '',
                name: response.data.data
            })
        } else {
            toast_msg.header= 'Preset did not saved yet';
            toast_msg.body = response.data.message;
            toast_msg.color = 'red';
        }
        save_disabled.value = false;
        toastBootstrap.show();
        document.getElementById('closePresetModal').click();
    })
    .catch((err) => {
        console.error(err);
    });
}

// Import preset
const files = ref(null);
function initImportPreset() {
    modalType.value = 'import';
    formInfo.title = '';
    formInfo.description = '';
}
function onFileChange(e) {
    files.value = e.target.files || e.dataTransfer.files;
}
function uploadPreset() {
    if (files.value === null || !files.value.length) {
        alert('You have to select a JSON file to upload.')
        return false;
    }
    if (!formInfo.title) {
        alert('Title cannot be empty!')
        presetTitle.value.focus();
        return false;
    }
    let url = 'index.php?t='+Math.random().toString(36).substring(7);
    const toastAstroidMsg = document.getElementById('loadPreset');
    const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
    const formData = new FormData(); // pass data as a form;
    formData.append(constant.astroid_admin_token, 1);
    formData.append('title', formInfo.title);
    formData.append('desc', formInfo.description);
    formData.append('file', files.value[0]);
    formData.append('astroid', 'importpreset');
    formData.append('option', 'com_ajax');
    formData.append('template', constant.tpl_template_name);
    axios.post(url, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
    .then((response) => {
        toast_msg.icon = 'fa-solid fa-upload';
        if (response.data.status === 'success') {
            toast_msg.header= 'Preset has been uploaded';
            toast_msg.body = 'Preset '+formInfo.title+' has been created. Click "Load Preset" to load your settings.';
            toast_msg.color = 'green';
            list.value.push({
                title: formInfo.title,
                desc: formInfo.description,
                keyword: formInfo.title.charAt(0),
                thumbnail: '',
                demo: '',
                name: response.data.data
            })
        } else {
            toast_msg.header= 'Preset did not uploaded yet';
            toast_msg.body = response.data.message;
            toast_msg.color = 'red';
        }
        toastBootstrap.show();
        document.getElementById('closePresetModal').click();
    })
    .catch((err) => {
        console.error(err);
    });
}

const download = async (url, filename) => {
    const data = await fetch(url);
    const blob = await data.blob();
    const objectUrl = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', objectUrl);
    link.setAttribute('download', filename);
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportPreset(preset) {
    download(constant.site_url+'/media/templates/site/'+constant.tpl_template_name+'/astroid/presets/'+preset.name+'.json', preset.name+'.json');
}
</script>
<template>
    <div class="row row-cols-lg-3 row-cols-2 g-3 g-lg-4">
        <div v-for="(preset, index) in list">
            <div class="preset-item card card-default position-relative">
                <img v-if="preset.thumbnail !== ''" :src="preset.thumbnail" :alt="preset.title" class="card-img-top">
                <div v-else class="preset-keyword d-flex justify-content-center align-items-center card-img-top" :style="{'background-color' : key_bg[Math.floor(Math.random() * 5)]}">
                    {{ preset.keyword }}
                </div>
                <div class="card-body">
                    <h5>{{ preset.title }}</h5>
                    <p v-if="preset.desc !==''">{{ preset.desc }}</p>
                    <button class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="loadPreset(preset)">Load Preset</button>
                    <a v-if="preset.demo" class="btn btn-sm btn-as btn-as-light ms-2" :href="preset.demo">Demo</a>
                    <div class="preset-toolbar">
                        <a href="#" @click.prevent="exportPreset(preset)" class="link-primary me-2" title="Export">
                            <font-awesome-layers class="fa-2x">
                                <font-awesome-icon :icon="['fas', 'circle']" />
                                <font-awesome-icon :icon="['fas', 'download']" transform="shrink-9" :style="{ color: 'white' }" />
                            </font-awesome-layers>
                        </a>
                        <a href="#" @click.prevent="deletePreset(index)" class="link-danger" title="Delete">
                            <font-awesome-layers class="fa-2x">
                                <font-awesome-icon :icon="['fas', 'circle']" />
                                <font-awesome-icon :icon="['fas', 'trash']" transform="shrink-9" :style="{ color: 'white' }" />
                            </font-awesome-layers>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="add-preset card card-default card-body h-100 d-flex justify-content-center align-items-center" @click.prevent="showModal">
                <i class="fa-solid fa-plus fa-3x"></i>
                <div class="mt-2">Save / Import</div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addPresetModal" tabindex="-1" aria-labelledby="addPresetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addPresetModalLabel">Save/Import Preset</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closePresetModal"></button>
                </div>
                <div class="modal-body">
                    <div v-if="!modalType" class="row row-cols-2 g-3">
                        <div>
                            <div class="add-preset-cta card card-default card-body d-flex justify-content-center align-items-center" @click.prevent="initSavePreset">
                                <i class="fa-solid fa-floppy-disk fa-2x"></i>
                                <div class="mt-2 form-text">Save Preset</div>
                            </div>
                        </div>
                        <div>
                            <div class="add-preset-cta card card-default card-body d-flex justify-content-center align-items-center" @click.prevent="initImportPreset">
                                <i class="fa-solid fa-upload fa-2x"></i>
                                <div class="mt-2 form-text">Import Preset</div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="mb-3">
                            <label for="preset-title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="preset-title" v-model="formInfo.title" placeholder="Your preset name" ref="presetTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="preset-description" class="form-label">Description</label>
                            <textarea class="form-control" id="preset-description" v-model="formInfo.description" placeholder="Describe about your preset"></textarea>
                        </div>
                        <div v-if="modalType === `import`" class="mb-3">
                            <label for="presetFile" class="form-label">Select your preset file</label>
                            <input class="form-control" type="file" @change="onFileChange" id="presetFile">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" v-if="modalType === `save`">
                    <button type="button" class="btn btn-sm btn-as btn-as-light" @click.prevent="modalType = ``" :disabled="save_disabled">Back</button>
                    <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="savePreset" :disabled="save_disabled">Save Settings</button>
                </div>
                <div class="modal-footer" v-else-if="modalType === `import`">
                    <button type="button" class="btn btn-sm btn-as btn-as-light" @click.prevent="modalType = ``">Back</button>
                    <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="uploadPreset">Submit</button>
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