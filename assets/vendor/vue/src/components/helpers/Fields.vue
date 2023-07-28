<script setup>
const props = defineProps({
  field: { type: Object, default: null },
  scope: { type: Object, default: null }
});
</script>
<template>
    <select v-if="props.field.input.type === `astroidlist`" v-model="props.scope[props.field.name]" :id="props.field.input.id" :name="props.field.input.name" class="form-select" :aria-label="props.field.label">
        <option v-for="option in props.field.input.options" :key="option.value" :value="option.value">{{ option.text }}</option>
    </select>
    <div v-if="props.field.input.type === `astroidradio`">
        <div v-if="props.field.input.role === `default`" class="btn-group" role="group" :aria-label="props.field.label">
            <span v-for="(option, idx) in props.field.input.options" :key="idx">
                <input type="radio" class="btn-check" v-model="props.scope[props.field.name]" :name="props.field.input.name" :id="props.field.input.id+idx" :value="option.value" autocomplete="off">
                <label class="btn btn-outline-primary" :for="props.field.input.id+idx">{{ option.text }}</label>
            </span>
        </div>
        <div v-if="props.field.input.role === `switch`" class="form-check form-switch">
            <input v-model="props.scope[props.field.name]" :name="props.field.input.name" class="form-check-input" type="checkbox" role="switch" :id="props.field.input.id">
        </div>
    </div>
</template>