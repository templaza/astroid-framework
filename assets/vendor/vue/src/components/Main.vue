<script setup>
import { onBeforeMount, ref } from 'vue';
import Fields from './helpers/Fields.vue'
const props = defineProps({
  config: { type: Object, default: null },
  pageIndex: { type: Object, default: null }
});

const $scope = ref(new Object());
const astroidcontentlayouts = ref(new Object());
let action_link = '';

onBeforeMount(() => {
  props.config.astroid_content.forEach((fieldSet, idx) => {
    Object.keys(fieldSet.childs).forEach(key => {
      fieldSet.childs[key].fields.forEach(field => {
        $scope.value[field.name] = field.value;
      });
    });
  });
  action_link = props.config.astroid_lib.astroid_action.replace(/\&amp\;/g, '&')
})

function checkShow(field) {
  if (field.ngShow !== '' && field.ngShow.match(/\[.+?\]/)) {
    const expression = field.ngShow.replace(/\[(.+?)\]/g, "$scope.value\['$1'\]");
    try {
      return new Function('$scope', 'return ' + expression)($scope);
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

function updateContentLayout(index, value) {
  if (typeof astroidcontentlayouts.value[index] === 'undefined') {
    astroidcontentlayouts.value[index] = {
      'astroid_content_layout' : '',
      'module_position' : '',
      'position' : 'after'
    }
  }
  Object.keys(value).forEach(key => {
    astroidcontentlayouts.value[index][key] = value[key];
  })
  let tmp = [];
  Object.keys(astroidcontentlayouts.value).forEach(key => {
    tmp.push(astroidcontentlayouts.value[key]['astroid_content_layout']+':'+astroidcontentlayouts.value[key]['module_position']+':'+astroidcontentlayouts.value[key]['position']);
  })
  $scope.value['astroidcontentlayouts'] = tmp.join(',');
}
</script>
<template>
  <main class="as-main order-1">
    <form method="POST" :action="action_link" id="astroid-form">
      <input type="hidden" id="astroid-admin-token" :name="props.config.astroid_lib.astroid_admin_token" value="1" />
      <div class="as-page ps-lg-2" :class="props.pageIndex[fieldSet.name]" v-for="fieldSet in props.config.astroid_content" :key="fieldSet.name">
        <div :id="`astroid-page-`+index" class="as-content" v-if="Object.keys(fieldSet.childs).length > 0" v-for="(group, index) in fieldSet.childs" :key="index" v-show="checkShowGroup(group.fields)">
          <h3 v-if="group.title !== ''">{{ group.title }}</h3>
          <p v-if="group.description !== ''">{{ group.description }}</p>
          <div v-if="group.fields.length > 0" class="as-group-content">
            <div :class="(idx !== 0 && field.input.type !== 'astroidhidden' && field.input.type !== 'hidden' ? 'mt-3 pt-3 border-top': '')" v-for="(field, idx) in group.fields" :key="field.id" v-show="checkShow(field)">
              <div class="row">
                <div v-if="(field.label || field.description) && field.input.type !== `astroidheading`" class="col-sm-6 col-md-5">
                  <div v-if="(field.input.type === `astroidradio` && field.input.role !== 'switch') || (['astroidpreloaders', 'astroidmedia', 'astroidcolor', 'astroidicon'].includes(field.input.type))" class="form-label" v-html="field.label"></div>
                  <label v-else :for="(typeof field.type !== 'undefined' && field.type === `json` ? field.input.id : 'params_'+field.name)" class="form-label" v-html="field.label"></label>
                  <p v-if="field.description !== ''" v-html="field.description" class="form-text"></p>
                </div>
                <div :class="{
                  'col-sm-6 col-md-7' : (field.label || field.description) && field.input.type !== `astroidheading`,
                  'col-12': !(field.label || field.description) || field.input.type === `astroidheading`
                }">
                  <div v-if="typeof field.type !== 'undefined' && field.type === `json`">
                    <Fields 
                      :field="field" 
                      :scope="$scope"
                      :constant="props.config.astroid_lib" 
                      @update:contentlayout="updateContentLayout"
                      />
                  </div>
                  <div v-else v-html="field.input"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </main>
</template>