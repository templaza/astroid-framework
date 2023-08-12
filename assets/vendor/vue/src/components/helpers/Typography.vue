<script setup>
import { onMounted, reactive, ref } from 'vue';
import axios from "axios";
import { ModelListSelect } from "vue-search-select"
import "vue-search-select/dist/VueSearchSelect.css"

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const options= ref([
    { code: "01", name: "aa", desc: "desc01" },
    { code: "02", name: "ab", desc: "desc02" },
    { code: "03", name: "bc", desc: "desc03" },
    { code: "04", name: "cd", desc: "desc04" },
    { code: "05", name: "de", desc: "desc05" },
    { code: "06", name: "ef", desc: "desc06" },
]);
const objectItem= ref({
    code: "",
    name: "",
    desc: "",
});
const searchText= ref('');
function codeAndNameAndDesc(item) {
    return `${item.code} - ${item.name} - ${item.desc}`
}
function reset() {
    objectItem = {}
}
function selectOption() {
    // select option from parent component
    objectItem = options.value[0]
}
onMounted(()=>{

})
</script>
<template>
    <div class="row row-cols-lg-3 g-3">
        <div>
            <div v-if="props.field.input.options.fontpicker">
                <label :for="props.field.input.id+`_font_face`" class="form-label">{{ props.field.input.lang.font_family }}</label>
                <model-list-select
                    :list="options"
                    v-model="objectItem"
                    option-value="code"
                    option-text="name"
                    :id="props.field.input.id+`_font_face`"
                    :name="props.field.input.name+`[font_face]`"
                    :placeholder="props.field.input.lang.inherit">
                </model-list-select>
            </div>
        </div>
    </div>
</template>