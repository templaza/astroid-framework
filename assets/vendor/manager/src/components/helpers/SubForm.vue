<script setup>
import { onBeforeMount, onMounted, ref } from "vue";
import draggable from "vuedraggable";
import Fields from './Fields.vue';

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const items = ref([]);
const edit  = ref(false);
const params = ref(new Object());
const currentIdx = ref(-1);
onBeforeMount(()=>{
    if (props.modelValue) {
        items.value = JSON.parse(props.modelValue);
    }
})

function listUpdated() {
    emit('update:modelValue', JSON.stringify(items.value));
}
function checkShow(field) {
    if (field.ngShow !== '' && field.ngShow.match(/\[.+?\]/)) {
        const expression = field.ngShow.replace(/\[(.+?)\]/g, "params.value\['$1'\]");
        try {
            return new Function('params', 'return ' + expression)(params);
        } catch (error) {
            console.log(error);
            console.log('Error at: '+expression);
        }
    }
    return true;
}
function editItem(index) {
    params.value = {};
    if (index === -1) {
        props.field.input.form.info.params.forEach(param => {
            params.value[param.name] = param.value;
        });
    } else {
        items.value[index].params.forEach(param => {
            params.value[param.name] = param.value;
        });
    }
    currentIdx.value = index;
    edit.value = true;
}

function saveItem(){
    let tmp = [];
    Object.keys(params.value).forEach(key => {
        if (typeof params.value[key] === 'object' && !Array.isArray(params.value[key]) && params.value[key] !== null) {
            tmp.push({
                'name': key,
                'value': JSON.parse(JSON.stringify(params.value[key]))
            });
        } else {
            tmp.push({
                'name': key,
                'value': params.value[key]
            });
        }
    });
    let id = Date.now() * 1000 + Math.random() * 1000;
    id = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
    if (currentIdx.value === -1) {
        items.value.push({
            id: id,
            params: tmp
        });
    }
    params.value = {};
    edit.value = false;
}
</script>
<template>
    <div v-if="!edit">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Items</h6>
            <button class="btn btn-as btn-sm btn-primary btn-as-primary" @click.prevent="editItem(-1)">Add Item</button>
        </div>
        <draggable 
            v-model="items" 
            tag="div"
            @change="listUpdated"
            class="astroid-subform row row-cols-1 g-2"
            ghost-class="ghost"
            item-key="id">
            <template #item="{ element, index }">
                <div>
                    <div class="card card-body">
                        {{ element.params.find((param) => param.name === 'title') ? element.params.find((param) => param.name === 'title').value : 'Item ' + (index+1) }}
                    </div>
                </div>
            </template>
        </draggable>
    </div>
    <div v-else>
        <div v-for="(fieldset, idx) in props.field.input.form.content" :key="fieldset.name" class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>Add Item</div>
                <button class="btn btn-sm btn-primary btn-as-primary" @click.prevent="saveItem">Apply</button>
            </div>
            <div v-for="(group, gid) in fieldset.childs" :key="gid" :class="`group-`+gid" class="card-body">
                <div v-if="group.title || group.description" class="heading-group mb-4">
                    <h5 v-if="group.title" class="astroid-heading-line"><span>{{ group.title }}</span></h5>
                    <p v-if="group.description" class="form-text">{{ group.description }}</p>
                </div>
                <div v-for="field in group.fields" :key="field.id" class="mb-4" v-show="checkShow(field)">
                    <div v-if="(field.input.type === `astroidradio` && field.input.role !== 'switch') || (['astroidpreloaders', 'astroidmedia', 'astroidcolor', 'astroidicon', 'astroidcalendar', 'astroidgradient', 'astroidspacing'].includes(field.input.type))" class="form-label fw-bold" v-html="field.label"></div>
                    <label v-else-if="field.input.type !== `astroidheading`" :for="field.input.id" class="form-label fw-bold" v-html="field.label"></label>
                    <div v-if="typeof field.type !== 'undefined' && field.type === `json`">
                        <Fields 
                            :field="field" 
                            :scope="params"
                            :constant="props.constant" 
                            />
                    </div>
                    <div v-else v-html="field.input"></div>
                    <p v-if="field.description !== ''" v-html="field.description" class="form-text"></p>
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