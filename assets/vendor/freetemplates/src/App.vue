<script setup>
import { onMounted, ref } from 'vue';
import axios from "axios";

const list = ref([]);
const configs = JSON.parse(document.getElementById('as-free-template-js').innerText);
onMounted(()=>{
    let url = "index.php?option=com_ajax&astroid=getFreeTemplates&ts="+Date.now();
    if (process.env.NODE_ENV === 'development') {
        url = "ajax.txt?ts="+Date.now();
    }
    axios.get(url)
        .then (function (response) {
            if (response.data.status === 'success') {
                list.value = response.data.data.templates;
                document.getElementById('as-free-templates-toggle').click();
            }
        })
        .catch (function (error) {
            // handle error
            console.log(error);
        });
})

const msg = ref('');
const installed = ref('');
const disabled = ref(false);
function installTemplate(item) {
    if (item.download === '') {
        return false;
    }
    let url = "index.php?option=com_ajax&astroid=installTemplate&ts="+Date.now();
    const formData = new FormData(); // pass data as a form
    formData.append('url', item.download);
    formData.append(configs.token, 1);
    disabled.value = true;
    axios.post(url, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    }).then((response) => {
        if (response.data.status === 'success') {
            installed.value = item.name;
        } else {
            msg.value = response.data.message;
            setTimeout(() => {
                msg.value = '';
            }, 5000);
        }
        disabled.value = false;
    }).catch((err) => {
        console.error(err);
    });
}
</script>

<template>
    <!-- Button trigger modal -->
    <button type="button" id="as-free-templates-toggle" class="btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#as-free-templates-modal">
        Free Template Toggle
    </button>
    <!-- Modal -->
    <div class="modal fade" id="as-free-templates-modal" tabindex="-1" aria-labelledby="as-free-templates-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-4" id="as-free-templates-title">{{ configs.language.title }}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div v-if="installed !== ``" class="modal-body p-5 text-center">
                    <h2>{{ configs.language.congrats + ' ' + installed }}</h2>
                    <div class="my-5"><img :src="configs.congrats" alt="Congratulation"></div>
                    <a href="index.php?option=com_templates&view=styles&client_id=0" class="btn btn-success px-5 py-1">{{ configs.language.view_templates }}</a>
                </div>
                <div v-else class="modal-body p-4">
                    <p>{{ configs.language.desc }}</p>
                    <div class="row row-cols-xl-3 row-cols-lg-2 row-cols-1 g-4">
                        <div v-for="item in list">
                            <div class="card border">
                                <img v-if="item.thumbnail !== ``" :src="item.thumbnail" class="card-img-top" :alt="item.name">
                                <div class="card-body">
                                    <h3 class="card-title">{{ item.name }}</h3>
                                    <p class="card-text">{{ item.description }}</p>
                                    <a v-if="item.download !== ``" @click.prevent="installTemplate(item)" href="#" class="btn btn-primary" :class="{'disabled': disabled}" role="button" :aria-disabled="disabled"><i v-if="disabled === true" class="fa-solid fa-sync fa-spin me-2"></i>{{ configs.language.install }}</a>
                                    <a v-if="item.preview !== ``" :href="item.preview" target="_blank" class="btn btn-outline-primary ms-2" role="button">{{ configs.language.preview }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="msg !== ``" class="alert alert-danger" role="alert" v-html="msg"></div>
                </div>
            </div>
        </div>
    </div>
</template>