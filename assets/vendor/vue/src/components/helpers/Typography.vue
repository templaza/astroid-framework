<script setup>
import { onMounted, reactive, ref } from 'vue';
import axios from "axios";
import { ModelListSelect } from "vue-search-select"
import "vue-search-select/dist/VueSearchSelect.css"

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field', 'constant']);
const options= ref([]);
const fontSelected= ref({
    value: "",
    text: "",
});
// const searchText= ref('');
// function codeAndNameAndDesc(item) {
//     return `${item.code} - ${item.name} - ${item.desc}`
// }
// function reset() {
//     objectItem = {}
// }
// function selectOption() {
//     // select option from parent component
//     objectItem = options.value[0]
// }
onMounted(()=>{
    let url = props.constant.site_url+"administrator/index.php?option=com_ajax&astroid=google-fonts&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "fonts_ajax.txt?ts="+Date.now();
    }
    axios.get(url)
    .then(function (response) {
        if (response.status === 200) {
            options.value = response.data;
            response.data.forEach(element => {
                if (props.field.input.value.font_face === element.value) {
                    fontSelected.value = element;
                }
            });
        }
    })
    .catch(function (error) {
        // handle error
        console.log(error);
    });
})
</script>
<template>
    <div class="row row-cols-lg-3 g-3">
        <div>
            <div v-if="props.field.input.options.fontpicker">
                <label :for="props.field.input.id+`_font_face`" class="form-label">{{ props.field.input.lang.font_family }}</label>
                <model-list-select
                    :list="options"
                    v-model="fontSelected"
                    option-value="value"
                    option-text="text"
                    :id="props.field.input.id+`_font_face`"
                    :name="props.field.input.name+`[font_face]`"
                    :placeholder="props.field.input.lang.inherit">
                </model-list-select>
                <label :for="props.field.input.id+`_alt_font_face`" class="form-label">{{ props.field.input.lang.font_family_alt }}</label>
                <select :id="props.field.input.id+`_alt_font_face`" :name="props.field.input.name+`[alt_font_face]`" class="form-select">
                    <option v-for="option in props.field.input.options.system_fonts" :value="option.value">{{ option.text }}</option>
                </select>
            </div>
        </div>
    </div>
</template>