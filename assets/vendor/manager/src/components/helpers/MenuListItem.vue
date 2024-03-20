<script setup>
const props = defineProps(['menuitem', 'field', 'modelValue']);
function selectAll(menuitem, value) {
    menuitem.links.forEach(item => {
        props.modelValue[item.id] = value;
        if (item.links.length > 0) {
            selectAll(item, value);
        }
    });
}
</script>
<template>
<li class="my-2">
    <div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" v-model="props.modelValue[props.menuitem.id]" :id="props.field.input.id + `_` + props.menuitem.id">
            <label class="form-check-label" :for="props.field.input.id + `_` + props.menuitem.id">
                {{ props.menuitem.text }}
            </label>
            <div v-if="props.menuitem.links.length > 0" class="dropdown d-inline-block ms-2">
                <button class="btn as-btn-xs btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Submenu Items
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" @click.prevent="selectAll(props.menuitem, true)"><i class="fa-regular fa-square-check"></i> Select</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="selectAll(props.menuitem, false)"><i class="fa-regular fa-square"></i> Deselect</a></li>
                </ul>
            </div>
        </div>
    </div>
    <ul v-if="props.menuitem.links.length > 0" class="tree-sublist">
        <MenuListItem v-for="menuitem in props.menuitem.links" v-model="props.modelValue" :key="menuitem.id" :menuitem="menuitem" :field="props.field" />
    </ul>
</li>
</template>