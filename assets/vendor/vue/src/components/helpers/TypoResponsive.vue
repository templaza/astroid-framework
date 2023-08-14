<script setup>
import { onMounted, ref } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faDesktop, faTablet, faMobile } from "@fortawesome/free-solid-svg-icons";
library.add(faDesktop, faTablet, faMobile);
const emit = defineEmits(['update:changeDevice']);
const props = defineProps(['modelValue', 'field', 'fieldname', 'currentDevice']);
const devices = ref(['desktop', 'tablet', 'mobile']);
function changeDevice(device) {
    emit('update:changeDevice', device);
}
</script>
<template>
    <div class="row g-3">
        <div class="col col-auto">
            <label>{{ props.field.input.lang[props.fieldname] }}</label>
        </div>
        <div class="col">
            <div class="row row-cols-auto g-3">
                <div v-for="device in devices">
                    <a href="#" @click.prevent="changeDevice(device)" :class="{'link-primary' : props.currentDevice === device, 'link-secondary' : props.currentDevice !== device}">
                        <font-awesome-icon :icon="['fas', device]" />
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div v-for="device in devices" v-show="props.currentDevice===device">
        <label :for="props.field.input.id+`_`+props.fieldname+`_`+device" class="form-label">{{ props.modelValue[props.fieldname][device] }}</label>
        <input type="range" class="form-range" min="0" max="100" v-model="props.modelValue[props.fieldname][device]" :name="props.field.input.name + `[` + props.fieldname + `]` + `[` + device + `]`" :id="props.field.input.id +`_`+ props.fieldname +`_`+ device">
    </div>
</template>