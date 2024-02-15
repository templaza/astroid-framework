<script setup>
import { onBeforeMount, ref } from 'vue';
import Fields from './Fields.vue';
const emit = defineEmits(['update:closeElement', 'update:saveElement']);
const props = defineProps(['element', 'form', 'constant']);
const params = ref(new Object());
const subFormOpen = ref({name: '', value: false});
onBeforeMount(()=>{
    props.form.info.params.forEach(param => {
        params.value[param.name] = param.value;
    });
    if (typeof props.element.params !== 'undefined') {
        props.element.params.forEach(el => {
            params.value[el.name] = el.value;
        });
    }
})
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
function saveModal(){
    let allowSave = true;
    if (subFormOpen.value.value) {
        if (!confirm(subFormOpen.value.name + ' has not been saved. Are you sure to skip the subform?')) {
            allowSave = false;
        }
    }
    if (allowSave) {
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
        emit('update:saveElement', tmp);
        emit('update:closeElement')
    }
}
function updateSubForm(value) {
    subFormOpen.value = value;
}
</script>
<template>
    <div class="astroid-modal modal d-block" :id="props.element.type+`-`+props.element.id" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="position-absolute top-0 end-0 p-3">
                    <button type="button" class="btn-close inverted" aria-label="Close" @click="emit('update:closeElement')"></button>
                </div>
                <ul class="nav nav-tabs" :id="`modal-tab-`+props.element.id" role="tablist">
                    <li v-for="(fieldset, idx) in form.content" :key="fieldset.name" class="nav-item" role="presentation">
                        <button class="nav-link" :class="{'active' : idx === 0}" :id="fieldset.name+`-tab-`+props.element.id" data-bs-toggle="tab" :data-bs-target="`#`+fieldset.name+`-tab-pane-`+props.element.id" type="button" role="tab" aria-selected="true">{{ fieldset.label }}</button>
                    </li>
                </ul>
                <div class="tab-content modal-body" :id="`modal-tab-content-`+props.element.id">
                    <div v-for="(fieldset, idx) in form.content" :key="fieldset.name" class="tab-pane fade" :class="{'show active' : idx === 0}" :id="fieldset.name+`-tab-pane-`+props.element.id" role="tabpanel" :aria-labelledby="fieldset.name+`-tab`" tabindex="0">
                        <div v-for="(group, gid) in fieldset.childs" :key="gid" :class="`group-`+gid">
                            <div v-if="group.title || group.description" class="heading-group mb-4">
                                <h5 v-if="group.title" class="astroid-heading-line"><span>{{ group.title }}</span></h5>
                                <p v-if="group.description" class="form-text">{{ group.description }}</p>
                            </div>
                            <div v-for="field in group.fields" :key="field.id" class="mb-4" v-show="checkShow(field)">
                                <div v-if="(field.input.type === `astroidradio` && field.input.role !== 'switch') || (['astroidpreloaders', 'astroidmedia', 'astroidcolor', 'astroidicon', 'astroidcalendar', 'astroidgradient', 'astroidspacing'].includes(field.input.type))" class="form-label fw-bold" v-html="field.label"></div>
                                <label v-else-if="field.input.type !== `astroidheading` && field.label" :for="field.input.id" class="form-label fw-bold" v-html="field.label"></label>
                                <div v-if="typeof field.type !== 'undefined' && field.type === `json`">
                                    <Fields 
                                        :field="field" 
                                        :scope="params"
                                        @update:subFormState="updateSubForm"
                                        />
                                </div>
                                <div v-else v-html="field.input"></div>
                                <p v-if="field.description !== '' && field.input.type !== `astroidheading`" v-html="field.description" class="form-text"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-as btn-as-light" @click="emit('update:closeElement')">Close</button>
                    <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click="saveModal">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>