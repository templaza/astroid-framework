<script setup>
import { onMounted, onBeforeMount, computed, ref, inject } from 'vue';
import draggable from "vuedraggable";
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const constant = inject('constant', {});
onBeforeMount(() => {
    if (typeof props.modelValue !== 'object') {
        emit('update:modelValue', {});
    }
});
onMounted(() => {
    Object.keys(props.field.input.value).forEach(key => {
        if (typeof props.modelValue[key] === 'undefined') {
            props.modelValue[key] = props.field.input.value[key];
        }
    })
});
const fields = computed(() => {
    let fields = [];
    Object.keys(constant.dynamic_source_fields).forEach(key => {
        if (constant.dynamic_source_fields[key].filters.includes(props.modelValue['source'])) {
            Object.keys(constant.dynamic_source_fields[key].fields).forEach(field => {
                fields.push({
                    value: key+'.'+field,
                    label: constant.dynamic_source_fields[key].name + ' - ' + constant.dynamic_source_fields[key].fields[field]
                });
            });
        }
    })
    return fields;
})
const conditions = [
    {value: '!', label: 'Is empty'},
    {value: '!!', label: 'Is not empty'},
    {value: '=', label: 'Is equal to'},
    {value: '!=', label: 'Is not equal to'},
    {value: '~=', label: 'Contains'},
    {value: '!~=', label: 'Does not contain'},
    {value: '<', label: 'Less than'},
    {value: '>', label: 'Greater than'},
    {value: '^=', label: 'Starts with'},
    {value: '!^=', label: 'Does not start with'},
    {value: '$=', label: 'Ends with'},
    {value: '!$=', label: 'Does not end with'}
];
function addCondition() {
    const sec = Date.now() * 1000 + Math.random() * 1000;
    props.modelValue['conditions'].push({
        operator: 'AND',
        field : fields.value[0].value,
        condition : '!!',
        value : '',
        id: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000)
    });
}
function updateValue(condition) {
    if (['!', '!!'].includes(condition.condition)) {
        condition.value = '';
    }
}
</script>
<template>
    <label :for="props.field.input.id+`_source`" class="form-label">Source</label>
    <select :id="props.field.input.id+`_source`" :name="props.field.input.name+`[source]`" v-model="props.modelValue['source']" @change="" class="form-select">
        <option v-for="(option, key) in constant.dynamic_source" :value="key" :key="key">{{ option }}</option>
    </select>
    <p class="form-text">Select a content source to make its fields available for mapping. Choose between sources of the current page or query a custom source.</p>
    <div class="row" v-if="props.modelValue['source'] !== 'none'">
        <div class="col-6">
            <label :for="props.field.input.id+`_start`" class="form-label">Start</label>
            <input :id="props.field.input.id+`_start`" :name="props.field.input.name+`[start]`" v-model="props.modelValue['start']" type="number" min="0" step="1" class="form-control">
        </div>
        <div class="col-6">
            <label :for="props.field.input.id+`_quantity`" class="form-label">Quantity</label>
            <input :id="props.field.input.id+`_quantity`" :name="props.field.input.name+`[quantity]`" v-model="props.modelValue['quantity']" type="number" min="0" step="1" class="form-control">
        </div>
        <div class="col-12"><p class="form-text">Set the starting point and limit the number of articles.</p></div>
        <div class="col-6">
            <label :for="props.field.input.id+`_order`" class="form-label">Order</label>
            <select v-if="typeof props.modelValue['source'] !== 'undefined'"  :id="props.field.input.id+`_order`" :name="props.field.input.name+`[order]`" v-model="props.modelValue['order']" class="form-select">
                <option v-for="(option, key) in constant.dynamic_source_fields[props.modelValue['source']].order" :value="key" :key="key">{{ option }}</option>
            </select>
        </div>
        <div class="col-6">
            <label :for="props.field.input.id+`_order_dir`" class="form-label">Direction</label>
            <select :id="props.field.input.id+`_order_dir`" :name="props.field.input.name+`[order_dir]`" v-model="props.modelValue['order_dir']" class="form-select">
                <option value="DESC">Descending</option>
                <option value="ASC">Ascending</option>
            </select>
        </div>
        <div class="col-12 mt-4">
            <label class="form-label">Dynamic Conditions</label>
            <draggable
                v-model="props.modelValue['conditions']"
                class="dynamic-conditions row row-cols-1 g-4"
                handle=".item-move"
                ghost-class="ghost"
                item-key="id">
                <template #item="{element, index}">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <label class="input-group-text" :for="props.field.input.id+`_operator_`+index">Condition #{{index}}</label>
                            <select :id="props.field.input.id+`_operator_`+index" v-model="element.operator" class="form-select" :disabled="index === 0">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                            <span class="input-group-text">
                                <i class="item-move fa-solid fa-up-down me-3"></i>
                                <a href="#" class="link-danger" @click.prevent=""><i class="fas fa-trash-alt"></i></a>
                            </span>
                        </div>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label :for="props.field.input.id+`_field_`+index" class="form-label">Field</label>
                                    <select :id="props.field.input.id+`_field_`+index" v-model="element.field" class="form-select">
                                        <option v-for="(field, key) in fields" :value="field.value" :key="key">{{ field.label }}</option>
                                    </select>
                                    <p class="form-text">Set a condition to display the element or its item depending on the content of a field.</p>
                                </div>
                                <div class="col-6">
                                    <label :for="props.field.input.id+`_condition_`+index" class="form-label">Condition</label>
                                    <select :id="props.field.input.id+`_condition_`+index" v-model="element.condition" class="form-select" @change="updateValue(element)">
                                        <option v-for="(cond, key) in conditions" :value="cond.value" :key="key">{{ cond.label }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label :for="props.field.input.id+`_value_`+index" class="form-label">Value</label>
                                    <input :id="props.field.input.id+`_value_`+index" v-model="element.value" class="form-control" :disabled="['!', '!!'].includes(element.condition)">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
            <button class="btn btn-as-primary btn-as mt-4" @click.prevent="addCondition">Add Condition</button>
        </div>
    </div>
</template>