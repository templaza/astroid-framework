<script setup>
import { onMounted, ref, watch } from 'vue';
import axios from "axios";
import { ModelListSelect } from "vue-search-select"
import TypoResponsive from './TypoResponsive.vue';
import "vue-search-select/dist/VueSearchSelect.css"

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field', 'constant']);
const options= ref([]);
const fontSelected= ref({
    value: "",
    text: "",
});

const currentDevice = ref('desktop');

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
                if (props.modelValue['font_face'] === element.value) {
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

watch(fontSelected, (newFont) => {
    if (newFont.value !== props.modelValue['font_face']) {
        props.modelValue['font_face'] = newFont.value;
    }
})

function changeDeviceStatus(device) {
    currentDevice.value = device;
}
</script>
<template>
    <div class="row row-cols-lg-3 g-4">
        <div>
            <div class="row row-cols-1 g-4">
                <div v-if="props.field.input.options.fontpicker">
                    <label :for="props.field.input.id+`_font_face`" class="form-label">{{ props.field.input.lang.font_family }}</label>
                    <model-list-select
                        :list="options"
                        v-model="fontSelected"
                        option-value="value"
                        option-text="text"
                        :id="props.field.input.id+`_font_face`"
                        :name="props.field.input.name+`[font_face]`"
                        :placeholder="props.field.input.lang.inherit"
                        >
                    </model-list-select>
                </div>
                <div v-if="props.field.input.options.fontpicker">
                    <label :for="props.field.input.id+`_alt_font_face`" class="form-label">{{ props.field.input.lang.font_family_alt }}</label>
                    <select :id="props.field.input.id+`_alt_font_face`" :name="props.field.input.name+`[alt_font_face]`" v-model="props.modelValue['alt_font_face']" class="form-select">
                        <option v-for="option in props.field.input.options.system_fonts" :value="option.value">{{ option.text }}</option>
                    </select>
                </div>
                <div v-if="props.field.input.options.weightpicker">
                    <label :for="props.field.input.id+`_font_weight`" class="form-label">{{ props.field.input.lang.font_weight }}</label>
                    <select :id="props.field.input.id+`_font_weight`" :name="props.field.input.name+`[font_weight]`" v-model="props.modelValue['font_weight']" class="form-select">
                        <option v-for="option in [100, 200, 300, 400, 500, 600, 700, 800, 900]" :value="option">{{ option }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="row row-cols-1 g-4">
                <div v-if="props.field.input.options.sizepicker">
                    <typo-responsive 
                        v-model="props.modelValue" 
                        :field="props.field" 
                        :fieldname="'font_size'" 
                        :current-device="currentDevice"
                        @update:change-device="changeDeviceStatus" />
                </div>
            </div>
        </div>
    </div>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="JSON.stringify(modelValue)"
        type="hidden"
    />
</template>