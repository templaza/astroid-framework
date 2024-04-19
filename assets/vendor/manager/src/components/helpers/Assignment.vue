<script setup>
import { onBeforeMount, ref, computed, watch } from 'vue';
import MenuListItem from './MenuListItem.vue';
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    field: { type: Object, default: null },
    modelValue: { type: String, default: '{}' }
});
const list = ref({});
onBeforeMount(() => {
    try {
        list.value = JSON.parse(props.modelValue);
    } catch (e) {
        props.field.input.menu.forEach(menu => {
            setDefault(menu);
        });
    }
});

function setDefault(menu) {
    menu.links.forEach(item => {
        list.value[item.id] = true;
        if (item.links.length > 0) {
            setDefault(item);
        }
    });
}

const list_text = computed(() => {
    return JSON.stringify(list.value);
})
watch(list_text, (newText) => {
    if (newText !== props.modelValue) {
        emit('update:modelValue', newText);
    }
})

function selectAll(value) {
    Object.keys(list.value).forEach(key => {
        list.value[key] = value;
    })
}
</script>
<template>
    <div v-for="(menu, idx) in props.field.input.menu" :key="menu.id" class="card card-body" :class="{'mt-3' : idx > 0}">
        <h5 class="mb-1">{{ menu.title }}</h5>
        <p><small class="text-body-secondary">{{ menu.description }}</small></p>
        <div class="btn-group d-block mb-2" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-outline-primary" @click.prevent="selectAll(true)">Select All</button>
            <button type="button" class="btn btn-outline-primary" @click.prevent="selectAll(false)">None</button>
        </div>
        <ul v-if="menu.links.length > 0" class="list-unstyled">
            <MenuListItem v-for="menuitem in menu.links" v-model="list" :key="menuitem.id" :menuitem="menuitem" :field="props.field" />
        </ul>
    </div>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>