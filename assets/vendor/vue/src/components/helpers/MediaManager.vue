<script setup>
import { onMounted, ref } from 'vue';
import { faFolder, faLeftLong } from "@fortawesome/free-solid-svg-icons";
import { library } from '@fortawesome/fontawesome-svg-core'
library.add(faFolder, faLeftLong);
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    field: { type: Object, default: null },
    modelValue: { type: String, default: '' },
    constant:  { type: Object, default: null },
});

const _showMediaContent = ref([]);
const _showDirLocation  = ref([]);
const _currentFolder    = ref('');
const _imagePreview     = ref('');

onMounted(() => {
    if (props.modelValue !== '') {
        _imagePreview.value = props.constant.site_url + `images/`+ props.modelValue;
    }
    const mediaContent = document.getElementById(props.field.input.id+'modal');
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
    id: 'go-back',
    icon: ['fas', 'left-long'],
    name: 'Go back',
    type: 'back'
  })
  json.folders.forEach((element,id) => {
    _showMediaContent.value.push({
      id: 'folder'+id,
      icon: ['fas', 'folder'],
      name: element.name,
      path_relative: element.path_relative,
      type: 'folder'
    })
  });
  json.images.forEach((element,id) => {
    _showMediaContent.value.push({
      id: 'image'+id,
      icon: ['fas', 'folder'],
      name: element.name,
      path_relative: element.path_relative,
      path: props.constant.site_url + _showDirLocation.value.join('/') + `/` + element.path_relative,
      type: 'image'
    })
  });
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

function selectMedia(item) {
    let dirLocation = _showDirLocation.value.join('/');
    if (item.type === 'image') {
        _imagePreview.value = props.constant.site_url + dirLocation + `/` + item.path_relative;
        dirLocation = (dirLocation === 'images') ? '' : dirLocation.substring(dirLocation.indexOf('/')+1) + `/`;
        emit('update:modelValue', dirLocation + item.path_relative);
        document.getElementById(props.field.input.id+'close').click();
    }
}
</script>
<template>
    <div v-if="_imagePreview !== ''" class="image-preview"><img :src="_imagePreview" :alt="props.field.name" /></div>
    <div class="astroid-media-selector btn-group" role="group">
        <button class="btn btn-sm btn-as btn-primary btn-as-primary" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`modal`">{{ props.field.input.lang['select_media'] }}</button>
        <button class="btn btn-sm btn-as btn-as-light">{{ props.field.input.lang['clear'] }}</button>
    </div>
    <div class="modal fade" :id="props.field.input.id+`modal`" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><font-awesome-icon :icon="['fas', 'folder']" /> / {{ _showDirLocation.join(' / ') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" :id="props.field.input.id+'close'"></button>
                </div>
                <div class="modal-body">
                    <div class="row row-cols-2 row-cols-lg-4 row-cols-xl-5 g-3">
                      <div v-for="item in _showMediaContent" :key="item.id" class="col">
                        <div class="card card-default card-body text-center py-5 media-icon justify-content-center" :class="item.type+`-type`" @click="selectMedia(item)">
                          <font-awesome-icon v-if="(item.type === 'folder' || item.type ==='back') && item.icon !== undefined && item.icon" :icon="item.icon" size="3x" class="icon-folder" />
                          <img v-if="(item.type === 'image' && item.icon !== undefined && item.icon)" :src="item.path" :alt="item.name" />
                        </div>
                        <div v-if="item.name !== undefined && item.name" class="form-text">{{ item.name }}</div>
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