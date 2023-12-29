<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from "axios";

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const btnIcon = ref('Select Icon');
const icons = ref([]);
const searchText = ref('');
const showIconsBox = ref(false);
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
    showIconsBox.value = false;
}
</script>
<template>
    <div class="input-group mb-3">
        <div class="form-control icon-text" @click="showIconsBox = true">
            <span v-if="modelValue!=``" v-html="iconSelected.name"></span>
            <span v-else>{{ btnIcon }}..</span>
        </div>
        <button class="btn btn-outline-secondary" type="button" @click.prevent="showIconsBox = true">{{ btnIcon }}</button>
    </div>
    <div v-if="showIconsBox" class="card icon-box">
        <div class="card-header d-flex justify-content-between align-items-center">
            <input type="text" class="form-control me-3" placeholder="Find your icon" v-model="searchText">
            <button type="button" class="btn-close" @click.prevent="showIconsBox = false"></button>
        </div>
        <div class="card-body">
            <div class="row row-cols-md-2 row-cols-1 g-3">
                <div v-for="icon in showIcons">
                    <div class="card icon-item" @click="selectIcon(icon)">
                        <div class="card-body" v-html="icon.name"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>