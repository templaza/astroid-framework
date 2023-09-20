<script setup>
import { onMounted, ref } from 'vue';

const emit = defineEmits(['update:closeElement', 'update:selectElement']);
const props = defineProps(['form', 'system']);
const currentFilter = ref('');
const addons = ref([]);
let filters = [];
let counter = {};
onMounted(()=> {
    let addon = {};
    Object.keys(props.form).every(key => {
        if (props.form[key].type === 'addon') {
            addon = props.form[key].info;
            if (['component', 'banner', 'message'].includes(addon.type) && !props.system[addon.type]) {
                return true;
            }
            addons.value.push(addon);
            addon.category.forEach(cat => {
                if (filters.includes(cat)) {
                    counter[cat]++;
                } else {
                    filters.push(cat);
                    counter[cat] = 1;
                }
            });
            return true;
        }
    });
})
function selectElement(addon) {
    emit('update:selectElement', addon);
    emit('update:closeElement');
}
</script>
<template>
    <div class="astroid-modal modal d-block" tabindex="-1" @click.self="emit('update:closeElement')">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select an Element</h5>
                    <button type="button" class="btn-close" aria-label="Close" @click="emit('update:closeElement')"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-auto col-12">
                            <ul class="astroid-element-nav nav nav-pills flex-column">
                                <li class="nav-item" :class="{'active' : currentFilter === ''}">
                                    <a class="nav-link" href="#" @click.prevent="currentFilter = ''">All<span class="form-text">{{ addons.length }}</span></a>
                                </li>
                                <li class="nav-item" v-for="filter in filters" :class="{'active' : currentFilter === filter}">
                                    <a class="nav-link" href="#" @click.prevent="currentFilter = filter">{{ filter }}<span class="form-text">{{ counter[filter] }}</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="col">
                            <div class="row row-cols-xl-4 row-cols-lg-3 row-cols-2 g-3">
                                <div v-for="addon in addons" v-show="currentFilter === '' || addon.category.includes(currentFilter)">
                                    <div class="addon-block card card-default card-body align-items-center justify-content-center" @click="selectElement(addon)">
                                        <i class="fa-2x mb-2 text-body-tertiary" :class="addon.icon"></i>
                                        <div class="form-text">{{ addon.title }}</div>
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