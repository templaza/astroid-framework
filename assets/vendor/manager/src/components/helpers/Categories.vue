<script setup>
import { onMounted, onUpdated, ref } from 'vue';
import axios from "axios";
import { MultiListSelect } from "vue-search-select"
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    field: { type: Object, default: null },
    modelValue: { type: String, default: '' },
});

const selectedItems = ref([]);
const list = ref([]);

onMounted(()=>{
    selectedItems.value = JSON.parse(props.modelValue);
    callAjax();
})

onUpdated(()=>{
    if (JSON.stringify(selectedItems.value) !== props.modelValue) {
        selectedItems.value = JSON.parse(props.modelValue);
    }
})

function callAjax() {
    let url = props.field.input.ajax+"&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "cat_ajax.txt?ts="+Date.now();
    }
    axios.get(url)
    .then (function (response) {
        if (response.data.status === 'success') {
            list.value = response.data.data;
        }
    })
    .catch (function (error) {
        // handle error
        console.log(error);
    });
}

function onSelectDevice(items, lastSelectItem) {
    selectedItems.value = items;
    emit('update:modelValue', JSON.stringify(selectedItems.value));
}
</script>
<template>
    <multi-list-select
        :list="list"
        option-value="value"
        option-text="label"
        :id="props.field.id"
        :selected-items="selectedItems"
        placeholder="Select category"
        @select="onSelectDevice"
    >
    </multi-list-select>
    <input type="hidden" :name="props.field.input.name" :value="modelValue">
</template>