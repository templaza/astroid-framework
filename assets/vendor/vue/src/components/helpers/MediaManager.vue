<script setup>
import { onMounted, ref, reactive, watch } from 'vue';
import { faFolder, faLeftLong } from "@fortawesome/free-solid-svg-icons";
import { library } from '@fortawesome/fontawesome-svg-core'
import { useDropzone } from "vue3-dropzone";

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
  if (_currentFolder.value !== '') {
    _showMediaContent.value.push({
      id: 'go-back',
      icon: ['fas', 'left-long'],
      name: 'Go back',
      type: 'back'
    })
  }
  json.folders.forEach((element,id) => {
    _showMediaContent.value.push({
      id: 'folder'+id,
      icon: ['fas', 'folder'],
      name: element.name,
      path_relative: element.path_relative,
      type: 'folder'
    })
  });
  if (props.field.input.media === 'images') {
    json.images.forEach((element,id) => {
      _showMediaContent.value.push({
        id: 'image'+id,
        name: element.name,
        path_relative: element.path_relative,
        path: props.constant.site_url + `images/` + element.path_relative,
        type: 'image'
      })
    });
  }
  
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
    _imagePreview.value = props.constant.site_url + `images/` + item.path_relative;
    emit('update:modelValue', item.path_relative);
    document.getElementById(props.field.input.id+'close').click();
  }
  if (item.type === 'folder'){
    _currentFolder.value = item.path_relative;
    callAjax();
  }
  if (item.type === 'back') {
    let tmp = dirLocation.substring(dirLocation.indexOf('/')+1);
    if (tmp.indexOf('/') > 0) {
      _currentFolder.value = tmp.substring(0, tmp.lastIndexOf('/'));
    } else {
      _currentFolder.value = '';
    }
    callAjax();
  }
}

function clearMedia() {
  _imagePreview.value = '';
  emit('update:modelValue', '');
}

// Upload media files
const _uploadForm = ref(false);
const state = reactive({
  files: [],
});

const { getRootProps, getInputProps, isDragActive, ...rest } = useDropzone({
  onDrop,
});

watch(state, () => {
  console.log('state', state);
});

watch(isDragActive, () => {
  console.log('isDragActive', isDragActive.value, rest);
});

function onDrop(acceptFiles, rejectReasons) {
  console.log('acceptFile: ',acceptFiles);
  console.log('rejectReason: ',rejectReasons);
  state.files = acceptFiles;
}

function handleClickDeleteFile(index) {
  state.files.splice(index, 1);
}

function uploadFile() {
  _uploadForm.value = true;
}
</script>
<template>
    <div v-if="_imagePreview !== ''" class="image-preview mb-3"><img :src="_imagePreview" :alt="props.field.name" /></div>
    <div v-if="_imagePreview === ''" class="astroid-media-selector">
      <button class="btn btn-sm btn-as btn-primary btn-as-primary" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`modal`">{{ props.field.input.lang['select_media'] }}</button>
    </div>
    <div v-if="_imagePreview !== ''" class="astroid-media-selector btn-group" role="group">
        <button class="btn btn-sm btn-as btn-primary btn-as-primary" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`modal`">{{ props.field.input.lang['change_media'] }}</button>
        <button class="btn btn-sm btn-as btn-as-light" @click="clearMedia">{{ props.field.input.lang['clear'] }}</button>
    </div>
    <div class="modal fade" :id="props.field.input.id+`modal`" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><font-awesome-icon :icon="['fas', 'folder']" /> / {{ _showDirLocation.join(' / ') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" :id="props.field.input.id+'close'"></button>
                </div>
                <div class="modal-body">
                    <div v-if="!_uploadForm" class="row row-cols-2 row-cols-lg-4 row-cols-xl-5 g-3">
                      <div v-for="item in _showMediaContent" :key="item.id" class="col">
                        <div class="card card-default media-icon justify-content-center align-items-center" :class="item.type+`-type`" @click="selectMedia(item)">
                          <font-awesome-icon v-if="(item.type === 'folder' || item.type ==='back') && item.icon !== undefined && item.icon" :icon="item.icon" size="3x" class="icon-folder" />
                          <img v-if="(item.type === 'image' && item.path !== undefined && item.path)" :src="item.path" :alt="item.name" />
                        </div>
                        <div v-if="item.name !== undefined && item.name" class="form-text">{{ item.name }}</div>
                      </div>
                    </div>
                    <div v-if="_uploadForm">
                      <div v-if="state.files.length > 0" class="files">
                        <div class="file-item" v-for="(file, index) in state.files" :key="index">
                          <span>{{ file.name }}</span>
                          <span class="delete-file" @click="handleClickDeleteFile(index)"
                            >Delete</span
                          >
                        </div>
                      </div>
                      <div v-else class="dropzone" v-bind="getRootProps()">
                        <div
                          class="border"
                          :class="{
                            isDragActive,
                          }"
                        >
                          <input v-bind="getInputProps()" />
                          <p v-if="isDragActive">Drop the files here ...</p>
                          <p v-else>Drag and drop files here, or Click to select files</p>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-as btn-as-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click="uploadFile">Upload</button>
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