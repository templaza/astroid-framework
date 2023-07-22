<script setup>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faHome, faHeading, faPaintbrush, faColumns, faFont, faShareNodes, faCode, faCogs, faPaintBrush, faRocket } from "@fortawesome/free-solid-svg-icons";
import { faNewspaper } from "@fortawesome/free-regular-svg-icons";

library.add(faHome, faHeading, faPaintbrush, faColumns, faFont, faShareNodes, faCode, faCogs, faPaintBrush, faRocket, faNewspaper);

const props = defineProps({
  config: { type: Object, default: null }
});
</script>

<template>
  <aside class="bd-sidebar">
    <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="bdSidebar" aria-labelledby="bdSidebarOffcanvasLabel">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="bdSidebarOffcanvasLabel">Browse menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" data-bs-target="#bdSidebar"></button>
      </div>
      <div class="offcanvas-body">
        <nav class="bd-links w-100" id="bd-sidebar-nav" aria-label="Sidebar navigation">
          <ul class="bd-links-nav list-unstyled mb-0 pb-3 pb-md-2 pe-lg-2">
            <li class="bd-links-group py-2" v-for="group in props.config.astroid_sidebar" :key="group.name">
              <strong class="bd-links-heading d-flex w-100 align-items-center fw-semibold">
                <font-awesome-icon :icon="group.icon" class="me-2" />
                {{ group.label }}
              </strong>
              {{ group.childs.length }}
              <ul class="list-unstyled fw-normal pb-2 small" v-if="Object.keys(group.childs).length > 0">
                <li v-for="(item, index) in group.childs" :key="index"><a href="#" class="bd-links-link d-inline-block rounded">{{ item.title }}</a></li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </aside>
</template>