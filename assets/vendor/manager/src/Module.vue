<script setup>
import { onBeforeMount, ref, provide } from 'vue';
import axios from "axios";
import Layout from "./components/helpers/Layout.vue";
import './assets/base.scss'
const data = JSON.parse(document.getElementById('astroid_layout_module_json').innerHTML);
const layout = ref('{"sections":[]}');
provide('constant', data.constant);
provide('language', data.language);
onBeforeMount(()=>{
    layout.value = JSON.stringify(data.layout);
})
</script>
<template>
    <Layout v-model="layout" source="joomla_module" :field="{
                id: data.id,
                input: {
                    id: data.id,
                    name: data.name,
                    value: data.layout
                }
            }" />
</template>