<script setup>
import { computed, onMounted, ref } from 'vue';
import draggable from "vuedraggable";
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const socialSearch = ref('');
const filterSocial = computed(() => {
    let tmp = [];
    props.field.input.options.forEach(element => {
        if (socialSearch.value === '' || (element.title.toLowerCase().includes(socialSearch.value.toLowerCase()))) {
            tmp.push(element);
        }
    });
    return tmp;
});
const list = ref([]);
onMounted(()=>{
    list.value = props.field.input.value;
})
const dragging = ref(false);
</script>
<template>
    <textarea class="d-none" :id="props.field.input.id" :name="props.field.input.name" v-text="modelValue"></textarea>
    <div class="row">
        <div class="col-lg-9">
            <draggable 
                v-model="list" 
                @start="dragging=true" 
                @end="dragging=false" 
                class="row row-cols-1 g-4"
                item-key="id">
                <template #item="{element}">
                    <div>
                        <div class="card card-default">
                            <div class="card-header d-flex justify-content-between">
                                <span>
                                    <i :class="element.icon" :style="{'color': element.color}"></i>
                                    {{ element.title }}
                                </span>
                                <span>
                                    <i class="fas fa-trash-alt"></i>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label v-if="element.id === 'whatsapp'" class="form-label">Mobile Number</label>
                                        <label v-else-if="element.id === 'telegram'" class="form-label">Username</label>
                                        <label v-else-if="element.id === 'skype'" class="form-label">Skype ID</label>
                                        <label v-else class="form-label">Link</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input v-model="element.link" type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
            <pre>
                {{ list }}
            </pre>
        </div>
        <div class="col-lg-3">
            <h3>{{ props.field.input.lang.social_brands }}</h3>
            <input type="text" class="form-control" :placeholder="props.field.input.lang.social_search" v-model="socialSearch">
            <div v-for="(social, key) in filterSocial" :key="key" class="border rounded mt-3 p-2">
                <i :class="social.icon"></i>
                {{ social.title }}
            </div>
        </div>
    </div>
</template>