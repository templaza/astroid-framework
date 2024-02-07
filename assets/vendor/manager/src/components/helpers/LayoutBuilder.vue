<script setup>
import { onBeforeMount, onMounted, onUpdated, ref } from "vue";
import draggable from "vuedraggable";
import LayoutGrid from "./LayoutGrid.vue";
const emit = defineEmits(['edit:Element', 'select:Element', 'update:System']);
const props = defineProps(['list', 'group', 'system', 'form', 'device']);
const layout = ref([]);
const map = {
    'root': 'sections',
    'sections': 'rows',
    'rows': 'cols',
    'cols': 'elements'
}

let elClass = '';
let handle = '';

function initLayout() {
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
        if (['component', 'banner', 'message'].includes(element.type)) {
            updateSystem(element.type);
        }
        if (element.type === 'column') {
            if (['1','2','3','4','5','6','7','8','9','10','11','12'].includes(element.size+'')) {
                const tmp = element.size;
                element.size = {
                    xxl: tmp,
                    xl: tmp,
                    lg: tmp,
                    md: 12,
                    sm: 12,
                    xs: 12
                }
            }
        }
        if (element.type === 'row') {
            if (typeof element.fill === 'undefined') {
                element.fill = true;
            }
        }
        if (typeof element.state === 'undefined') {
            element.state = 1;
        }
        showGrid.value[element.id] = false;
    });
}

onBeforeMount(()=>{
    initLayout();
})

onUpdated(()=>{
    initLayout();
})

onMounted(()=>{
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
})

function updateSystem(addonType, value = false) {
    emit('update:System', addonType, value);
}

// Grid layout configure
const showGrid  = ref(new Object());
const _addtype = ref('');

function showGridModal(id, type) {
    _addtype.value = type;
    showGrid.value[id] = true;
}

function addGrid(element, index, grid = []) {
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
    switch (_addtype.value) {
        case 'section':
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

function editGrid(element, grid = []) {
    const sec = Date.now() * 1000 + Math.random() * 1000;
    let col_idx = 0;
    grid.forEach(col => {
        if (col > 0) {
            if (col_idx < element.cols.length) {
                element.cols[col_idx].size[props.device] = col;
            } else {
                element.cols.push({
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
                });
            }
            col_idx ++
        } 
    });
    while (col_idx < element.cols.length) {
        if (col_idx>0 && element.cols[col_idx].elements.length > 0) {
            element.cols[col_idx - 1].elements = [...element.cols[col_idx - 1].elements , ...element.cols[col_idx].elements];
        }
        element.cols.splice(col_idx, 1);
    }
    showGrid.value[element.id] = false;
}

// Element Configure
function selectElement(element) {
    emit('select:Element', element);
}

function cloneElement(element) {
    let id = Date.now() * 1000 + Math.random() * 1000;
    id = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
    let tmp = {};
    Object.keys(element).forEach(key => {
        if (key === 'rows' || key === 'cols' || key === 'elements') {
            tmp[key] = [];
            element[key].forEach(el => {
                tmp[key].push(cloneElement(el));
            });
        } else if (key === 'id') {
            tmp.id = id;
        } else {
            tmp[key] = element[key];
        }
    });
    return tmp;
}

function duplicateElement(element, index) {
    layout.value[map[props.group]].splice(index+1, 0, cloneElement(element));
}

function _editElement(element) {
    emit('edit:Element', element);
}

function elementState(element) {
    if (typeof element.state === 'undefined') {
        element.state = 0;
    } else {
        element.state = Math.abs(element.state - 1);
    }
}

function findSystemAddon(element) {
    switch (element.type) {
        case 'section':
            element.rows.forEach(el => {
                findSystemAddon(el);
            })
            break;
        case 'row':
            element.cols.forEach(el => {
                findSystemAddon(el);
            })
            break;
        case 'column':
            element.elements.forEach(el => {
                findSystemAddon(el);
            })
            break;
        default:
            if (['component', 'banner', 'message'].includes(element.type)) {
                updateSystem(element.type, true);
            }
            break;
    }
}

function deleteElement(element, index) {
    if (confirm('Are you sure?')) {
        findSystemAddon(element);
        layout.value[map[props.group]].splice(index, 1);
    }
}

function getColumnClass(element) {
    let column_class = props.device !== 'xs' ? `col-`+element.size[props.device] : `col-`+element.size.xs;
    let column_order = 0;
    if (typeof element.params !== 'undefined') {
        element.params.every(param => {
            if (param.name === 'column_order_' + props.device) {
                column_order = parseInt(param.value);
                return false;
            }
            return true;
        });
    }
    if (column_order > 0) {
        column_class += ' order-' + column_order;
    }
    return column_class;
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
                            <a class="nav-link px-1" href="#" data-bs-toggle="tooltip" data-bs-title="Edit Section" @click.prevent="_editElement(element)"><i class="fas fa-pencil-alt"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#" data-bs-toggle="tooltip" data-bs-title="Duplicate Section" @click.prevent="duplicateElement(element, index)"><i class="fas fa-copy"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#" @click.prevent="deleteElement(element, index)" data-bs-toggle="tooltip" data-bs-title="Remove Section"><i class="fas fa-trash-alt"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#" @click.prevent="showGridModal(element.id, 'row')"><i class="fas fa-plus"></i> New Row</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#" @click.prevent="showGridModal(element.id, 'section')"><i class="fas fa-plus"></i> New Section</a>
                        </li>
                    </ul>
                </nav>
                <LayoutBuilder :list="element" :group="map[props.group]" :system="props.system" :form="props.form" :device="props.device" @edit:Element="_editElement" @select:Element="selectElement" @update:System="updateSystem" />
                <Transition name="fade">
                    <LayoutGrid v-if="showGrid[element.id]" :element="element" @update:close-element="(id) => {showGrid[id] = false}" @update:saveElement="(grid) => {addGrid(element, index, grid)}" />
                </Transition>
            </div>
            <div v-else-if="props.group === `sections`" class="astroid-row-container position-relative">
                <div class="row-toolbar position-absolute">
                    <div class="row-handle handle text-dark-emphasis"><i class="fa-solid fa-arrows-up-down-left-right"></i></div>
                    <div><a href="#" data-bs-toggle="tooltip" data-bs-title="Edit Columns" class="text-dark-emphasis" @click.prevent="showGridModal(element.id, 'row')"><i class="fa-solid fa-table-columns"></i></a></div>
                    <div><a href="#" @click.prevent="_editElement(element)" data-bs-toggle="tooltip" data-bs-title="Edit Row" class="text-dark-emphasis"><i class="fa-solid fa-pencil"></i></a></div>
                    <div><a href="#" @click.prevent="deleteElement(element, index)" data-bs-toggle="tooltip" data-bs-title="Remove Row" class="text-dark-emphasis"><i class="fa-solid fa-trash"></i></a></div>
                    <div>
                        <input class="form-check-input" type="checkbox" v-model="element.fill" :id="`fill-row-`+element.id" data-bs-toggle="tooltip" data-bs-title="Fill Up Row">
                    </div>
                </div>
                <LayoutBuilder :list="element" :group="map[props.group]" :system="props.system" :form="props.form" :device="props.device" @edit:Element="_editElement" @select:Element="selectElement" @update:System="updateSystem" />
                <Transition name="fade">
                    <LayoutGrid v-if="showGrid[element.id]" :element="element" @update:close-element="(id) => {showGrid[id] = false}" @update:saveElement="(grid) => {editGrid(element, grid)}" />
                </Transition>
            </div>
            <div v-else-if="props.group === `rows`" class="astroid-col-container" :class="getColumnClass(element)">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="column-size mb-2">
                        <select class="form-select form-select-sm" v-model="element.size[props.device]" aria-label="Select column size" :id="`select-column-size-`+element.id">
                            <option v-for="option in [1,2,3,4,5,6,7,8,9,10,11,12]" :value="option" :key="option">{{ 'col'+(props.device !== 'xs' ? '-'+props.device+'-'+option : '-'+option) }}</option>
                        </select>
                    </div>
                    <div class="column-toolbar">
                        <span class="column-handle handle bg-body-secondary px-1 py-1 rounded text-dark-emphasis me-1"><i class="fa-solid fa-arrows-up-down-left-right"></i></span>
                        <a href="#" @click.prevent="_editElement(element)" data-bs-toggle="tooltip" data-bs-title="Edit Column"><span class="bg-body-secondary px-1 py-1 rounded text-dark-emphasis me-1"><i class="fas fa-pencil-alt"></i></span></a>
                    </div>
                </div>
                <LayoutBuilder :list="element" :group="map[props.group]" :system="props.system" :form="props.form" :device="props.device" @edit:Element="_editElement" @select:Element="selectElement" @update:System="updateSystem" />
                <div class="add-element d-flex justify-content-center">
                    <a href="#" @click.prevent="selectElement(element)" class="bg-light text-dark border px-2 py-1 rounded-pill"><i class="fas fa-plus"></i><span class="add-element-text ms-1">Add Element</span></a>
                </div>
            </div>
            <div v-else-if="props.group === `cols`" class="astroid-element card card-default card-body" :class="{'element-disabled' : !element.state}">
                <div class="d-flex justify-content-between">
                    <div class="element-name">
                        <div><i class="text-body-tertiary me-2" :class="props.form[element.type].info.icon"></i>{{ element.params.find((param) => param.name === 'title').value }}</div>
                        <div class="text-body-tertiary form-text">{{ element.type }}</div>
                    </div>
                    <div class="element-toolbar">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link py-0 ps-0 pe-1" href="#" data-bs-toggle="tooltip" data-bs-title="Enable/Disable Element" @click.prevent="elementState(element)"><i :class="{'fas fa-eye' : element.state, 'fas fa-eye-slash' : !element.state}"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-0 px-1" href="#" data-bs-toggle="tooltip" data-bs-title="Edit Element" @click.prevent="_editElement(element)"><i class="fas fa-pencil-alt"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-0 px-1" href="#" data-bs-toggle="tooltip" data-bs-title="Duplicate Element" @click.prevent="duplicateElement(element, index)"><i class="fas fa-copy"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-0 pe-0 ps-1" href="#" @click.prevent="deleteElement(element, index)" data-bs-toggle="tooltip" data-bs-title="Remove Element"><i class="fas fa-trash-alt"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div v-else>
                {{ element.id }}
                <LayoutBuilder :list="element" :group="map[props.group]" :system="props.system" :form="props.form" :device="props.device" @edit:Element="_editElement" @select:Element="selectElement" @update:System="updateSystem" />
            </div>
        </template>
    </draggable>
</template>