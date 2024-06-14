<script setup>
import axios from "axios";
import { ref, reactive, onMounted, inject } from "vue";
import Layout from "./Layout.vue";

const props = defineProps({
    field: { type: Object, default: null }
});
const constant  =   inject('constant', {});
const language  =   inject('language', []);
const items     =   ref([]);
const editItem  =   ref(false);
const layout    =   ref('{"sections":[]}')
const formInfo = reactive({
    title: '',
    desc: '',
    thumbnail: '',
    name: ''
});
const toast_msg = reactive({
    header: '',
    body:'',
    icon: '',
    color:'darkviolet'
});
const save_disabled = ref(false);
const files = ref(null);
const checklist = ref([]);

onMounted(() => {
    callAjax();
})

function editLayout(filename = '') {
    if (filename !== '') {
        let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=getlayout&ts="+Date.now();
        if (process.env.NODE_ENV === 'development') {
            url = "editlayout_ajax.txt?ts="+Date.now();
        }
        const formData = new FormData(); // pass data as a form
        formData.append(constant.astroid_admin_token, 1);
        formData.append('name', filename);
        formData.append('template', constant.tpl_template_name);
        axios.post(url, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
        .then((response) => {
            if (response.data.status === 'success') {
                layout.value = response.data.data.data;
                formInfo.title = response.data.data.title;
                formInfo.desc = response.data.data.desc;
                formInfo.thumbnail = response.data.data.thumbnail;
                formInfo.name = filename;
            }
        })
        .catch((err) => {
            console.error(err);
        });
    }
    editItem.value = true;
}

function onFileChange(e) {
    files.value = e.target.files || e.dataTransfer.files;
}

function saveLayout() {
    let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=savelayout&ts="+Date.now();
    const formData = new FormData(); // pass data as a form
    const toastAstroidMsg = document.getElementById(props.field.input.id+`_saveLayoutToast`);
    const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
    formData.append(constant.astroid_admin_token, 1);
    formData.append('title', formInfo.title);
    formData.append('desc', formInfo.desc);
    formData.append('data', layout.value);
    formData.append('thumbnail_old', formInfo.thumbnail);
    if (files.value !== null && files.value.length) {
        formData.append('thumbnail', files.value[0]);
    }
    if (formInfo.name !== ``) {
        formData.append('name', formInfo.name);
    }
    formData.append('template', constant.tpl_template_name);
    save_disabled.value = true;
            
    axios.post(url, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
    .then((response) => {
        if (response.data.status === 'success') {
            toast_msg.icon = 'fa-solid fa-rocket';
            toast_msg.header = 'Sub-Layout '+formInfo.title+' is saved.';
            toast_msg.body = 'You can use it to contribute to your layout builder.';
            toast_msg.color = 'green';
            save_disabled.value = false;
            editItem.value = false;
            resetValues();
            callAjax();
            document.getElementById(props.field.input.id+`_saveLayout_close`).click();
        } else {
            toast_msg.icon = 'fa-regular fa-face-sad-tear';
            toast_msg.header = 'Sub-layout '+formInfo.title+' is not saved.';
            toast_msg.body = response.data.message;
            toast_msg.color = 'red';
        }
        toastBootstrap.show();
    })
    .catch((err) => {
        console.error(err);
    });
}

function resetValues() {
    layout.value = '{"sections":[]}';
    formInfo.title = '';
    formInfo.desc = '';
    formInfo.name = '';
    formInfo.thumbnail = '';
    files.value = null;
}

function cancelLayout() {
    if (confirm('Are you sure?')) {
        editItem.value = false;
        resetValues();
    }
}

function deleteLayout() {
    if (confirm(language.JGLOBAL_CONFIRM_DELETE) && checklist.value.length) {
        let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=deletelayouts&ts="+Date.now();
        const formData = new FormData(); // pass data as a form
        const toastAstroidMsg = document.getElementById(props.field.input.id+`_saveLayoutToast`);
        const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
        formData.append(constant.astroid_admin_token, 1);
        checklist.value.forEach(element => {
            formData.append('layouts[]', element);
        });
        formData.append('template', constant.tpl_template_name);
        axios.post(url, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
        .then((response) => {
            if (response.data.status === 'success') {
                if (response.data.data) {
                    toast_msg.icon = 'fa-solid fa-rocket';
                    toast_msg.header = 'Sub-Layouts deleted.';
                    toast_msg.body = 'You cannot undo this process.';
                    toast_msg.color = 'green';
                } else {
                    toast_msg.icon = 'fa-regular fa-face-sad-tear';
                    toast_msg.header = 'Error!';
                    toast_msg.body = response.data.message;
                    toast_msg.color = 'red';
                }
                callAjax();
                toastBootstrap.show();
            }
        })
        .catch((err) => {
            console.error(err);
        });
    }
}

function callAjax() {
    let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=getlayouts&template="+constant.tpl_template_name+"&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "layout_ajax.txt?ts="+Date.now();
    }
    axios.get(url)
    .then(function (response) {
        if (response.data.status === 'success') {
            items.value = response.data.data;
        }
    })
    .catch(function (error) {
        // handle error
        console.log(error);
    });
}

const checkAll = ref(false);
function checkAllList() {
    checklist.value = [];
    if (!checkAll.value) {
        items.value.forEach(element => {
            checklist.value.push(element.name);
        });
    } 
}
</script>
<template>
    <div>
        <div class="as-sublayouts" v-if="editItem === false">
            <div v-if="items.length === 0">
                <div class="alert alert-info" role="alert">
                    There are no layouts in this template.
                </div>
            </div>
            <table v-else class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" width="1%"><input class="form-check-input" type="checkbox" value="" v-model="checkAll" @click="checkAllList"></th>
                        <th scope="col">Title</th>
                        <th scope="col">Description</th>
                        <th scope="col" width="180">Thumbnail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in items">
                        <td scope="row"><input class="form-check-input" type="checkbox" :value="item.name" v-model="checklist"></td>
                        <td><a href="#" :title="`Edit: ` + item.title" class="link-body-emphasis link-offset-2 link-underline-opacity-0 link-underline-opacity-75-hover" @click.prevent="editLayout(item.name)">{{ item.title }}</a></td>
                        <td>{{ item.desc }}</td>
                        <td><img v-if="item.thumbnail !== ``" class="img-fluid" :src="item.thumbnail" :alt="item.title"></td>
                    </tr>
                </tbody>
            </table>
            <div class="as-sublayout-bottom-toolbox sticky-bottom bg-body-tertiary px-4 py-3 border border-bottom-0 rounded-top-3 mt-5">
                <a href="#" @click.prevent="editLayout()" class="btn btn-sm btn-as btn-as-primary me-2">New Layout</a>
                <a href="#" @click.prevent="deleteLayout()" class="btn btn-sm btn-as btn-outline-danger">Delete Layout</a>
            </div>
        </div>
        <div v-else class="astroid-layout px-2">
            <Layout v-model="layout" :field="{
                id: props.field.id,
                input: {
                    id: props.field.input.id,
                    name: props.field.input.name,
                    value: JSON.parse(layout)
                }
            }" />
            <div class="modal fade" :id="props.field.input.id+`_saveLayout`" tabindex="-1" aria-labelledby="saveLayoutLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title fs-5" id="saveLayoutLabel">Layout Information</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" :id="props.field.input.id+`_saveLayout_close`"></button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <div class="mb-3">
                                    <label :for="props.field.input.id+`_saveLayout_title`" class="form-label">Title</label>
                                    <input type="text" v-model="formInfo.title" class="form-control" :id="props.field.input.id+`_saveLayout_title`" placeholder="Title" required>
                                </div>
                                <div class="mb-3">
                                    <label :for="props.field.input.id+`_saveLayout_desc`" class="form-label">Description</label>
                                    <textarea class="form-control" v-model="formInfo.desc" :id="props.field.input.id+`_saveLayout_desc`" rows="3"></textarea>
                                </div>
                                <div v-if="formInfo.thumbnail !== ``" class="mb-3">
                                    <img class="img-thumbnail" :src="constant.site_url + `/media/templates/site/` + constant.tpl_template_name + `/images/layouts/` + formInfo.thumbnail" :alt="formInfo.title">
                                </div>
                                <div class="mb-3">
                                    <label :for="props.field.input.id+`_saveLayout_thumbnail`" class="form-label">Select your thumbnail</label>
                                    <input class="form-control" type="file" @change="onFileChange" :id="props.field.input.id+`_saveLayout_thumbnail`">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-as btn-as-light" data-bs-dismiss="modal" aria-label="Close" :disabled="save_disabled">Back</button>
                            <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="saveLayout()" :disabled="save_disabled">Save Settings</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="as-sublayout-bottom-toolbox sticky-bottom bg-body-tertiary px-4 py-3 border border-bottom-0 rounded-top-3 mt-5">
                <a href="#" @click.prevent="" class="btn btn-sm btn-as btn-as-primary me-2" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`_saveLayout`">Save</a>
                <a href="#" @click.prevent="cancelLayout()" class="btn btn-sm btn-as btn-as-light">Cancel</a>
            </div>
        </div>
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div :id="props.field.input.id+`_saveLayoutToast`" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
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
    </div>
</template>