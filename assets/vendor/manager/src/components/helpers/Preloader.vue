<script setup>
import { onMounted, ref } from 'vue';
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    field: { type: Object, default: null },
    modelValue: { type: String, default: '' },
});
const _showPreloaders = ref(false);
onMounted(()=>{
    const _preloaderContent = document.getElementById(props.field.input.id+'modal');
    if (_preloaderContent) {
        _preloaderContent.addEventListener('show.bs.modal', event => {
            _showPreloaders.value = true;
        })
        _preloaderContent.addEventListener('hide.bs.modal', event => {
            _showPreloaders.value = false;
        })
    }
})
const updatePreloader = (item) => {
    emit('update:modelValue', item);
    document.getElementById(props.field.input.id+'close').click();
}
</script>
<template>
    <div v-if="props.field.input.style === `fontawesome`" class="select-preloader" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`modal`">
        <i :class="props.modelValue" class="fa-3x"></i>
    </div>
    <div v-else class="select-preloader" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`modal`" v-html="props.field.input.preloader[props.modelValue].code"></div>
    <div class="modal fade" :id="props.field.input.id+`modal`" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Preloader Style</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" :id="props.field.input.id+'close'"></button>
                </div>
                <div class="modal-body">
                    <div v-if="_showPreloaders" class="row g-3">
                        <div class="col col-auto" v-for="(item, key) in props.field.input.preloader" :key="key">
                            <div class="preloader-item d-flex justify-content-center align-items-center border rounded" @click="updatePreloader(item.name)">
                                <div v-if="props.field.input.style === `fontawesome`">
                                    <i :class="item.name" class="fa-3x"></i>
                                </div>
                                <div v-else class="preloader-code" v-html="item.code"></div>
                            </div>
                        </div>
                    </div>
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