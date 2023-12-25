<script setup>
import { onBeforeMount, onMounted, computed, ref } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faCircle } from "@fortawesome/free-solid-svg-icons";
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'
import './assets/style.scss';
import Switcher from './components/Switcher.vue';
import FontAwesome from './components/FontAwesome.vue';
import LayoutBuilder from './components/LayoutBuilder.vue';
import LayoutGrid from './components/LayoutGrid.vue';
import SelectElement from './components/SelectElement.vue';

library.add(faCircle);
const config = JSON.parse(document.getElementById("astroid-menu-params").innerHTML);
const scope  = ref({});
const layout = ref([]);
const gridEditIdx = ref(null);
onBeforeMount(() => {
    scope.value     =   config.value;
    layout.value    =   JSON.parse(config.value.rows);
})

onMounted(()=>{
    document.addEventListener('click', function(event) {
        const badge_color_picker        = document.getElementById(config.id+`_badge_color_colorpicker`);
        const badge_color_circle        = document.getElementById(config.id+`-badge-colorcircle`);
        if (_showBadgeColorPicker.value === true) {
            if (
                badge_color_picker 
                && badge_color_circle 
                && !badge_color_circle.contains(event.target) 
                && !badge_color_picker.contains(event.target)
            ) {
                _showBadgeColorPicker.value = false;
            }
        }
        const badge_bgcolor_picker      = document.getElementById(config.id+`_badge_bgcolor_colorpicker`);
        const badge_bgcolor_circle      = document.getElementById(config.id+`-badge-bgcolorcircle`);
        if (_showBadgeBGColorPicker.value === true) {
            if (
                badge_bgcolor_picker 
                && badge_bgcolor_circle 
                && !badge_bgcolor_circle.contains(event.target) 
                && !badge_bgcolor_picker.contains(event.target)
            ) {
                _showBadgeBGColorPicker.value = false;
            }
        }
    });
})

const layout_text = computed(() => {
  return JSON.stringify(layout.value);
})

function editGrid(grid = []) {
    const sec = Date.now() * 1000 + Math.random() * 1000;
    if (gridEditIdx.value !== null) {
        let col_idx = 0;
        grid.forEach(col => {
            if (col > 0) {
                if (col_idx < layout.value[gridEditIdx.value].cols.length) {
                    layout.value[gridEditIdx.value].cols[col_idx].size = col;
                } else {
                    layout.value[gridEditIdx.value].cols.push({
                        gid: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
                        type: 'column',
                        size: col,
                        elements: []
                    });
                }
                col_idx ++
            } 
        });
        
        while (col_idx < layout.value[gridEditIdx.value].cols.length) {
            if (col_idx>0 && layout.value[gridEditIdx.value].cols[col_idx].elements.length > 0) {
                layout.value[gridEditIdx.value].cols[col_idx].elements.forEach(el => {
                    layout.value[gridEditIdx.value].cols[col_idx - 1].elements.push(el);
                });
            }
            layout.value[gridEditIdx.value].cols.splice(col_idx, 1);
        }
        gridEditIdx.value = null
    } else {
        let tmp_grid = [];
        grid.forEach(col => {
            if (col > 0) {
                tmp_grid.push({
                    gid: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
                    type: 'column',
                    size: col,
                    elements: []
                })
            }
        });
        layout.value.push({
            gid: sec.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000),
            type: 'row',
            cols: tmp_grid
        });
    }
    document.getElementById('astroid-layout-grid-close').click();
}

function gridState(index) {
    gridEditIdx.value = index;
}

const _addColumnEl = ref(null);
function addColumnElement(element) {
    _addColumnEl.value = element.gid;
}

function addElement(addon) {
    let id = Date.now() * 1000 + Math.random() * 1000;
    id = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
    let new_element = {};
    if (addon.astroidmenucategory === 'module') {
        new_element = {gid: id};
        Object.keys(addon).forEach(key => {
            new_element[key] = addon[key];
        });
    } else {
        new_element = {
            gid: id,
            id: addon.id,
            type: addon.astroidmenucategory,
            title: addon.title
        }
    }
    
    layout.value.every(row => {
        row.cols.every(column => {
            if (_addColumnEl.value === column.gid) {
                column.elements.push(new_element);
                _addColumnEl.value = null;
                return false;
            }
            return true;
        });
        return true;
    });
    document.getElementById('astroid-select-element-close').click();
}

//Color configuration
const _showBadgeColorPicker = ref(false);
const _showBadgeBGColorPicker = ref(false);
function changeColor (color, key) {
    const { r, g, b, a } = color.rgba;
    if (r === 0, g === 0, b === 0, a === 0) {
        scope.value[key] = '';
    } else {
        scope.value[key] = `rgba(${r}, ${g}, ${b}, ${a})`;
    }
}
</script>

<template>
    <h2>{{ config.language.TPL_ASTROID_MENU_OPTIONS }}</h2>
    <p>{{ config.language.TPL_ASTROID_MENU_TEXT }}</p>
    <div class="row g-3">
        <div class="col-6 col-lg-auto">
            <Switcher v-model="scope.megamenu" :config="config" fieldname="megamenu" :label="config.language.TPL_ASTROID_MEGA_MENU" /> 
        </div>
        <div class="col-6 col-lg-auto">
            <Switcher v-model="scope.showtitle" :config="config" fieldname="showtitle" :label="config.language.TPL_ASTROID_SHOW_TITLE" /> 
        </div>
        <div class="col-12 col-lg-auto">
            <label :for="config.id+`_subtitle`" class="form-label">{{ config.language.TPL_ASTROID_SUBTITLE }}</label>
            <input type="text" class="form-control" :id="config.id+`_subtitle`" :name="config.name+`[subtitle]`" v-model="scope.subtitle">
        </div>
        <div class="col-12 col-lg-3">
            <div :for="config.id+`_icon`" class="form-label">{{ config.language.TPL_ASTROID_SELECT_ICON }}</div>
            <FontAwesome v-model="scope.icon" :config="config" fieldname="icon" />
            <input type="hidden" :name="config.name+`[icon]`" v-model="scope.icon">
        </div>
        <div class="col-12 col-lg-auto">
            <label :for="config.id+`_customclass`" class="form-label">{{ config.language.ASTROID_CUSTOM_CLASS }}</label>
            <input type="text" class="form-control" :id="config.id+`_customclass`" :name="config.name+`[customclass]`" v-model="scope.customclass">
        </div>
        <div class="col-12 col-lg-auto" v-show="!parseInt(scope.megamenu)">
            <label :for="config.id+`_width`" class="form-label">{{ config.language.TPL_ASTROID_MENU_OPTIONS_WIDTH }}</label>
            <input type="text" class="form-control" :id="config.id+`_width`" :name="config.name+`[width]`" v-model="scope.width">
        </div>
        <div class="col-12 col-lg-auto" v-show="!parseInt(scope.megamenu)">
            <label :for="config.id+`_alignment`" class="form-label">{{ config.language.TPL_ASTROID_MENU_OPTIONS_DROPDOWN_ALIGNMENT }}</label>
            <select class="form-select" v-model="scope.alignment" :id="config.id+`_alignment`" :name="config.name+`[alignment]`" :aria-label="config.language.TPL_ASTROID_MENU_OPTIONS_SELECT_ALIGNMENT">
                <option value="left">{{ config.language.JGLOBAL_LEFT }}</option>
                <option value="right">{{ config.language.JGLOBAL_RIGHT }}</option>
                <option value="center">{{ config.language.JGLOBAL_CENTER }}</option>
                <option value="full">{{ config.language.TPL_ASTROID_CONTAINER }}</option>
                <option value="edge">{{ config.language.TPL_ASTROID_FULL }}</option>
            </select>
        </div>
        <div class="col-12 col-lg-auto" v-show="parseInt(scope.megamenu)">
            <label :for="config.id+`_megamenu_width`" class="form-label">{{ config.language.TPL_ASTROID_MENU_OPTIONS_MEGAMENU_WIDTH }}</label>
            <input type="text" class="form-control" :id="config.id+`_megamenu_width`" :name="config.name+`[megamenu_width]`" v-model="scope.megamenu_width">
        </div>
        <div class="col-12 col-lg-auto" v-show="parseInt(scope.megamenu)">
            <label :for="config.id+`_megamenu_direction`" class="form-label">{{ config.language.TPL_ASTROID_MENU_OPTIONS_DROPDOWN_ALIGNMENT }}</label>
            <select class="form-select" v-model="scope.megamenu_direction" :id="config.id+`_megamenu_direction`" :name="config.name+`[megamenu_direction]`" :aria-label="config.language.TPL_ASTROID_MENU_OPTIONS_SELECT_ALIGNMENT">
                <option value="left">{{ config.language.JGLOBAL_LEFT }}</option>
                <option value="right">{{ config.language.JGLOBAL_RIGHT }}</option>
                <option value="center">{{ config.language.JGLOBAL_CENTER }}</option>
                <option value="full">{{ config.language.TPL_ASTROID_CONTAINER }}</option>
                <option value="edge">{{ config.language.TPL_ASTROID_FULL }}</option>
            </select>
        </div>
        <div class="col-12" v-show="parseInt(scope.megamenu)">
            <hr>
            <h3>{{ config.language.TPL_ASTROID_MEGA_MENU_OPTIONS }}</h3>
            <p>{{ config.language.TPL_ASTROID_MEGA_MENU_TEXT }}</p>
            <input type="hidden" :name="config.name+`[rows]`" :value="layout_text">
            <LayoutBuilder :list="layout" group="root" @edit:Grid="gridState" @update:selectElement="addColumnElement" />
            <LayoutGrid @update:saveElement="editGrid" />
            <SelectElement :options="config.options" @update:selectElement="addElement" />
            <div class="text-center mt-3">
                <button class="btn btn-primary" @click.prevent="" data-bs-toggle="modal" data-bs-target="#astroid-select-grid"><i class="fa-solid fa-plus"></i> Add Row</button>
            </div>
        </div>
        <div class="col-12">
            <hr>
            <h3>{{ config.language.TPL_ASTROID_MEGA_BADGE_OPTIONS }}</h3>
            <p>{{ config.language.TPL_ASTROID_MEGA_BADGE_OPTIONS_TEXT }}</p>
        </div>
        <div class="col-12 col-lg-auto">
            <Switcher v-model="scope.badge" :config="config" fieldname="badge" :label="config.language.TPL_ASTROID_MENU_BADGE" /> 
        </div>
        <div class="col-12 col-lg-4" v-show="parseInt(scope.badge)">
            <label :for="config.id+`_badge_text`" class="form-label">{{ config.language.TPL_ASTROID_MENU_BADGE_TEXT }}</label>
            <input type="text" class="form-control" :id="config.id+`_badge_text`" :name="config.name+`[badge_text]`" v-model="scope.badge_text">
        </div>
        <div class="col-12 col-lg-3" v-show="parseInt(scope.badge)">
            <div class="form-label">
                {{ config.language.TPL_ASTROID_MENU_BADGE_COLOR }}
            </div>
            <div>
                <font-awesome-icon :id="config.id+`-badge-colorcircle`" :icon="['fas', 'circle']" size="3x" class="border astroid-color-picker" :style="{'color': scope.badge_color}" @click="_showBadgeColorPicker = true" />
                <input type="hidden" :name="config.name+`[badge_color]`" :id="config.id+`_badge_color`" :value="scope.badge_color" />
                <ColorPicker v-if="_showBadgeColorPicker"
                    theme="light"
                    :color="scope.badge_color"
                    :sucker-hide="true"
                    :sucker-canvas="null"
                    :sucker-area="[]"
                    :id="config.id+`_badge_color_colorpicker`"
                    @changeColor="(color) => {changeColor(color, 'badge_color')}"
                />
            </div>
        </div>
        <div class="col-12 col-lg-3" v-show="parseInt(scope.badge)">
            <div class="form-label">
                {{ config.language.TPL_ASTROID_MENU_BADGE_BGCOLOR }}
            </div>
            <div>
                <font-awesome-icon :id="config.id+`-badge-bgcolorcircle`" :icon="['fas', 'circle']" size="3x" class="border astroid-color-picker" :style="{'color': scope.badge_bgcolor}" @click="_showBadgeBGColorPicker = true" />
                <input type="hidden" :name="config.name+`[badge_bgcolor]`" :id="config.id+`_badge_bgcolor`" :value="scope.badge_bgcolor" />
                <ColorPicker v-if="_showBadgeBGColorPicker"
                    theme="light"
                    :color="scope.badge_bgcolor"
                    :sucker-hide="true"
                    :sucker-canvas="null"
                    :sucker-area="[]"
                    :id="config.id+`_badge_bgcolor_colorpicker`"
                    @changeColor="(color) => {changeColor(color, 'badge_bgcolor')}"
                />
            </div>
        </div>
    </div>
</template>