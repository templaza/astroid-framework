<script setup>
import {inject, onBeforeMount, ref} from "vue";
import SelectBox from "./SelectBox.vue";
const emit = defineEmits(['update:modelValue', 'update:statusField']);
const props = defineProps({
    modelValue: { type: String, default: '' },
    options: { type: Object, default: {} },
    icons: { type: Object, default: {} },
    fieldChanged: { type: Boolean, default: false }
});
const language  =   inject('language', []);
const icons = ref({
    larger_desktop: 'fa-solid fa-tv',
    large_desktop: 'fa-solid fa-desktop',
    desktop: 'fa-solid fa-computer',
    tablet: 'fa-solid fa-laptop',
    landscape_mobile: 'fa-solid fa-tablet',
    mobile: 'fa-solid fa-house-laptop'
})
const options = ref({
    mobile: language.JDEFAULT,
    landscape_mobile: language.ASTROID_SM,
    tablet: language.ASTROID_LAPTOP,
    desktop: language.ASTROID_DESKTOP,
    large_desktop: language.ASTROID_XL,
    larger_desktop: language.ASTROID_XXL
})
onBeforeMount(()=>{
    if (Object.keys(props.options).length) {
        options.value = props.options;
    }
    if (Object.keys(props.icons).length) {
        icons.value = props.icons;
    }
})
</script>
<template>
    <SelectBox
        :modelValue="props.modelValue"
        @update:modelValue="data => emit('update:modelValue', data)"
        :options="options"
        :icons="icons"
        :fieldChanged="props.fieldChanged"
        @update:statusField="data => emit('update:statusField', data)"
    />
</template>