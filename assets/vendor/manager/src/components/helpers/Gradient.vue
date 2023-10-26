<script setup>
import { computed, onBeforeMount, onMounted, ref } from 'vue';
import { ColorPicker } from 'vue-color-kit'
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const _showColorPicker = ref(false);
const _currentColor = ref('#59c7f9');
const _currentColorMode = ref('start');
const gradient = ref({
    'type' : 'linear',
    'start': '',
    'stop' : '',
    'start_pos' : 0,
    'stop_pos' : 100,
    'angle': 0,
    'position' : 'at center center'
});
onBeforeMount(()=>{
    if (typeof props.modelValue !== 'undefined' && props.modelValue !== '') {
        gradient.value = {
            ...gradient.value,
            ...JSON.parse(props.modelValue)
        };
    }
})
onMounted(()=>{
    document.addEventListener('click', function(event) {
        const elem          = document.getElementById(props.field.input.id+'-colorpicker');
        const circle_light  = document.getElementById(props.field.input.id+'-colorcircle-start');
        const circle_dark   = document.getElementById(props.field.input.id+'-colorcircle-stop');
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
                    || props.field.input.colormode === '0' 
                )
            ) {
                _showColorPicker.value = false;
            }
        }    
    });
})
function showColorPicker(color) {
    _currentColorMode.value = color;
    _currentColor.value = gradient.value[_currentColorMode.value];
    _showColorPicker.value = true;
}
function changeColor(color) {
    const { r, g, b, a } = color.rgba;
    gradient.value[_currentColorMode.value] = `rgba(${r}, ${g}, ${b}, ${a})`;
}
const getGradientStyle = computed(() => {
    emit('update:modelValue', JSON.stringify(gradient.value));
    if (gradient.value.type === 'linear') {
        return gradient.value.type+'-gradient('+gradient.value.angle+'deg, '+gradient.value.start+' '+gradient.value.start_pos+'%, '+gradient.value.stop+' '+gradient.value.stop_pos+'%)';
    } else {
        return gradient.value.type+'-gradient('+gradient.value.position+', '+gradient.value.start+' '+gradient.value.start_pos+'%, '+gradient.value.stop+' '+gradient.value.stop_pos+'%)';
    }
})
</script>
<template>
    <div class="gradient-preview mb-3">
        <div class="gradient" :style="{'background': getGradientStyle}"></div>
    </div>
    <div class="color-picker mb-3">
        <div class="row" style="max-width: 210px;">
            <div class="col-4 text-center">
                <i class="border astroid-color-picker fas fa-circle fa-3x" :id="props.field.input.id+`-colorcircle-start`" :style="{'color': gradient.start}" @click="showColorPicker('start')"></i>
            </div>
            <div class="col text-center py-3">
                <i class="fa-solid fa-arrow-right"></i>
            </div>
            <div class="col-4 text-center">
                <i class="border astroid-color-picker fas fa-circle fa-3x" :id="props.field.input.id+`-colorcircle-stop`" :style="{'color': gradient.stop}" @click="showColorPicker('stop')"></i>
            </div>
        </div>
        <div v-if="_showColorPicker" class="row">
            <div class="col-auto">
                <div class="position-relative">
                    <a href="#" @click.prevent="_showColorPicker = false" class="link-body-emphasis position-absolute top-0 start-100 translate-middle"><i class="fa-solid fa-circle-xmark fa-xl"></i></a>
                    <ColorPicker
                        theme="light"
                        :color="_currentColor"
                        :sucker-hide="true"
                        :sucker-canvas="null"
                        :sucker-area="[]"
                        :id="props.field.input.id+`-colorpicker`"
                        @changeColor="changeColor"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="gradient-start-stop-position mb-3">
        <div class="row row-cols-2">
            <div>
                <label :for="props.field.input.id+`-gradient-start-pos`" class="form-label form-text">Start Color Position: {{ gradient.start_pos }}%</label>
                <input type="range" v-model="gradient.start_pos" :id="props.field.input.id+`-gradient-start-pos`" class="form-range" min="0" max="100">
            </div>
            <div>
                <label :for="props.field.input.id+`-gradient-stop-pos`" class="form-label form-text">Stop Color Position: {{ gradient.stop_pos }}%</label>
                <input type="range" v-model="gradient.stop_pos" :id="props.field.input.id+`-gradient-stop-pos`" class="form-range" min="0" max="100">
            </div>
        </div>
    </div>
    <div class="gradient-type">
        <div class="row row-cols-2">
            <div>
                <label :for="props.field.input.id+`-gradient-type`" class="form-label form-text">Gradient Type</label>
                <select v-model="gradient.type" :id="props.field.input.id+`-gradient-type`" class="form-select">
                    <option value="linear">Linear</option>
                    <option value="radial">Radial</option>
                </select>
            </div>
            <div>
                <div class="gradient-angle" v-if="gradient.type === `linear`">
                    <label :for="props.field.input.id+`-gradient-angle`" class="form-label form-text">Gradient Angle: {{ gradient.angle }}</label>
                    <input type="range" v-model="gradient.angle" :id="props.field.input.id+`-gradient-angle`" class="form-range" min="0" max="360">
                </div>
                <div class="gradient-position" v-else-if="gradient.type === `radial`">
                    <label :for="props.field.input.id+`-gradient-position`" class="form-label form-text">Gradient Position</label>
                    <select v-model="gradient.position" :id="props.field.input.id+`-gradient-position`" class="form-select">
                        <option value="at center top">Top Center</option>
                        <option value="at left top">Top Left</option>
                        <option value="at right top">Top Right</option>
                        <option value="at center center">Center Center</option>
                        <option value="at left center">Left Center</option>
                        <option value="at right center">Right Center</option>
                        <option value="at center bottom">Bottom Center</option>
                        <option value="at left bottom">Bottom Left</option>
                        <option value="at right bottom">Bottom Right</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>