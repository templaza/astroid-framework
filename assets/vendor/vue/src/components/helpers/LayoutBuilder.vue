<script setup>
import { onBeforeMount, onMounted, ref } from "vue";
import draggable from "vuedraggable";
import Modal from "./Modal.vue";
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
    });
})

onMounted(()=>{
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
})

const showModal = ref(new Object());

function editElement(id) {
    showModal.value[id] = true;
}

function closeElement(id) {
    showModal.value[id] = false;
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
        <template #item="{ element }">
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
                            <a class="nav-link px-1" href="#" data-bs-toggle="tooltip" data-bs-title="Remove Section"><i class="fas fa-trash-alt"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#"><i class="fas fa-plus"></i> New Row</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-1" href="#"><i class="fas fa-plus"></i> New Section</a>
                        </li>
                    </ul>
                </nav>
                <LayoutBuilder :field="props.field" :list="element" :group="map[props.group]" />
                <Transition name="fade">
                    <Modal v-if="showModal[element.id]" :element="element" :form="props.field.input.form[element.type]" :constant="props.constant" @update:saveElement="(value)=>{element.params = value}" @update:close-element="closeElement" />
                </Transition>
            </div>
            <div v-else-if="props.group === `sections`" class="astroid-row-container position-relative">
                <div class="row-toolbar position-absolute">
                    <div class="row-handle handle text-dark-emphasis"><i class="fa-solid fa-arrows-up-down-left-right"></i></div>
                    <div><a href="#" data-bs-toggle="tooltip" data-bs-title="Edit Columns" class="text-dark-emphasis"><i class="fa-solid fa-table-columns"></i></a></div>
                    <div><a href="#" @click.prevent="editElement(element.id)" data-bs-toggle="tooltip" data-bs-title="Edit Row" class="text-dark-emphasis"><i class="fa-solid fa-pencil"></i></a></div>
                    <div><a href="#" data-bs-toggle="tooltip" data-bs-title="Remove Row" class="text-dark-emphasis"><i class="fa-solid fa-trash"></i></a></div>
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
                        <a href="#"><span class="bg-body-secondary px-1 py-1 rounded text-dark-emphasis"><i class="fas fa-plus"></i><span class="d-none d-md-inline">Element</span></span></a>
                    </div>
                </div>
                <LayoutBuilder :field="props.field" :list="element" :group="map[props.group]" />
                <Transition name="fade">
                    <Modal v-if="showModal[element.id]" :element="element" :form="props.field.input.form[element.type]" :constant="props.constant" @update:saveElement="(value)=>{element.params = value}" @update:close-element="closeElement" />
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
                                <a class="nav-link py-0 pe-0 ps-1" href="#" data-bs-toggle="tooltip" data-bs-title="Remove Element"><i class="fas fa-trash-alt"></i></a>
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