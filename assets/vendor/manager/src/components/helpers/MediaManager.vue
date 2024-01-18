<script setup>
import { onMounted, onUpdated, ref, inject } from 'vue';
import axios from "axios";
import DropZone from './DropZone.vue';

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  field: { type: Object, default: null },
  modelValue: { type: String, default: '' },
});
const constant = inject('constant', {});

const _showMediaContent = ref([]);
const _showDirLocation  = ref([]);
const _currentFolder    = ref('');
const _imagePreview     = ref('');

onMounted(() => {
    if (props.modelValue !== '') {
        _imagePreview.value = constant.site_url + props.field.input.mediaPath + '/'+ props.modelValue;
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

onUpdated(()=>{
  if (props.modelValue !== _imagePreview.value.replace(constant.site_url + props.field.input.mediaPath + '/','')) {
    _imagePreview.value = constant.site_url + props.field.input.mediaPath + '/'+ props.modelValue;
  }
})

function generateData(json = null) {
  if (!json) return false;
  _showDirLocation.value = json.current_folder.split('/');
  _showMediaContent.value = [];
  if (_currentFolder.value !== '') {
    _showMediaContent.value.push({
      id: 'go-back',
      icon: 'fas fa-left-long',
      name: 'Go back',
      type: 'back'
    })
  }
  json.folders.forEach((element,id) => {
    _showMediaContent.value.push({
      id: 'folder'+id,
      icon: 'fas fa-folder',
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
        path: constant.site_url + props.field.input.mediaPath + '/' + element.path_relative,
        type: 'image'
      })
    });
  }
  if (props.field.input.media === 'videos') {
    json.videos.forEach((element,id) => {
      _showMediaContent.value.push({
        id: 'video'+id,
        name: element.name,
        path_relative: element.path_relative,
        path: constant.site_url + props.field.input.mediaPath + '/' + element.path_relative,
        type: 'video'
      })
    });
  }
}

function callAjax() {
  let url = props.field.input.ajax+"&action=library&asset=com_templates&folder="+_currentFolder.value+"&ts="+Date.now();
  if (process.env.NODE_ENV === 'development') {
    url = "media_ajax.txt?ts="+Date.now();
  }
  axios.get(url)
  .then(function (response) {
    if (response.data.status === 'success') {
      generateData(response.data.data);
    }
  })
  .catch(function (error) {
    // handle error
    console.log(error);
  });
}

function selectMedia(item) {
  let dirLocation = _showDirLocation.value.join('/');
  if (item.type === 'image' || item.type === 'video') {
    _imagePreview.value = constant.site_url + props.field.input.mediaPath + '/' + item.path_relative;
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

const _uploadForm = ref(false);
const _clickUpload = ref(false);
const _uploadBtnText = ref('Upload');
function uploadFile() {
  if (_uploadForm.value === true) {
    _clickUpload.value = true;
  } else {
    _uploadForm.value = true;
    _clickUpload.value = false;
    _uploadBtnText.value = 'Click to Upload';
  }
}

function uploadReset() {
  _uploadForm.value = false;
  _clickUpload.value = false;
  _uploadBtnText.value = 'Upload';
  callAjax();
}
</script>
<template>
    <div v-if="_imagePreview !== ''" class="image-preview mb-3">
      <i v-if="props.field.input.media === 'videos'" class="fa-solid fa-video fa-3x"></i>
      <img v-else :src="_imagePreview" :alt="props.field.name" />
    </div>
    <div v-if="_imagePreview === ''" class="astroid-media-selector">
      <button class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`modal`">{{ props.field.input.lang['select_media'] }}</button>
    </div>
    <div v-else="_imagePreview !== ''" class="astroid-media-selector btn-group" role="group">
        <button class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="" data-bs-toggle="modal" :data-bs-target="`#`+props.field.input.id+`modal`">{{ props.field.input.lang['change_media'] }}</button>
        <button class="btn btn-sm btn-as btn-as-light" @click.prevent="clearMedia">{{ props.field.input.lang['clear'] }}</button>
    </div>
    <div class="modal fade" :id="props.field.input.id+`modal`" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-folder"></i> / {{ _showDirLocation.join(' / ') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" :id="props.field.input.id+'close'"></button>
                </div>
                <div class="modal-body">
                    <div v-if="!_uploadForm" class="row row-cols-2 row-cols-lg-4 row-cols-xl-5 g-3">
                      <div v-for="item in _showMediaContent" :key="item.id" class="col">
                        <div class="card card-default media-icon justify-content-center align-items-center" :class="item.type+`-type`" @click="selectMedia(item)">
                          <i v-if="(item.type === 'folder' || item.type ==='back') && item.icon !== undefined && item.icon" :class="item.icon" class="icon-folder fa-3x"></i>
                          <img v-else-if="(item.type === 'image' && item.path !== undefined && item.path)" :src="item.path" :alt="item.name" />
                          <i v-else-if="item.type === 'video'" class="fa-solid fa-video fa-3x"></i>
                        </div>
                        <div v-if="item.name !== undefined && item.name" class="form-text">{{ item.name }}</div>
                      </div>
                    </div>
                    <div v-else>
                      <DropZone 
                        :url="props.field.input.ajax+`&action=upload&media=`+props.field.input.media+`&dir=images/`+_currentFolder" 
                        :click-upload="_clickUpload"
                        @update:media="uploadReset" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-if="!_uploadForm" type="button" class="btn btn-sm btn-as btn-as-light" data-bs-dismiss="modal">Close</button>
                    <button v-else type="button" class="btn btn-sm btn-as btn-as-light" @click="uploadReset">Cancel</button>
                    <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click="uploadFile">{{ _uploadBtnText }}</button>
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