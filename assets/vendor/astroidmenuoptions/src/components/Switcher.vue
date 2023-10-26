<script setup>
import { onMounted, ref, watch } from 'vue';

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'config', 'fieldname', 'label']);
const switcher = ref(false);
onMounted(() => {
    switcher.value = parseInt(props.modelValue) ? true : false;
})
watch(switcher, (newValue) => {
    emit('update:modelValue', newValue ? 1 : 0);
})
</script>
<template>
    <div class="astroid-radio">
        <label class="form-label" :for="props.config.id+`_`+props.fieldname">{{ props.label }}</label>
        <div class="form-check form-switch">
            <input class="form-check-input" :id="props.config.id+`_`+props.fieldname" type="checkbox" role="switch" v-model="switcher">
        </div>
        <input type="hidden" :name="props.config.name+`[`+props.fieldname+`]`" :value="modelValue"/>
    </div>
</template>