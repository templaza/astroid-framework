<script setup>
import { onMounted, onUpdated, onBeforeMount, reactive, ref, watch } from 'vue';
import ResponsiveToggle from "./ResponsiveToggle.vue";
const emit = defineEmits(['update:changeDevice', 'update:statusField']);
const props = defineProps(['modelValue', 'field', 'fieldname', 'currentDevice', 'fieldChanged']);
const devices = ['mobile', 'landscape_mobile', 'tablet', 'desktop', 'large_desktop', 'larger_desktop'];
const unitOptions = ['px', 'em', 'rem', 'pt', '%'];
const rangeConfig = reactive(
    {
        'larger_desktop' : {
            'max' : 100,
            'step': 1,
            'priority': 5
        },
        'large_desktop' : {
            'max' : 100,
            'step': 1,
            'priority': 4
        },
        'desktop' : {
            'max' : 100,
            'step': 1,
            'priority': 3
        },
        'tablet'  : {
            'max' : 100,
            'step': 1,
            'priority': 2
        },
        'landscape_mobile'  : {
            'max' : 100,
            'step': 1,
            'priority': 1
        },
        'mobile'  : {
            'max' : 100,
            'step': 1,
            'priority': 0
        }
    }
)
const placeholder = ref({
    'larger_desktop' : '',
    'large_desktop' : '',
    'desktop' : '',
    'tablet'  : '',
    'landscape_mobile'  : '',
    'mobile'  : ''
});
function changeDevice(device) {
    emit('update:changeDevice', device, props.fieldname);
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
onBeforeMount(() => {
    updateUnit();
})
onMounted(() => {
    Object.keys(props.modelValue[props.fieldname+`_unit`]).forEach(key => {
        updateRange(key);
    });
    updatePlaceholder();
    devices.forEach(device => {
        if (typeof props.modelValue[props.fieldname][device] !== 'undefined' && props.modelValue[props.fieldname][device] !== '') {
            if (rangeConfig[props.currentDevice].priority < rangeConfig[device].priority) {
                changeDevice(device);
            }
        }
    })
})
onUpdated(() => {
    updateUnit();
    updateRange(props.currentDevice);
    updatePlaceholder();
})
function updateUnit() {
    let lastDevice = 'px';
    devices.forEach(device => {
        if (typeof props.modelValue[props.fieldname][device] === 'undefined' || props.modelValue[props.fieldname][device] === '') {
            props.modelValue[props.fieldname+`_unit`][device] = lastDevice;
        } else {
            lastDevice = props.modelValue[props.fieldname+`_unit`][device];
        }
    })
}
function updatePlaceholder() {
    let lastDevice = '';
    devices.forEach(device => {
        placeholder.value[device] = lastDevice;
        if (props.modelValue[props.fieldname][device]) {
            lastDevice = props.modelValue[props.fieldname][device];
        }
    })
}
</script>
<template>
    <div class="row g-3 justify-content-between">
        <div class="col col-auto">
            {{ props.field.input.lang[props.fieldname] }}
        </div>
        <div class="col col-auto">
            <ResponsiveToggle
                :modelValue="props.currentDevice"
                @update:modelValue="device => changeDevice(device)"
                :fieldChanged="props.fieldChanged"
                @update:statusField="data => emit('update:statusField', data)"
            />
        </div>
    </div>
    <div class="mt-2" v-for="device in devices" v-show="props.currentDevice===device">
        <div class="row align-items-center g-3 mb-2">
            <div class="col col-3">
                <div class="row gx-1 align-items-center form-text">
                    <div class="col">
                        <input class="form-control form-control-sm" :id="props.field.input.id +`_`+ props.fieldname +`_`+ device" :name="props.field.input.name + `[` + props.fieldname + `]` + `[` + device + `]`" type="text" v-model="props.modelValue[props.fieldname][device]" :placeholder="placeholder[device]">
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