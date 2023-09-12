<script setup>
import { onBeforeMount, ref } from 'vue';

const props = defineProps({
    field: { type: Object, default: null },
});
const list = ref([]);
onBeforeMount(() => {
    list.value = props.field.input.value;
})
</script>
<template>
    <div class="row row-cols-lg-3 g-3">
        <div v-for="preset in list">
            <div class="card card-default">
                <img v-if="preset.thumbnail !== ''" :src="preset.thumbnail" :alt="preset.title" class="card-img-top">
                <div class="card-body">
                    <h5>{{ preset.title }}</h5>
                    <p v-if="preset.desc !==''">{{ preset.desc }}</p>
                </div>
            </div>
        </div>
    </div>
</template>