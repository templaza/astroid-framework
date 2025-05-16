<script setup>
import axios from "axios";
import {ref, inject, onBeforeMount} from "vue";
const props = defineProps(['field']);
const constant = inject('constant', {});
const title = ref(props.field.input.title);
const desc = ref(props.field.input.desc);
onBeforeMount(()=>{
    let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=get-astroid-promotion&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "promo_ajax.txt?ts="+Date.now();
    }
    axios.get(url)
        .then(response => {
            if (response.data.status === 'success' && response.data.data.length > 0) {
                title.value = response.data.data[0].title;
                desc.value = '<p>' + response.data.data[0].description + '</p><a href="' +response.data.data[0].link + '" target="_blank" class="btn btn-sm btn-as btn-danger">' + response.data.data[0].link_title + '</a>';
            }
        })
        .catch(error => {
            console.error("There was an error fetching the data!", error);
        });
})
</script>
<template>
    <h6 class="card-title">{{ title }}</h6>
    <div class="card-text form-text" v-html="desc"></div>
</template>