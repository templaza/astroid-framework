import { createApp } from 'vue'
import App from './App.vue'

let as_sublayouts = document.querySelectorAll('.as-select-sublayout');
as_sublayouts.forEach(sublayout => {
    createApp(App, {
        sublayout_id : sublayout.id
    }).mount('#'+sublayout.id);
});