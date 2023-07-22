<script setup>
import Sidebar from './Sidebar.vue'
import Content from './Content.vue'
const props = defineProps({
  config: { type: Object, default: null }
});
</script>
<template>
  <div class="container-xxl bd-gutter mt-3 my-md-4 bd-layout">
    <Sidebar :config="props.config" />
    <Content />
  </div>
</template>