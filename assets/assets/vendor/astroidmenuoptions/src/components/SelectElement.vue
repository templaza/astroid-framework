<script setup>
import { onMounted, ref } from 'vue';

const emit = defineEmits(['update:selectElement']);
const props = defineProps(['options']);
const currentFilter = ref('');
const addons = ref([]);
let filters = [];
let counter = {};
onMounted(()=> {
    props.options.submenu.forEach(element => {
        element.astroidmenucategory = 'submenu';
        element.icon = 'fa-solid fa-bars';
        addons.value.push(element);
    });
    props.options.module.forEach(element => {
        element.astroidmenucategory = 'module';
        element.icon = 'fa-solid fa-layer-group';
        addons.value.push(element);
    });
})
function selectElement(addon) {
    emit('update:selectElement', addon);
}
</script>
<template>
    <div class="modal fade" id="astroid-select-element" tabindex="-1" aria-labelledby="Select an element" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select an Element</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="astroid-select-element-close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-auto col-12">
                            <ul class="astroid-element-nav nav nav-pills flex-column">
                                <li class="nav-item" :class="{'active' : currentFilter === ''}">
                                    <a class="nav-link" href="#" @click.prevent="currentFilter = ''">All<span class="form-text">{{ addons.length }}</span></a>
                                </li>
                                <li class="nav-item" v-for="filter in ['submenu', 'module']" :class="{'active' : currentFilter === filter}">
                                    <a class="nav-link" href="#" @click.prevent="currentFilter = filter">{{ filter }}<span class="form-text">{{ props.options[filter].length }}</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="col">
                            <div class="row row-cols-xl-4 row-cols-lg-3 row-cols-2 g-3">
                                <div v-for="addon in addons" v-show="currentFilter === '' || addon.astroidmenucategory === currentFilter">
                                    <div class="addon-block card card-default card-body align-items-center justify-content-center py-5" @click="selectElement(addon)">
                                        <div>{{ addon.title }}</div>
                                        <div class="text-capitalize form-text"><i :class="addon.icon" class="me-2"></i>{{ addon.astroidmenucategory }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>