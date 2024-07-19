<script setup>
import axios from "axios";
import { ref, reactive, onMounted, inject, onUpdated } from "vue";
import Layout from "./Layout.vue";
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: { type: String, default: '' },
    field: { type: Object, default: null },
    type: { type: String, default: 'layouts' },
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
    name: '',
    default: false
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

onUpdated(()=>{
    if (props.modelValue === 'update') {
        callAjax();
    }
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
        formData.append('type', props.type);
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
                editItem.value = true;
            }
        })
        .catch((err) => {
            console.error(err);
        });
    } else {
        editItem.value = true;
    }
}

function loadDefault() {
    let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=getlayout&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "editlayout_ajax.txt?ts="+Date.now();
    }
    const formData = new FormData(); // pass data as a form
    formData.append(constant.astroid_admin_token, 1);
    formData.append('template', constant.tpl_template_name);
    formData.append('type', props.type);
    axios.post(url, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
    .then((response) => {
        if (response.data.status === 'success') {
            layout.value = response.data.data.data;
        }
    })
    .catch((err) => {
        console.error(err);
    });
}

function onFileChange(e) {
    files.value = e.target.files || e.dataTransfer.files;
}

function saveLayout(action = 'save') {
    if (formInfo.title === '') {
        if (action === 'save_dialog') {
            alert('You have to input the Title')
            _formTitle.value.focus();
        } else {
            document.getElementById(props.field.input.id+`_saveLayout_opendialog`).click();
        }
        return true;
    }
    if (formInfo.default) {
        if (!confirm(language.TPL_ASTROID_OVERRIDE_DEFAULT_LAYOUT_WARNING)) {
            return true;
        }
    }
    let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=savelayout&ts="+Date.now();
    const formData = new FormData(); // pass data as a form
    const toastAstroidMsg = document.getElementById(props.field.input.id+`_saveLayoutToast`);
    const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
    formData.append(constant.astroid_admin_token, 1);
    formData.append('title', formInfo.title);
    formData.append('desc', formInfo.desc);
    formData.append('data', layout.value);
    formData.append('thumbnail_old', formInfo.thumbnail);
    formData.append('default', formInfo.default);
    if (files.value !== null && files.value.length) {
        formData.append('thumbnail', files.value[0]);
    }
    if (formInfo.name !== ``) {
        formData.append('name', formInfo.name);
    }
    formData.append('template', constant.tpl_template_name);
    formData.append('type', props.type);
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
            if (action !== 'apply') {
                editItem.value = false;
                resetValues();
                callAjax();
                document.getElementById(props.field.input.id+`_saveLayout_close`).click();
            }
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
    formInfo.default = false;
    files.value = null;
}

function cancelLayout() {
    if (confirm('Are you sure?')) {
        editItem.value = false;
        resetValues();
        callAjax();
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
        formData.append('type', props.type);
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
    let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=getlayouts&type="+props.type+"&template="+constant.tpl_template_name+"&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "layout_ajax.txt?ts="+Date.now();
    }
    axios.get(url)
    .then(function (response) {
        if (response.data.status === 'success') {
            items.value = response.data.data;
            emit('update:modelValue', '');
        }
    })
    .catch(function (error) {
        // handle error
        console.log(error);
    });
}
const _formTitle = ref(null);
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
                <div class="alert alert-info" role="alert">{{ language.TPL_ASTROID_NO_LAYOUT_INFO }}</div>
            </div>
            <table v-else class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" width="1%"><input class="form-check-input" type="checkbox" value="" v-model="checkAll" @click="checkAllList"></th>
                        <th scope="col">{{ language.JGLOBAL_TITLE }}</th>
                        <th scope="col">{{ language.JGLOBAL_DESCRIPTION }}</th>
                        <th scope="col" width="180">{{ language.TPL_ASTROID_THUMBNAIL }}</th>
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
                <a href="#" @click.prevent="editLayout()" class="btn btn-sm btn-as btn-as-primary me-2">{{ language.TPL_ASTROID_NEW_LAYOUT }}</a>
                <a href="#" @click.prevent="deleteLayout()" class="btn btn-sm btn-as btn-outline-danger">{{ language.TPL_ASTROID_DELETE_LAYOUT }}</a>
            </div>
        </div>
        <div v-else class="astroid-layout px-2">
            <Layout v-model="layout" :source="props.type" :field="{
                id: props.field.id,
                input: {
                    id: props.field.input.id,
                    name: props.field.input.name,
                    value: JSON.parse(layout)
                }
            }" />
            <div class="modal fade" :id="props.field.input.id+`_saveLayout`" tabindex="-1" :aria-labelledby="props.field.input.id+`saveLayoutLabel`" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title fs-5" :id="props.field.input.id+`_saveLayoutLabel`">{{ language.TPL_ASTROID_LAYOUT_INFO }}</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" :id="props.field.input.id+`_saveLayout_close`"></button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <div class="mb-3">
                                    <label :for="props.field.input.id+`_saveLayout_title`" class="form-label">{{ language.JGLOBAL_TITLE }}</label>
                                    <input type="text" v-model="formInfo.title" class="form-control" :id="props.field.input.id+`_saveLayout_title`" ref="_formTitle" placeholder="Title" required>
                                </div>
                                <div class="mb-3">
                                    <label :for="props.field.input.id+`_saveLayout_desc`" class="form-label">{{ language.JGLOBAL_DESCRIPTION }}</label>
                                    <textarea class="form-control" v-model="formInfo.desc" :id="props.field.input.id+`_saveLayout_desc`" rows="3"></textarea>
                                </div>
                                <div v-if="formInfo.thumbnail !== ``" class="mb-3">
                                    <img class="img-thumbnail" :src="constant.site_url + `/media/templates/site/` + constant.tpl_template_name + `/images/layouts/` + formInfo.thumbnail" :alt="formInfo.title">
                                </div>
                                <div class="mb-3">
                                    <label :for="props.field.input.id+`_saveLayout_thumbnail`" class="form-label">{{ language.TPL_ASTROID_SELECT_YOUR_THUMBNAIL }}</label>
                                    <input class="form-control" type="file" @change="onFileChange" :id="props.field.input.id+`_saveLayout_thumbnail`">
                                </div>
                                <div class="form-check" v-if="props.type === `article_layouts` && formInfo.name !== `default`">
                                    <input class="form-check-input" type="checkbox" v-model="formInfo.default" value="" :id="props.field.input.id+`_saveLayout_default`">
                                    <label class="form-check-label" :for="props.field.input.id+`_saveLayout_default`">{{ language.TPL_ASTROID_SAVE_AS_DEFAULT }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button v-if="formInfo.name !== `` && !formInfo.default" type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" data-bs-dismiss="modal" aria-label="Close" :disabled="save_disabled" v-html="language.JAPPLY"></button>
                            <button v-if="formInfo.name === `` || formInfo.default" type="button" class="btn btn-sm btn-as btn-as-light" data-bs-dismiss="modal" aria-label="Close" :disabled="save_disabled">{{ language.ASTROID_BACK }}</button>
                            <button v-if="formInfo.name === `` || formInfo.default" type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="saveLayout('save_dialog')" :disabled="save_disabled" v-html="language.JSAVE"></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="as-sublayout-bottom-toolbox sticky-bottom bg-body-tertiary px-4 py-3 border border-bottom-0 rounded-top-3 mt-5">
                <button :id="props.field.input.id+`_saveLayout_opendialog`" class="d-none" type="button" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`_saveLayout`">Open Dialog</button>
                <a v-if="formInfo.name !== ``" href="#" @click.prevent="saveLayout('apply')" class="btn btn-sm btn-as btn-as-primary me-2" :disabled="save_disabled">{{ language.JAPPLY }}</a>
                <a href="#" @click.prevent="saveLayout('save')" class="btn btn-sm btn-as btn-primary me-2" :disabled="save_disabled" v-html="language.JSAVE"></a>
                <a v-if="formInfo.name !== ``" href="#" @click.prevent="" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`_saveLayout`" class="btn btn-sm btn-as btn-as-light me-2" :disabled="save_disabled">{{ language.TPL_ASTROID_EDIT_INFO }}</a>
                <a v-if="props.type === `article_layouts` && formInfo.name !== `default`" href="#" @click.prevent="loadDefault()" class="btn btn-sm btn-as btn-as-light me-2" :disabled="save_disabled">{{ language.TPL_ASTROID_LOAD_DEFAULT_SETTINGS }}</a>
                <a href="#" @click.prevent="cancelLayout()" class="btn btn-sm btn-as btn-as-light" :disabled="save_disabled">{{ language.JCANCEL }}</a>
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