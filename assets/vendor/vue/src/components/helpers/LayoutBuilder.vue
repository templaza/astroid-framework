<script setup>
import { computed, onBeforeMount, onMounted, ref } from "vue";
import draggable from "vuedraggable";
const emit = defineEmits(['update:layoutBuilder']);
const props = defineProps(['list', 'group']);
const layout = ref([]);
const map = {
    'root': 'sections',
    'sections': 'rows',
    'rows': 'cols',
    'cols': 'elements'
}

let elClass = '';

onBeforeMount(()=>{
    layout.value = props.list;
    if (props.group === 'root') {
        elClass = 'astroid-sections row row-cols-1 g-3'; 
    } else if (props.group === 'sections') {
        elClass = 'astroid-section';
    } else if (props.group === 'rows') {
        elClass = 'astroid-rows row g-2';
    } else if (props.group === 'cols') {
        elClass = 'astroid-cols';
    } else if (props.group === 'elements') {
        elClass = 'astroid-elements';
    }
})
function updateLayout() {
    emit('update:layoutBuilder', layout);
}
</script>
<template>
    <draggable
        :class="elClass"
        tag="div"
        :list="layout[map[props.group]]"
        :group="{ name: map[props.group] }"
        ghost-class="ghost"
        @change="updateLayout"
        item-key="id"
    >
        <template #item="{ element }">
            <div v-if="props.group === `sections`">
                {{ element.id }}
                <LayoutBuilder :list="element" :group="map[props.group]" />
            </div>
            <div v-else-if="props.group === `rows`" :class="`col-`+element.size">
                {{ element.id }}
                <LayoutBuilder :list="element" :group="map[props.group]" />
            </div>
            <div v-else-if="props.group === `cols`" class="card card-default card-body">
                {{ element.id }}
            </div>
            <div v-else class="card card-default card-body">
                {{ element.id }}
                <LayoutBuilder :list="element" :group="map[props.group]" />
            </div>
        </template>
    </draggable>
</template>