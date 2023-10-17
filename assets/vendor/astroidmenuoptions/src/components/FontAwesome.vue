<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from "axios";

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'config', 'fieldname']);
const btnIcon = ref('Select Icon');
const icons = ref([]);
const searchText = ref('');
const iconSelected= ref({
    value: "",
    name: "",
});

const showIcons = computed(()=>{
    let tmp = [];
    icons.value.forEach(element => {
        if (searchText.value === '' || (element.name.toLowerCase().includes(searchText.value.toLowerCase()))) {
            tmp.push(element);
        }
    });
    return tmp;
})

onMounted(()=>{
    let url = "index.php?option=com_ajax&astroid=search&format=html&search=icon&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "icon_ajax.txt?ts="+Date.now();
    }
    axios.get(url)
    .then(function (response) {
        if (response.status === 200 && response.data.success === true) {
            icons.value = response.data.results;
            response.data.results.forEach(element => {
                if (props.modelValue === element.value) {
                    iconSelected.value = element;
                }
            });
        }
    })
})

function selectIcon(icon) {
    iconSelected.value = icon;
    emit('update:modelValue', icon.value);
    document.getElementById(props.config.id+'_close').click();
}
</script>
<template>
    <div class="input-group mb-3">
        <div class="form-control"><span v-if="modelValue!=``" v-html="iconSelected.name"></span></div>
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" :data-bs-target="`#`+props.config.id+`_`+props.fieldname+`_modal`">{{ btnIcon }}</button>
    </div>
    <div class="fontawesome-modal modal fade" :id="props.config.id+`_`+props.fieldname+`_modal`" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 w-100">Select an Icon</h1>
                    <input type="text" class="form-control me-3" placeholder="Find your icon" v-model="searchText">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" :id="props.config.id+'_close'"></button>
                </div>
                <div class="modal-body">
                    <div class="row row-cols-xl-5 row-cols-lg-4 row-cols-2 g-3">
                        <div v-for="icon in showIcons">
                            <div class="card card-default card-body d-flex justify-content-center align-items-center" v-html="icon.name" @click="selectIcon(icon)"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>