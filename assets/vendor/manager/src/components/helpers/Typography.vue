<script setup>
import { onMounted, onUpdated, ref, watch, inject, reactive } from 'vue';
import axios from "axios";
import { ModelListSelect } from "vue-search-select"
import TypoResponsive from './TypoResponsive.vue';
import { ColorPicker } from 'vue-color-kit'

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const theme = inject('theme', 'light');
const constant = inject('constant', {});
const font_styles = [
    {'value':'bold', 'text':'<strong>Bold</strong>'},
    {'value':'italic', 'text':'<em>Italic</em>'},
    {'value':'underline', 'text':'<span class="typography-underline">Underline</span>'},
]
const system_fonts = {
    "Arial, Helvetica, sans-serif" : 'Arial, Helvetica',
    "Arial Black, Gadget, sans-serif" : 'Arial Black, Gadget',
    "Bookman Old Style, serif" : 'Bookman Old Style',
    "Comic Sans MS, cursive" : 'Comic Sans MS',
    "Courier, monospace" : 'Courier',
    "Garamond, serif" : 'Garamond',
    "Georgia, serif" : 'Georgia',
    "Impact, Charcoal, sans-serif" : 'Impact, Charcoal',
    "Lucida Console, Monaco, monospace" : 'Lucida Console, Monaco',
    "Lucida Sans Unicode, sans-serif" : 'Lucida Sans Unicode',
    "MS Sans Serif, Geneva, sans-serif" : 'MS Sans Serif, Geneva',
    "MS Serif, New York, sans-serif" : 'MS Serif, New York',
    "Palatino Linotype, Book Antiqua, Palatino, serif" : 'Palatino Linotype, Book Antiqua, Palatino',
    "Tahoma, Geneva, sans-serif" : 'Tahoma, Geneva',
    "Times New Roman, Times, serif" : 'Times New Roman, Times',
    "Trebuchet MS, Helvetica, sans-serif" : 'Trebuchet MS, Helvetica',
    "Verdana, Geneva, sans-serif" : 'Verdana, Geneva'
}
const quotes = [
    "I love you and that's the beginning and end of everything.",
    'I saw that you were perfect, and so I loved you. Then I saw that you were not perfect and I loved you even more.',
    "You know you're in love when you can't fall asleep because reality is finally better than your dreams.",
    "Love is that condition in which the happiness of another person is essential to your own.",
    "The best thing to hold onto in life is each other.",
    "I need you like a heart needs a beat.",
    "I am who I am because of you. You are every reason, every hope, and every dream I've ever had.",
    "If I had a flower for every time I thought of you.. I could walk through my garden forever.",
    "Take my hand, take my whole life too. For I can't help falling in love with you.",
    "If you live to be a hundred, I want to live to be a hundred minus one day so I never have to live without you.",
    "You are the finest, loveliest, tenderest, and most beautiful person I have ever known and even that is an understatement.",
    "In all the world, there is no heart for me like yours. In all the world, there is no love for you like mine."
];
const options= reactive({
    'system': [],
    'google': [],
    'local' : [],
});
const fontSelected= ref({
    value: "",
    text: "",
});
const fonttypes = ref(['system','google'])
const font_type = ref('google');
const currentDevice = ref('desktop');

function getFontType(font_face) {
    if (font_face.search(/^library-font-/) !== -1) {
        font_type.value = 'local';
    } else if (typeof system_fonts[font_face] !== 'undefined') {
        font_type.value = 'system';
    } else {
        font_type.value = 'google';
    }
}

onMounted(()=>{
    let url = constant.site_url+"administrator/index.php?option=com_ajax&astroid=google-fonts&template="+constant.template_name+"&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "fonts_ajax.txt?ts="+Date.now();
    }
    Object.keys(props.field.input.value).forEach(key => {
        if (typeof props.modelValue[key] === 'undefined') {
            props.modelValue[key] = props.field.input.value[key];
        }
    })
    getFontType(props.field.input.value.font_face);
    axios.get(url)
    .then(function (response) {
        if (response.status === 200) {
            options.system = response.data.system;
            options.google = response.data.google;
            options.local = response.data.local;
            if (options.local.length > 1) {
                fonttypes.value.push('local');
            }
            response.data[font_type.value].forEach(element => {
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

    if (props.modelValue['font_color'].trim() !== '') {
        try {
            const tmp = JSON.parse(props.modelValue['font_color']);
            _color.light    = tmp.light;
            _color.dark     = tmp.dark;
        } catch (e) {
            _color.light = props.modelValue['font_color'];
            _color.dark = props.modelValue['font_color'];
        }
    }
    document.addEventListener('click', function(event) {
        const elem          = document.getElementById(props.field.input.id+'-colorpicker');
        const circle_light  = document.getElementById(props.field.input.id+'-colorcircle-light');
        const circle_dark   = document.getElementById(props.field.input.id+'-colorcircle-dark');
        if (_showColorPicker.value === true) {
            if (
                elem 
                && circle_light 
                && !circle_light.contains(event.target) 
                && !elem.contains(event.target)
                && (
                    (
                        circle_dark
                        && !circle_dark.contains(event.target)
                    )
                    || parseInt(props.field.input.options.colormode) === 0 
                )
            ) {
                _showColorPicker.value = false;
            }
        }    
    });
})

onUpdated(()=>{
    if (fontSelected.value.value !== '' && fontSelected.value.value !== props.modelValue['font_face']) {
        getFontType(props.modelValue['font_face']);
        fontSelected.value = options[font_type.value].find((option) => option.value === props.modelValue['font_face']);
    }
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
const _currentColor = ref('');
const _currentColorMode = ref('light');
const _color = reactive({
    light: '',
    dark: ''
})

function showColorPicker(colorMode) {
    _currentColor.value     = _color[colorMode];
    _currentColorMode.value = colorMode;
    _showColorPicker.value  = true;
}

function updateColor(color) {
    try {
        if (!!props.modelValue['font_color']) {
            let tmp = JSON.parse(props.modelValue['font_color']);
            tmp[_currentColorMode.value] = color;
            props.modelValue['font_color'] = JSON.stringify(tmp);
        } else {
            let tmp = {'light': '', 'dark': ''};
            tmp[_currentColorMode.value] = color;
            props.modelValue['font_color'] = JSON.stringify(tmp);
        }
    } catch (e) {
        const tmp = {'light': color, 'dark': color};
        props.modelValue['font_color'] = JSON.stringify(tmp);
    }
}

function changeColor(color) {
    const { r, g, b, a } = color.rgba;
    if (r === 0, g === 0, b === 0, a === 0) {
        _color[_currentColorMode.value] = '';
    } else {
        _color[_currentColorMode.value] = `rgba(${r}, ${g}, ${b}, ${a})`;
    }
    updateColor(_color[_currentColorMode.value]);
}

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
</script>
<template>
    <div class="row g-4" :class="`row-cols-lg-`+(Math.ceil(props.field.input.options.columns/2))+` row-cols-xl-`+props.field.input.options.columns">
        <div>
            <div class="row row-cols-1 g-4">
                <div v-if="props.field.input.options.fontpicker">
                    <div class="row g-3 mb-2 justify-content-center">
                        <div class="col col-auto">
                            <label :for="props.field.input.id+`_font_face_search`" class="form-label m-0">{{ props.field.input.lang.font_family }}</label>
                        </div>
                        <div class="col">
                            <div class="astroid-btn-group text-end">
                                <span v-for="fonttype in fonttypes" :key="fonttype">
                                    <input type="radio" class="btn-check" v-model="font_type" :id="props.field.input.id+`_font_type_`+fonttype" :value="fonttype" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-primary btn-as-outline-primary text-capitalize" :for="props.field.input.id+`_font_type_`+fonttype">{{ fonttype }}</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <model-list-select
                        :list="options[font_type]"
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
                    <div class="astroid-color">
                        <div class="row">
                            <div :class="{
                                'col-4 text-center' : (props.field.input.options.colormode === '1'),
                                'col-12': (props.field.input.options.colormode !== '1')
                            }">
                                <font-awesome-icon :id="props.field.input.id+`-colorcircle-light`" :icon="['fas', 'circle']" size="3x" class="border astroid-color-picker" :style="{'color': _color.light}" @click="showColorPicker('light')" />
                                <div v-if="props.field.input.options.colormode === '1'">Light</div>
                            </div>
                            <div v-if="props.field.input.options.colormode === '1'" class="col text-center py-3">
                                <font-awesome-icon :icon="['fas', 'arrows-left-right']" />
                            </div>
                            <div v-if="props.field.input.options.colormode === '1'" class="col-4 text-center">
                                <font-awesome-icon :id="props.field.input.id+`-colorcircle-dark`" :icon="['fas', 'circle']" size="3x" class="border astroid-color-picker" :style="{'color': _color.dark}" @click="showColorPicker('dark')" />
                                <div>Dark</div>
                            </div>
                        </div>
                        <input type="hidden" :name="props.field.input.name+`[font_color]`" :id="props.field.input.id+`_font_color`" v-model="props.modelValue['font_color']" />
                        <ColorPicker v-if="_showColorPicker"
                            :theme="theme"
                            :color="_currentColor"
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
                    <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                        <span v-for="(option, key) in font_styles">
                            <input type="checkbox" class="btn-check" v-model="props.modelValue['font_style']" :name="props.field.input.name+`[font_style]`" :id="props.field.input.id+`_font_style_`+key" :value="option.value" autocomplete="off">
                            <label class="btn btn-sm" :for="props.field.input.id+`_font_style_`+key" v-html="option.text"></label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="props.field.input.options.preview" class="typography-preview">
        <link v-if="font_type === `google` && (typeof options[font_type].find((font) => font.value === fontSelected.value) !== 'undefined') && fontSelected.value !== `` && fontSelected.value !== `__default` && fontSelected.value.search(/^library-font-/) === -1" :href="`https://fonts.googleapis.com/css?family=`+fontSelected.value" rel="stylesheet" />
        <div class="card card-default card-body mt-4" :style="
            {
            'font-family' : fontSelected.text,
            'font-weight' : props.modelValue['font_weight'],
            'text-transform': props.modelValue['text_transform'],
            'font-size' : props.modelValue['font_size'][currentDevice]+props.modelValue['font_size_unit'][currentDevice],
            'line-height' : props.modelValue['line_height'][currentDevice]+props.modelValue['line_height_unit'][currentDevice],
            'letter-spacing' : props.modelValue['letter_spacing'][currentDevice]+props.modelValue['letter_spacing_unit'][currentDevice],
            }">
            <p>{{ quotes[getRandomInt(0, quotes.length - 1)] }}</p>
            <p>Aa Bb Cc Dd Ee Ff Gg Hh Ii Jj Kk Ll Mm Nn Oo Pp Qq Rr Ss Tt Uu Vv Ww Xx Yy Zz</p>
            <p class="mb-0">0 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20</p>
        </div>
    </div>
</template>