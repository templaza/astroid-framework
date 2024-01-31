<script setup>
import { onBeforeMount, ref, reactive } from 'vue';
import axios from "axios";
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
  if (field.ngShow !== '' && field.ngShow.match(/\[\S+?\]/)) {
    const expression = field.ngShow.replace(/\[(\S+?)\]/g, "$scope.value\['$1'\]");
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

const presets = ref([]);
const toast_msg = reactive({
  header: '',
  body:'',
  icon: '',
  color:'darkviolet'
});
function loadPreset(value) {
  let tmp = JSON.parse(value);
  Object.keys(tmp).forEach(key => {
    $scope.value[key] = tmp[key];
  })
}
function getPreset(value) {
  presets.value = value;
}
function selectPreset(event, group) {
  if (event.target.value !== '' & confirm('Your current configure will be lost and overwritten by new data. Are you sure?')) {
    const toastAstroidMsg = document.getElementById('loadGroupPreset');
    const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
    let url = 'index.php?t='+Math.random().toString(36).substring(7);
    if (process.env.NODE_ENV === 'development') {
        url = "preset_ajax.txt?ts="+Date.now();
    }
    const formData = new FormData(); // pass data as a form
    formData.append(props.config.astroid_lib.astroid_admin_token, 1);
    formData.append('name', event.target.value);
    formData.append('astroid', 'loadpreset');
    formData.append('option', 'com_ajax');
    formData.append('template', props.config.astroid_lib.tpl_template_name);
    axios.post(url, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })
    .then((response) => {
      if (response.data.status === 'success') {
        const tmp = JSON.parse(response.data.data);
        group.fields.forEach(field => {
          if (typeof tmp[field.name] !== 'undefined') {
            $scope.value[field.name] = tmp[field.name]
          }
        });
        toast_msg.icon = 'fa-solid fa-rocket';
        toast_msg.header = 'Preset '+group.title+' Applied.';
        toast_msg.body = 'Please click "Save" button to save your changes!';
        toast_msg.color = 'green';
        toastBootstrap.show();
      } else {
        toast_msg.icon = 'fa-regular fa-face-sad-tear';
        toast_msg.header = 'Preset '+group.title+' is not Applied.';
        toast_msg.body = response.data.message;
        toast_msg.color = 'red';
        toastBootstrap.show();
      }
      event.target.value = '';
    })
    .catch((err) => {
      console.error(err);
    });
  }
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
          <div class="input-group mb-3">
            <label :for="`preset_`+fieldSet.name+`_`+index" class="input-group-text">Load default configure</label>
            <select class="form-select" :id="`preset_`+fieldSet.name+`_`+index" @change="selectPreset($event, group)">
              <option value="">Select a preset</option>
              <option v-for="(preset, preset_idx) in presets" :key="preset_idx" :value="preset.name">{{ preset.title }}</option>
            </select>
          </div>
          <div v-if="group.fields.length > 0" class="as-group-content">
            <div :class="(idx !== 0 && field.input.type !== 'astroidhidden' && field.input.type !== 'hidden' ? 'mt-3 pt-3 border-top': '')" v-for="(field, idx) in group.fields" :key="field.id" v-show="checkShow(field)">
              <div class="row">
                <div v-if="(field.label || field.description) && field.input.type !== `astroidheading`" class="col-sm-6 col-md-5">
                  <div v-if="(field.input.type === `astroidradio` && field.input.role !== 'switch') || (['astroidpreloaders', 'astroidmedia', 'astroidcolor', 'astroidicon', 'astroidcalendar', 'astroidgradient', 'astroidspacing', 'astroidicons'].includes(field.input.type))" class="form-label" v-html="field.label"></div>
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
                      @update:contentlayout="updateContentLayout"
                      @update:loadPreset="loadPreset"
                      @update:getPreset="getPreset"
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
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
      <div id="loadGroupPreset" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <i class="me-2" :class="toast_msg.icon" :style="{ color: toast_msg.color }"></i>
          <strong class="me-auto">{{ toast_msg.header }}</strong>
          <small>1 second ago</small>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">{{ toast_msg.body }}</div>
      </div>
    </div>
  </main>
</template>