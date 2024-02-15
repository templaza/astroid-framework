<script setup>
import { computed, onBeforeMount, onUpdated, reactive, ref, watch } from 'vue';
import { MultiListSelect } from "vue-search-select"
import LayoutBuilder from "./LayoutBuilder.vue";
import Modal from "./Modal.vue";
import SelectElement from "./SelectElement.vue";
import LayoutGrid from "./LayoutGrid.vue";
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: { type: String, default: '' },
    field: { type: Object, default: null }
});
onBeforeMount(()=>{
    layout.value    =   props.field.input.value;
    if (typeof layout.value.devices === 'undefined') {
        layout.value.devices = [ 
            { "code": "lg", "icon": "fa-solid fa-computer", "title": "Large Device" }, 
            { "code": "md", "icon": "fa-solid fa-laptop", "title": "Medium Device" }, 
            { "code": "sm", "icon": "fa-solid fa-tablet-screen-button", "title": "On Tablet" }, 
            { "code": "xs", "icon": "fa-solid fa-mobile-screen", "title": "On Mobile" } 
        ];
    }
})
onUpdated(()=>{
    if (layout_text.value !== props.modelValue) {
        const tmp = JSON.parse(props.modelValue);
        layout.value.sections = tmp.sections;
    }
})
const layout = ref([]);
const system = reactive({
    component: true,
    banner: true,
    message: true
});
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
    system[addonType] = value;
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
function selectElement(el) {
    element.value = el;
    _showElement.value = true;
}
function addElement(addon) {
    let id = Date.now() * 1000 + Math.random() * 1000;
    id = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
    const new_element = {
        id: id,
        type: addon.type,
        state: 1,
        params: [
            {name: 'title', value: addon.title}
        ]
    }
    layout.value.sections.every(section => {
        section.rows.every(row => {
            row.cols.every(column => {
                if (element.value.id === column.id) {
                    column.elements.push(new_element);
                    if (['component', 'banner', 'message'].includes(addon.type)) {
                        system[addon.type] = false;
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
    editElement(new_element);
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
</script>
<template>
    <div class="astroid-btn-group responsive-devices text-center" role="group" aria-label="Responsive Devices">
        <span v-for="(option, idx) in layout.devices" :key="idx">
            <input type="radio" class="btn-check" v-model="activeDevice" :id="`responsive-device-`+option.code" :value="option.code" autocomplete="off">
            <label class="btn btn-sm btn-as btn-outline-secondary" data-bs-toggle="tooltip" :data-bs-title="option.title" :for="`responsive-device-`+option.code"><i class="fa-xl" :class="option.icon"></i></label>
        </span>
        <span>
            <button class="layout-config btn btn-sm btn-as btn-outline-secondary" @click.prevent="" data-bs-toggle="modal" data-bs-target="#selectDevices"><i class="fas fa-cog"></i></button>
        </span>
    </div>
    <div class="modal fade" id="selectDevices" tabindex="-1" aria-labelledby="selectDevicesLabel" aria-hidden="true">
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
        <button class="btn btn-lg btn-as btn-as-primary" @click="_showGrid = true"><i class="fa-solid fa-plus me-2"></i>Add Section</button>
        <Transition name="fade">
            <LayoutGrid v-if="_showGrid" @update:close-element="_showGrid = false" @update:saveElement="addGrid" />
        </Transition>
    </div>
    <LayoutBuilder :list="layout" group="root" :system="system" :form="props.field.input.form" :device="activeDevice" @edit:Element="editElement" @select:Element="selectElement" @update:System="updateSystem" />
    <Transition name="fade">
        <Modal v-if="_showModal" :element="element" :form="props.field.input.form[element.type]" @update:saveElement="saveElement" @update:close-element="closeElement" />
    </Transition>
    <Transition name="fade">
        <SelectElement v-if="_showElement" :form="props.field.input.form" :system="system" @update:close-element="_showElement = false" @update:selectElement="addElement" />
    </Transition>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>