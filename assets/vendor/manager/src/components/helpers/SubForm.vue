<script setup>
import { onBeforeMount, ref, onUpdated } from "vue";
import draggable from "vuedraggable";
import Fields from './Fields.vue';

const emit = defineEmits(['update:modelValue', 'update:subFormState']);
const props = defineProps(['modelValue', 'field', 'actSave', 'colorMode']);
const items = ref([]);
const edit  = ref(false);
const params = ref(new Object());
const itemLabel = ref('');
const currentIdx = ref(-1);
const element_id = ref(props.field.input.id);
onBeforeMount(()=>{
    element_id.value += '-'+(Date.now() * 1000 + Math.random() * 1000).toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
    if (props.modelValue) {
        items.value = JSON.parse(props.modelValue);
    }
    itemLabel.value = props.field.input.form.index !== '' ? props.field.input.form.index : 'title';
})

onUpdated(() => {
    if (props.actSave && edit.value) {
        saveItem();
    }
})

function listUpdated() {
    emit('update:subFormState', {name: props.field.label, value: false});
    emit('update:modelValue', JSON.stringify(items.value));
}

function checkShow(field) {
    if (field.ngShow !== '' && field.ngShow.match(/\[\S+?\]/)) {
        const expression = field.ngShow.replace(/\[(\S+?)\]/g, "params.value\['$1'\]");
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
    props.field.input.form.info.params.forEach(param => {
        params.value[param.name] = param.value;
    });
    if (index !== -1) {
        items.value[index].params.forEach(param => {
            params.value[param.name] = param.value;
        });
    }
    currentIdx.value = index;
    edit.value = true;
    emit('update:subFormState', {name: props.field.label, value: true});
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
    } else {
        items.value[currentIdx.value].params = tmp;
    }
    listUpdated();
    params.value = {};
    edit.value = false;
}

function duplicateItem(element, index) {
    let id = Date.now() * 1000 + Math.random() * 1000;
    id = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
    let tmp = {
        id: id,
        params: element.params
    };
    items.value.splice(index+1, 0, tmp);
    listUpdated();
}

function deleteItem(index) {
    if (confirm('Are you sure?')) {
        items.value.splice(index, 1);
        listUpdated();
    }
}
</script>
<template>
    <div v-if="!edit">
        <div class="add-item d-flex justify-content-between align-items-center mb-3 p-3 rounded">
            <h6 class="mb-0">Items</h6>
            <button class="btn btn-as btn-sm btn-primary btn-as-primary" @click.prevent="editItem(-1)">Add Item</button>
        </div>
        <draggable 
            v-model="items" 
            tag="div"
            @change="listUpdated"
            class="astroid-subform row row-cols-1 g-2"
            ghost-class="subform-ghost"
            handle=".item-move"
            item-key="id">
            <template #item="{ element, index }">
                <div>
                    <div class="card card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><i class="item-move fa-solid fa-up-down me-3"></i>{{ element.params.find((param) => param.name === itemLabel) ? (element.params.find((param) => param.name === itemLabel) && element.params.find((param) => param.name === itemLabel).value !== '' ? element.params.find((param) => param.name === itemLabel).value : 'Item ' + (index+1)) : 'Item ' + (index+1) }}</div>
                            <div class="toolbar">
                                <a href="#" title="Edit" class="me-2" @click.prevent="editItem(index)"><i class="fa-solid fa-gear"></i></a>
                                <a href="#" title="Duplicate" class="me-2" @click.prevent="duplicateItem(element,index)"><i class="fa-solid fa-copy"></i></a>
                                <a href="#" title="Delete" @click.prevent="deleteItem(index)"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </draggable>
    </div>
    <div v-else class="position-relative">
        <div class="position-absolute top-0 end-0">
            <button class="as-subform-apply btn btn-sm btn-link" @click.prevent="saveItem"><i class="fa-solid fa-arrow-right me-1"></i>Apply Changes</button>
        </div>
        <ul class="nav nav-tabs" :id="`modal-tab-`+element_id" role="tablist">
            <li v-for="(fieldset, idx) in props.field.input.form.content" :key="fieldset.name" class="nav-item" role="presentation">
                <button class="nav-link" :class="{'active' : idx === 0}" :id="fieldset.name+`-tab-`+element_id" data-bs-toggle="tab" :data-bs-target="`#`+fieldset.name+`-tab-pane-`+element_id" type="button" role="tab" aria-selected="true">{{ fieldset.label }}</button>
            </li>
        </ul>
        <div class="tab-content modal-body border border-top-0 p-0" :id="`modal-tab-content-`+element_id">
            <div v-for="(fieldset, idx) in props.field.input.form.content" :key="fieldset.name" class="tab-pane fade p-4" :class="{'show active' : idx === 0}" :id="fieldset.name+`-tab-pane-`+element_id" role="tabpanel" :aria-labelledby="fieldset.name+`-tab`" tabindex="0">
                <div v-for="(group, gid) in fieldset.childs" :key="gid" :class="`group-`+gid" class="card-body">
                    <div v-if="group.title || group.description" class="heading-group mb-4">
                        <h5 v-if="group.title" class="astroid-heading-line"><span>{{ group.title }}</span></h5>
                        <p v-if="group.description" class="form-text">{{ group.description }}</p>
                    </div>
                    <div v-for="field in group.fields" :key="field.id" class="mb-4" v-show="checkShow(field)">
                        <div v-if="(field.input.type === `astroidradio` && field.input.role !== 'switch') || (['astroidpreloaders', 'astroidmedia', 'astroidcolor', 'astroidicon', 'astroidcalendar', 'astroidgradient', 'astroidspacing'].includes(field.input.type))" class="form-label fw-bold" v-html="field.label"></div>
                        <label v-else-if="field.input.type !== `astroidheading`" :for="field.input.id" class="form-label fw-bold" v-html="field.label"></label>
                        <div v-if="typeof field.type !== 'undefined' && field.type === `json`" class="position-relative">
                            <Fields
                                :field="field"
                                :scope="params"
                                :colorMode="props.colorMode"
                            />
                        </div>
                        <div v-else v-html="field.input"></div>
                        <p v-if="field.description !== ''" v-html="field.description" class="form-text"></p>
                    </div>
                </div>
            </div>
            <div class="border-top px-4 py-3 d-flex justify-content-between align-items-center">
                <div class="opacity-50">Do you want to save these changes?</div>
                <button class="as-subform-apply btn btn-as btn-sm btn-primary btn-as-primary" @click.prevent="saveItem">Apply Changes</button>
            </div>
        </div>
    </div>
    <input
        :id="element_id"
        :name="props.field.input.name"
        :value="modelValue"
        type="hidden"
    />
</template>