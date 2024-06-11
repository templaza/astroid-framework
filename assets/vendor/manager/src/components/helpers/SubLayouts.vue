<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import Layout from "./Layout.vue";

const props = defineProps({
    field: { type: Object, default: null }
});
const items     =   ref([]);
const editItem  =   ref('');
const layout    =   ref('{"sections":[]}')

onMounted(() => {
    callAjax();
})

function editLayout(filename = '') {
    if (filename !== '') {

    } else {
        editItem.value = 'new';
    }
}

function callAjax() {
    let url = props.field.input.ajax+"&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "layout_ajax.txt?ts="+Date.now();
    }
    axios.get(url)
    .then(function (response) {
        if (response.data.status === 'success') {
            items.value = response.data.data;
        }
    })
    .catch(function (error) {
        // handle error
        console.log(error);
    });
}
</script>
<template>
    <div class="row gy-3" v-if="editItem === ``">
        <div class="col-md-9 order-1 order-md-0">
            <div v-if="items.length === 0">
                <div class="alert alert-info" role="alert">
                    There are no layouts in this template.
                </div>
            </div>
            <div v-for="(item, index) in items">
                <pre>{{ item }}</pre>
            </div>
        </div>
        <div class="col-md-3 order-0 order-md-1">
            <div class="sticky-md-top d-grid gap-2">
                <a href="#" @click.prevent="editLayout()" class="btn btn-sm btn-as btn-as-primary">New Layout</a>
                <a href="#" class="btn btn-sm btn-as btn-as-light">Edit Layout</a>
                <a href="#" class="btn btn-sm btn-as btn-outline-danger">Delete Layout</a>
            </div>
        </div>
    </div>
    <div v-else class="astroid-layout px-2">
        <Layout v-model="layout" :field="{
            id: props.field.id,
            input: {
                id: props.field.input.id,
                name: props.field.input.name,
                value: JSON.parse(layout)
            }
        }" />
    </div>
</template>