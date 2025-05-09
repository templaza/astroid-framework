<script setup>
import { onBeforeMount, onMounted, onBeforeUnmount, ref } from 'vue';
import Fields from './Fields.vue';
const emit = defineEmits(['update:closeElement', 'update:saveElement']);
const props = defineProps(['element', 'form', 'colorMode']);
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

const handleEscKey = (event) => {
    if (event.key === 'Escape') {
        emit('update:closeElement');
    }
};
onMounted(() => {
    document.addEventListener('keydown', handleEscKey);
});
onBeforeUnmount(() => {
    document.removeEventListener('keydown', handleEscKey);
});
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
function checkShowGroup(fields) {
    let hasField = false;
    if (fields.length) {
        fields.forEach(field => {
            if (typeof field.ngShow === 'string' && checkShow(field)) {
                hasField = true;
                return hasField;
            }
        });
    }
    return hasField;
}
const actSave = ref(false);
function storeParams() {
    const tmp = Object.keys(params.value).map(key => ({
        name: key,
        value: typeof params.value[key] === 'object' && !Array.isArray(params.value[key]) && params.value[key] !== null
            ? JSON.parse(JSON.stringify(params.value[key]))
            : params.value[key]
    }));
    emit('update:saveElement', tmp);
    emit('update:closeElement');
}
function saveModal() {
    if (subFormOpen.value.value) {
        const message = `${subFormOpen.value.name} has not been saved. Are you sure to save the subform?`;
        if (confirm(message)) {
            actSave.value = true;
            setTimeout(() => {
                storeParams();
                actSave.value = false;
            }, 1000);
        } else {
            storeParams();
        }
    } else {
        storeParams();
    }
}
function updateSubForm(value) {
    subFormOpen.value = value;
}
function sidebarClick(id) {
    document.getElementById(id).scrollIntoView();
}
const pro_badge = '<span class="badge text-bg-danger ms-2">PRO</span>';
</script>
<template>
    <div class="astroid-modal modal d-block" :id="props.element.type+`-`+props.element.id" tabindex="-1" aria-modal="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="position-absolute top-0 end-0 p-3">
                    <button type="button" class="btn-close inverted" aria-label="Close" @click="emit('update:closeElement')"></button>
                </div>
                <ul class="astroid-modal-tabs nav nav-tabs" :id="`modal-tab-`+props.element.id" role="tablist">
                    <li v-for="(fieldset, idx) in form.content" :key="fieldset.name" class="nav-item" role="presentation">
                        <button class="nav-link" :class="{'active' : idx === 0}" :id="fieldset.name+`-tab-`+props.element.id" data-bs-toggle="tab" :data-bs-target="`#`+fieldset.name+`-tab-pane-`+props.element.id" type="button" role="tab" aria-selected="true">{{ fieldset.label }}</button>
                    </li>
                </ul>
                <div class="tab-content modal-body p-4" :id="`modal-tab-content-`+props.element.id">
                    <div v-for="(fieldset, idx) in form.content" :key="fieldset.name" class="tab-pane fade" :class="{'show active' : idx === 0}" :id="fieldset.name+`-tab-pane-`+props.element.id" role="tabpanel" :aria-labelledby="fieldset.name+`-tab`" tabindex="0">
                        <nav v-if="Object.keys(fieldset.childs).length > 3" class="nav nav-pills d-none d-xl-block flex-column position-fixed overflow-hidden top-50 start-0 translate-middle-y rounded-end-4">
                            <a v-for="(group, gid) in fieldset.childs" :key="gid" class="nav-link bg-body-tertiary rounded-0" v-show="checkShowGroup(group.fields)" @click.prevent="sidebarClick(`group-`+gid+`-`+props.element.id)" href="#" :class="{'d-none' : gid === 'none' || group.title === ''}">{{ group.title }}</a>
                        </nav>
                        <div v-for="(group, gid) in fieldset.childs" :key="gid" :class="`group-`+gid" :id="`group-`+gid+`-`+props.element.id" v-show="checkShowGroup(group.fields)">
                            <div v-if="group.title || group.description" class="heading-group mb-4">
                                <h5 v-if="group.title" class="astroid-heading-line"><span>{{ group.title }}</span></h5>
                                <p v-if="group.description" class="form-text">{{ group.description }}</p>
                            </div>
                            <div v-for="field in group.fields" :key="field.id" class="mb-4" v-show="checkShow(field)">
                                <div v-if="(field.input.type === `astroidradio` && field.input.role !== 'switch') || (['astroidpreloaders', 'astroidmedia', 'astroidcolor', 'astroidicon', 'astroidcalendar', 'astroidgradient', 'astroidspacing', 'astroidgetpro'].includes(field.input.type))" class="form-label fw-bold" v-html="(field.label + (field.input.type === `astroidgetpro` ? pro_badge : ``))"></div>
                                <label v-else-if="field.input.type !== `astroidheading` && field.label" :for="field.input.id" class="form-label fw-bold" v-html="field.label"></label>
                                <div v-if="typeof field.type !== 'undefined' && field.type === `json`" class="position-relative">
                                    <Fields 
                                        :field="field" 
                                        :scope="params"
                                        :colorMode="props.colorMode"
                                        :actSave="actSave"
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