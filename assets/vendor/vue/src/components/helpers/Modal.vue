<script setup>
import { onBeforeMount, ref } from 'vue';
import Fields from './Fields.vue';
const emit = defineEmits(['update:closeElement', 'update:saveElement']);
const props = defineProps(['element', 'form', 'constant']);
const params = ref(new Object());
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
function saveModal(){
    let tmp = [];
    Object.keys(params.value).forEach(key => {
        tmp.push({
            'name': key,
            'value': params.value[key]
        });
    });
    emit('update:saveElement', tmp);
    emit('update:closeElement', props.element.type + '-' + props.element.id)
}
</script>
<template>
    <div class="astroid-modal modal d-block" :id="props.element.type+`-`+props.element.id" tabindex="-1" @click.self="emit('update:closeElement', props.element.type + '-' + props.element.id)">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="position-absolute top-0 end-0 p-3">
                    <button type="button" class="btn-close inverted" aria-label="Close" @click="emit('update:closeElement', props.element.type + '-' + props.element.id)"></button>
                </div>
                <ul class="nav nav-tabs" :id="`modal-tab-`+props.element.id" role="tablist">
                    <li v-for="(fieldset, idx) in form.content" :key="fieldset.name" class="nav-item" role="presentation">
                        <button class="nav-link" :class="{'active' : idx === 0}" :id="fieldset.name+`-tab-`+props.element.id" data-bs-toggle="tab" :data-bs-target="`#`+fieldset.name+`-tab-pane-`+props.element.id" type="button" role="tab" aria-selected="true">{{ fieldset.label }}</button>
                    </li>
                </ul>
                <div class="tab-content modal-body" :id="`modal-tab-content-`+props.element.id">
                    <div v-for="(fieldset, idx) in form.content" :key="fieldset.name" class="tab-pane fade" :class="{'show active' : idx === 0}" :id="fieldset.name+`-tab-pane-`+props.element.id" role="tabpanel" :aria-labelledby="fieldset.name+`-tab`" tabindex="0">
                        <div v-for="(group, gid) in fieldset.childs" :key="gid" :class="`group-`+gid">
                            <div v-for="(field, fid) in group.fields" :key="field.id" :class="(fid !== 0 && field.input.type !== 'astroidhidden' && field.input.type !== 'hidden' ? 'mt-3 pt-3 border-top': '')" v-show="checkShow(field)">
                                <div class="row">
                                    <div v-if="field.label || field.description" class="col-lg-5">
                                        <label :for="field.input.id" class="form-label" v-html="field.label"></label>
                                        <p v-if="field.description !== ''" v-html="field.description" class="form-text"></p>
                                    </div>
                                    <div :class="{
                                        'col-lg-7' : (field.label || field.description),
                                        'col-12': !(field.label || field.description)
                                    }">
                                        <div v-if="typeof field.type !== 'undefined' && field.type === `json`">
                                            <Fields 
                                                :field="field" 
                                                :scope="params"
                                                :constant="props.constant" 
                                                />
                                        </div>
                                        <div v-else v-html="field.input"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-as btn-as-light" @click="emit('update:closeElement', props.element.type + '-' + props.element.id)">Close</button>
                    <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click="saveModal">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>