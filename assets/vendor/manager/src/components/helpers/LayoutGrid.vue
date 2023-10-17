<script setup>
const emit = defineEmits(['update:closeElement', 'update:saveElement']);
const props = defineProps(['element']);
const grids = [
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

function chooseGrid(grid) {
    if (grid[0] === 0) {
        let resp = window.prompt("Please enter custom grid size (eg. 2+3+6+1)");
        emit('update:saveElement', resp.split('+'))
    } else {
        emit('update:saveElement', grid);
    }
}
</script>
<template>
    <div class="astroid-modal modal d-block" tabindex="-1" @click.self="emit('update:closeElement', props.element.id)">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select a Grid Layout</h5>
                    <button type="button" class="btn-close" aria-label="Close" @click="emit('update:closeElement', props.element.id)"></button>
                </div>

                <div class="modal-body">
                    <div class="row row-cols-4 g-4">
                        <div v-for="grid in grids">
                            <div class="row g-1 astroid-grid" @click="chooseGrid(grid)">
                                <div v-for="col in grid" :class="`col-`+col">
                                    <div class="border py-5 text-center astroid-grid-column">
                                        {{ col }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="astroid-grid" @click="chooseGrid([0])">
                            <div class="border py-5 text-center astroid-grid-column">
                                Custom
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>