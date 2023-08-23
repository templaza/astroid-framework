<script setup>
const props = defineProps(['id']);
</script>
<template>
    <div class="modal fade" :id="props.id" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="position-absolute top-0 end-0 p-2">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <ul class="nav nav-tabs" :id="`modal-tab`+props.id" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" :id="`general-tab`+props.id" data-bs-toggle="tab" :data-bs-target="`#general-tab-pane`+props.id" type="button" role="tab" aria-controls="general-tab-pane" aria-selected="true">General Settings</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :id="`design-tab`+props.id" data-bs-toggle="tab" :data-bs-target="`#design-tab-pane`+props.id" type="button" role="tab" aria-controls="design-tab-pane" aria-selected="false">Design Settings</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :id="`responsive-tab`+props.id" data-bs-toggle="tab" :data-bs-target="`#responsive-tab-pane`+props.id" type="button" role="tab" aria-controls="responsive-tab-pane" aria-selected="false">Responsive Settings</button>
                    </li>
                </ul>
                <div class="tab-content modal-body" :id="`modal-tab-content`+props.id">
                    <div class="tab-pane fade show active" :id="`general-tab-pane`+props.id" role="tabpanel" aria-labelledby="general-tab" tabindex="0"><slot name="general"></slot></div>
                    <div class="tab-pane fade" :id="`design-tab-pane`+props.id" role="tabpanel" aria-labelledby="design-tab" tabindex="0"><slot name="design"></slot></div>
                    <div class="tab-pane fade" :id="`responsive-tab-pane`+props.id" role="tabpanel" aria-labelledby="responsive-tab" tabindex="0"><slot name="responsive"></slot></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>