<script setup>
const props = defineProps({
  config: { type: Object, default: null }
});
</script>

<template>
  <aside class="as-sidebar">
    <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="asSidebar" aria-labelledby="asSidebarOffcanvasLabel">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="asSidebarOffcanvasLabel">Browse menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" data-bs-target="#asSidebar"></button>
      </div>
      <div class="offcanvas-body">
        <nav class="as-links w-100" id="as-sidebar-nav" aria-label="Sidebar navigation">
          <ul class="as-links-nav list-unstyled mb-0 pb-3 pb-md-2 pe-lg-2">
            <li class="as-links-group py-2" v-for="group in props.config.astroid_content" :key="group.name">
              <a :href="`#`+group.name" class="as-page-link" @click.prevent="$emit('sidebarActive', group.name)">
                <strong class="as-links-heading d-flex w-100 align-items-center fw-semibold">
                  <i :class="group.icon" class="me-2"></i>
                  <span class="d-flex align-items-center" v-html="group.label"></span>
                </strong>
              </a>
              <ul class="list-unstyled fw-normal pb-2 small" v-if="Object.keys(group.childs).length > 0">
                <li v-for="(item, index) in group.childs" :key="index"><a href="#" @click.prevent="$emit('sidebarActive', group.name, index)" class="as-links-link d-inline-block rounded">{{ item.title }}</a></li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </aside>
</template>