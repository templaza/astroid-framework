<script setup>
import {onMounted, onBeforeMount, computed, inject, ref, watch} from 'vue';
import draggable from "vuedraggable";
import { MultiListSelect } from "vue-search-select"
import { ModelListSelect } from "vue-search-select"
const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const constant = inject('constant', {});
const language = inject('language', []);
const element_id = ref(props.field.input.id);
onBeforeMount(() => {
    if (typeof props.modelValue !== 'object') {
        emit('update:modelValue', {});
    }
    element_id.value += '-'+(Date.now() * 1000 + Math.random() * 1000).toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
});
onMounted(() => {
    Object.keys(props.field.input.value).forEach(key => {
        if (typeof props.modelValue[key] === 'undefined') {
            props.modelValue[key] = props.field.input.value[key];
        }
    })
    if (typeof props.modelValue['dynamic_content'] !== 'undefined' && Array.isArray(props.modelValue['dynamic_content'])) {
        props.modelValue['dynamic_content'] = {};
    }
    let options = JSON.parse(props.modelValue['options']);

    if (typeof options['content_catids'] === 'undefined') {
        options['content_catids'] = [];
    }
    selectedCategories.value = options['content_catids'];

    if (typeof options['content_include_subcategories'] === 'undefined') {
        options['content_include_subcategories'] = 'exclude';
    }
    content_include_subcategories.value = options['content_include_subcategories'];

    if (typeof options['category_parent'] === 'undefined') {
        options['category_parent'] = '';
    }
    selectedCategory.value = options['category_parent'];
});
const selectedCategories = ref([]);
const content_include_subcategories = ref('exclude');
const selectedCategory = ref('');
const dynamicFields = computed(() => {
    let fields = [];
    Object.keys(constant.dynamic_source_fields).forEach(key => {
        if (constant.dynamic_source_fields[key].filters.includes(props.modelValue['source'])) {
            fields.push({
                type: 'heading',
                label: constant.dynamic_source_fields[key].name
            })
            Object.keys(constant.dynamic_source_fields[key].fields).forEach(field => {
                fields.push({
                    type: 'field',
                    value: field,
                    label: constant.dynamic_source_fields[key].fields[field],
                    category: {
                        value: constant.dynamic_source_fields[key].value,
                        name: constant.dynamic_source_fields[key].name
                    }
                });
            });
        }
    })
    return fields;
})
function changeSource() {
    props.modelValue['conditions'] = [];
    props.modelValue['dynamic_content'] = {};
    props.modelValue['order'] = '';
}
function selectDynamicField(element, field) {
    element.field = field;
}
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
        field : dynamicFields.value[1],
        condition : '!!',
        value : '',
        id: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000)
    });
}
function removeCondition(index) {
    props.modelValue['conditions'].splice(index, 1);
}
function updateValue(condition) {
    if (['!', '!!'].includes(condition.condition)) {
        condition.value = '';
    }
}
function onSelectCategories(items, lastSelectItem) {
    selectedCategories.value = items;
    let options = JSON.parse(props.modelValue['options']);
    options['content_catids'] = selectedCategories.value;
    props.modelValue['options'] = JSON.stringify(options);
}
function onSelectIncludeSubcategories() {
    let options = JSON.parse(props.modelValue['options']);
    options['content_include_subcategories'] = content_include_subcategories.value;
    props.modelValue['options'] = JSON.stringify(options);
}
watch(selectedCategory, (newText) => {
    let options = JSON.parse(props.modelValue['options']);
    options['category_parent'] = newText;
    props.modelValue['options'] = JSON.stringify(options);
})
</script>
<template>
    <div v-if="!constant.is_pro" class="astroid-get-pro card alert alert-warning">
        <h6 class="card-title">{{ props.field.input.pro_msg.title }}</h6>
        <div class="card-text form-text" v-html="props.field.input.pro_msg.desc"></div>
    </div>
    <label :for="element_id+`_source`" class="form-label">{{language.ASTROID_SOURCE_LABEL}}</label>
    <select :id="element_id+`_source`" :name="props.field.input.name+`[source]`" v-model="props.modelValue['source']" @change="changeSource" class="form-select" :disabled="!constant.is_pro">
        <option v-for="(option, key) in constant.dynamic_source" :value="key" :key="key">{{ option }}</option>
    </select>
    <p class="form-text">{{language.ASTROID_SOURCE_DESC}}</p>
    <div v-if="props.modelValue['source'] === `content`">
        <label :for="element_id+`_option_categories`" class="form-label">{{ language.ASTROID_DYNAMIC_CONTENT_FILTER_BY_CATEGORIES_LABEL }}</label>
        <multi-list-select
            :list="constant.dynamic_source_options.categories"
            option-value="value"
            option-text="label"
            :id="element_id+`_option_categories`"
            :selected-items="selectedCategories"
            :isDisabled="!constant.is_pro"
            placeholder="Filter by Categories"
            @select="onSelectCategories"
        >
        </multi-list-select>
        <select class="form-select mt-2" v-model="content_include_subcategories" @change="onSelectIncludeSubcategories" :disabled="!constant.is_pro">
            <option value="exclude">{{ language.ASTROID_DYNAMIC_CONTENT_FILTER_EXCLUDE_CHILD_CATEGORIES }}</option>
            <option value="include">{{ language.ASTROID_DYNAMIC_CONTENT_FILTER_INCLUDE_CHILD_CATEGORIES }}</option>
        </select>
        <p class="form-text">{{ language.ASTROID_DYNAMIC_CONTENT_FILTER_BY_CATEGORIES_DESC }}</p>
    </div>
    <div v-if="props.modelValue['source'] === `categories`">
        <label :for="element_id+`_option_category`" class="form-label">{{ language.ASTROID_DYNAMIC_CONTENT_PARENT_CATEGORY_LABEL }}</label>
        <model-list-select
            :list="constant.dynamic_source_options.parent_category"
            v-model="selectedCategory"
            option-value="value"
            option-text="label"
            :id="element_id+`_option_category`"
            :isDisabled="!constant.is_pro"
            placeholder="Parent Category"
        >
        </model-list-select>
        <p class="form-text">{{ language.ASTROID_DYNAMIC_CONTENT_PARENT_CATEGORY_DESC }}</p>
    </div>
    <div class="row" v-if="props.modelValue['source'] !== 'none'">
        <div class="col-6">
            <label :for="element_id+`_start`" class="form-label">{{ language.ASTROID_START_LABEL }}</label>
            <input :id="element_id+`_start`" :name="props.field.input.name+`[start]`" v-model="props.modelValue['start']" type="number" min="1" step="1" class="form-control" :disabled="!constant.is_pro">
        </div>
        <div class="col-6">
            <label :for="element_id+`_quantity`" class="form-label">{{ language.ASTROID_QUANTITY_LABEL }}</label>
            <input :id="element_id+`_quantity`" :name="props.field.input.name+`[quantity]`" v-model="props.modelValue['quantity']" type="number" min="1" step="1" class="form-control" :disabled="!constant.is_pro">
        </div>
        <div class="col-12"><p class="form-text">{{ language.ASTROID_QUANTITY_DESC }}</p></div>
        <div class="col-6">
            <label :for="element_id+`_order`" class="form-label">{{ language.ASTROID_ORDER_BY_LABEL }}</label>
            <select v-if="typeof props.modelValue['source'] !== 'undefined'"  :id="element_id+`_order`" :name="props.field.input.name+`[order]`" v-model="props.modelValue['order']" class="form-select" :disabled="!constant.is_pro">
                <option value="">{{ language.ASTROID_SELECT_ORDER_FIELD_LABEL }}</option>
                <option v-for="(option, key) in constant.dynamic_source_fields[props.modelValue['source']].order" :value="key" :key="key">{{ option }}</option>
            </select>
        </div>
        <div class="col-6">
            <label :for="element_id+`_order_dir`" class="form-label">{{ language.ASTROID_DIRECTION_LABEL }}</label>
            <select :id="element_id+`_order_dir`" :name="props.field.input.name+`[order_dir]`" v-model="props.modelValue['order_dir']" class="form-select" :disabled="props.modelValue['order'] === `` || !constant.is_pro">
                <option value="DESC">Descending</option>
                <option value="ASC">Ascending</option>
            </select>
        </div>
        <div class="col-12 mt-4">
            <label class="form-label">{{ language.ASTROID_DYNAMIC_CONDITIONS_LABEL }}</label>
            <draggable
                v-model="props.modelValue['conditions']"
                class="dynamic-conditions row row-cols-1 g-4"
                handle=".item-move"
                ghost-class="ghost"
                item-key="id">
                <template #item="{element, index}">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <label class="input-group-text" :for="element_id+`_operator_`+index">{{ language.ASTROID_CONDITION_LABEL }} #{{index}}</label>
                            <select :id="element_id+`_operator_`+index" v-model="element.operator" class="form-select" :disabled="index === 0">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                            <span class="input-group-text">
                                <i class="item-move fa-solid fa-up-down me-3"></i>
                                <a href="#" class="link-danger" @click.prevent="removeCondition(index)"><i class="fas fa-trash-alt"></i></a>
                            </span>
                        </div>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label :for="element_id+`_field_`+index" class="form-label">{{ language.ASTROID_FIELD_LABEL }}</label>
                                    <div class="dynamic-select">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-database me-2"></i>{{ element.field.category.name + ' - ' + element.field.label }}</button>
                                        <ul class="dropdown-menu">
                                            <li v-for="(field, key) in dynamicFields" :key="key">
                                                <hr v-if="field.type === `heading` && key !== 0" class="dropdown-divider">
                                                <h6 v-if="field.type === `heading`" class="dropdown-header">{{ field.label }}</h6>
                                                <a v-else class="dropdown-item" href="#" @click.prevent="selectDynamicField(element, field)">{{ field.label }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <p class="form-text">{{ language.ASTROID_CONDITION_DESC }}</p>
                                </div>
                                <div class="col-6">
                                    <label :for="element_id+`_condition_`+index" class="form-label">{{ language.ASTROID_CONDITION_LABEL }}</label>
                                    <select :id="element_id+`_condition_`+index" v-model="element.condition" class="form-select" @change="updateValue(element)">
                                        <option v-for="(cond, key) in conditions" :value="cond.value" :key="key">{{ cond.label }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label :for="element_id+`_value_`+index" class="form-label">{{ language.ASTROID_VALUE_LABEL }}</label>
                                    <input :id="element_id+`_value_`+index" v-model="element.value" class="form-control" :disabled="['!', '!!'].includes(element.condition)">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
            <button class="btn btn-as-primary btn-as mt-4" @click.prevent="addCondition" :disabled="!constant.is_pro">{{ language.ASTROID_ADD_CONDITION_LABEL }}</button>
        </div>
    </div>
</template>