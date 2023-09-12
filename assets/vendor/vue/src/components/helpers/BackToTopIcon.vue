<script setup>
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  field: { type: Object, default: null },
  modelValue: { type: String, default: '' }
});
const iconList = [
  ['fas', 'fa-long-arrow-alt-up'],
  ['fas', 'fa-arrow-up'],
  ['fas', 'fa-arrow-circle-up'],
  ['fas', 'fa-arrow-alt-circle-up'],
  ['fas', 'fa-angle-double-up'],
  ['fas', 'fa-sort-up'],
  ['fas', 'fa-level-up-alt'],
  ['fas', 'fa-cloud-upload-alt'],
  ['fas', 'fa-chevron-up'],
  ['fas', 'fa-chevron-circle-up'],
  ['fas', 'fa-hand-point-up'],
  ['fas', 'fa-caret-square-up'],
]

function updateIcon(icon) {
  emit('update:modelValue', icon.join(' '));
}
</script>
<template>
    <div class="arrow-icon-picker">
      <div class="icon-display mb-4"><i class="fa-3x" :class="modelValue"></i></div>
      <div class="row g-2 g-lg-3">
        <div v-for="icon in iconList" class="col-auto">
          <a class="icon border rounded p-2 link-underline link-underline-opacity-0 link-underline-opacity-0-hover" @click.prevent="updateIcon(icon)" :class="{'active' : icon.join(' ') === modelValue}">
            <i :class="icon.join(' ')"></i>
          </a>
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