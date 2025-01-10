<script setup>
import {onMounted, ref} from "vue";

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'options', 'icons']);
const options = ref({})
const icons = ref({})
onMounted(()=>{
    if (typeof props.options === 'object' && !Array.isArray(props.options) && props.options !== null && Object.keys(props.options).length) {
        options.value = props.options;
    }
    if (typeof props.icons === 'object' && !Array.isArray(props.icons) && props.icons !== null && Object.keys(props.icons).length) {
        icons.value = props.icons;
    }
})
</script>
<template>
    <div class="input-group">
        <span class="input-group-text"><i :class="icons[modelValue]"></i></span>
        <select @change="event => emit('update:modelValue', event.target.value)" class="form-select form-select-sm">
            <option v-for="(text, value) in options" :value="value">{{ text }}</option>
        </select>
    </div>
</template>