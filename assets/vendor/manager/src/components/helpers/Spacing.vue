<script setup>
import { onBeforeMount, onMounted, ref } from 'vue';
import ResponsiveToggle from "./ResponsiveToggle.vue";
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const devices = ['mobile', 'landscape_mobile', 'tablet', 'desktop', 'large_desktop', 'larger_desktop'];
const unitOptions = ['px', 'em', 'rem', 'pt', '%', 'Custom'];
const currentDevice = ref('mobile');
const data = ref({
    'larger_desktop' : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
        'lock'      : false,
        'unit'      : 'px'
    },
    'large_desktop' : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
        'lock'      : false,
        'unit'      : 'px'
    },
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
    'landscape_mobile'  : {
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
const placeholder = ref({
    'larger_desktop' : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
    },
    'large_desktop' : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
    },
    'desktop' : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
    },
    'tablet'  : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
    },
    'landscape_mobile'  : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
    },
    'mobile'  : {
        'top'       : null,
        'right'     : null,
        'bottom'    : null,
        'left'      : null,
    }
});
const positions = ['top', 'right', 'bottom', 'left'];
onBeforeMount(()=>{
    if (typeof props.modelValue !== 'undefined' && props.modelValue !== '') {
        data.value = {
            ...data.value,
            ...JSON.parse(props.modelValue)
        };
    }
})
const fieldChanged = ref(false);
onMounted(()=>{
    let lastDevice = null;
    positions.forEach(position => {
        lastDevice = null;
        devices.forEach(device => {
            placeholder.value[device][position] = lastDevice;
            if (data.value[device][position]) {
                lastDevice = data.value[device][position];
            }
        });
    });
    devices.forEach(device => {
        positions.forEach(position => {
            if (data.value[device][position] && currentDevice.value !== device) {
                fieldChanged.value = true;
                currentDevice.value = device;
            }
        })
    })
})
function updatePlaceholder(position) {
    let lastDevice = null;
    devices.forEach(device => {
        placeholder.value[device][position] = lastDevice;
        if (data.value[device][position]) {
            lastDevice = data.value[device][position];
        }
    });
}
function changeDevice(device) {
    currentDevice.value = device;
}
function updateLock(device) {
    let lockValue = null;
    positions.every(position => {
        if (data.value[device][position]) {
            lockValue = data.value[device][position];
            return false;
        }
        return true;
    });
    positions.forEach(position => {
        data.value[device][position] = lockValue;
        updatePlaceholder(position);
    });
    emit('update:modelValue', JSON.stringify(data.value));
}
function updateData(device, position) {
    updatePlaceholder(position);
    if (data.value[device].lock === true) {
        positions.forEach(pos => {
            if (pos !== position) {
                data.value[device][pos] = data.value[device][position];
                updatePlaceholder(pos);
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
    <div class="row g-3 justify-content-between">
        <div class="col col-auto">
            <div v-for="device in devices" v-show="currentDevice===device" class="input-group input-group-sm">
                <div class="input-group-text">
                    <input type="checkbox"  class="btn-check" v-model="data[device].lock" @change="updateLock(device)" :id="props.field.input.id+`-lock-`+device" autocomplete="off">
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
        <div class="col col-auto">
            <ResponsiveToggle v-model="currentDevice" :fieldChanged="fieldChanged" @update:statusField="status => (fieldChanged = status)"/>
        </div>
    </div>
    <div v-for="device in devices" class="mt-2" v-show="currentDevice===device">
        <div class="input-group">
            <input v-for="position in positions" v-model="data[device][position]" @input="updateData(device, position)" type="text" class="form-control" :aria-label="position" :placeholder="placeholder[device][position]">
        </div>
        <div class="row">
            <div v-for="position in positions" class="col text-center form-text">{{ position }}</div>
        </div>
    </div>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>