<script setup>
const emit = defineEmits(['update:closeElement', 'update:saveElement']);
const props = defineProps(['element']);
const grid = [
    [12],
    [10, 2],
    [9, 3],
    [8, 4],
    [7, 5],
    [6, 6],
    [4, 4, 4],
    [3, 6, 3],
    [2, 6, 4],
    [3, 3, 3, 3],
    [2, 2, 2, 2, 2, 2]
]
</script>
<template>
    <div class="astroid-modal modal d-block" tabindex="-1" @click.self="emit('update:closeElement', props.element.id)">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-body">
                    {{ grid }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-as btn-as-light" @click="emit('update:closeElement', props.element.id)">Close</button>
                    <button type="button" class="btn btn-sm btn-as btn-primary btn-as-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>