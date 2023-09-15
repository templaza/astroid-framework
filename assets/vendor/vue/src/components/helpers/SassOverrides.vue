<script setup>
import { computed, onBeforeMount, onUpdated, ref, watch } from 'vue';
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: { type: String, default: '' },
    field: { type: Object, default: null },
});

const list = ref([]);

onBeforeMount(() => {
    list.value = JSON.parse(props.modelValue);
});

onUpdated(()=>{
    if (JSON.stringify(list.value) !== props.modelValue) {
        list.value = JSON.parse(props.modelValue);
    }
})

const override_text = computed(() => {
    return JSON.stringify(list.value);
});
watch(override_text, (newText) => {
    emit('update:modelValue', newText);
})

function addVariable() {
    list.value.push({
        variable: '',
        value: '',
        color: false
    })
}

function removeVariable(index) {
    if (confirm('Are you sure?')) {
        _showColorPicker[index] = false;
        list.value.splice(index, 1);
    }
}

const _showColorPicker = ref([]);

</script>
<template>
    <table v-if="list.length > 0" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">Variable</th>
                <th scope="col">Value</th>
                <th scope="col" width="1%">Color</th>
                <th scope="col" width="1%">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in list" :key="index">
                <td><input type="text" class="form-control mw-100" v-model="item.variable"></td>
                <td>
                    <input v-if="item.color===false" type="text" class="form-control mw-100" v-model="item.value">
                    <div v-else>
                        <i class="fas fa-circle fa-3x border astroid-color-picker" :id="props.field.input.id+`-colorcircle-`+index" :style="{'color': item.value}" @click="_showColorPicker[index] = true"></i>
                        <div v-if="_showColorPicker[index]" class="row">
                            <div class="col-auto">
                                <div class="position-relative">
                                    <a href="#" @click.prevent="_showColorPicker[index] = false" class="link-body-emphasis position-absolute top-0 start-100 translate-middle"><i class="fa-solid fa-circle-xmark fa-xl"></i></a>
                                    <ColorPicker
                                        theme="light"
                                        :color="item.value"
                                        :sucker-hide="true"
                                        :sucker-canvas="null"
                                        :sucker-area="[]"
                                        :id="props.field.input.id+`-colorpicker-`+index"
                                        @changeColor="(color) => {item.value = `rgba(${color.rgba.r}, ${color.rgba.g}, ${color.rgba.b}, ${color.rgba.a})`; }"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" :id="`color-`+index" v-model="item.color">
                    </div>
                </td>
                <td class="text-center"><button class="btn btn-danger" @click.prevent="removeVariable(index)"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        </tbody>
    </table>
    <div class="text-center"><button class="btn btn-lg btn-as btn-as-primary" @click.prevent="addVariable">Add Variable</button></div>
    <input
        :id="props.field.input.id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>