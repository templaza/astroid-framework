<script setup>
import { reactive, onUpdated } from 'vue';
import axios from "axios";
import { faCloudArrowUp } from "@fortawesome/free-solid-svg-icons";
import { library } from '@fortawesome/fontawesome-svg-core'
import { useDropzone } from "vue3-dropzone";

library.add(faCloudArrowUp);
const props = defineProps({
  url: { type: String, default: '' },
  clickUpload: { type: Boolean, default: false },
});
const emit = defineEmits(['update:media']);
const state = reactive({
  files: [],
});

const { getRootProps, getInputProps, isDragActive, ...rest } = useDropzone({
  onDrop,
});

// watch(state, () => {
//   console.log('state', state);
// });

// watch(isDragActive, () => {
//   console.log('isDragActive', isDragActive.value, rest);
// });

function onDrop(acceptFiles, rejectReasons) {
//   console.log('acceptFile: ',acceptFiles);
//   console.log('rejectReason: ',rejectReasons);
  acceptFiles.forEach(element => {
    state.files.push(element);
  });
}

function handleClickDeleteFile(index) {
  state.files.splice(index, 1);
}

const saveFiles = (files) => {
    for (var x = 0; x < files.length; x++) {
        const formData = new FormData(); // pass data as a form
        formData.append("file", files[x]);
        axios.post(props.url, formData, {
            headers: {
            "Content-Type": "multipart/form-data",
            },
        })
        .then((response) => {
            if (x === files.length) {
                emit('update:media');
            }
        })
        .catch((err) => {
            console.error(err);
        });
    }
};

onUpdated(()=>{
    if (props.clickUpload === true && state.files.length) {
        saveFiles(state.files);
    }
})
</script>

<template>
  <div v-if="state.files.length > 0" class="files mb-3">
    <div class="file-item" v-for="(file, index) in state.files" :key="index">
      <span>{{ file.name }}</span>
      <span class="delete-file" @click="handleClickDeleteFile(index)"
      >Delete</span
      >
    </div>
  </div>
  <div class="dropzone" v-bind="getRootProps()">
    <div
        class="border"
        :class="{isDragActive,}"
    >
        <input v-bind="getInputProps()" />
        <div v-if="isDragActive" class="text-center py-5">
            <font-awesome-icon :icon="['fas', 'cloud-arrow-up']" size="4x" class="mb-3" />
            <div>Drop the files here ...</div>
        </div>
        <div v-else class="text-center py-5">
            <font-awesome-icon :icon="['fas', 'cloud-arrow-up']" size="4x" class="mb-3" />
            <div>Drag and drop files here, or Click to select files</div>
        </div>
    </div>
  </div>
</template>