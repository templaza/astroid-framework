<script setup>
import axios from "axios";
import { inject, onMounted, reactive, ref, watch } from "vue";

const emit = defineEmits(['update:ColorMode']);
const props = defineProps({
  config: { type: Object, default: null },
});

const theme = inject('theme', 'light');
const template_link = props.config.astroid_lib.jtemplate_link.replace(/\&amp\;/g, '&');
const save_icon = ref('fa-floppy-disk');
const cache_icon = ref('fa-eraser');
const save_disabled = ref(false);
const toast_msg = reactive({
  header: '',
  body:'',
  icon: '',
  color:'darkviolet'
});

const switcher = ref(false);

onMounted (() => {
  switcher.value = theme.value === 'light' ? false : true;
})

watch(switcher, (newValue) => {
  emit('update:ColorMode', newValue ? 'dark' : 'light');
})

const social_menu = [
  {title: 'Docs', href: props.config.astroid_lib.document_link, icon: 'fas fa-book'},
  {title: 'GitHub', href: props.config.astroid_lib.github_link, icon: 'fab fa-github'},
  {title: 'Videos Tutorial', href: props.config.astroid_lib.video_tutorial, icon: 'fab fa-youtube'},
  {title: 'Astroid Website', href: props.config.astroid_lib.astroid_link, icon: 'fas fa-meteor'},
]

function submitForm() {
  const action_link = props.config.astroid_lib.astroid_action.replace(/\&amp\;/g, '&');
  const toastAstroidMsg = document.getElementById('astroidMessage');
  const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
  const formData = new FormData(document.getElementById('astroid-form')); // pass data as a form;
  save_icon.value = 'fa-sync fa-spin'
  save_disabled.value = true;
  axios.post(action_link, formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  })
  .then((response) => {
    toast_msg.icon = 'fa-solid fa-floppy-disk';
    if (response.data.status === 'success') {
      toast_msg.header= 'Style has been saved';
      toast_msg.body = 'Style '+props.config.astroid_lib.template_name+' has been saved';
      toast_msg.color = 'darkviolet';
    } else {
      toast_msg.header= 'Style did not saved yet';
      toast_msg.body = response.data.message;
      toast_msg.color = 'red';
    }
    save_icon.value = 'fa-floppy-disk';
    save_disabled.value = false;
    toastBootstrap.show();
  })
  .catch((err) => {
    console.error(err);
  });
}

function clearCache() {
  const toastAstroidMsg = document.getElementById('astroidMessage');
  const toastBootstrap = Toast.getOrCreateInstance(toastAstroidMsg);
  cache_icon.value = 'fa-sync fa-spin';
  axios.get(props.config.astroid_lib.base_url+'/index.php?option=com_ajax&astroid=clear-cache&template='+props.config.astroid_lib.template_name)
  .then(function (response) {
    if (response.data.status === 'success') {
      toast_msg.icon  = 'fa-solid fa-eraser';
      toast_msg.header= 'Template Clear Cache';
      toast_msg.body = response.data.data.message;
      toast_msg.color = 'darkviolet';
      toastBootstrap.show();
      axios.get(props.config.astroid_lib.base_url+'/index.php?option=com_ajax&astroid=clear-joomla-cache')
      .then(function (response) {
        if (response.data.status === 'success') {
          cache_icon.value = 'fa-eraser';
          toast_msg.header= 'Joomla Clear Cache';
          toast_msg.body = response.data.data.message;
          toastBootstrap.show();
        }
      })
      .catch(function (error) {
        // handle error
        console.log(error);
      });
    }
  })
  .catch(function (error) {
    // handle error
    console.log(error);
  });
}
</script>
<template>
  <header class="navbar navbar-expand-lg as-navbar sticky-top">
    <nav class="container-xxl as-gutter flex-wrap flex-lg-nowrap" aria-label="Main navigation">
      <div class="as-navbar-toggle">
        <button class="navbar-toggler p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#asSidebar" aria-controls="asSidebar" aria-label="Toggle docs navigation">
          <i class="fas fa-bars"></i>
          <span class="d-none fs-6 pe-1">Browse</span>
        </button>
      </div>
      <a class="navbar-brand p-0 me-0 me-lg-4 d-flex align-items-center" href="javascript:void(0);" aria-label="Astroid">
        <div class="fa-layers fa-2x me-1" full-width=""><svg class="svg-inline--fa me-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="meteor" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path class="" fill="currentColor" d="M493.7 .9L299.4 75.6l2.3-29.3c1-12.8-12.8-21.5-24-15.1L101.3 133.4C38.6 169.7 0 236.6 0 309C0 421.1 90.9 512 203 512c72.4 0 139.4-38.6 175.7-101.3L480.8 234.3c6.5-11.1-2.2-25-15.1-24l-29.3 2.3L511.1 18.3c.6-1.5 .9-3.2 .9-4.8C512 6 506 0 498.5 0c-1.7 0-3.3 .3-4.8 .9zM192 192a128 128 0 1 1 0 256 128 128 0 1 1 0-256zm0 96a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm16 96a16 16 0 1 0 0-32 16 16 0 1 0 0 32z"></path></svg><span class="fa-layers-counter fa-layers-bottom-right">v{{ props.config.astroid_lib.astroid_version }}</span></div>
        Astroid
      </a>
      <div class="d-flex">
        <button class="navbar-toggler d-flex d-lg-none order-3 p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#bdNavbar" aria-controls="bdNavbar" aria-label="Toggle navigation">
          <i class="fas fa-ellipsis"></i>
        </button>
      </div>
      <div class="offcanvas-lg offcanvas-end flex-grow-1" tabindex="-1" id="bdNavbar" aria-labelledby="bdNavbarOffcanvasLabel" data-bs-scroll="true">
        <div class="offcanvas-header px-4 pb-0">
          <h5 class="offcanvas-title" id="bdNavbarOffcanvasLabel">{{ props.config.astroid_lib.template_title }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" data-bs-target="#bdNavbar"></button>
        </div>
        <div class="offcanvas-body p-4 pt-0 p-lg-0">
          <hr class="d-lg-none">
          <div class="cta-button d-lg-flex">
            <div class="vr d-none d-lg-flex h-100 me-lg-4"></div>
            <ul class="navbar-nav flex-row flex-wrap">
              <li class="nav-item col-6 col-lg-auto d-grid">
                <button class="btn btn-sm btn-as btn-as-primary" type="button" @click.prevent="submitForm" :disabled="save_disabled">
                  <i class="fas me-1" :class="save_icon"></i>
                  {{ props.config.astroid_lang.ASTROID_SAVE }}
                </button>
              </li>
              <li class="nav-item col-6 col-lg-auto d-grid">
                <button class="btn btn-sm btn-as btn-as-light" type="button" @click.prevent="clearCache" :disabled="save_disabled">
                  <i class="fas me-1" :class="cache_icon"></i>
                  {{ props.config.astroid_lang.ASTROID_TEMPLATE_CLEAR_CACHE }}
                </button>
              </li>
              <li class="nav-item col-6 col-lg-auto d-grid">
                <a class="btn btn-sm btn-as btn-as-light" :href="props.config.astroid_lib.site_url" target="_blank" tabindex="-1" role="button" :aria-disabled="save_disabled" :class="{'disabled' : save_disabled}">
                  <i class="fas fa-up-right-from-square me-1"></i>
                  {{ props.config.astroid_lang.ASTROID_TEMPLATE_PREVIEW }}
                </a>
              </li>
              <li class="nav-item col-6 col-lg-auto d-grid">
                <a class="btn btn-sm btn-as btn-as-light" :href="template_link" tabindex="-1" role="button" :aria-disabled="save_disabled" :class="{'disabled' : save_disabled}">
                  <i class="fas fa-xmark me-1"></i>
                  {{ props.config.astroid_lang.ASTROID_TEMPLATE_CLOSE }}
                </a>
              </li>
            </ul>
          </div>
          <div class="template-title m-auto d-none d-lg-block">{{ props.config.astroid_lib.template_title }}</div>
          <hr class="d-lg-none">
          <ul class="navbar-nav flex-row flex-wrap ms-md-auto">
            <li class="nav-item col-6 col-lg-auto" v-for="item in social_menu" :key="item.value">
              <a class="nav-link py-2 px-0 px-lg-2" :href="item.href" :title="item.title" target="_blank" rel="noopener">
                <i :class="item.icon"></i>
                <small class="d-lg-none ms-2">{{ item.title }}</small>
              </a>
            </li>
          </ul>
          <div class="astroid-color-mode d-lg-flex align-items-center ms-lg-2">
            <hr class="d-lg-none">
            <div class="form-check form-switch"><input class="form-check-input switcher" id="astroid-color-mode-switcher" type="checkbox" role="switch" v-model="switcher"></div>
          </div>
        </div>
      </div>
    </nav>
  </header>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="astroidMessage" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <i class="me-2" :class="toast_msg.icon" :style="{color: toast_msg.color}"></i>
        <strong class="me-auto">{{ toast_msg.header }}</strong>
        <small>1 second ago</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        {{ toast_msg.body }}
      </div>
    </div>
  </div>
</template>