<script setup>
import { computed, onBeforeMount, onUpdated, reactive, ref, watch, inject } from 'vue';
import { MultiListSelect } from "vue-search-select"
import axios from "axios";
import LayoutBuilder from "./LayoutBuilder.vue";
import Modal from "./Modal.vue";
import SelectElement from "./SelectElement.vue";
import LayoutGrid from "./LayoutGrid.vue";
const emit = defineEmits(['update:modelValue', 'update:subLayouts']);
const props = defineProps({
    modelValue: { type: String, default: '' },
    field: { type: Object, default: null },
    source: { type: String, default: 'root' },
});
const constant  =   inject('constant', {});
const language  =   inject('language', []);

onBeforeMount(()=>{
    layout.value    =   props.field.input.value;
    form_template.value = constant.form_template;
    Object.keys(constant.form_template).forEach(key => {
        if (constant.form_template[key].info.element_type === 'system' && !constant.form_template[key].info.multiple && props.source !== 'article_layouts') {
            system.value[constant.form_template[key].info.type] = true;
        }
        if (constant.form_template[key].info.element_type === 'article' && !constant.form_template[key].info.multiple && props.source === 'article_layouts') {
            system.value[constant.form_template[key].info.type] = true;
        }
    })
    if (typeof layout.value.devices === 'undefined') {
        layout.value.devices = [ 
            { "code": "lg", "icon": "fa-solid fa-computer", "title": "Large Device" }, 
            { "code": "md", "icon": "fa-solid fa-laptop", "title": "Medium Device" }, 
            { "code": "sm", "icon": "fa-solid fa-tablet-screen-button", "title": "On Tablet" }, 
            { "code": "xs", "icon": "fa-solid fa-mobile-screen", "title": "On Mobile" } 
        ];
    }
    activeDevice.value = layout.value.devices[0].code;

    if (props.source === 'article_layouts') {
        let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=getArticleFormTemplate&id="+constant.template_id+"&ts="+Date.now();
        if (process.env.NODE_ENV === 'development') {
            url = "articleformtemplate_ajax.txt?ts="+Date.now();
        }
        axios.get(url)
        .then(function (response) {
            if (response.data.status === 'success') {
                form_template.value = response.data.data;
            }
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });
    }
})
onUpdated(()=>{
    if (layout_text.value !== props.modelValue) {
        const tmp = JSON.parse(props.modelValue);
        layout.value.sections = tmp.sections;
        layout.value.devices = tmp.devices;
        activeDevice.value = tmp.devices[0].code;
    }
})
const layout = ref([]);
const system = ref({});
const activeDevice = ref('lg');
const responsive = [
    {
        code: 'xxl',
        icon: 'fa-solid fa-tv',
        title: 'Extra Extra Large'
    },
    {
        code: 'xl',
        icon: 'fa-solid fa-desktop',
        title: 'Extra Large'
    },
    {
        code: 'lg',
        icon: 'fa-solid fa-computer',
        title: 'Large Device'
    },
    {
        code: 'md',
        icon: 'fa-solid fa-laptop',
        title: 'Medium Device'
    },
    {
        code: 'sm',
        icon: 'fa-solid fa-tablet-screen-button',
        title: 'On Tablet'
    },
    {
        code: 'xs',
        icon: 'fa-solid fa-mobile-screen',
        title: 'On Mobile'
    },
]

function onSelectDevice(items, lastSelectItem) {
    if (items.length) {
        layout.value.devices = items
        activeDevice.value = layout.value.devices[0].code;
    } else {
        alert('You can not remove all devices!');
    }
}

function updateSystem(addonType, value = false) {
    if (typeof system.value[addonType] !== 'undefined') {
        system.value[addonType] = value;
    }
}

const _showModal = ref(false);
const _showElement = ref(false);
const _showGrid = ref(false);
const layout_text = computed(() => {
  return JSON.stringify(layout.value);
})
watch(layout_text, (newText) => {
    if (newText !== props.modelValue) {
        emit('update:modelValue', newText);
    }
})
const element = ref({});
const form_template = ref({});
function editElement(el) {
    element.value = el;
    _showModal.value = true;
}
function saveElement(param) {
    layout.value.sections.every(section => {
        if (element.value.type === section.type && element.value.id === section.id) {
            section.params = param;
            element.value = {};
            return false;
        }
        section.rows.every(row => {
            if (element.value.type === row.type && element.value.id === row.id) {
                row.params = param;
                element.value = {};
                return false;
            }
            row.cols.every(column => {
                if (element.value.type === column.type && element.value.id === column.id) {
                    column.params = param;
                    element.value = {};
                    return false;
                }
                column.elements.every(el => {
                    if (element.value.type === el.type && element.value.id === el.id) {
                        el.params = param;
                        element.value = {};
                        return false;
                    }
                    return true;
                });
                return true;
            });
            return true;
        });
        return true;
    });
}

const select_element_type = ref('');
function selectElement(el, type = '') {
    select_element_type.value = type;
    element.value = el;
    _showElement.value = true;
}
function addElement(addon) {
    if (select_element_type.value === 'loadSublayout') {
        let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=getlayout&ts="+Date.now();
        let sublayout_data = {};
        const sec = Date.now() * 1000 + Math.random() * 1000;
        if (process.env.NODE_ENV === 'development') {
            url = "editlayout_ajax.txt?ts="+Date.now();
        }
        const formData = new FormData(); // pass data as a form
        formData.append(constant.astroid_admin_token, 1);
        formData.append('name', addon.name);
        formData.append('template', constant.tpl_template_name);
        formData.append('type', 'layouts');
        axios.post(url, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        }).then((response) => {
            if (response.data.status === 'success') {
                sublayout_data = JSON.parse(response.data.data.data);
                layout.value.sections.every((section, index) => {
                    if (element.value.id === section.id) {
                        sublayout_data.sections.forEach((section, sub_idx) => {
                            layout.value.sections.splice(index+sub_idx+1, 0, {
                                id: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
                                type: section.type,
                                rows: section.rows,
                                params: section.params,
                                state: 1
                            });
                        });
                        // continue
                        element.value = {};
                        return false;
                    }
                    return true;
                });
            }
        }).catch((err) => {
            console.error(err);
        });
    } else {
        let id = Date.now() * 1000 + Math.random() * 1000;
        id = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
        let params = [
            {name: 'title', value: addon.title}
        ];
        if (addon.type === `sublayout`) {
            params.push({name: 'source', value: addon.name});
            params.push({name: 'desc', value: addon.desc});
            params.push({name: 'thumbnail', value: addon.thumbnail});
        }
        const new_element = {
            id: id,
            type: addon.type,
            state: 1,
            params: params
        }
        layout.value.sections.every(section => {
            section.rows.every(row => {
                row.cols.every(column => {
                    if (element.value.id === column.id) {
                        column.elements.push(new_element);
                        if (typeof system.value[addon.type] !== 'undefined') {
                            system.value[addon.type] = false;
                        }
                        element.value = {};
                        return false;
                    }
                    return true;
                });
                return true;
            });
            return true;
        });
        if (addon.type !== `sublayout`) {
            editElement(new_element);
        }
    }
}
function closeElement() {
    _showModal.value = false;
}

function addGrid(grid = []) {
    const sec = Date.now() * 1000 + Math.random() * 1000;
    let tmp_grid = [];
    grid.forEach(col => {
        if (col > 0) {
            tmp_grid.push({
                id: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
                type: 'column',
                size: {
                    xxl: col,
                    xl: col,
                    lg: col,
                    md: 12,
                    sm: 12,
                    xs: 12
                },
                elements: []
            })
        }
    });
    layout.value.sections.push({
        id: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
        type: 'section',
        rows: [
            {
                id: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
                type: 'row',
                cols: tmp_grid
            }
        ],
        params: [
            {name: 'title', value: 'Astroid Section'}
        ]
    });
    _showGrid.value = false;
}

// Sublayout
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
const files = ref(null);
const save_disabled = ref(false);
const sublayout =   ref('{"sections":[]}');
const _showSublayoutModal = ref(false);
const _subFormTitle = ref(null);
function openSaveLayout(data) {
    _showSublayoutModal.value = true;
    sublayout.value = JSON.stringify(data);
}
function onFileChange(e) {
    files.value = e.target.files || e.dataTransfer.files;
}
function saveSublayout() {
    let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=savelayout&ts="+Date.now();
    if (!formInfo.title) {
        alert('You have to input the Title')
        _subFormTitle.value.focus();
        return false;
    } 
    const formData = new FormData(); // pass data as a form
    const toastAstroidMsg = document.getElementById(props.field.input.id+`_saveSectionToast`);
    const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
    formData.append(constant.astroid_admin_token, 1);
    formData.append('title', formInfo.title);
    formData.append('desc', formInfo.desc);
    formData.append('data', sublayout.value);
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
            formInfo.title = '';
            formInfo.desc = '';
            formInfo.name = '';
            formInfo.thumbnail = '';
            files.value = null;
            document.getElementById(props.field.input.id+`_saveLayout_form`).reset();
            sublayout.value = '{"sections":[]}';
            emit('update:subLayouts');
            _showSublayoutModal.value = false;
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
</script>
<template>
    <div class="astroid-btn-group responsive-devices text-center" role="group" aria-label="Responsive Devices">
        <span v-for="(option, idx) in layout.devices" :key="idx">
            <input type="radio" class="btn-check" v-model="activeDevice" :id="props.field.input.id+`responsive-device-`+option.code" :value="option.code" autocomplete="off">
            <label class="btn btn-sm btn-as btn-outline-secondary" data-bs-toggle="tooltip" :data-bs-title="option.title" :for="props.field.input.id+`responsive-device-`+option.code"><i class="fa-xl" :class="option.icon"></i></label>
        </span>
        <span>
            <button class="layout-config btn btn-sm btn-as btn-outline-secondary" @click.prevent="" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`_selectDevices`"><i class="fas fa-cog"></i></button>
        </span>
    </div>
    <div class="modal fade" :id="props.field.input.id+`_selectDevices`" tabindex="-1" aria-labelledby="selectDevicesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="selectDevicesLabel">Layout Configurations</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <label :for="props.field.id+`-responsive-device-select`" class="form-label">Select your devices</label>
                        <multi-list-select
                            :list="responsive"
                            option-value="code"
                            option-text="title"
                            :id="props.field.id+`-responsive-device-select`"
                            :selected-items="layout.devices"
                            placeholder="Select a device"
                            @select="onSelectDevice"
                        >
                        </multi-list-select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="(typeof layout.sections === 'undefined' || layout.sections.length === 0)" class="text-center">
        <button class="btn btn-lg btn-as btn-as-primary mt-4" @click.prevent="_showGrid = true"><i class="fa-solid fa-plus me-2"></i>Add Section</button>
        <Transition name="fade">
            <LayoutGrid v-if="_showGrid" @update:close-element="_showGrid = false" @update:saveElement="addGrid" />
        </Transition>
    </div>
    <LayoutBuilder :list="layout" 
        group="root" :system="system" 
        :form="form_template" 
        :device="activeDevice" 
        :source="props.source"
        @edit:Element="editElement" 
        @select:Element="selectElement" 
        @update:System="updateSystem" 
        @save:Sublayout="openSaveLayout"
        />
    <Transition name="fade">
        <Modal v-if="_showModal" :element="element" :form="form_template[element.type]" @update:saveElement="saveElement" @update:close-element="closeElement" />
    </Transition>
    <Transition name="fade">
        <SelectElement v-if="_showElement" :form="form_template" :type="select_element_type" :system="system" :source="props.source" @update:close-element="_showElement = false" @update:selectElement="addElement" />
    </Transition>
    <form :id="props.field.input.id+`_saveLayout_form`" v-if="props.source === `root` && _showSublayoutModal">
        <div class="astroid-modal modal d-block" :id="props.field.input.id+`_saveLayout`" tabindex="-1" :aria-labelledby="props.field.input.id+`saveLayoutLabel`" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5" :id="props.field.input.id+`_saveLayoutLabel`">Layout Information</h3>
                        <button type="button" class="btn-close" aria-label="Close" @click="_showSublayoutModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="mb-3">
                                <label :for="props.field.input.id+`_saveLayout_title`" class="form-label">Title</label>
                                <input type="text" v-model="formInfo.title" class="form-control" :id="props.field.input.id+`_saveLayout_title`" placeholder="Title" ref="_subFormTitle" required>
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
                        <button type="button" class="btn btn-sm btn-as btn-as-light" aria-label="Close" :disabled="save_disabled" @click="_showSublayoutModal = false">{{ language.ASTROID_BACK }}</button>
                        <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="saveSublayout()" :disabled="save_disabled" v-html="language.JSAVE"></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div :id="props.field.input.id+`_saveSectionToast`" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
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
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>