<script setup>
import { onMounted, onUpdated, ref } from 'vue';
import { MultiListSelect } from "vue-search-select"
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    field: { type: Object, default: null },
    modelValue: { type: String, default: '' },
});
const selectedItems = ref([]);

onMounted(()=>{
    selectedItems.value = JSON.parse(props.modelValue);
})

onUpdated(()=>{
    if (JSON.stringify(selectedItems.value) !== props.modelValue) {
        selectedItems.value = JSON.parse(props.modelValue);
    }
})

function onSelectDevice(items, lastSelectItem) {
    selectedItems.value = items;
    emit('update:modelValue', JSON.stringify(selectedItems.value));
}
</script>
<template>
    <multi-list-select
        :list="props.field.input.options"
        option-value="value"
        option-text="text"
        :id="props.field.id"
        :selected-items="selectedItems"
        :placeholder="props.field.input.hint"
        @select="onSelectDevice"
    >
    </multi-list-select>
    <input type="hidden" :name="props.field.input.name" :value="modelValue">
</template>