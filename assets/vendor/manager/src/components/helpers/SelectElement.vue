<script setup>
import { onMounted, ref, inject } from 'vue';
import axios from "axios";

const emit = defineEmits(['update:closeElement', 'update:selectElement']);
const props = defineProps(['form', 'type', 'system', 'source']);
const constant = inject('constant', {});
const currentFilter = ref('');
const addons = ref([]);
const filters = ref([]);
let orders = {}
let counter = {};
const sublayouts  = ref([]);
const title = ref('Select an Element');
onMounted(()=> {
    let addon = {};
    if (props.type !== 'loadSublayout') {
        filters.value.push('System');
        orders['System'] = 0;
        counter['System'] = 0;
        Object.keys(props.form).every(key => {
            if (props.form[key].type === 'addon') {
                addon = props.form[key].info;
                if (typeof props.system[addon.type] !== 'undefined' && !props.system[addon.type]) {
                    return true;
                }
                if ((props.source !== `article_layouts` && addon.element_type === 'article') || (props.source === 'article_layouts' && addon.element_type === 'system')) {
                    return true;
                }
                if (addon.element_type === 'widget' && parseInt(constant.enable_widget) === 0) {
                    return true;
                }
                addon.category.forEach(cat => {
                    if (filters.value.includes(cat)) {
                        counter[cat]++;
                    } else {
                        filters.value.push(cat);
                        orders[cat] = Object.keys(orders).length;
                        counter[cat] = 1;
                    }
                    if (typeof addon.order === 'undefined') {
                        addon.order = orders[cat];
                    }
                });
                addons.value.push(addon);
                return true;
            }
        });
    } else {
        title.value = 'Select a sub-layout';
    }
    getSublayouts();
})

function getSublayouts() {
    let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=getlayouts&template="+constant.tpl_template_name+"&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "layout_ajax.txt?ts="+Date.now();
    }
    axios.get(url)
    .then(function (response) {
        if (response.data.status === 'success') {
            sublayouts.value = response.data.data;
            if (sublayouts.value.length) {
                filters.value.push('Sublayouts');
                orders['Sublayouts'] = Object.keys(orders).length;
                counter['Sublayouts'] = sublayouts.value.length;
                sublayouts.value.forEach(sublayout => {
                    sublayout.type = 'sublayout';
                });
            }
        }
    })
    .catch(function (error) {
        // handle error
        console.log(error);
    });
}

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
                    <h5 class="modal-title">{{ title }}</h5>
                    <button type="button" class="btn-close" aria-label="Close" @click="emit('update:closeElement')"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div v-if="props.type !== `loadSublayout`" class="col-lg-auto col-12">
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
                                <div v-if="props.type !== `loadSublayout`" v-for="order_key in Object.keys(orders)" v-show="currentFilter === '' || currentFilter === order_key" :class="`order-` + orders[order_key]" class="col-xl-12 col-lg-12 col-12">
                                    <h5 class="mt-1 mb-0">{{ order_key }}</h5>
                                </div>
                                <div v-for="addon in addons" v-show="currentFilter === '' || addon.category.includes(currentFilter)" :class="`order-` + addon.order">
                                    <div class="addon-block card card-default card-body align-items-center justify-content-center" @click="selectElement(addon)">
                                        <i class="fa-2x mb-2 text-body-tertiary" :class="addon.icon"></i>
                                        <div class="form-text">{{ addon.title }}</div>
                                    </div>
                                </div>
                                <div v-for="sublayout in sublayouts" v-show="currentFilter === '' || currentFilter === 'Sublayouts'" :class="`order-` + orders['Sublayouts']">
                                    <div class="addon-block card card-default card-body align-items-center justify-content-center" @click="selectElement(sublayout)">
                                        <img v-if="sublayout.thumbnail !== ``" class="img-fluid" :src="sublayout.thumbnail" :alt="sublayout.title">
                                        <i v-else class="fa-2x mb-2 text-body-tertiary as-icon as-icon-layers"></i>
                                        <div class="form-text">{{ sublayout.title }}</div>
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