<script setup>
import { onBeforeMount, ref } from 'vue';

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const devices = ['desktop', 'tablet', 'mobile'];
const unitOptions = ['px', 'em', 'rem', 'pt', '%', 'Custom'];
const currentDevice = ref('desktop');
const data = ref({
    'desktop' : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
        'lock'      : false,
        'unit'      : 'px'
    },
    'tablet'  : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
        'lock'      : false,
        'unit'      : 'px'
    },
    'mobile'  : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
        'lock'      : false,
        'unit'      : 'px'
    }
});
onBeforeMount(()=>{
    if (typeof props.modelValue !== 'undefined' && props.modelValue !== '') {
        data.value = {
            ...data.value,
            ...JSON.parse(props.modelValue)
        };
    }
})
function changeDevice(device) {
    currentDevice.value = device;
}
function updateData(device, position) {
    if (data.value[device].lock === true) {
        ['top', 'right', 'bottom', 'left'].forEach(pos => {
            if (pos !== position) {
                data.value[device][pos] = data.value[device][position];
            }
        });
    }
    emit('update:modelValue', JSON.stringify(data.value));
}
function updateUnit() {
    emit('update:modelValue', JSON.stringify(data.value));
}
</script>
<template>
    <div class="row g-3">
        <div class="col col-auto">
            <div v-for="device in devices" v-show="currentDevice===device" class="input-group input-group-sm">
                <div class="input-group-text">
                    <input type="checkbox"  class="btn-check" v-model="data[device].lock" :id="props.field.input.id+`-lock-`+device" autocomplete="off">
                    <label class="spacing-lock" :for="props.field.input.id+`-lock-`+device"><i class="fas" :class="{ 
                        'fa-unlock ': data[device].lock === false,
                        'fa-lock' : data[device].lock === true
                    }"></i></label>
                </div>
                <select class="form-select" v-model="data[device].unit" :id="props.field.input.id+`-unit-`+device" @change="updateUnit">
                    <option v-for="unit in unitOptions">{{ unit }}</option>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="row row-cols-auto g-3 justify-content-end">
                <div v-for="device in devices" :key="device">
                    <a href="#" @click.prevent="changeDevice(device)" :class="{'link-primary' : currentDevice === device, 'link-secondary' : currentDevice !== device}">
                        <i class="fas" :class="`fa-`+device"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div v-for="device in devices" class="input-group mt-2" v-show="currentDevice===device">
        <input v-for="position in ['top', 'right', 'bottom', 'left']" v-model="data[device][position]" @input="updateData(device, position)" type="text" class="form-control" :aria-label="position" :placeholder="position">
    </div>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>