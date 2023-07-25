<script setup>
import Sidebar from './Sidebar.vue'
import Content from './Content.vue'
import { onMounted, ref } from 'vue';

const props = defineProps({
  config: { type: Object, default: null }
});

const pageIndex = ref(new Object());
onMounted(() => {
  props.config.astroid_content.forEach((fieldSet, idx) => {
    if (idx === 0) {
      pageIndex.value[fieldSet.name] = 'block';
    } else {
      pageIndex.value[fieldSet.name] = 'none';
    }
  });
})

function pageActive(pgIndex) {
  props.config.astroid_content.forEach(fieldSet => {
    pageIndex.value[fieldSet.name] = 'none';
  });
  pageIndex.value[pgIndex] = 'block';
}
</script>
<template>
  <div class="container-xxl bd-gutter mt-3 my-md-4 bd-layout">
    <Sidebar :config="props.config" @sidebar-active="pageActive" />
    <Content :config="props.config" :page-index="pageIndex" />
  </div>
</template>