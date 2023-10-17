<script setup>
import { onMounted, onUpdated, reactive, ref, watch } from 'vue';
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
        rangeConfig[device]['step'] = 0.01;
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
            {{ props.field.input.lang[props.fieldname] }}
        </div>
        <div class="col">
            <div class="row row-cols-auto g-3 justify-content-end">
                <div v-for="device in devices" :key="device">
                    <a href="#" @click.prevent="changeDevice(device)" :class="{'link-primary' : props.currentDevice === device, 'link-secondary' : props.currentDevice !== device}">
                        <i class="fas" :class="`fa-`+device"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-2" v-for="device in devices" v-show="props.currentDevice===device">
        <div class="row align-items-center g-3 mb-2">
            <div class="col col-3">
                <div class="row gx-1 align-items-center form-text">
                    <div class="col">
                        <input class="form-control form-control-sm" :id="props.field.input.id +`_`+ props.fieldname +`_`+ device" :name="props.field.input.name + `[` + props.fieldname + `]` + `[` + device + `]`" type="text" v-model="props.modelValue[props.fieldname][device]">
                    </div> 
                    <div class="col-auto">
                        {{ props.modelValue[props.fieldname+`_unit`][device] }}
                    </div>
                </div>
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
        <input type="range" class="form-range" min="0" :step="rangeConfig[device]['step']" :max="rangeConfig[device]['max']" v-model="props.modelValue[props.fieldname][device]" :id="props.field.input.id+`_`+props.fieldname+`_range_`+device">
    </div>
</template>