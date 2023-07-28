<script setup>
import { onBeforeMount, ref } from 'vue';
import Fields from './helpers/Fields.vue'
const props = defineProps({
  config: { type: Object, default: null },
  pageIndex: { type: Object, default: null }
});

const $scope = ref(new Object());

onBeforeMount(() => {
  props.config.astroid_content.forEach((fieldSet, idx) => {
    Object.keys(fieldSet.childs).forEach(key => {
      fieldSet.childs[key].fields.forEach(field => {
        $scope.value[field.name] = field.value;
      });
    });
  });
})

function checkShow(field) {
  if (field.ngShow !== '' && field.ngShow.match(/\[.+?\]/)) {
    const expression = field.ngShow.replace(/\[(.+?)\]/g, "$scope.value\['$1'\]");
    try {
      if (!(new Function('return ' + expression)())) {
        return false;
      } else {
        return true;
      }
    } catch (error) {
      console.log('Error at: '+expression);
    }
  }
  return true;
}
</script>
<template>
  <main class="as-main order-1">
    <div class="as-page ps-lg-2" :class="props.pageIndex[fieldSet.name]" v-for="fieldSet in props.config.astroid_content" :key="fieldSet.name">
      <div :id="`astroid-page-`+index" class="as-content" v-if="Object.keys(fieldSet.childs).length > 0" v-for="(group, index) in fieldSet.childs" :key="index">
        <h3 v-if="group.title !== ''">{{ group.title }}</h3>
        <p v-if="group.description !== ''">{{ group.description }}</p>
        <div v-if="group.fields.length > 0" class="as-group-content">
          <div :class="(idx !== 0 ? 'mt-3 pt-3 border-top': '')" v-for="(field, idx) in group.fields" :key="field.id" v-show="checkShow(field)">
            <fieldset>
              <legend>{{ field.label }}</legend>
              <p v-if="field.description !== ''" v-html="field.description"></p>
              <div v-if="field.type === `string`" v-html="field.input"></div>
              <div v-else-if="field.type === `json`">
                <Fields :field="field" :scope="$scope" />
              </div>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
