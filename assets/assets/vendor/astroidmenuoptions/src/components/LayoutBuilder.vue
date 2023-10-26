<script setup>
import { onBeforeMount, onMounted, onUpdated, ref } from "vue";
import draggable from "vuedraggable";
import SelectElement from './SelectElement.vue';
const emit = defineEmits(['edit:Grid', 'update:selectElement']);
const props = defineProps(['list', 'group', 'options']);
const layout = ref([]);
const map = {
    'root': 'cols',
    'cols': 'elements'
}
let elClass = '';
let handle = '';
onBeforeMount(()=>{
    layout.value = props.list;
    layout.value.forEach(element => {
        if (typeof element.gid === 'undefined') {
            let id = Date.now() * 1000 + Math.random() * 1000;
            element.gid = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
        }
    });
    if (props.group === 'root') {
        elClass = 'astroid-rows row row-cols-1 g-4';
        handle = '.row-handle';
    } else if (props.group === 'cols') {
        elClass = 'row g-2';
        handle = '.column-handle';
    } else if (props.group === 'elements') {
        elClass = 'astroid-elements';
    }
})
// Element Configure
const _showElement = ref(false);
function selectElement(element) {
    emit('update:selectElement', element);
}
function deleteElement(index) {
    if (confirm('Are you sure?')) {
        layout.value.splice(index, 1);
    }
}
</script>
<template>
    <draggable
        :class="elClass"
        tag="div"
        :list="layout"
        :group="{ name: props.group }"
        ghost-class="ghost"
        :handle="handle"
        item-key="gid"
    >
    <template #item="{ element, index }">
        <div v-if="element.type === `row`" class="row-container">
            <ul class="nav">
                <li class="nav-item"><span class="row-handle px-2 py-1 rounded bg-body-secondary"><i class="fa-solid fa-arrows-up-down-left-right"></i> Move</span></li>
                <li class="nav-item"><span class="px-2 py-1 rounded bg-body-secondary" @click="emit('edit:Grid', index)" data-bs-toggle="modal" data-bs-target="#astroid-select-grid"><i class="fa-solid fa-table-columns"></i> Edit Grid</span></li>
                <li class="nav-item"><span class="px-2 py-1 rounded bg-body-secondary" @click="deleteElement(index)"><i class="fa-solid fa-trash"></i> Remove Row</span></li>
            </ul>
            <LayoutBuilder :list="element.cols" :group="map[props.group]" :options="props.options" @update:selectElement="(value) => {emit('update:selectElement', value)}" />
        </div>
        <div v-else-if="element.type === `column`" class="astroid-col-container" :class="`col-`+element.size">
            <div class="d-flex justify-content-between">
                <div class="font-monospace text-body-tertiary mb-2">
                    col-lg-{{ element.size }}
                </div>
                <div class="column-toolbar">
                    <span class="column-handle handle bg-body-secondary px-1 py-1 rounded text-dark-emphasis me-1"><i class="fa-solid fa-arrows-up-down-left-right"></i></span>
                </div>
            </div>
            <LayoutBuilder :list="element.elements" :group="map[props.group]" :options="props.options" @update:selectElement="(value) => {emit('update:selectElement', value)}" />
            <div class="add-element d-flex justify-content-center">
                <a href="#" @click="emit('update:selectElement', element)" data-bs-toggle="modal" data-bs-target="#astroid-select-element" class="bg-light text-dark border px-2 py-1 rounded-pill"><i class="fas fa-plus"></i><span class="add-element-text ms-1">Add Element</span></a>
            </div>
        </div>
        <div v-else class="astroid-element border">
            <div class="d-flex justify-content-between">
                <div class="element-name">
                    <div>{{ element.title }}</div>
                    <div class="text-body-tertiary form-text text-capitalize">{{ element.type }}</div>
                </div>
                <div class="element-toolbar">
                    <a class="link-danger py-0 pe-0 ps-1" href="#" @click.prevent="deleteElement(index)"><i class="fas fa-trash-alt"></i></a>
                </div>
            </div>
            
        </div>
    </template>
    </draggable>
</template>