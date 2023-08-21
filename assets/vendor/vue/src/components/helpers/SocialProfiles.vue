<script setup>
import { computed, onMounted, ref } from 'vue';
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'
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
function listUpdated() {
    emit('update:modelValue', JSON.stringify(list.value));
}
function removeAt(idx) {
    if (confirm('Are you sure?')) {
        list.value.splice(idx, 1);
        listUpdated();
    }
}
const customSocial = {
    title   : 'My Social Link',
    link    : '#',
    icons   : [],
    color   : 'rgb(42, 99, 156)',
    enabled : false,
    icon    : '',
    id      : ''
}
function addSocial(item){
    const sec = Date.now() * 1000 + Math.random() * 1000;
    const newItem = {
        title   : item.title,
        link    : item.link,
        icons   : item.icons,
        color   : item.color,
        enabled : false,
        icon    : item.icon,
        id      : sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000)
    }
    list.value.push(newItem);
    listUpdated();
    _showColorPicker.value[newItem.id] = false;
}
const _showColorPicker = ref({});
function showColorPicker(id) {
    _showColorPicker.value[id]  = true;
}
</script>
<template>
    <textarea class="d-none" :id="props.field.input.id" :name="props.field.input.name" v-text="modelValue"></textarea>
    <div class="row">
        <div class="col-lg-9">
            <draggable 
                v-model="list" 
                @start="dragging=true" 
                @end="dragging=false" 
                @change="listUpdated"
                class="astroid-profile row row-cols-1 g-4"
                item-key="id">
                <template #item="{element, index}">
                    <div>
                        <div class="card card-default">
                            <div class="card-header d-flex justify-content-between">
                                <span>
                                    <i :class="element.icon" :style="{'color': element.color}"></i>
                                    {{ element.title }}
                                </span>
                                <span>
                                    <a href="#" class="link-danger" @click.prevent="removeAt(index)"><i class="fas fa-trash-alt"></i></a>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-4">
                                        <label v-if="element.title === 'WhatsApp'" class="form-label">{{ props.field.input.lang.astroid_mobile_number }}</label>
                                        <label v-else-if="element.title === 'Telegram'" class="form-label">{{ props.field.input.lang.astroid_username }}</label>
                                        <label v-else-if="element.title === 'Skype'" class="form-label">{{ props.field.input.lang.astroid_skype_id }}</label>
                                        <label v-else class="form-label">{{ props.field.input.lang.astroid_link }}</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input v-model="element.link" @input="listUpdated()" type="text" class="form-control">
                                    </div>
                                </div>
                                <div v-if="element.icons.length === 0" class="row g-2">
                                    <div class="col-sm-4">
                                        <label class="form-label">{{ props.field.input.lang.astroid_icon_class }}</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input v-model="element.icon" @input="listUpdated()" type="text" class="form-control">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label">{{ props.field.input.lang.astroid_title }}</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input v-model="element.title" @input="listUpdated()" type="text" class="form-control">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label">{{ props.field.input.lang.astroid_color }}</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <i :id="props.field.input.id+`-colorcircle-`+element.id" class="fas fa-circle fa-3x border astroid-color-picker" :style="{'color': element.color}" @click="showColorPicker(element.id)"></i>
                                        <div v-if="_showColorPicker[element.id]" class="row">
                                            <div class="col-auto">
                                                <div class="position-relative">
                                                    <a href="#" @click.prevent="_showColorPicker[element.id] = false" class="link-body-emphasis position-absolute top-0 start-100 translate-middle"><i class="fa-solid fa-circle-xmark fa-xl"></i></a>
                                                    <ColorPicker
                                                        theme="light"
                                                        :color="element.color"
                                                        :sucker-hide="true"
                                                        :sucker-canvas="null"
                                                        :sucker-area="[]"
                                                        :id="props.field.input.id+`-colorpicker-`+element.id"
                                                        @changeColor="(color) => {element.color = `rgba(${color.rgba.r}, ${color.rgba.g}, ${color.rgba.b}, ${color.rgba.a})`; listUpdated();}"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="row g-2">
                                    <div class="col-sm-4">
                                        <label class="form-label">{{ props.field.input.lang.astroid_icon }}</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <ul class="list-inline">
                                            <li v-for="(icon, key) in element.icons" :key="key" class="list-inline-item astroid-icons">
                                                <a href="#" @click.prevent="element.icon = icon; listUpdated();" class="link-body-emphasis link-opacity-100-hover" :class="{'link-opacity-100' : element.icon === icon, 'link-opacity-50' : element.icon !== icon}"><i :class="icon" class="fa-lg"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
            <div class="text-center mt-4">
                <button @click="addSocial(customSocial)" class="btn btn-as btn-primary btn-as-primary">{{ props.field.input.lang.add_custom_social_label }}</button>
            </div>
        </div>
        <div class="col-lg-3">
            <h3>{{ props.field.input.lang.social_brands }}</h3>
            <input type="text" class="form-control" :placeholder="props.field.input.lang.social_search" v-model="socialSearch">
            <div class="d-grid gap-2 mt-3">
                <button v-for="(social, key) in filterSocial" :key="key" class="btn btn-outline-secondary" @click="addSocial(social)">
                    <i :class="social.icon"></i>
                    {{ social.title }}
                </button>
            </div>
        </div>
    </div>
</template>