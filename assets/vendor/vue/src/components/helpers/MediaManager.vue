<script setup>
import { onMounted, ref } from 'vue';
import { faFolder, faLeftLong } from "@fortawesome/free-solid-svg-icons";
import { library } from '@fortawesome/fontawesome-svg-core'
library.add(faFolder, faLeftLong);

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  field: { type: Object, default: null },
  modelValue: { type: String, default: '' }
});

const _showMediaContent = ref([]);
const _showDirLocation  = ref([]);
const _currentFolder    = ref('');

onMounted(() => {
  const mediaContent = document.getElementById(props.field.input.id+'modal')
  if (mediaContent) {
    mediaContent.addEventListener('show.bs.modal', event => {
      callAjax();
    })
    mediaContent.addEventListener('hide.bs.modal', event => {
      _showMediaContent.value = [];
    })
  }
})

function generateData(json = null) {
  if (!json) return false;
  _showDirLocation.value = json.current_folder.split('/');
  _showMediaContent.value = [];
  _showMediaContent.value.push({
    icon: ['fas', 'left-long'],
    name: 'Go back'
  })
}

function callAjax() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    const jsonData = JSON.parse(this.responseText);
    if (jsonData.status === 'success') {
      generateData(jsonData.data);
    }
  }
  if (process.env.NODE_ENV === 'development') {
    xhttp.open("GET", "media_ajax.txt?ts="+Date.now());
  } else {
    xhttp.open("GET", props.field.input.ajax+"&folder="+_currentFolder.value+"&ts="+Date.now());
  }
  xhttp.send();
}
</script>
<template>
    <div class="astroid-media-selector btn-group" role="group">
        <button class="btn btn-sm btn-as btn-primary btn-as-primary" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`modal`">{{ props.field.input.lang['select_media'] }}</button>
        <button class="btn btn-sm btn-as btn-as-light">{{ props.field.input.lang['clear'] }}</button>
    </div>
    <div class="modal fade" :id="props.field.input.id+`modal`" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><font-awesome-icon :icon="['fas', 'folder']" /> / {{ _showDirLocation.join(' / ') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                      <div v-for="item in _showMediaContent" class="col-xl-2 col-lg-3 col-sm-4 col-4">
                        <div class="card card-default card-body text-center">
                          <font-awesome-icon v-if="item.icon !== undefined && item.icon" :icon="item.icon" />
                          <div v-if="item.name !== undefined && item.name" class="form-text">{{ item.name }}</div>
                        </div>
                      </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
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