<script setup>
import { onBeforeMount, onMounted, onUpdated, ref } from "vue";
import draggable from "vuedraggable";
import Modal from "./Modal.vue";
import LayoutGrid from "./LayoutGrid.vue";
import SelectElement from "./SelectElement.vue";

const props = defineProps(['field', 'list', 'group', 'showModal', 'constant']);
const layout = ref([]);
const map = {
    'root': 'sections',
    'sections': 'rows',
    'rows': 'cols',
    'cols': 'elements'
}

let elClass = '';
let handle = '';

onBeforeMount(()=>{
    layout.value = props.list;
    if (props.group === 'root') {
        elClass = 'astroid-sections row row-cols-1 g-3'; 
        handle = '.section-handle';
    } else if (props.group === 'sections') {
        elClass = 'astroid-section';
        handle = '.row-handle';
    } else if (props.group === 'rows') {
        elClass = 'astroid-rows row g-2';
        handle = '.column-handle';
    } else if (props.group === 'cols') {
        elClass = 'astroid-cols';
    } else if (props.group === 'elements') {
        elClass = 'astroid-elements';
    }
    layout.value[map[props.group]].forEach(element => {
        showModal.value[element.id] = false;
        showGrid.value[element.id] = false;
        showElement.value[element.id] = false;
    });
})

onMounted(()=>{
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
})

const showModal = ref(new Object());
const showGrid  = ref(new Object());

function editElement(id) {
    showModal.value[id] = true;
}

function closeElement(id) {
    showModal.value[id] = false;
}

function addGrid(element, index, grid = []) {
    const sec = Date.now() * 1000 + Math.random() * 1000;
    let tmp_grid = [];
    
    switch (_addtype.value) {
        case 'section':
            grid.forEach(col => {
                if (col > 0) {
                    tmp_grid.push({
                        id: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
                        type: 'column',
                        size: col,
                        elements: []
                    })
                }
            });
            layout.value[map[props.group]].splice(index+1, 0, {
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
            break;

        case 'row':
            grid.forEach(col => {
                if (col > 0) {
                    tmp_grid.push({
                        id: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
                        type: 'column',
                        size: col,
                        elements: []
                    })
                }
            });
            layout.value[map[props.group]][index].rows.push({
                id: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
                type: 'row',
                cols: tmp_grid
            });
            break;
    
        default:
            break;
    }
    showGrid.value[element.id] = false;
}

const showElement = ref(new Object());
function selectElement(id) {
    showElement.value[id] = true;
}

function addElement(addon, index) {
    let id = Date.now() * 1000 + Math.random() * 1000;
    id = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
    layout.value[map[props.group]][index].elements.push({
        id: id,
        type: addon.type,
        params: [
            {name: 'title', value: addon.title}
        ]
    });
    showModal.value[id] = true;
}

const _addtype = ref('');
function showGridModal(id, type) {
    _addtype.value = type;
    showGrid.value[id] = true;
}

function deleteElement(index) {
    if (confirm('Are you sure?')) {
        layout.value[map[props.group]].splice(index, 1);
    }
}
</script>
<template>
    <draggable
        :class="elClass"
        tag="div"
        :list="layout[map[props.group]]"
        :group="{ name: map[props.group] }"
        ghost-class="ghost"
        :handle="handle"
        item-key="id"
    >
        <template #item="{ element, index }">
            <div v-if="props.group === `root`" class="astroid-section-container">
                <nav class="section-toolbar navbar">
                    <span class="navbar-text" href="#"><span class="section-handle handle bg-body-secondary px-1 py-1 rounded me-1"><i class="fa-solid fa-arrows-up-down-left-right"></i></span> {{ element.params.find((param) => param.name === 'title').value }}</span>
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#" data-bs-toggle="tooltip" data-bs-title="Edit Section" @click.prevent="editElement(element.id)"><i class="fas fa-pencil-alt"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#" data-bs-toggle="tooltip" data-bs-title="Duplicate Section"><i class="fas fa-copy"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#" @click.prevent="deleteElement(index)" data-bs-toggle="tooltip" data-bs-title="Remove Section"><i class="fas fa-trash-alt"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#" @click.prevent="showGridModal(element.id, 'row')"><i class="fas fa-plus"></i> New Row</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#" @click.prevent="showGridModal(element.id, 'section')"><i class="fas fa-plus"></i> New Section</a>
                        </li>
                    </ul>
                </nav>
                <LayoutBuilder :field="props.field" :list="element" :group="map[props.group]" />
                <Transition name="fade">
                    <Modal v-if="showModal[element.id]" :element="element" :form="props.field.input.form[element.type]" :constant="props.constant" @update:saveElement="(value)=>{element.params = value}" @update:close-element="closeElement" />
                </Transition>
                <Transition name="fade">
                    <LayoutGrid v-if="showGrid[element.id]" :element="element" @update:close-element="(id) => {showGrid[id] = false}" @update:saveElement="(grid) => {addGrid(element, index, grid)}" />
                </Transition>
            </div>
            <div v-else-if="props.group === `sections`" class="astroid-row-container position-relative">
                <div class="row-toolbar position-absolute">
                    <div class="row-handle handle text-dark-emphasis"><i class="fa-solid fa-arrows-up-down-left-right"></i></div>
                    <div><a href="#" data-bs-toggle="tooltip" data-bs-title="Edit Columns" class="text-dark-emphasis"><i class="fa-solid fa-table-columns"></i></a></div>
                    <div><a href="#" @click.prevent="editElement(element.id)" data-bs-toggle="tooltip" data-bs-title="Edit Row" class="text-dark-emphasis"><i class="fa-solid fa-pencil"></i></a></div>
                    <div><a href="#" @click.prevent="deleteElement(index)" data-bs-toggle="tooltip" data-bs-title="Remove Row" class="text-dark-emphasis"><i class="fa-solid fa-trash"></i></a></div>
                </div>
                <LayoutBuilder :field="props.field" :list="element" :group="map[props.group]" />
                <Transition name="fade">
                    <Modal v-if="showModal[element.id]" :element="element" :form="props.field.input.form[element.type]" :constant="props.constant" @update:saveElement="(value)=>{element.params = value}" @update:close-element="closeElement" />
                </Transition>
            </div>
            <div v-else-if="props.group === `rows`" class="astroid-col-container" :class="`col-`+element.size">
                <div class="d-flex justify-content-between">
                    <div class="font-monospace text-body-tertiary mb-2">col-lg-{{ element.size }}</div>
                    <div class="column-toolbar">
                        <span class="column-handle handle bg-body-secondary px-1 py-1 rounded text-dark-emphasis me-1"><i class="fa-solid fa-arrows-up-down-left-right"></i></span>
                        <a href="#" @click.prevent="editElement(element.id)" data-bs-toggle="tooltip" data-bs-title="Edit Column"><span class="bg-body-secondary px-1 py-1 rounded text-dark-emphasis me-1"><i class="fas fa-pencil-alt"></i></span></a>
                    </div>
                </div>
                <LayoutBuilder :field="props.field" :list="element" :group="map[props.group]" />
                <div class="add-element d-flex justify-content-center">
                    <a href="#" @click.prevent="selectElement(element.id)" class="bg-light text-dark border px-2 py-1 rounded-pill"><i class="fas fa-plus"></i><span class="add-element-text ms-1">Add Element</span></a>
                </div>
                <Transition name="fade">
                    <Modal v-if="showModal[element.id]" :element="element" :form="props.field.input.form[element.type]" :constant="props.constant" @update:saveElement="(value)=>{element.params = value}" @update:close-element="closeElement" />
                </Transition>
                <Transition name="fade">
                    <SelectElement v-if="showElement[element.id]" :element="element" :form="props.field.input.form" @update:close-element="showElement[element.id] = false" @update:selectElement="(addon) => {addElement(addon, index)}" />
                </Transition>
            </div>
            <div v-else-if="props.group === `cols`" class="astroid-element card card-default card-body">
                <div class="d-flex justify-content-between">
                    <div class="element-name">
                        <div>{{ element.params.find((param) => param.name === 'title').value }}</div>
                        <div class="text-body-tertiary form-text">{{ element.type }}</div>
                    </div>
                    <div class="element-toolbar">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link py-0 ps-0 pe-1" href="#" data-bs-toggle="tooltip" data-bs-title="Edit Element" @click.prevent="editElement(element.id)"><i class="fas fa-pencil-alt"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-0 px-1" href="#" data-bs-toggle="tooltip" data-bs-title="Duplicate Element"><i class="fas fa-copy"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-0 pe-0 ps-1" href="#" @click.prevent="deleteElement(index)" data-bs-toggle="tooltip" data-bs-title="Remove Element"><i class="fas fa-trash-alt"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <Transition name="fade">
                    <Modal v-if="showModal[element.id]" :element="element" :form="props.field.input.form[element.type]" :constant="props.constant" @update:saveElement="(value)=>{element.params = value}" @update:close-element="closeElement" />
                </Transition>
            </div>
            <div v-else>
                {{ element.id }}
                <LayoutBuilder :field="props.field" :list="element" :group="map[props.group]" />
            </div>
        </template>
    </draggable>
</template>