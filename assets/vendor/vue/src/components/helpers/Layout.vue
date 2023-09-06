<script setup>
import { computed, onBeforeMount, ref, watch } from 'vue';
import LayoutBuilder from "./LayoutBuilder.vue";
import Modal from "./Modal.vue";
import SelectElement from "./SelectElement.vue";
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: { type: String, default: '' },
    field: { type: Object, default: null },
    constant: { type: Object, default: null }
});
onBeforeMount(()=>{
    layout.value    =   props.field.input.value;
})
const layout = ref([]);
const _showModal = ref(false);
const _showElement = ref(false);
const layout_text = computed(() => {
  return JSON.stringify(layout.value);
})
watch(layout_text, (newText) => {
    emit('update:modelValue', newText);
})
const element = ref({});
function editElement(el) {
    element.value = el;
    _showModal.value = true;
}
function saveElement(param) {
    layout.value.sections.every(section => {
        if (element.value.type === section.type && element.value.id === section.id) {
            section.params = param;
            element.value = {};
            return false;
        }
        section.rows.every(row => {
            if (element.value.type === row.type && element.value.id === row.id) {
                row.params = param;
                element.value = {};
                return false;
            }
            row.cols.every(column => {
                if (element.value.type === column.type && element.value.id === column.id) {
                    column.params = param;
                    element.value = {};
                    return false;
                }
                column.elements.every(el => {
                    if (element.value.type === el.type && element.value.id === el.id) {
                        el.params = param;
                        element.value = {};
                        return false;
                    }
                    return true;
                });
                return true;
            });
            return true;
        });
        return true;
    });
}
function selectElement(el) {
    element.value = el;
    _showElement.value = true;
}
function addElement(addon) {
    let id = Date.now() * 1000 + Math.random() * 1000;
    id = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
    const new_element = {
        id: id,
        type: addon.type,
        params: [
            {name: 'title', value: addon.title}
        ]
    }
    layout.value.sections.every(section => {
        section.rows.every(row => {
            row.cols.every(column => {
                if (element.value.id === column.id) {
                    column.elements.push(new_element);
                    element.value = {};
                    return false;
                }
                return true;
            });
            return true;
        });
        return true;
    });
    editElement(new_element);
}
function closeElement(id) {
    _showModal.value = false;
}
</script>
<template>
    <LayoutBuilder :field="props.field" :list="layout" group="root" :constant="props.constant" @edit:Element="editElement" @select:Element="selectElement" />
    <Transition name="fade">
        <Modal v-if="_showModal" :element="element" :form="props.field.input.form[element.type]" :constant="props.constant" @update:saveElement="saveElement" @update:close-element="closeElement" />
    </Transition>
    <Transition name="fade">
        <SelectElement v-if="_showElement" :element="element" :form="props.field.input.form" @update:close-element="_showElement = false" @update:selectElement="addElement" />
    </Transition>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>