<script setup>
import { onBeforeMount, ref, provide } from 'vue';
import axios from "axios";
import { faCircle, faArrowsLeftRight, faTrash, faDownload } from "@fortawesome/free-solid-svg-icons";
import { library } from '@fortawesome/fontawesome-svg-core'
import './assets/article_data.scss'
import Modal from "./components/helpers/Modal.vue";
library.add(faCircle, faArrowsLeftRight, faTrash, faDownload);
const props = defineProps(['widget_json_id']);
const data = JSON.parse(document.getElementById(props.widget_json_id+'_json').innerHTML);
provide('constant', data.constant);
const widgets = ref();
const save_disabled = ref(false);
onBeforeMount(()=>{
    widgets.value = data.widgets;
})

const _showModal = ref(false);
const element = ref({});

function editElement(el) {
    element.value = el;
    _showModal.value = true;
}
function closeElement() {
    _showModal.value = false;
}
function saveElement(params) {
    const action_link = 'index.php?option=com_ajax&astroid=saveArticleElement&ts='+Date.now();
    const formData = new FormData(); 
    widgets.value.every(widget => {
        if (element.value.type === widget.type && element.value.id === widget.id) {
            widget.params = params;
            formData.append(data.constant.astroid_admin_token, 1);
            formData.append('article_id', data.article_id);
            formData.append('template', data.template);
            formData.append('data', JSON.stringify(widget));
            save_disabled.value = true;
            axios.post(action_link, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
            .then((response) => {
                if (response.data.status === 'success') {
                    save_disabled.value = false;
                }
            })
            .catch((err) => {
                console.error(err);
            });
            element.value = {};
            return false;
        }
        return true;
    });
}
</script>
<template>
    <div class="article-layout-data row row-cols-xl-4 row-cols-lg-3 row-cols-md-2 row-cols-sm-1 row-cols-2 g-4">
        <div v-for="widget in widgets">
            <div class="article-widget card card-body border">
                <div class="d-flex justify-content-between">
                    <div class="widget-name">
                        <div><i class="text-body-tertiary me-2" :class="data.constant.form_template[widget.type].info.icon"></i>{{ widget.params.find((param) => param.name === 'title').value }}</div>
                        <div class="text-body-tertiary form-text">{{ widget.type }}</div>
                    </div>
                    <div class="widget-toolbar">
                        <ul class="nav">
                            <li class="nav-item">
                                <a v-if="!save_disabled" class="nav-link py-0 px-1" href="#" @click.prevent="editElement(widget)"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <i v-else class="fa-solid fa-spinner fa-spin-pulse"></i>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Transition name="fade">
        <Modal v-if="_showModal" :element="element" :form="data.constant.form_template[element.type]" @update:saveElement="saveElement" @update:close-element="closeElement" />
    </Transition>
</template>