<script setup>
import { onMounted, onUpdated, reactive, ref, watch } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faDesktop, faTablet, faMobile } from "@fortawesome/free-solid-svg-icons";
library.add(faDesktop, faTablet, faMobile);
const emit = defineEmits(['update:changeDevice']);
const props = defineProps(['modelValue', 'field', 'fieldname', 'currentDevice']);
const devices = ['desktop', 'tablet', 'mobile'];
const unitOptions = ['px', 'em', 'rem', 'pt', '%'];
const rangeConfig = reactive(
    {
        'desktop' : {
            'max' : 100,
            'step': 1
        },
        'tablet'  : {
            'max' : 100,
            'step': 1
        },
        'mobile'  : {
            'max' : 100,
            'step': 1
        }
    }
)
function changeDevice(device) {
    emit('update:changeDevice', device);
}
function updateRange(device) {
    if (['em', 'rem'].includes(props.modelValue[props.fieldname+`_unit`][device])) {
        rangeConfig[device]['max']  = 10;
        rangeConfig[device]['step'] = 0.1;
    }
    if (['px', 'pt'].includes(props.modelValue[props.fieldname+`_unit`][device])) {
        rangeConfig[device]['max']  = 100;
        rangeConfig[device]['step'] = 1;
    }
    if (props.modelValue[props.fieldname+`_unit`][device] === '%') {
        rangeConfig[device]['max']  = 1000;
        rangeConfig[device]['step'] = 1;
    }
}
onMounted(() => {
    Object.keys(props.modelValue[props.fieldname+`_unit`]).forEach(key => {
        updateRange(key);
    });
})
onUpdated(() => {
    updateRange(props.currentDevice);
})
</script>
<template>
    <div class="row g-3">
        <div class="col col-auto">
            <label>{{ props.field.input.lang[props.fieldname] }}</label>
        </div>
        <div class="col">
            <div class="row row-cols-auto g-3 justify-content-end">
                <div v-for="device in devices" :key="device">
                    <a href="#" @click.prevent="changeDevice(device)" :class="{'link-primary' : props.currentDevice === device, 'link-secondary' : props.currentDevice !== device}">
                        <font-awesome-icon :icon="['fas', device]" />
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-2" v-for="device in devices" v-show="props.currentDevice===device">
        <div class="row align-items-center g-3 mb-2">
            <div class="col col-auto">
                <label :for="props.field.input.id+`_`+props.fieldname+`_`+device" class="form-label form-text mt-0 mb-0">{{ props.modelValue[props.fieldname][device] + props.modelValue[props.fieldname+`_unit`][device] }}</label>
            </div>
            <div class="col">
                <div class="astroid-btn-group text-end">
                    <span v-for="(unit, key) in unitOptions" :key="unit">
                        <input type="radio" class="btn-check" v-model="props.modelValue[props.fieldname+`_unit`][device]" :name="props.field.input.name + `[` + props.fieldname + `_unit` + `]` + `[` + device + `]`" :id="props.field.input.id+`_`+props.fieldname+`_unit_`+device+`_`+key" :value="unit" autocomplete="off">
                        <label class="btn btn-sm btn-outline-primary btn-as-outline-primary" :for="props.field.input.id+`_`+props.fieldname+`_unit_`+device+`_`+key">{{ unit }}</label>
                    </span>
                </div>
            </div>
        </div>
        <input type="range" class="form-range" min="0" :step="rangeConfig[device]['step']" :max="rangeConfig[device]['max']" v-model="props.modelValue[props.fieldname][device]" :name="props.field.input.name + `[` + props.fieldname + `]` + `[` + device + `]`" :id="props.field.input.id +`_`+ props.fieldname +`_`+ device">
    </div>
</template>