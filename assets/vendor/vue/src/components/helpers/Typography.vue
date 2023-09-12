<script setup>
import { onMounted, ref, watch } from 'vue';
import axios from "axios";
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCircle, faArrowsLeftRight } from "@fortawesome/free-solid-svg-icons";
import { ModelListSelect } from "vue-search-select"
import TypoResponsive from './TypoResponsive.vue';
import "vue-search-select/dist/VueSearchSelect.css"
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'

library.add(faCircle, faArrowsLeftRight);
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field', 'constant']);
const font_styles = [
    {'value':'bold', 'text':'<strong>Bold</strong>'},
    {'value':'italic', 'text':'<em>Italic</em>'},
    {'value':'underline', 'text':'<span class="typography-underline">Underline</span>'},
]
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

    document.addEventListener('click', function(event) {
        const elem          = document.getElementById(props.field.input.id+'-colorpicker');
        const circle_color  = document.getElementById(props.field.input.id+'-colorcircle');
        if (_showColorPicker.value === true) {
            if (
                elem 
                && circle_color 
                && !circle_color.contains(event.target) 
                && !elem.contains(event.target)
            ) {
                _showColorPicker.value = false;
            }
        }    
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

const _showColorPicker = ref(false);

function showColorPicker() {
    _showColorPicker.value  = true;
}

function changeColor(color) {
    const { r, g, b, a } = color.rgba;
    props.modelValue['font_color'] = `rgba(${r}, ${g}, ${b}, ${a})`;
}
</script>
<template>
    <div class="row  row-cols-lg-2 row-cols-xl-3 g-4">
        <div>
            <div class="row row-cols-1 g-4">
                <div v-if="props.field.input.options.fontpicker">
                    <label :for="props.field.input.id+`_font_face_search`" class="form-label">{{ props.field.input.lang.font_family }}</label>
                    <model-list-select
                        :list="options"
                        v-model="fontSelected"
                        option-value="value"
                        option-text="text"
                        :id="props.field.input.id+`_font_face_search`"
                        :placeholder="props.field.input.lang.inherit"
                        >
                    </model-list-select>
                    <input type="hidden" :id="props.field.input.id+`_font_face`" :name="props.field.input.name+`[font_face]`" v-model="fontSelected.value">
                </div>
                <div v-if="props.field.input.options.fontpicker">
                    <label :for="props.field.input.id+`_alt_font_face`" class="form-label">{{ props.field.input.lang.font_family_alt }}</label>
                    <select :id="props.field.input.id+`_alt_font_face`" :name="props.field.input.name+`[alt_font_face]`" v-model="props.modelValue['alt_font_face']" class="form-select">
                        <option v-for="option in props.field.input.options.system_fonts" :value="option.value" :key="option.value">{{ option.text }}</option>
                    </select>
                </div>
                <div v-if="props.field.input.options.weightpicker">
                    <label :for="props.field.input.id+`_font_weight`" class="form-label">{{ props.field.input.lang.font_weight }}</label>
                    <select :id="props.field.input.id+`_font_weight`" :name="props.field.input.name+`[font_weight]`" v-model="props.modelValue['font_weight']" class="form-select">
                        <option v-for="option in [100, 200, 300, 400, 500, 600, 700, 800, 900]" :value="option" :key="option">{{ option }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="row row-cols-1 g-2">
                <div v-if="props.field.input.options.sizepicker">
                    <typo-responsive 
                        v-model="props.modelValue" 
                        :field="props.field" 
                        :fieldname="'font_size'" 
                        :current-device="currentDevice"
                        @update:change-device="changeDeviceStatus" />
                </div>
                <div v-if="props.field.input.options.letterspacingpicker">
                    <typo-responsive 
                        v-model="props.modelValue" 
                        :field="props.field" 
                        :fieldname="'letter_spacing'" 
                        :current-device="currentDevice"
                        @update:change-device="changeDeviceStatus" />
                </div>
                <div v-if="props.field.input.options.lineheightpicker">
                    <typo-responsive 
                        v-model="props.modelValue" 
                        :field="props.field" 
                        :fieldname="'line_height'" 
                        :current-device="currentDevice"
                        @update:change-device="changeDeviceStatus" />
                </div>
            </div>
        </div>
        <div>
            <div class="row row-cols-1 g-4">
                <div v-if="props.field.input.options.colorpicker">
                    <div class="form-label">{{ props.field.input.lang.font_color }}</div>
                    <div>
                        <font-awesome-icon :id="props.field.input.id+`-colorcircle`" :icon="['fas', 'circle']" size="3x" class="border astroid-color-picker" :style="{'color': props.modelValue['font_color']}" @click="showColorPicker()" />
                        <input type="hidden" :name="props.field.input.name+`[font_color]`" :id="props.field.input.id+`_font_color`" v-model="props.modelValue['font_color']" />
                        <ColorPicker v-if="_showColorPicker"
                            theme="light"
                            :color="props.modelValue['font_color']"
                            :sucker-hide="true"
                            :sucker-canvas="null"
                            :sucker-area="[]"
                            :id="props.field.input.id+`-colorpicker`"
                            @changeColor="changeColor"
                        />
                    </div>
                </div>
                <div v-if="props.field.input.options.transformpicker">
                    <label :for="props.field.input.id+`_text_transform`" class="form-label">{{ props.field.input.lang.text_transform }}</label>
                    <select :id="props.field.input.id+`_text_transform`" :name="props.field.input.name+`[text_transform]`" v-model="props.modelValue['text_transform']" class="form-select">
                        <option v-for="(value, key) in props.field.input.options.text_transform_options" :value="key" :key="key">{{ value }}</option>
                    </select>
                </div>
                <div v-if="props.field.input.options.stylepicker">
                    <div class="form-label">{{ props.field.input.lang.font_style }}</div>
                    <div>
                        <span v-for="(option, key) in font_styles">
                            <input type="checkbox" class="btn-check" v-model="props.modelValue['font_style']" :name="props.field.input.name+`[font_style]`" :id="props.field.input.id+`_font_style_`+key" :value="option.value" autocomplete="off">
                            <label class="btn btn-sm" :for="props.field.input.id+`_font_style_`+key" v-html="option.text"></label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>