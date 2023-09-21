<script setup>
import { onBeforeMount, onMounted, ref } from 'vue';
import './assets/style.scss';
import Switcher from './components/Switcher.vue';
import FontAwesome from './components/FontAwesome.vue';

const config = JSON.parse(document.getElementById("astroid-menu-params").innerHTML);
const scope  = ref({});
onBeforeMount(() => {
    scope.value = config.value;
})
</script>

<template>
    <h2>{{ config.language.TPL_ASTROID_MENU_OPTIONS }}</h2>
    <p>{{ config.language.TPL_ASTROID_MENU_TEXT }}</p>
    <div class="row">
        <div class="col-6 col-lg-auto">
            <Switcher v-model="scope.megamenu" :config="config" fieldname="megamenu" :label="config.language.TPL_ASTROID_MEGA_MENU" /> 
        </div>
        <div class="col-6 col-lg-auto">
            <Switcher v-model="scope.showtitle" :config="config" fieldname="showtitle" :label="config.language.TPL_ASTROID_SHOW_TITLE" /> 
        </div>
        <div class="col-12 col-lg-auto">
            <label :for="config.id+`_subtitle`" class="form-label">{{ config.language.TPL_ASTROID_SUBTITLE }}</label>
            <input type="text" class="form-control" :id="config.id+`_subtitle`" :name="config.id+`[subtitle]`">
        </div>
        <div class="col-12 col-lg-3">
            <div :for="config.id+`_icon`" class="form-label">{{ config.language.TPL_ASTROID_SELECT_ICON }}</div>
            <FontAwesome v-model="scope.icon" :config="config" fieldname="showtitle" />
            <input type="hidden" :name="config.name+`[icon]`" v-model="scope.icon">
        </div>
    </div>
</template>