<script setup>
import { computed, onMounted, onUpdated, ref, watch } from 'vue';
import { ColorPicker } from 'vue-color-kit'
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
    list.value = JSON.parse(props.modelValue);
})
onUpdated(()=>{
    if (JSON.stringify(list.value) !== props.modelValue) {
        list.value = JSON.parse(props.modelValue);
    }
})
const list_text = computed(() => {
  return JSON.stringify(list.value);
})
watch(list_text, (newText) => {
    if (newText !== props.modelValue) {
        emit('update:modelValue', newText);
    }
})
function removeAt(idx) {
    if (confirm('Are you sure?')) {
        list.value.splice(idx, 1);
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
                class="astroid-profile row row-cols-1 g-4"
                handle=".item-move"
                item-key="id">
                <template #item="{element, index}">
                    <div>
                        <div class="card card-default">
                            <div class="card-header d-flex justify-content-between">
                                <span>
                                    <i class="me-2" :class="element.icon" :style="{'color': element.color}"></i>{{ element.title }}
                                </span>
                                <span>
                                    <i class="item-move fa-solid fa-up-down me-3"></i>
                                    <a href="#" class="link-danger" @click.prevent="removeAt(index)"><i class="fas fa-trash-alt"></i></a>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-4">
                                        <label v-if="element.title === 'WhatsApp'" class="form-label" :for="`astroid_profile_link`+index">{{ props.field.input.lang.astroid_mobile_number }}</label>
                                        <label v-else-if="element.title === 'Telegram'" class="form-label" :for="`astroid_profile_link`+index">{{ props.field.input.lang.astroid_username }}</label>
                                        <label v-else-if="element.title === 'Skype'" class="form-label" :for="`astroid_profile_link`+index">{{ props.field.input.lang.astroid_skype_id }}</label>
                                        <label v-else class="form-label" :for="`astroid_profile_link`+index">{{ props.field.input.lang.astroid_link }}</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input v-model="element.link" type="text" class="form-control" :id="`astroid_profile_link`+index">
                                    </div>
                                </div>
                                <div v-if="element.icons.length === 0" class="row g-2">
                                    <div class="col-sm-4">
                                        <label class="form-label" :for="`astroid_profile_icon_class`+index">{{ props.field.input.lang.astroid_icon_class }}</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input v-model="element.icon" type="text" class="form-control" :id="`astroid_profile_icon_class`+index">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label" :for="`astroid_profile_title`+index">{{ props.field.input.lang.astroid_title }}</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input v-model="element.title" type="text" class="form-control" :id="`astroid_profile_title`+index">
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-label">{{ props.field.input.lang.astroid_color }}</div>
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
                                                        @changeColor="(color) => {element.color = color.rgba.r || color.rgba.g || color.rgba.b || color.rgba.a ? `rgba(${color.rgba.r}, ${color.rgba.g}, ${color.rgba.b}, ${color.rgba.a})` : '';}"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="row g-2">
                                    <div class="col-sm-4">
                                        <div class="form-label">{{ props.field.input.lang.astroid_icon }}</div>
                                    </div>
                                    <div class="col-sm-8">
                                        <ul class="list-inline">
                                            <li v-for="(icon, key) in element.icons" :key="key" class="list-inline-item astroid-icons">
                                                <a href="#" @click.prevent="element.icon = icon;" class="link-body-emphasis link-opacity-100-hover" :class="{'link-opacity-100' : element.icon === icon, 'link-opacity-50' : element.icon !== icon}"><i :class="icon" class="fa-lg"></i></a>
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
                <button @click.prevent="addSocial(customSocial)" class="btn btn-as btn-primary btn-as-primary">{{ props.field.input.lang.add_custom_social_label }}</button>
            </div>
        </div>
        <div class="col-lg-3">
            <h3>{{ props.field.input.lang.social_brands }}</h3>
            <input type="text" class="form-control" :placeholder="props.field.input.lang.social_search" v-model="socialSearch">
            <div class="d-grid gap-2 mt-3">
                <button v-for="(social, key) in filterSocial" :key="key" class="btn btn-outline-secondary" @click.prevent="addSocial(social)">
                    <i :class="social.icon"></i>
                    {{ social.title }}
                </button>
            </div>
        </div>
    </div>
</template>