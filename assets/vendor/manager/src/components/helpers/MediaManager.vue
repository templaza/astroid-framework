<script setup>
import { onMounted, onUpdated, ref, inject, reactive } from 'vue';
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
        updatePreview();
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
    updatePreview();
})

function updatePreview() {
    if (props.modelValue.search(/https*:\/\//i) !== -1) {
        _imagePreview.value = props.modelValue;
    } else if (props.modelValue !== '') {
        _imagePreview.value = constant.site_url + props.field.input.mediaPath + '/'+ props.modelValue;
    }
}

const _isloading = ref(false);
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
    let url = props.field.input.ajax+"&action=library&asset=com_templates&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "media_ajax.txt?ts="+Date.now();
    }
    const formData = new FormData(); // pass data as a form
    formData.append("folder", _currentFolder.value);
    formData.append(constant.astroid_admin_token, 1);
    _isloading.value = true;
    axios.post(url, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    }).then(function (response) {
        if (response.data.status === 'success') {
            generateData(response.data.data);
            _isloading.value = false;
        }
    }).catch(function (error) {
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

const newFolderName = ref('');
const oldFolderName = reactive({
    name: '',
    type: ''
});
const newFolderInput = ref(null);
const editItem = ref('');

function initFormEdit(type, item = null) {
    editItem.value = type;
    if (item !== null) {
        oldFolderName.name = item.name;
        oldFolderName.type = item.type;
        newFolderName.value = item.name;
    } else {
        newFolderName.value = '';
    }
}
function createFolder() {
    if (newFolderName.value.trim() === '') {
        alert('Folder Name can not empty!');
        newFolderInput.value.focus();
        return false;
    }
    let url = constant.site_url+`administrator/index.php?option=com_ajax&astroid=media&action=createFolder&ts=`+Date.now();
    const formData = new FormData(); // pass data as a form
    formData.append("name", newFolderName.value.trim());
    formData.append("dir", 'images/'+_currentFolder.value);
    formData.append(constant.astroid_admin_token, 1);
    axios.post(url, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    }).then((response) => {
        document.getElementById(props.field.input.id+`close_edit_item_dialog`).click();
    }).catch((err) => {
        console.error(err);
    });
}
function rename() {
    if (newFolderName.value.trim() === '') {
        alert('Item Name can not empty!');
        newFolderInput.value.focus();
        return false;
    }
    let url = constant.site_url+`administrator/index.php?option=com_ajax&astroid=media&action=rename&ts=`+Date.now();
    const formData = new FormData(); // pass data as a form
    formData.append("name", oldFolderName.name);
    formData.append("type", oldFolderName.type);
    formData.append("new_name", newFolderName.value.trim());
    formData.append("dir", 'images/'+_currentFolder.value);
    formData.append(constant.astroid_admin_token, 1);
    axios.post(url, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    }).then((response) => {
        document.getElementById(props.field.input.id+`close_edit_item_dialog`).click();
    }).catch((err) => {
        console.error(err);
    });
}

function remove(item) {
    if (!confirm('This item will be deleted. You cannot undo this action. Are you sure?')) {
        return false;
    }
    let url = constant.site_url+`administrator/index.php?option=com_ajax&astroid=media&action=remove&ts=`+Date.now();
    const formData = new FormData(); // pass data as a form
    formData.append("name", item.name);
    formData.append("type", item.type);
    formData.append("dir", 'images/'+_currentFolder.value);
    formData.append(constant.astroid_admin_token, 1);
    axios.post(url, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    }).then((response) => {
        callAjax();
    }).catch((err) => {
        console.error(err);
    });
}
</script>
<template>
    <div v-if="_imagePreview !== ''" class="image-preview mb-3">
      <i v-if="props.field.input.media === 'videos'" class="fa-solid fa-video fa-3x"></i>
      <img v-else :src="_imagePreview" :alt="props.field.name" />
    </div>
    <div class="input-group input-group-sm mb-3">
        <span class="input-group-text" :id="props.field.input.id+`url`">Url</span>
        <input type="text" class="form-control" :value="modelValue" @input="emit('update:modelValue', $event.target.value)" aria-label="URL" :aria-describedby="props.field.input.id+`url`">
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
                <div class="modal-body p-3">
                    <div v-if="!_uploadForm" class="row row-cols-2 row-cols-lg-4 row-cols-xl-5 gx-3 gy-2">
                        <div v-if="!_isloading" v-for="item in _showMediaContent" :key="item.id" class="col p-4 text-center media-item">
                            <div class="card card-default media-icon justify-content-center align-items-center border" :class="item.type+`-type`" @click="selectMedia(item)">
                                <i v-if="(item.type === 'folder' || item.type ==='back') && item.icon !== undefined && item.icon" :class="item.icon" class="as-system-icon fa-3x"></i>
                                <img v-else-if="(item.type === 'image' && item.path !== undefined && item.path)" :src="item.path" class="img-fluid" :alt="item.name" />
                                <i v-else-if="item.type === 'video'" class="fa-solid fa-video fa-3x"></i>
                            </div>
                            <div v-if="item.name !== undefined && item.name">
                                <div class="form-text">{{ item.name }}</div>
                                <ul v-if="item.type !== 'back'" class="nav toolbox justify-content-center">
                                    <li class="nav-item"><a class="nav-link position-relative px-2" href="#" title="Rename" @click.prevent="initFormEdit('rename', item)" :data-bs-target="`#`+props.field.input.id+`edit_item_dialog`" data-bs-toggle="modal"><i class="fa-solid fa-pencil"></i><span class="position-absolute top-100 start-50 translate-middle form-text">Rename</span></a></li>
                                    <li class="nav-item"><a class="nav-link position-relative px-2" href="#" title="Remove" @click.prevent="remove(item)"><i class="fa-solid fa-trash"></i><span class="position-absolute top-100 start-50 translate-middle form-text">Delete</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div v-else class="loading d-flex justify-content-center flex-column w-100" style="align-items: center;">
                            <i class="fa-solid fa-basketball fa-bounce fa-3x" style="--fa-bounce-land-scale-x: 1.2;--fa-bounce-land-scale-y: .8;--fa-bounce-rebound: 5px;"></i>
                            <div class="fa-beat-fade mt-3" style="--fa-beat-fade-opacity: 0.1; --fa-beat-fade-scale: 1.05;">Loading...</div>
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
                    <button v-if="!_uploadForm" class="btn btn-sm btn-as btn-as-light" @click.prevent="initFormEdit('createfolder')" :data-bs-target="`#`+props.field.input.id+`edit_item_dialog`" data-bs-toggle="modal">Create New Folder</button>
                    <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary" @click="uploadFile">{{ _uploadBtnText }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" :id="props.field.input.id+`edit_item_dialog`" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ (editItem === `createfolder` ? 'Create New Folder': 'Rename') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <label :for="props.field.input.id + `folder_name`" class="form-label">{{ (editItem === `createfolder` ? 'Folder Name': 'Enter new name') }}</label>
                    <input type="text" ref="newFolderInput" class="form-control" v-model="newFolderName" :id="props.field.input.id + `folder_name`" placeholder="Enter Item name">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-as btn-as-light" :id="props.field.input.id+`close_edit_item_dialog`" :data-bs-target="`#`+props.field.input.id+`modal`" @click.prevent="" data-bs-toggle="modal">Cancel</button>
                    <button v-if="editItem === `createfolder`" class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="createFolder">Create</button>
                    <button v-else class="btn btn-sm btn-as btn-primary btn-as-primary" @click.prevent="rename">Rename</button>
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