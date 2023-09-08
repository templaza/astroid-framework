<script setup>
import axios from "axios";
import { library } from '@fortawesome/fontawesome-svg-core'
import { faMeteor, faBars, faEllipsis, faBook, faSave, faEraser, faUpRightFromSquare, faXmark } from "@fortawesome/free-solid-svg-icons";
import { faGithub, faYoutube } from "@fortawesome/free-brands-svg-icons";
import { ref } from "vue";

library.add(faMeteor, faBars, faEllipsis, faGithub, faYoutube, faBook, faSave, faEraser, faUpRightFromSquare, faXmark);

const props = defineProps({
  config: { type: Object, default: null }
});

const template_link = props.config.astroid_lib.jtemplate_link.replace(/\&amp\;/g, '&');
const save_icon = ref('fa-floppy-disk');
const toast_msg = ref('');

const social_menu = [
  {title: 'Docs', href: props.config.astroid_lib.document_link, icon: ['fas', 'book']},
  {title: 'GitHub', href: props.config.astroid_lib.github_link, icon: ['fab', 'github']},
  {title: 'Videos Tutorial', href: props.config.astroid_lib.video_tutorial, icon: ['fab', 'youtube']},
  {title: 'Astroid Website', href: props.config.astroid_lib.astroid_link, icon: ['fas', 'meteor']},
]

function submitForm() {
  const action_link = props.config.astroid_lib.astroid_action.replace(/\&amp\;/g, '&');
  const form = document.getElementById('astroid-form');
  const formData = new FormData(form); // pass data as a form;
  const toastLiveExample = document.getElementById('saveMessage');
  const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
  save_icon.value = 'fa-sync fa-spin'
  axios.post(action_link, formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  })
  .then((response) => {
    if (response.data.status === 'success') {
      toast_msg.value = 'Template Saved'
    } else {
      toast_msg.value = response.data.message;
    }
    save_icon.value = 'fa-floppy-disk';
    toastBootstrap.show();
  })
  .catch((err) => {
    console.error(err);
  });
}
</script>
<template>
  <header class="navbar navbar-expand-lg as-navbar sticky-top">
    <nav class="container-xxl as-gutter flex-wrap flex-lg-nowrap" aria-label="Main navigation">
      <div class="as-navbar-toggle">
        <button class="navbar-toggler p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#asSidebar" aria-controls="asSidebar" aria-label="Toggle docs navigation">
          <font-awesome-icon :icon="['fas', 'bars']" />
          <span class="d-none fs-6 pe-1">Browse</span>
        </button>
      </div>
      <a class="navbar-brand p-0 me-0 me-lg-4 d-flex align-items-center" href="javascript:void(0);" aria-label="Astroid">
        <font-awesome-layers class="fa-2x me-1" full-width>
          <font-awesome-icon class="me-2" :icon="['fas', 'meteor']" />
          <font-awesome-layers-text counter :value="`v`+props.config.astroid_lib.astroid_version" position="bottom-right" />
        </font-awesome-layers>
        Astroid
      </a>
      <div class="d-flex">
        <button class="navbar-toggler d-flex d-lg-none order-3 p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#bdNavbar" aria-controls="bdNavbar" aria-label="Toggle navigation">
          <font-awesome-icon :icon="['fas', 'ellipsis']" />
        </button>
      </div>
      <div class="offcanvas-lg offcanvas-end flex-grow-1" tabindex="-1" id="bdNavbar" aria-labelledby="bdNavbarOffcanvasLabel" data-bs-scroll="true">
        <div class="offcanvas-header px-4 pb-0">
          <h5 class="offcanvas-title" id="bdNavbarOffcanvasLabel">Astroid</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" data-bs-target="#bdNavbar"></button>
        </div>
        <div class="offcanvas-body p-4 pt-0 p-lg-0">
          <hr class="d-lg-none">
          <div class="cta-button d-lg-flex">
            <div class="vr d-none d-lg-flex h-100 me-lg-4"></div>
            <ul class="navbar-nav flex-row flex-wrap">
              <li class="nav-item col-6 col-lg-auto d-grid">
                <button class="btn btn-sm btn-as btn-as-primary" type="button" @click.prevent="submitForm">
                  <i class="fas me-1" :class="save_icon"></i>
                  {{ props.config.astroid_lang.ASTROID_SAVE }}
                </button>
              </li>
              <li class="nav-item col-6 col-lg-auto d-grid">
                <button class="btn btn-sm btn-as btn-as-light" type="button">
                  <font-awesome-icon :icon="['fas', 'eraser']" class="me-1" />
                  {{ props.config.astroid_lang.ASTROID_TEMPLATE_CLEAR_CACHE }}
                </button>
              </li>
              <li class="nav-item col-6 col-lg-auto d-grid">
                <a class="btn btn-sm btn-as btn-as-light" type="button" :href="props.config.astroid_lib.site_url" target="_blank">
                  <font-awesome-icon :icon="['fas', 'up-right-from-square']" class="me-1" />
                  {{ props.config.astroid_lang.ASTROID_TEMPLATE_PREVIEW }}
                </a>
              </li>
              <li class="nav-item col-6 col-lg-auto d-grid">
                <a class="btn btn-sm btn-as btn-as-light" type="button" :href="template_link">
                  <font-awesome-icon :icon="['fas', 'xmark']" class="me-1" />
                  {{ props.config.astroid_lang.ASTROID_TEMPLATE_CLOSE }}
                </a>
              </li>
            </ul>
          </div>
          <hr class="d-lg-none">
          <ul class="navbar-nav flex-row flex-wrap ms-md-auto">
            <li class="nav-item col-6 col-lg-auto" v-for="item in social_menu" :key="item.value">
              <a class="nav-link py-2 px-0 px-lg-2" :href="item.href" :title="item.title" target="_blank" rel="noopener">
                <font-awesome-icon :icon="item.icon" />
                <small class="d-lg-none ms-2">{{ item.title }}</small>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="saveMessage" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <i class="fa-solid fa-floppy-disk me-2" :style="{color: 'darkviolet'}"></i>
        <strong class="me-auto">Astroid Template</strong>
        <small>1 second ago</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        {{ toast_msg }}
      </div>
    </div>
  </div>
</template>