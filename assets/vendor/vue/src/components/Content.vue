<script setup>
const props = defineProps({
  config: { type: Object, default: null },
  pageIndex: { type: Object, default: null }
});
</script>
<template>
  <TransitionGroup name="slide-fade" class="bd-main order-1" tag="main">
    <div class="bd-page ps-lg-2" v-for="fieldSet in props.config.astroid_content" :key="fieldSet.name" :style="{display:props.pageIndex[fieldSet.name]}">
      <div class="bd-content" v-if="Object.keys(fieldSet.childs).length > 0" v-for="(group, index) in fieldSet.childs" :key="index">
        <h3 v-if="group.title !== ''">{{ group.title }}</h3>
        <p v-if="group.description !== ''">{{ group.description }}</p>
        <div v-if="group.fields.length > 0" class="bd-group-content">
          <div :class="(idx !== 0 ? 'mt-3 pt-3 border-top': '')" v-for="(field, idx) in group.fields" :key="field.id">
            <fieldset>
              <legend>{{ field.label }}</legend>
              <p v-html="field.description"></p>
              <div v-html="field.input"></div>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
  </TransitionGroup>
</template>
<style scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>